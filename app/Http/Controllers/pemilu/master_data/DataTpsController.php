<?php

namespace App\Http\Controllers\pemilu\master_data;

use App\Http\Controllers\Controller;
use App\Models\DataTps;
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

class DataTpsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(session('role_id') == 1 || session('role_id') == 2 || session('role_id') == 4){
            return view('content.pemilu.master-data.tps');
            } else {
            return view('content.pages.pages-misc-not-authorized');
        }
    }

    public function find(Request $request)
    {
      $search = $request->search;
      $data_tps = DataTps::orderby('tps_id', 'DESC')
        ->select('tps_id', 'tps_code', 'tps_name')
        ->where('tps_name', 'like', '%' . $search . '%')
        ->orWhere('tps_code', 'like', '%' . $search . '%')
        ->isActive()
        ->get();  

      $response = array();
      foreach ($data_tps as $tps) {
        $response[] = array(
          "id"    => $tps->tps_id,
          "text"  => $tps->tps_code . ' - ' . $tps->tps_name
        );
      }

      return response()->json($response);
    }

    // public function datatable(Request $request)
    // {
    //     $columns = [
    //       0 => 'tps_id',
    //       1 => 'tps_code',
    //       2 => 'tps_name',
    //       3 => 'tps_address',
    //       4 => 'tps_province',
    //       5 => 'tps_regency',
    //       6 => 'tps_district',
    //       7 => 'tps_village',
    //       8 => 'tps_saksi', // buat status 4 = saksi
    //       9 => 'tps_suara_caleg',
    //       9 => 'tps_suara_partai',
    //       9 => 'tps_docs',
    //     ];

    //     $search = [];
    //     $totalData = DataTps::with('province', 'regency', 'district', 'village', 'role')->where('tps_status', '!=', 5)->count();
    //     $totalFiltered = $totalData;

    //     if (!empty($request->input())) {
    //         $limit = $request->input('length');
    //         $start = $request->input('start');
    //         $order = $columns[$request->input('order.0.column')];
    //         $dir = $request->input('order.0.dir');
    
    //         if (empty($request->input('search.value'))) {
    //           $data_tps = DataTps::where('tps_status', '!=', 5)
    //             ->offset($start)
    //             ->limit($limit)
    //             ->orderBy($order, $dir)
    //             ->get();
    //         } else {
    //           $search = $request->input('search.value');
    
    //           $data_tps = DataTps::with('province', 'regency', 'district', 'village', 'role')->where('tps_code', 'LIKE', "%{$search}%")
    //             ->where('tps_status', '!=', 5)
    //             ->orWhere('tps_name', 'LIKE', "%{$search}%")
    //             ->orWhereRelation('province', 'name', 'LIKE', "%{$search}%")
    //             ->orWhereRelation('district', 'name', 'LIKE', "%{$search}%")
    //             ->orWhereRelation('regency', 'name', 'LIKE', "%{$search}%")
    //             ->orWhereRelation('village', 'name', 'LIKE', "%{$search}%")
    //             ->orWhereRelation('role', 'role_name', 'LIKE', "%{$search}%")
    //             ->offset($start)
    //             ->limit($limit)
    //             ->orderBy($order, $dir)
    //             ->get();
    
    //           $totalFiltered = DataTps::with('province', 'regency', 'district', 'village', 'role')->where('tps_code', 'LIKE', "%{$search}%")
    //             ->where('tps_status', '!=', 5)
    //             ->orWhere('tps_name', 'LIKE', "%{$search}%")
    //             ->orWhereRelation('province', 'name', 'LIKE', "%{$search}%")
    //             ->orWhereRelation('district', 'name', 'LIKE', "%{$search}%")
    //             ->orWhereRelation('regency', 'name', 'LIKE', "%{$search}%")
    //             ->orWhereRelation('village', 'name', 'LIKE', "%{$search}%")
    //             ->orWhereRelation('role', 'role_name', 'LIKE', "%{$search}%")
    //             ->count();
    //         }
    //     } else {
    //       $start = 0;  
    //       $data_tps = DataTps::with('province', 'regency', 'district', 'village', 'role')->where('tps_status', '!=', 5)->get();
    //     }

    //     $data = [];

    //     if (!empty($data_tps)) {
    //       $no = $start;
    //       foreach ($data_tps as $tps) {
    //         $nestedData['no']               = ++$no;
    //         $nestedData['tps_id']           = Crypt::encrypt($tps->tps_id);
    //         $nestedData['tps_code']         = $tps->tps_code;
    //         $nestedData['tps_name']         = $tps->tps_name;
    //         $nestedData['tps_address']      = $tps->tps_address;
    //         $nestedData['tps_province']     = $tps->province->name;
    //         $nestedData['tps_regency']      = $tps->regency->name;
    //         $nestedData['tps_district']     = $tps->district->name;
    //         $nestedData['tps_village']      = $tps->village->name;
    //         $nestedData['role_name']        = $tps->role->role_name;
    //         $nestedData['tps_suara_caleg']  = $tps->tps_suara_caleg;
    //         $nestedData['tps_suara_partai'] = $tps->tps_suara_partai;
    //         $nestedData['tps_docs']         = $tps->tps_docs;
    //         $nestedData['tps_status']       = $tps->tps_status;
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

        $role_id = session('role_id');

        $columns = [
          0 => 'tps_id',
          1 => 'tps_code',
          2 => 'tps_name',
          3 => 'tps_address',
          4 => 'tps_province',
          5 => 'tps_regency',
          6 => 'tps_district',
          7 => 'tps_village',
          8 => 'tps_saksi', // buat status 4 = saksi
          9 => 'tps_suara_caleg',
          9 => 'tps_suara_partai',
          9 => 'tps_docs',
        ];

        $search = [];
        $totalData = 0;
        $totalFiltered = 0;
        $data_tps = [];

        if (!empty($request->input())) {
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
    
            if (empty($request->input('search.value'))) {
               // Logika berdasarkan role_id
               if ($role_id == 1) {
                  // Jika role_id adalah 1, tampilkan semua data
                  $totalData = DataTps::where('tps_status', '!=', 5)->count();
                  $totalFiltered = $totalData;
                  // Tambahkan logika query sesuai kebutuhan
                  $data_tps = DataTps::with('province', 'regency', 'district', 'village', 'role')
                      ->where('tps_status', '!=', 5)
                      ->offset($start)
                      ->limit($limit)
                      ->orderBy($order, $dir)
                      ->get();
              } elseif ($role_id >= 2 && $role_id <= 4) {
                  // Jika role_id adalah 2, 3, atau 4, tampilkan data yang sesuai dengan role_id
                  $totalData = DataTps::where('tps_status', '!=', 5)
                      ->where('role_id', $role_id)
                      ->count();
                  $totalFiltered = $totalData;
                  // Tambahkan logika query sesuai kebutuhan
                  $data_tps = DataTps::with('province', 'regency', 'district', 'village', 'role')
                      ->where('tps_status', '!=', 5)
                      ->where('role_id', $role_id)
                      ->offset($start)
                      ->limit($limit)
                      ->orderBy($order, $dir)
                      ->get();
              }
            } else {
              $search = $request->input('search.value');
    
              $data_tps = DataTps::with('province', 'regency', 'district', 'village', 'role')->where('tps_code', 'LIKE', "%{$search}%")
                ->where('tps_status', '!=', 5)
                ->orWhere('tps_name', 'LIKE', "%{$search}%")
                ->orWhereRelation('province', 'name', 'LIKE', "%{$search}%")
                ->orWhereRelation('district', 'name', 'LIKE', "%{$search}%")
                ->orWhereRelation('regency', 'name', 'LIKE', "%{$search}%")
                ->orWhereRelation('village', 'name', 'LIKE', "%{$search}%")
                ->orWhereRelation('role', 'role_name', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
    
              $totalFiltered = DataTps::with('province', 'regency', 'district', 'village', 'role')->where('tps_code', 'LIKE', "%{$search}%")
                ->where('tps_status', '!=', 5)
                ->orWhere('tps_name', 'LIKE', "%{$search}%")
                ->orWhereRelation('province', 'name', 'LIKE', "%{$search}%")
                ->orWhereRelation('district', 'name', 'LIKE', "%{$search}%")
                ->orWhereRelation('regency', 'name', 'LIKE', "%{$search}%")
                ->orWhereRelation('village', 'name', 'LIKE', "%{$search}%")
                ->orWhereRelation('role', 'role_name', 'LIKE', "%{$search}%")
                ->count();
            }
        } else {
          $start = 0;  
          $data_tps = DataTps::with('province', 'regency', 'district', 'village', 'role')->where('tps_status', '!=', 5)->get();
        }

        $data = [];

        if (!empty($data_tps)) {
          $no = $start;
          foreach ($data_tps as $tps) {
            $nestedData['no']               = ++$no;
            $nestedData['tps_id']           = Crypt::encrypt($tps->tps_id);
            $nestedData['tps_code']         = $tps->tps_code;
            $nestedData['tps_name']         = $tps->tps_name;
            $nestedData['tps_address']      = $tps->tps_address;
            $nestedData['tps_province']     = $tps->province->name;
            $nestedData['tps_regency']      = $tps->regency->name;
            $nestedData['tps_district']     = $tps->district->name;
            $nestedData['tps_village']      = $tps->village->name;
            $nestedData['role_name']        = $tps->role->role_name;
            $nestedData['tps_suara_caleg']  = $tps->tps_suara_caleg;
            $nestedData['tps_suara_partai'] = $tps->tps_suara_partai;
            $nestedData['tps_docs']         = $tps->tps_docs;
            $nestedData['tps_status']       = $tps->tps_status;
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
            'tps_code'          => 'required|max:255',
            'tps_name'          => 'required|max:255',
            'tps_address'       => 'required|max:255',
            'tps_province'      => 'required',
            'tps_regency'       => 'required',
            'tps_saksi'         => 'required',
            'tps_suara_caleg'   => 'required',
            'tps_suara_partai'  => 'required',
            'tps_docs'          => 'required|file|image|mimes:jpeg,png,jpg',
        ]);
  
        //  dd($validator->errors());
        // dd($request->all());
  
        if ($validator->fails()) {
        return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
        }
        
        // tps Status
        $status = !empty($request->tps_status) && $request->tps_status == 'on' ? 2 : 1;

         // tps code check duplicate entry
         $code_exist = DataTps::where('tps_code', $request->tps_code)->exists();
         if ($code_exist) {
           return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'Code already registered on another TPS!']]);
         }

        $timeNow = Carbon::now()->format('Ymd');

        // Check if a photo is uploaded
        if ($request->hasFile('tps_docs')) {
            $image = $request->file('tps_docs');

             // Compress the image and save it
             $filename = Carbon::now()->format('Hisu_').'tps'.($request->caleg_id).'.'.$image->getClientOriginalExtension();
             $compressedImage = Image::make($image)->resize(300, 300, function ($constraint) {
                 $constraint->aspectRatio();
             });
             Storage::disk('public')->put('tps_uploads/'.$timeNow.'/'.$filename, $compressedImage->encode());
        } else {
            // If no photo is uploaded, use default.jpeg
            $filename = 'default.jpeg';
        }

        DataTps::create([
            'tps_status'      => $status,
            'tps_code'        => $request->tps_code,
            'tps_name'        => $request->tps_name,
            'tps_address'     => $request->tps_address,
            'tps_province'    => $request->tps_province,
            'tps_regency'     => $request->tps_regency,
            'tps_district'    => $request->tps_district,
            'tps_village'     => $request->tps_village,
            'tps_saksi'       => $request->tps_saksi,
            'tps_suara_caleg' => $request->tps_suara_caleg,
            'tps_suara_partai'=> $request->tps_suara_partai,
            'tps_docs'        => $filename,
            'role_id'         => session('role_id'),
            'tps_created_by'  => session('user_id'),
            'tps_created_date'=> Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        return response()->json(['status' => true, 'message' => ['title' => 'Successfully created!', 'text' => 'TPS ' . $request->tps_name . ' created successfully!']]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DataTps  $dataTps
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data_tps = DataTps::with('province', 'regency', 'district', 'village', 'role')->where('tps_id', Crypt::decrypt($id))->first();
        if($data_tps) {
            return response()->json(['status' => true, 'data' => $data_tps]);
        } else {
            return response()->json(['status' => false, 'data' => []]);
        }
    }

    public function statusUpdate(Request $request)
    {
      $this->validate($request, [
        'tps_id'     => 'required',
        'tps_status' => 'required',
      ]);

      $tps = DataTps::where('tps_id', Crypt::decrypt($request->tps_id))->first();
      $tps->tps_status        = $request->tps_status;
      $tps->tps_updated_by    = session('user_id');
      $tps->save();

      if ($request->tps_status == 2) {
        return response()->json(['status' => true, 'message' => ['title' => 'TPS Activated!', 'text' => 'TPS ' . $tps->tps_name . ' status has been activated!']]);
      } else {
        return response()->json(['status' => true, 'message' => ['title' => 'TPS Deactivated!', 'text' => 'TPS ' . $tps->tps_name . ' status has been deactivated!']]);
      }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\DataTps  $dataTps
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
         $this->validate($request, [
            'tps_code'          => 'required|max:255',
            'tps_name'          => 'required|max:255',
            'tps_address'       => 'required|max:255',
            'tps_province'      => 'required',
            'tps_regency'       => 'required',
            'tps_saksi'         => 'required',
            'tps_suara_caleg'   => 'required',
            'tps_suara_partai'  => 'required',
            'tps_docs'          => 'nullable|file|image|mimes:jpeg,png,jpg',
          ]);
  
          // tps Status
          $status = !empty($request->tps_status) && $request->tps_status == 'on' ? 2 : 1;

          // tps code check duplicate entry
         $code_exist = DataTps::where('tps_code', $request->tps_code)->where('tps_id', '!=', Crypt::decrypt($id))->exists();
         if ($code_exist) {
           return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'Code already registered on another TPS!']]);
         }
          
          $data_tps = DataTps::where('tps_id', Crypt::decrypt($id))->first();
          
          $timeNow = Carbon::now()->format('Ymd');
          if ($request->hasFile('tps_docs')) {
              $image = $request->file('tps_docs');

              // Get the old image path from the database
              $dir = Carbon::parse($data_tps->caleg_created_date)->format('Ymd');
              $oldImage = $dir.'/'.$data_tps->tps_docs;

              // Check if the old image is not the default image and exists in the storage
              if ($oldImage != 'default.jpeg' && Storage::disk('public')->exists('tps_uploads/' . $oldImage)) {
                  // Unlink (delete) the old image
                  Storage::disk('public')->delete('tps_uploads/' . $oldImage);
              }
              // Compress the image and save it
              $filename = Carbon::now()->format('Hisu_').'tps'.($request->caleg_id).'.'.$image->getClientOriginalExtension();
              $compressedImage = Image::make($image)->resize(300, 300, function ($constraint) {
                  $constraint->aspectRatio();
              });
              Storage::disk('public')->put('tps_uploads/'.$timeNow.'/'.$filename, $compressedImage->encode());

              // Update the tps_docs column only if the photo is changed
              $data_tps->tps_docs = $filename;
          }
          
          $data_tps->tps_status            = $status;
          $data_tps->tps_code              = $request->tps_code;
          $data_tps->tps_name              = $request->tps_name;
          $data_tps->tps_address           = $request->tps_address;
          $data_tps->tps_province          = $request->tps_province;
          $data_tps->tps_regency           = $request->tps_regency;
          $data_tps->tps_district          = $request->tps_district;
          $data_tps->tps_village           = $request->tps_village;
          $data_tps->tps_saksi             = $request->tps_saksi;
          $data_tps->tps_suara_caleg       = $request->tps_suara_caleg;
          $data_tps->tps_suara_partai      = $request->tps_suara_partai;
          $data_tps->role_id               = session('role_id');
          $data_tps->tps_updated_by        = session('user_id');
          $data_tps->tps_updated_date      = Carbon::now()->format('Y-m-d H:i:s');
          $data_tps->save();
          
          return response()->json(['status' => true, 'message' => ['title' => 'Successfully Updated!', 'text' => 'TPS ' . $request->tps_name . ' updated successfully!']]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DataTps  $dataTps
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $tps = DataTps::where('tps_id', Crypt::decrypt($request->tps_id))->first();
        if ($tps) {
            $dir = Carbon::parse($tps->tps_created_date)->format('Ymd');
            $file_path = $dir.'/'.$tps->tps_docs;
            Storage::disk('public')->delete('tps_uploads/' . $file_path);

            $tps->tps_status        = '5';
            $tps->tps_deleted_by    = session('user_id');
            $tps->tps_deleted_date  = Carbon::now()->format('Y-m-d H:i:s');
            $tps->save();
            return response()->json(['status' => true, 'message' => ['title' => 'TPS Deleted!', 'text' => 'TPS ' . $request->tps_name . ' has been deleted!']]);
        } else {
            return response()->json(['status' => false, 'message' => ['title' => 'TPS not Deleted!', 'text' => 'TPS ' . $request->tps_name . ' not deleted!']]);
        }
    }

    public function show_upload_tps($tps_id)
    {
      $tps = DataTps::find(Crypt::decrypt($tps_id));
      if (!$tps) {
        abort(404);
      }

      $dir = Carbon::parse($tps->tps_created_date)->format('Ymd');
      $file_path = $dir.'/'.$tps->tps_docs;

      $path = storage_path('app/public/tps_uploads/'.$file_path);
      if (!File::exists($path)) {
        $path = public_path('assets/upload/user/default.jpeg');
      }

      $file = File::get($path);
      $type = File::mimeType($path);
      $response = response($file, 200);
      $response->header("Content-Type", $type);

      return $response;
    }

     /**
     * Display data to provinces, regencies, districts and village
     */
    public function getProvinces()
    {
        $provinces = MasterProvinces::all();

        if ($provinces->count() > 0) {
            return response()->json(['status' => true, 'data' => $provinces]);
        } else {
            return response()->json(['status' => false, 'data' => 'Data is empty!']);
        }
    }
    public function getRegencies(Request $request)
    {
        $provinceId = $request->input('provinceId');

        $regencies = MasterRegencies::where('province_id', $provinceId)->get();

        if ($regencies->count() > 0) {
            return response()->json(['status' => true, 'data' => $regencies]);
        } else {
            return response()->json(['status' => false, 'data' => 'Data is empty!']);
        }
    }

    public function getDistricts(Request $request)
    {
        $regencyId = $request->input('regencyId');

        $districts = MasterDistricts::where('regency_id', $regencyId)->get();

        if ($districts->count() > 0) {
            return response()->json(['status' => true, 'data' => $districts]);
        } else {
            return response()->json(['status' => false, 'data' => 'Data is empty!']);
        }
    }

    public function getVillages(Request $request)
    {
        $districtId = $request->input('districtId');

        $villages = MasterVillages::where('district_id', $districtId)->get();

        if ($villages->count() > 0) {
            return response()->json(['status' => true, 'data' => $villages]);
        } else {
            return response()->json(['status' => false, 'data' => 'Data is empty!']);
        }
    }

     /**
     * End Display data to provinces, regencies, districts and village
     */

}
