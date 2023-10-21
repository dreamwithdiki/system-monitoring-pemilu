<?php

namespace App\Http\Controllers\pendukung;

use App\Http\Controllers\Controller;
use App\Models\DataDpt;
use App\Models\Kecamatan;
use App\Models\KecamatanCeklisDpt;
use Illuminate\Http\Request;
use App\Models\MasterDistricts;
use App\Models\MasterProvinces;
use App\Models\MasterRegencies;
use App\Models\MasterVillages;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class DataDptController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(session('role_id') == 1 || session('role_id') == 2 || session('role_id') == 3 || session('role_id') == 4){
            // return view('content.pendukung.dpt');
            $ceklisKecamatan = Kecamatan::orderby('kecamatan_name', 'ASC')->isActive()->get();
          
            return view('content.pendukung.dpt', [
              'ceklisKecamatan'=> $ceklisKecamatan
            ]);
        } else {
            return view('content.pages.pages-misc-not-authorized');
        }
    }

    // public function datatable(Request $request)
    // {
    //     $columns = [
    //       0 => 'dpt_id',
    //       1 => 'dpt_nik',
    //       2 => 'dpt_name',
    //       3 => 'dpt_jenkel',
    //       4 => 'dpt_province',
    //       5 => 'dpt_regency',
    //       6 => 'dpt_district',
    //       7 => 'dpt_village',
    //       8 => 'tps_id', // diambil dari data TPS (sesuai dengan Kecamatan dan kelurahan)
    //     ];

    //     $search = [];
    //     $totalData = DataDpt::with('province', 'regency', 'district', 'village', 'tps')->where('dpt_status', '!=', 5)->count();
    //     $totalFiltered = $totalData;

    //     if (!empty($request->input())) {
    //         $limit = $request->input('length');
    //         $start = $request->input('start');
    //         $order = $columns[$request->input('order.0.column')];
    //         $dir = $request->input('order.0.dir');
    
    //         if (empty($request->input('search.value'))) {
    //           $data_dpt = DataDpt::where('dpt_status', '!=', 5)
    //             ->offset($start)
    //             ->limit($limit)
    //             ->orderBy($order, $dir)
    //             ->get();
    //         } else {
    //           $search = $request->input('search.value');
    
    //           $data_dpt = DataDpt::with('province', 'regency', 'district', 'village', 'tps')->where('dpt_nik', 'LIKE', "%{$search}%")
    //             ->where('dpt_status', '!=', 5)
    //             ->orWhere('dpt_name', 'LIKE', "%{$search}%")
    //             ->orWhereRelation('province', 'name', 'LIKE', "%{$search}%")
    //             ->orWhereRelation('district', 'name', 'LIKE', "%{$search}%")
    //             ->orWhereRelation('regency', 'name', 'LIKE', "%{$search}%")
    //             ->orWhereRelation('village', 'name', 'LIKE', "%{$search}%")
    //             ->orWhereRelation('tps', 'tps_name', 'LIKE', "%{$search}%")
    //             ->offset($start)
    //             ->limit($limit)
    //             ->orderBy($order, $dir)
    //             ->get();
    
    //           $totalFiltered = DataDpt::with('province', 'regency', 'district', 'village', 'tps')->where('dpt_nik', 'LIKE', "%{$search}%")
    //             ->where('dpt_status', '!=', 5)
    //             ->orWhere('dpt_name', 'LIKE', "%{$search}%")
    //             ->orWhereRelation('province', 'name', 'LIKE', "%{$search}%")
    //             ->orWhereRelation('district', 'name', 'LIKE', "%{$search}%")
    //             ->orWhereRelation('regency', 'name', 'LIKE', "%{$search}%")
    //             ->orWhereRelation('village', 'name', 'LIKE', "%{$search}%")
    //             ->orWhereRelation('tps', 'tps_name', 'LIKE', "%{$search}%")
    //             ->count();
    //         }
    //     } else {
    //       $start = 0;  
    //       $data_dpt = DataDpt::with('province', 'regency', 'district', 'village', 'tps')->where('dpt_status', '!=', 5)->get();
    //     }

    //     $data = [];

    //     if (!empty($data_dpt)) {
    //       $no = $start;
    //       foreach ($data_dpt as $dpt) {
    //         $nestedData['no']               = ++$no;
    //         $nestedData['dpt_id']           = Crypt::encrypt($dpt->dpt_id);
    //         $nestedData['dpt_nik']          = $dpt->dpt_nik;
    //         $nestedData['dpt_name']         = $dpt->dpt_name;
    //         $nestedData['dpt_jenkel']       = $dpt->dpt_jenkel;
    //         $nestedData['dpt_province']     = $dpt->province->name;
    //         $nestedData['dpt_regency']      = $dpt->regency->name;
    //         $nestedData['dpt_district']     = $dpt->district->name;
    //         $nestedData['dpt_village']      = $dpt->village->name;
    //         $nestedData['tps_name']         = $dpt->tps->tps_code .'-'. $dpt->tps->tps_name;
    //         $nestedData['dpt_status']       = $dpt->dpt_status;
    //         $data[] = $nestedData;
    //       }
    //     }

    //     if ($data) {
    //       return response()->json([
    //         'draw' => intval($request->input('draw')),
    //         'recordsTotal' => intval($totalData),
    //         'recordsFiltered' => intval($totalFiltered),
    //         'code' => 200,
    //         'data' => $data,
    //       ]);
    //     } else {
    //       return response()->json([
    //         'message' => 'Internal Server Error',
    //         'code' => 500,
    //         'data' => [],
    //       ]);
    //     }
    // }

    public function datatable(Request $request)
    {

        // Mendapatkan role_id dari sesi
        $role_id = session('role_id');

        $columns = [
          0 => 'dpt_id',
          1 => 'dpt_nik',
          2 => 'dpt_name',
          3 => 'dpt_jenkel',
          4 => 'tps_id', // diambil dari data TPS (sesuai dengan Kecamatan dan kelurahan)
        ];

        $search = [];
        $totalData = 0;
        $totalFiltered = 0;
        $data_dpt = [];

        
        if (!empty($request->input())) {
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
    
            if (empty($request->input('search.value'))) {
              // Logika berdasarkan role_id
              if ($role_id == 1) {
                  // Jika role_id adalah 1, tampilkan semua data
                  $totalData = DataDpt::where('dpt_status', '!=', 5)->count();
                  $totalFiltered = $totalData;
                  // Tambahkan logika query sesuai kebutuhan
                  $data_dpt = DataDpt::with('tps')->where('dpt_status', '!=', 5)
                      ->offset($start)
                      ->limit($limit)
                      ->orderBy($order, $dir)
                      ->get();
              } elseif ($role_id >= 2 && $role_id <= 4) {
                  // Jika role_id adalah 2, 3, atau 4, tampilkan data yang sesuai dengan role_id
                  $totalData = DataDpt::with('tps')->where('dpt_status', '!=', 5)
                      ->where('role_id', $role_id)
                      ->count();
                  $totalFiltered = $totalData;
                  // Tambahkan logika query sesuai kebutuhan
                  $data_dpt = DataDpt::with('tps')->where('dpt_status', '!=', 5)
                      ->where('role_id', $role_id)
                      ->offset($start)
                      ->limit($limit)
                      ->orderBy($order, $dir)
                      ->get();
              }

            } else {
              $search = $request->input('search.value');
    
              $data_dpt = DataDpt::with('tps')->where('dpt_nik', 'LIKE', "%{$search}%")
                ->where('dpt_status', '!=', 5)
                ->orWhere('dpt_name', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
    
              $totalFiltered = DataDpt::with('tps')->where('dpt_nik', 'LIKE', "%{$search}%")
                ->where('dpt_status', '!=', 5)
                ->orWhere('dpt_name', 'LIKE', "%{$search}%")
                ->count();
            }
        } else {
          $start = 0;  
          $data_dpt = DataDpt::with('tps')->where('dpt_status', '!=', 5)->get();
        }

        $data = [];

        if (!empty($data_dpt)) {
          $no = $start;
          foreach ($data_dpt as $dpt) {
            $nestedData['no']               = ++$no;
            $nestedData['dpt_id']           = Crypt::encrypt($dpt->dpt_id);
            $nestedData['dpt_nik']          = $dpt->dpt_nik;
            $nestedData['dpt_name']         = $dpt->dpt_name;
            $nestedData['dpt_jenkel']       = $dpt->dpt_jenkel;
            $nestedData['tps_name']         = $dpt->tps->tps_code .'-'. $dpt->tps->tps_name;
            $nestedData['dpt_status']       = $dpt->dpt_status;
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
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dpt_nik'          => 'required|max:16',
            'dpt_name'         => 'required|max:255',
            'dpt_jenkel'       => 'required',
            'kecamatan_type'   => 'required|array|min:1',
            'kecamatan_type.*' => 'integer',
            'tps_id'           => 'required'
        ]);
  
        //  dd($validator->errors());
        // dd($request->all());
  
        if ($validator->fails()) {
        return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
        }
        
        // dpt Status
        $status = !empty($request->dpt_status) && $request->dpt_status == 'on' ? 2 : 1;

         // dpt NIK check duplicate entry
         $nik_exist = DataDpt::where('dpt_nik', $request->dpt_nik)->exists();
         if ($nik_exist) {
           return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'NIK already registered on another DPT!']]);
         }

        $dataDpt = new DataDpt();
        $dataDpt->dpt_status        = $status;
        $dataDpt->dpt_nik           = $request->dpt_nik;
        $dataDpt->dpt_name          = $request->dpt_name;
        $dataDpt->dpt_jenkel        = $request->dpt_jenkel;
        $dataDpt->tps_id            = $request->tps_id;
        $dataDpt->role_id           = session('role_id');
        $dataDpt->dpt_created_by    = session('user_id');
        $dataDpt->dpt_created_date  = Carbon::now()->format('Y-m-d H:i:s');
        $dataDpt->save();
        
        // return response()->json(['status' => true, 'message' => ['title' => 'Successfully created!', 'text' => 'DPT ' . $request->dpt_name . ' created successfully!']]);
        $kecamatan_type_data = $request->kecamatan_type;
        $dpt_id            = $dataDpt->dpt_id;
        
        // Menyimpan data checklist kecamatan yang dipilih 
        foreach ($kecamatan_type_data as $check) {
          $checklist_kec_object = Kecamatan::where('kecamatan_id', $check)->first();
          if (!empty($checklist_kec_object)) {
              $checklist_group_data = [
                  'dpt_id'                 => $dpt_id,
                  'kecamatan_id'           => $checklist_kec_object->kecamatan_id,
                  'kecamatan_created_by'   => session('user_id'),
                  'kecamatan_created_date' => now()->format('Y-m-d H:i:s')
              ];
              
              KecamatanCeklisDpt::create(
                  [
                      'dpt_id'                        => $dpt_id,
                      'kecamatan_id'                  => $checklist_kec_object->kecamatan_id,
                      'kecamatan_ceklis_dpt_created_by'   => session('user_id'),
                      'kecamatan_ceklis_dpt_created_date' => now()->format('Y-m-d H:i:s')
                  ],
                  $checklist_group_data
              );
            
          }
      }

      return response()->json([
          'status' => true,
          'message' => ['title' => 'Successfully created!', 'text' => 'DPT ' . $request->dpt_name . ' created successfully!'],
          'data' => $checklist_group_data
      ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DataDpt  $dataDpt
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $data_dpt = DataDpt::with('kecamatan_ceklis', 'tps')->where('dpt_id', Crypt::decrypt($id))->first();
        $data_dpt = DataDpt::with(['kecamatan_ceklis.checklist_kec', 'tps'])->where('dpt_id', Crypt::decrypt($id))->first();
        if($data_dpt) {
            return response()->json(['status' => true, 'data' => $data_dpt]);
        } else {
            return response()->json(['status' => false, 'data' => []]);
        }
    }

    public function statusUpdate(Request $request)
    {
      $this->validate($request, [
        'dpt_id'     => 'required',
        'dpt_status' => 'required',
      ]);

      $dpt = DataDpt::where('dpt_id', Crypt::decrypt($request->dpt_id))->first();
      $dpt->dpt_status        = $request->dpt_status;
      $dpt->dpt_updated_by    = session('user_id');
      $dpt->save();

      if ($request->dpt_status == 2) {
        return response()->json(['status' => true, 'message' => ['title' => 'DPT Activated!', 'text' => 'DPT ' . $dpt->dpt_name . ' status has been activated!']]);
      } else {
        return response()->json(['status' => true, 'message' => ['title' => 'DPT Deactivated!', 'text' => 'DPT ' . $dpt->dpt_name . ' status has been deactivated!']]);
      }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\DataDpt  $dataDpt
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
         $this->validate($request, [
            'dpt_nik'               => 'required|max:16',
            'dpt_name'              => 'required|max:255',
            'dpt_jenkel'            => 'required',
            'edit_kecamatan_type'   => 'required|array|min:1',
            'edit_kecamatan_type.*' => 'integer',
            'tps_id'                => 'required'
          ]);
  
          // dpt Status
          $status = !empty($request->dpt_status) && $request->dpt_status == 'on' ? 2 : 1;

          // dpt NIK check duplicate entry
         $nik_exist = DataDpt::where('dpt_nik', $request->dpt_nik)->where('dpt_id', '!=', Crypt::decrypt($id))->exists();
         if ($nik_exist) {
           return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'NIK already registered on another DPT!']]);
         }
          
          $data_dpt = DataDpt::where('dpt_id', Crypt::decrypt($id))->first();
          
          $data_dpt->dpt_status            = $status;
          $data_dpt->dpt_nik               = $request->dpt_nik;
          $data_dpt->dpt_name              = $request->dpt_name;
          $data_dpt->dpt_jenkel            = $request->dpt_jenkel;
          $data_dpt->tps_id                = $request->tps_id;
          $data_dpt->role_id               = session('role_id');
          $data_dpt->dpt_updated_by        = session('user_id');
          $data_dpt->dpt_updated_date      = Carbon::now()->format('Y-m-d H:i:s');
          $data_dpt->save();
          
          $kecamatan_type_data = $request->edit_kecamatan_type;
          $dpt_id     = $data_dpt->dpt_id;

          // Menghapus data checklist kec. yang tidak dipilih kembali
          KecamatanCeklisDpt::where('dpt_id', $dpt_id)->whereNotIn('kecamatan_id', $kecamatan_type_data)->delete();

          // Menyimpan atau memperbarui data checklist kec. yang dipilih kembali
          foreach ($kecamatan_type_data as $check) {
            $checklist_kec_object = Kecamatan::where('kecamatan_id', $check)->first();
            if (!empty($checklist_kec_object)) {
                $checklist_group_data = [
                    'dpt_id'                        => $dpt_id,
                    'kecamatan_id'                  => $checklist_kec_object->kecamatan_id,
                    'kecamatan_ceklis_dpt_created_by'   => session('user_id'),
                    'kecamatan_ceklis_dpt_created_date' => now()->format('Y-m-d H:i:s')
                ];
                
                 // Update or insert the record based on dpt_id and kecamatan_id
                KecamatanCeklisDpt::updateOrInsert(
                    [
                        'dpt_id'        => $dpt_id,
                        'kecamatan_id'  => $checklist_kec_object->kecamatan_id,
                    ],
                    $checklist_group_data
                );
              
            }
        }
        // return response()->json(['status' => true, 'message' => ['title' => 'Successfully Updated!', 'text' => 'DPT ' . $request->dpt_name . ' updated successfully!']]);

        return response()->json([
            'status' => true,
            'message' => ['title' => 'Successfully created!', 'text' => 'Caleg ' . $request->dpt_name . ' updated successfully!'],
            'data' => $checklist_group_data
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DataDpt  $dataDpt
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $dpt = DataDpt::where('dpt_id', Crypt::decrypt($request->dpt_id))->first();
        if ($dpt) {
            $dpt->dpt_status        = '5';
            $dpt->dpt_deleted_by    = session('user_id');
            $dpt->dpt_deleted_date  = Carbon::now()->format('Y-m-d H:i:s');
            $dpt->save();
            return response()->json(['status' => true, 'message' => ['title' => 'DPT Deleted!', 'text' => 'DPT ' . $request->dpt_name . ' has been deleted!']]);
        } else {
            return response()->json(['status' => false, 'message' => ['title' => 'DPT not Deleted!', 'text' => 'DPT ' . $request->dpt_name . ' not deleted!']]);
        }
    }

}
