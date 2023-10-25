<?php

namespace App\Http\Controllers\pengaduan;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Models\PengaduanHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PengaduanChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (session('role_id') == 2 || session('role_id') == 3 || session('role_id') == 4) {
            return view('content.pengaduan.pengaduan-chat', [
                'code_number' => $this->generateUniqueCode('SIM'),
            ]);
        } else {
            return view('content.pages.pages-misc-not-authorized');
        }
    }

    /**
     * Generate a unique code based on the given prefix and year/month of creation.
     * The code will be incremented automatically based on the latest code in the database.
     *
     * @param string $prefix The prefix for the code, e.g. SIM.
     * @return string The generated code.
     */
    private function generateUniqueCode($prefix)
    {
        $currentYear = Carbon::now()->format('Y');
        $currentMonth = Carbon::now()->format('m');

        // Get the last visit order for the current month
        $lastPengaduan = Pengaduan::where('pengaduan_number', 'LIKE', "$prefix/$currentYear/$currentMonth/%")
            ->orderBy('pengaduan_id', 'desc')
            ->first();

        if ($lastPengaduan) {
            // Extract the last number from the last visit order in the current month
            $lastNumber = intval(substr($lastPengaduan->pengaduan_number, -5));

            // Check if it's still the same month
            if ($lastPengaduan->pengaduan_created_date->format('m') == $currentMonth) {
                $newNumber = $lastNumber + 1;
            } else {
                // If it's a new month, start with 00001
                $newNumber = 1;
            }
        } else {
            // If there are no previous visit orders for the current month, start with 00001
            $newNumber = 1;
        }

        $formattedNumber = sprintf("%05d", $newNumber);
        return $prefix . '/' . $currentYear . '/' . $currentMonth . '/' . $formattedNumber;
    }

    /**
     * save data to sys_pengaduan_history
     * 
     */
    private function pengaduan_history($pengaduanId, $historyDesc, $createBy, $status)
    {
        $pengaduan_history = PengaduanHistory::create([
            'pengaduan_id'                   => $pengaduanId,
            'pengaduan_status'               => $status,
            'pengaduan_history_desc'         => $historyDesc,
            'pengaduan_history_created_by'   => $createBy,
            'pengaduan_history_created_date' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        $pengaduan_history->save();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pengaduan_note'      => 'required|max:1000'
        ]);

        //   dd($request->all());
        // dd($validator->errors());

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
        }

        // panggil fungsi generateUniqueCode() untuk membuat pengaduan_number baru
        $pengaduanNumber = $this->generateUniqueCode('SIM');

        $pengaduan = Pengaduan::create([
            'pengaduan_status'        => 1, // terkirim
            'role_id'                 => session('role_id'),
            'pengaduan_number'        => $pengaduanNumber,
            'pengaduan_note'          => $request->pengaduan_note,
            'pengaduan_created_by'    => session('user_id'),
            'pengaduan_created_date'  => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        // Simpan data pengaduan ke history
        $pengaduanId = $pengaduan->pengaduan_id;
        $statusHistoryDescSending = 'Pengaduan ' . $request->pengaduan_number . ' status is Sending!';
        $createdBy = session('user_id');

        // Save the history for status 'Sending'
        $this->pengaduan_history($pengaduanId, $statusHistoryDescSending, $createdBy, 1); // Status code 1 for 'sending'

        return response()->json(['status' => true, 'message' => ['title' => 'Successfully created!', 'text' => 'Pengaduan ' . $request->visit_order_number . ' sending successfully!']]);
    }
}
