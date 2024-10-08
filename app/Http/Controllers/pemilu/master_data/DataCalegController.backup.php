<?php

namespace App\Http\Controllers\pemilu\master_data;

use App\Http\Controllers\Controller;
use App\Models\DataCaleg;
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

class DataCalegController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(session('role_id') == 1){
            return view('content.pemilu.master-data.caleg');
            } else {
            return view('content.pages.pages-misc-not-authorized');
        }
    }

    public function datatable(Request $request)
    {
        $columns = [
          0 => 'caleg_id',
          1 => 'caleg_photo',
          2 => 'caleg_nik',
          3 => 'caleg_name',
          4 => 'caleg_province',
          5 => 'caleg_regency',
          6 => 'caleg_district',
          7 => 'caleg_village',
          8 => 'caleg_visi_misi',
          9 => 'caleg_nama_partai'
        ];

        $search = [];
        $totalData = DataCaleg::where('caleg_status', '!=', 5)->count();
        $totalFiltered = $totalData;

        if (!empty($request->input())) {
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
    
            if (empty($request->input('search.value'))) {
              $caleg = DataCaleg::where('caleg_status', '!=', 5)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            } else {
              $search = $request->input('search.value');
    
              $caleg = DataCaleg::where('caleg_nik', 'LIKE', "%{$search}%")
                ->where('caleg_status', '!=', 5)
                ->orWhere('caleg_name', 'LIKE', "%{$search}%")
                ->orWhere('caleg_nama_partai', 'LIKE', "%{$search}%")
                ->orWhereRelation('province', 'name', 'LIKE', "%{$search}%")
                ->orWhereRelation('district', 'name', 'LIKE', "%{$search}%")
                ->orWhereRelation('regency', 'name', 'LIKE', "%{$search}%")
                ->orWhereRelation('village', 'name', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
    
              $totalFiltered = DataCaleg::where('caleg_nik', 'LIKE', "%{$search}%")
                ->where('caleg_status', '!=', 5)
                ->orWhere('caleg_name', 'LIKE', "%{$search}%")
                ->orWhere('caleg_nama_partai', 'LIKE', "%{$search}%")
                ->orWhereRelation('province', 'name', 'LIKE', "%{$search}%")
                ->orWhereRelation('district', 'name', 'LIKE', "%{$search}%")
                ->orWhereRelation('regency', 'name', 'LIKE', "%{$search}%")
                ->orWhereRelation('village', 'name', 'LIKE', "%{$search}%")
                ->count();
            }
        } else {
          $start = 0;  
          $caleg = DataCaleg::where('caleg_status', '!=', 5)->get();
        }

        $data = [];

        if (!empty($caleg)) {
          $no = $start;
          foreach ($caleg as $cal) {
            $nestedData['no']               = ++$no;
            $nestedData['caleg_id']         = Crypt::encrypt($cal->caleg_id);
            $nestedData['caleg_photo']      = $cal->caleg_photo;
            $nestedData['caleg_nik']        = $cal->caleg_nik;
            $nestedData['caleg_name']       = $cal->caleg_name;
            $nestedData['caleg_province']   = $cal->province->name;
            $nestedData['caleg_regency']    = $cal->regency->name;
            $nestedData['caleg_district']   = $cal->district->name;
            $nestedData['caleg_village']    = $cal->village->name;
            $nestedData['caleg_visi_misi']  = $cal->caleg_visi_misi;
            $nestedData['caleg_nama_partai']= $cal->caleg_nama_partai;
            $nestedData['caleg_status']     = $cal->caleg_status;
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
            'caleg_nik'           => 'required|max:255',
            'caleg_name'          => 'required|max:255',
            'caleg_province'      => 'required',
            'caleg_regency'       => 'required',
            'caleg_visi_misi'     => 'required',
            'caleg_nama_partai'   => 'required',
        ]);
  
        //  dd($validator->errors());
        // dd($request->all());
  
        if ($validator->fails()) {
        return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
        }
        
        // caleg Status
        $status = !empty($request->caleg_status) && $request->caleg_status == 'on' ? 2 : 1;

        // caleg nik check duplicate entry
        $nik_exist = DataCaleg::where('caleg_nik', $request->caleg_nik)->exists();
        if ($nik_exist) {
          return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'NIK already registered on another caleg!']]);
        }

        $timeNow = Carbon::now()->format('Ymd');

        // Check if a photo is uploaded
        if ($request->hasFile('caleg_photo')) {
            $image = $request->file('caleg_photo');

             // Compress the image and save it
            $filename = Carbon::now()->format('Hisu_').'caleg'.($request->caleg_id).'.'.$image->getClientOriginalExtension();
            $compressedImage = Image::make($image)->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            });
            Storage::disk('public')->put('caleg_uploads/'.$timeNow.'/'.$filename, $compressedImage->encode());
        } else {
            // If no photo is uploaded, use default.jpeg
            $filename = 'default.jpeg';
        }

        DataCaleg::create([
            'caleg_status'      => $status,
            'caleg_nik'         => $request->caleg_nik,
            'caleg_name'        => $request->caleg_name,
            'caleg_photo'       => $filename,
            'caleg_province'    => $request->caleg_province,
            'caleg_regency'     => $request->caleg_regency,
            'caleg_district'    => $request->caleg_district,
            'caleg_village'     => $request->caleg_village,
            'caleg_visi_misi'   => $request->caleg_visi_misi,
            'caleg_nama_partai' => $request->caleg_nama_partai,
            'caleg_created_by'  => session('user_id'),
            'caleg_created_date'=> Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        return response()->json(['status' => true, 'message' => ['title' => 'Successfully created!', 'text' => 'Caleg ' . $request->caleg_name . ' created successfully!']]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DataCaleg  $dataCaleg
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $caleg = DataCaleg::with('province', 'regency', 'district', 'village')->where('caleg_id', Crypt::decrypt($id))->first();
        if($caleg) {
            return response()->json(['status' => true, 'data' => $caleg]);
        } else {
            return response()->json(['status' => false, 'data' => []]);
        }
    }

    public function statusUpdate(Request $request)
    {
      $this->validate($request, [
        'caleg_id'     => 'required',
        'caleg_status' => 'required',
      ]);

      $caleg = DataCaleg::where('caleg_id', Crypt::decrypt($request->caleg_id))->first();
      $caleg->caleg_status        = $request->caleg_status;
      $caleg->caleg_updated_by    = session('user_id');
      $caleg->save();

      if ($request->caleg_status == 2) {
        return response()->json(['status' => true, 'message' => ['title' => 'Caleg Activated!', 'text' => 'Caleg ' . $caleg->caleg_name . ' status has been activated!']]);
      } else {
        return response()->json(['status' => true, 'message' => ['title' => 'Caleg Deactivated!', 'text' => 'Caleg ' . $caleg->caleg_name . ' status has been deactivated!']]);
      }
    }

        /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\DataCaleg  $dataCaleg
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
         $this->validate($request, [
            'caleg_nik'           => 'required|max:255',
            'caleg_name'          => 'required|max:255',
            'caleg_province'      => 'required',
            'caleg_regency'       => 'required',
            'caleg_visi_misi'     => 'required',
            'caleg_nama_partai'   => 'required',
          ]);
  
          // caleg Status
          $status = !empty($request->caleg_status) && $request->caleg_status == 'on' ? 2 : 1;
          
          // caleg nik check duplicate entry
          $nik_exist = DataCaleg::where('caleg_nik', $request->caleg_nik)->where('caleg_id', '!=', Crypt::decrypt($id))->exists();
          if ($nik_exist) {
            return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'NIK already registered on another caleg!']]);
          }
  
          $cal = DataCaleg::where('caleg_id', Crypt::decrypt($id))->first();
          
          $timeNow = Carbon::now()->format('Ymd');
          if ($request->hasFile('caleg_photo')) {
              $image = $request->file('caleg_photo');

              // Get the old image path from the database
              $dir = Carbon::parse($cal->caleg_created_date)->format('Ymd');
              $oldImage = $dir.'/'.$cal->caleg_photo;

              // Check if the old image is not the default image and exists in the storage
              if ($oldImage != 'default.jpeg' && Storage::disk('public')->exists('caleg_uploads/' . $oldImage)) {
                  // Unlink (delete) the old image
                  Storage::disk('public')->delete('caleg_uploads/' . $oldImage);
              }
              // Compress the image and save it
              $filename = Carbon::now()->format('Hisu_').'caleg'.($request->caleg_id).'.'.$image->getClientOriginalExtension();
              $compressedImage = Image::make($image)->resize(300, 300, function ($constraint) {
                  $constraint->aspectRatio();
              });
              Storage::disk('public')->put('caleg_uploads/'.$timeNow.'/'.$filename, $compressedImage->encode());

              // Update the caleg_photo column only if the photo is changed
              $cal->caleg_photo = $filename;
          }
          
          $cal->caleg_status            = $status;
          $cal->caleg_nik               = $request->caleg_nik;
          $cal->caleg_name              = $request->caleg_name;
          $cal->caleg_province          = $request->caleg_province;
          $cal->caleg_regency           = $request->caleg_regency;
          $cal->caleg_district          = $request->caleg_district;
          $cal->caleg_village           = $request->caleg_village;
          $cal->caleg_visi_misi         = $request->caleg_visi_misi;
          $cal->caleg_nama_partai       = $request->caleg_nama_partai;
          $cal->caleg_updated_by        = session('user_id');
          $cal->caleg_updated_date      = Carbon::now()->format('Y-m-d H:i:s');
          $cal->save();
          
          return response()->json(['status' => true, 'message' => ['title' => 'Successfully Updated!', 'text' => 'Caleg ' . $request->caleg_name . ' updated successfully!']]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DataCaleg  $dataCaleg
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $part = DataCaleg::where('caleg_id', Crypt::decrypt($request->caleg_id))->first();
        if ($part) {
            $dir = Carbon::parse($part->caleg_created_date)->format('Ymd');
            $file_path = $dir.'/'.$part->caleg_photo;
            Storage::disk('public')->delete('caleg_uploads/' . $file_path);

            $part->caleg_status        = '5';
            $part->caleg_photo         = 'default.jpeg';
            $part->caleg_deleted_by    = session('user_id');
            $part->caleg_deleted_date  = Carbon::now()->format('Y-m-d H:i:s');
            $part->save();
            return response()->json(['status' => true, 'message' => ['title' => 'Caleg Deleted!', 'text' => 'Caleg ' . $request->caleg_name . ' has been deleted!']]);
        } else {
            return response()->json(['status' => false, 'message' => ['title' => 'Caleg not Deleted!', 'text' => 'Caleg ' . $request->caleg_name . ' not deleted!']]);
        }
    }

    public function show_upload_caleg($caleg_id)
    {
      $caleg = DataCaleg::find(Crypt::decrypt($caleg_id));
      if (!$caleg) {
        abort(404);
      }

      $dir = Carbon::parse($caleg->caleg_created_date)->format('Ymd');
      $file_path = $dir.'/'.$caleg->caleg_photo;

      $path = storage_path('app/public/caleg_uploads/'.$file_path);
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