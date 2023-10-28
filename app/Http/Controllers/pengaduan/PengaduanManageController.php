<?php

namespace App\Http\Controllers\pengaduan;;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Models\PengaduanHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class PengaduanManageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (session('role_id') == 1 || session('role_id') == 2 || session('role_id') == 3 || session('role_id') == 4 || session('role_id') == 5) {
        return view('content.pengaduan.pengaduan-manage');
        } else {
        return view('content.pages.pages-misc-not-authorized');
        }
    }

    public function datatable(Request $request)
    {
        $role_id = session('role_id');
        $user_name = session('user_uniq_name');

        $columns = [
        0 => 'pengaduan_id',
        1 => 'pengaduan_number',
        2 => 'pengaduan_note',
        3 => 'pengaduan_answer',
        4 => 'role_id', 
        5 => 'pengaduan_created_by',
        6 => 'pengaduan_status',
        ];

        $search = [];
        $totalData = Pengaduan::where('pengaduan_status', '!=', 5)->count();
        $totalFiltered = $totalData;

        if (!empty($request->input())) {
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $order = 'pengaduan_id'; 
            $dir = 'desc'; 

            // Logika berdasarkan role_id
            if ($role_id == 1) {
                // Jika role_id adalah 1, tampilkan semua data
                $totalData = Pengaduan::with('role', 'user')->where('pengaduan_status', '!=', 5)->count();
                $totalFiltered = $totalData;
                // Tambahkan logika query sesuai kebutuhan
                $pengaduan = Pengaduan::with('role', 'user')
                    ->where('pengaduan_status', '!=', 5)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
            } elseif ($role_id >= 2 && $role_id <= 4) {
                // Jika role_id adalah 2, 3, atau 4, tampilkan data yang sesuai dengan role_id
                $totalData = Pengaduan::with('role', 'user')->where('pengaduan_status', '!=', 5)
                    ->where('role_id', $role_id)
                    ->count();
                $totalFiltered = $totalData;
                // Tambahkan logika query sesuai kebutuhan
                $pengaduan = Pengaduan::with('role', 'user')
                    ->where('pengaduan_status', '!=', 5)
                    ->where('role_id', $role_id)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
            }
        } else {
            $search = $request->input('search.value');

            $pengaduan = Pengaduan::with('role', 'user')
            ->where('pengaduan_number', 'LIKE', "%{$search}%")
            ->orWhere('pengaduan_note', 'LIKE', "%{$search}%")
            ->orWhere('pengaduan_answer', 'LIKE', "%{$search}%")
            ->orWhereRelation('role', 'role_name', 'LIKE', "%{$search}%")
            ->orWhereRelation('user', 'user_uniq_name', 'LIKE', "%{$search}%")
            ->where('pengaduan_status', '!=', 5)
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

            $totalFiltered = Pengaduan::where('pengaduan_number', 'LIKE', "%{$search}%")
            ->orWhere('pengaduan_note', 'LIKE', "%{$search}%")
            ->orWhere('pengaduan_answer', 'LIKE', "%{$search}%")
            ->orWhereRelation('role', 'role_name', 'LIKE', "%{$search}%")
            ->orWhereRelation('user', 'user_uniq_name', 'LIKE', "%{$search}%")
            ->where('pengaduan_status', '!=', 5)
            ->count();
        }
        } else {
        $start = 0;
        $pengaduan = Pengaduan::with('role')->get();
        }

        $data = [];

        if (!empty($pengaduan)) {
        $no = $start;
            foreach ($pengaduan as $row) {
                $nestedData['no']                    = ++$no;
                $nestedData['pengaduan_id']          = Crypt::encrypt($row->pengaduan_id);
                $nestedData['role_id']               = $role_id;
                $nestedData['pengaduan_number']      = $row->pengaduan_number;
                $nestedData['pengaduan_note']        = $row->pengaduan_note;
                $nestedData['user_name']             = $user_name;
                $nestedData['role_name']             = $row->role->role_name;
                $nestedData['pengaduan_created_by']  = $row->user->user_uniq_name;
                $nestedData['pengaduan_answer']      = $row->pengaduan_answer;
                $nestedData['pengaduan_status']      = $row->pengaduan_status;
                $data[] = $nestedData;
            }
        }

        if ($data) {
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'code' => 200,
            'data' => $data,
        ]);
        } else {
        return response()->json([
            'message' => 'Internal Server Error',
            'code' => 500,
            'data' => [],
        ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pengaduan = Pengaduan::with('role', 'history.user')->where('pengaduan_id', Crypt::decrypt($id))->first();
        if($pengaduan) {

            foreach ($pengaduan->history as $his) {
                $his['date_created_format'] = Carbon::parse($his->pengaduan_history_created_date)->translatedFormat('d F Y, H:i');
            }

            return response()->json(['status' => true, 'data' => $pengaduan]);
        } else {
            return response()->json(['status' => false, 'data' => []]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role_id = session('role_id');
        $user_id = session('user_id');

        $validator = null;

        if ($role_id == 1) {
            $validator = Validator::make($request->all(), [
                'pengaduan_answer' => 'nullable|max:1000'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'pengaduan_note' => 'nullable|max:1000'
            ]);
        }

        // dd($request->all());
        // dd($validator->errors());

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the fields']]);
        }

        $pengaduan = Pengaduan::where('pengaduan_id', Crypt::decrypt($id))->first();
        $pengaduan->pengaduan_number = $request->pengaduan_number;

        if ($role_id == 1) {
            $pengaduan->pengaduan_answer = $request->pengaduan_answer;
            // Jika pengaduan_answer diisi, ubah pengaduan_status menjadi 2 (selesai)
            $pengaduan->pengaduan_status = 2;

            // Simpan data terjawab ke history
            $pengaduanId = $pengaduan->pengaduan_id;
            $pengaduanStatus = $pengaduan->pengaduan_status;
            $statusHistoryDescAnswered = 'Pengaduan ' . $request->pengaduan_number . ' status is Answered!';

            $pengaduan_history = PengaduanHistory::create([
                'pengaduan_id'                   => $pengaduanId,
                'pengaduan_status'               => $pengaduanStatus,
                'pengaduan_history_desc'         => $statusHistoryDescAnswered,
                'pengaduan_history_created_by'   => $user_id,
                'pengaduan_history_created_date' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
    
            $pengaduan_history->save();
        } else {
            $pengaduan->pengaduan_note = $request->pengaduan_note;
        }

        $pengaduan->pengaduan_updated_by = session('user_id');
        $pengaduan->pengaduan_updated_date = Carbon::now()->format('Y-m-d H:i:s');
        $pengaduan->save();

        // Menambahkan log history ketika update pengaduan
        $pengaduanId = $pengaduan->pengaduan_id;
        $pengaduanStatus = $pengaduan->pengaduan_status;
        $statusHistoryDescSending = 'Pengaduan ' . $request->pengaduan_number . ' status has been updated!';

        // $this->pengaduan_history($pengaduanId, $pengaduanStatus, $statusHistoryDescSending, $createdBy);

        $pengaduan_history = PengaduanHistory::create([
            'pengaduan_id'                   => $pengaduanId,
            'pengaduan_status'               => $pengaduanStatus,
            'pengaduan_history_desc'         => $statusHistoryDescSending,
            'pengaduan_history_created_by'   => $user_id,
            'pengaduan_history_created_date' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        $pengaduan_history->save();

        return response()->json(['status' => true, 'message' => ['title' => 'Successfully Updated!', 'text' => 'Pengaduan ' . $request->pengaduan_number . ' updated successfully!']]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $pengaduan = Pengaduan::where('pengaduan_id', Crypt::decrypt($request->pengaduan_id))->first();
        if ($pengaduan) {
            $pengaduan->pengaduan_status        = '5';
            $pengaduan->pengaduan_deleted_by    = session('user_id');
            $pengaduan->pengaduan_deleted_date  = Carbon::now()->format('Y-m-d H:i:s');
            $pengaduan->save();
            return response()->json(['status' => true, 'message' => ['title' => 'Pengaduan Deleted!', 'text' => 'Pengaduan ' . $request->pengaduan_number . ' has been deleted!']]);
        } else {
            return response()->json(['status' => false, 'message' => ['title' => 'Pengaduan not Deleted!', 'text' => 'Pengaduan ' . $request->pengaduan_number . ' not deleted!']]);
        }
    }
}
