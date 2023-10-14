<?php

namespace App\Http\Controllers\site_visit\master_data;

use Carbon\Carbon;
use App\Models\Partner;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MasterDistricts;
use App\Models\MasterProvinces;
use App\Models\MasterRegencies;
use App\Models\MasterVillages;
use App\Models\VisitOrder;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(session('role_id') == 1 || session('role_id') == 2){
          return view('content.site-visit.master-data.partner');
        } else {
          return view('content.pages.pages-misc-not-authorized');
        }
    }

    public function find(Request $request)
    {
      $search = $request->search;
      $partners = Partner::orderby('partner_name','asc')
        ->select('partner_id','partner_name')
        ->where('partner_name', 'like', '%' . $search . '%')
        ->isActive()
        ->get();

      $response = array();
      foreach($partners as $partner){
         $response[] = array(
              "id"    => $partner->partner_id,
              "text"  => $partner->partner_name
         );
      }

      return response()->json($response);
    }

    public function datatable(Request $request)
    {
        $columns = [
          0 => 'partner_id',
          1 => 'partner_photo',
          2 => 'partner_name',
          3 => 'partner_address',
          4 => 'total_visit',
        ];

        $search = [];
        $totalData = Partner::where('partner_status', '!=', 5)->count();
        $totalFiltered = $totalData;

        if (!empty($request->input())) {
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
    
            if (empty($request->input('search.value'))) {
              $partner = Partner::where('partner_status', '!=', 5)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            } else {
              $search = $request->input('search.value');
    
              $partner = Partner::Where('partner_name', 'LIKE', "%{$search}%")
                ->where('partner_status', '!=', 5)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
    
              $totalFiltered = Partner::Where('partner_name', 'LIKE', "%{$search}%")
                ->where('partner_status', '!=', 5)
                ->count();
            }
        } else {
          $start = 0;  
          $partner = Partner::where('partner_status', '!=', 5)->get();
        }

        $data = [];

        if (!empty($partner)) {
          $no = $start;
          foreach ($partner as $part) {
            $nestedData['no']             = ++$no;
            $nestedData['partner_id']     = Crypt::encrypt($part->partner_id);
            $nestedData['partner_photo']  = $part->partner_photo;
            $nestedData['partner_name']   = $part->partner_name;
            $nestedData['partner_address']= $part->partner_address;
            $nestedData['total_visit']    = VisitOrder::where('partner_id', $part->partner_id)->whereIn('visit_order_status', array(6, 7, 8))->count();
            $nestedData['partner_status'] = $part->partner_status;
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
            'partner_name'        => 'required|max:255',
            'partner_nik'         => 'required|max:255',
            'partner_email'       => 'required',
            'partner_province'    => 'required',
            'partner_regency'     => 'required',
            'partner_phone'     => 'required'
        ]);
  
        //   dd($request->all());
  
        if ($validator->fails()) {
        return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
        }
        
        // partner Status
        $status = !empty($request->partner_status) && $request->partner_status == 'on' ? 2 : 1;

        // partner email check duplicate entry
        $email_exist = Partner::where('partner_email', $request->partner_email)->exists();
        if ($email_exist) {
          return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'Email already registered on another partner!']]);
        }

        $timeNow = Carbon::now()->format('Ymd');

        // Check if a photo is uploaded
        if ($request->hasFile('partner_photo')) {
            $image = $request->file('partner_photo');

            // Compress the image and save it
            $filename = Carbon::now()->format('Hisu_').'partner'.($request->partner_id).'.'.$image->getClientOriginalExtension();
            $compressedImage = Image::make($image)->fit(300, 300);
            Storage::disk('public')->put('partner_uploads/'.$timeNow.'/'.$filename, $compressedImage->encode());
        } else {
            // If no photo is uploaded, use default.jpeg
            $filename = 'default.jpeg';
        }

        Partner::create([
            'partner_status'     => $status,
            'partner_name'       => $request->partner_name,
            'partner_nik'        => $request->partner_nik,
            'partner_photo'      => $filename,
            'partner_email'      => $request->partner_email,
            'partner_address'    => $request->partner_address,
            'partner_postal_code'=> $request->partner_postal_code,
            'partner_province'   => $request->partner_province,
            'partner_regency'    => $request->partner_regency,
            'partner_district'   => $request->partner_district,
            'partner_village'    => $request->partner_village,
            'partner_phone'      => $request->partner_phone,
            'partner_desc'       => $request->partner_desc,
            'partner_created_by' => session('user_id'),
            'partner_created_date' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        return response()->json(['status' => true, 'message' => ['title' => 'Successfully created!', 'text' => 'Partner created successfully!']]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $part = Partner::with('province', 'regency', 'district', 'village')->where('partner_id', Crypt::decrypt($id))->first();
        if($part) {
            return response()->json(['status' => true, 'data' => $part]);
        } else {
            return response()->json(['status' => false, 'data' => []]);
        }
    }

    public function statusUpdate(Request $request)
    {
      $this->validate($request, [
        'partner_id'     => 'required',
        'partner_status' => 'required',
      ]);

      $part = Partner::where('partner_id', Crypt::decrypt($request->partner_id))->first();
      $part->partner_status        = $request->partner_status;
      $part->partner_updated_by    = session('user_id');
      $part->save();

      if ($request->partner_status == 2) {
        return response()->json(['status' => true, 'message' => ['title' => 'Partner Activated!', 'text' => 'Partner ' . $part->partner_name . ' status has been activated!']]);
      } else {
        return response()->json(['status' => true, 'message' => ['title' => 'Partner Deactivated!', 'text' => 'Partner ' . $part->partner_name . ' status has been deactivated!']]);
      }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'partner_code'        => 'required|max:50',
            'partner_name'        => 'required|max:255',
            'partner_nik'         => 'required|max:255',
            'partner_email'       => 'required',
            'partner_address'     => 'required|max:255',
            'partner_province'    => 'required',
            'partner_regency'     => 'required'
          ]);
  
          // Partner Status
          $status = !empty($request->partner_status) && $request->partner_status == 'on' ? 2 : 1;
          
          // partner email check duplicate entry
          $email_exist = Partner::where('partner_email', $request->partner_email)->where('partner_id', '!=', Crypt::decrypt($id))->exists();
          if ($email_exist) {
            return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'Email already registered on another partner!']]);
          }
          
          // Partner code check duplicate entry
          $partner_code_exist = Partner::where('partner_code', $request->partner_code)->where('partner_id', '!=', Crypt::decrypt($id))->first();
          if ($partner_code_exist) {
            if ($partner_code_exist->partner_status == 5) {
              return response()->json(['status' => false, 'message' => ['title' => 'Wrong Code', 'text' => 'Code already used by deleted partner!']]);
            }
            return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'Code already used by another partner!']]);
          }
  
          $part = Partner::where('partner_id', Crypt::decrypt($id))->first();
          
          $timeNow = Carbon::now()->format('Ymd');
          if ($request->hasFile('partner_photo')) {
              $image = $request->file('partner_photo');

              // Get the old image path from the database
              $dir = Carbon::parse($part->partner_created_date)->format('Ymd');
              $oldImage = $dir.'/'.$part->partner_photo;

              // Check if the old image is not the default image and exists in the storage
              if ($oldImage != 'default.jpeg' && Storage::disk('public')->exists('partner_uploads/' . $oldImage)) {
                  // Unlink (delete) the old image
                  Storage::disk('public')->delete('partner_uploads/' . $oldImage);
              }
              // Compress the image and save it
              $filename = Carbon::now()->format('Hisu_').'partner'.($request->partner_id).'.'.$image->getClientOriginalExtension();
              $compressedImage = Image::make($image)->fit(300, 300);
              Storage::disk('public')->put('partner_uploads/'.$timeNow.'/'.$filename, $compressedImage->encode());

              // Update the partner_photo column only if the photo is changed
              $part->partner_photo = $filename;
          }
          
          $part->partner_status            = $status;
          $part->partner_code              = $request->partner_code;
          $part->partner_name              = $request->partner_name;
          $part->partner_nik               = $request->partner_nik;
          $part->partner_email             = $request->partner_email;
          $part->partner_address           = $request->partner_address;
          $part->partner_postal_code       = $request->partner_postal_code;
          $part->partner_province          = $request->partner_province;
          $part->partner_regency           = $request->partner_regency;
          $part->partner_district          = $request->partner_district;
          $part->partner_village           = $request->partner_village;
          $part->partner_phone             = $request->partner_phone;
          $part->partner_desc              = $request->partner_desc;
          $part->partner_updated_by        = session('user_id');
          $part->partner_updated_date      = Carbon::now()->format('Y-m-d H:i:s');
          $part->save();
          
          return response()->json(['status' => true, 'message' => ['title' => 'Successfully Updated!', 'text' => 'Partner ' . $request->partner_name . ' updated successfully!']]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $part = Partner::where('partner_id', Crypt::decrypt($request->partner_id))->first();
        if ($part) {
            $dir = Carbon::parse($part->partner_created_date)->format('Ymd');
            $file_path = $dir.'/'.$part->partner_photo;
            Storage::disk('public')->delete('partner_uploads/' . $file_path);

            $part->partner_status        = '5';
            $part->partner_photo         = 'default.jpeg';
            $part->partner_deleted_by    = session('user_id');
            $part->partner_deleted_date  = Carbon::now()->format('Y-m-d H:i:s');
            $part->save();
            return response()->json(['status' => true, 'message' => ['title' => 'Partner Deleted!', 'text' => 'Partner ' . $request->partner_name . ' has been deleted!']]);
        } else {
            return response()->json(['status' => false, 'message' => ['title' => 'Partner not Deleted!', 'text' => 'Partner ' . $request->partner_name . ' not deleted!']]);
        }
    }

    public function show_upload_partner($partner_id)
    {
      $partner = Partner::find(Crypt::decrypt($partner_id));
      if (!$partner) {
        abort(404);
      }

      $dir = Carbon::parse($partner->partner_created_date)->format('Ymd');
      $file_path = $dir.'/'.$partner->partner_photo;

      $path = storage_path('app/public/partner_uploads/'.$file_path);
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
