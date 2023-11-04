<?php

namespace App\Http\Controllers\pemilu\master_data;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\MasterDistricts;
use App\Models\MasterProvinces;
use App\Models\MasterRegencies;
use App\Models\MasterVillages;
use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(session('role_id') == 1){
          return view('content.pemilu.master-data.user');
        } else {
          if(session('role_id') == 2 || session('role_id') == 3 || session('role_id') == 4 || session('role_id') == 5) {
            // Membuat objek Carbon dari string tanggal dan waktu
            $created_date = Carbon::parse(session('user_created_date'));
            // Mengubah format menjadi "d F Y \j\a\m H:i:s"
            $user_created_date = $created_date->format('d F Y \j\a\m H:i:s');

            $last_login = Carbon::parse(session('user_last_login'));
            $user_last_login = $last_login->format('d F Y \j\a\m H:i:s');

            return view('content.settings.user.user-profile', [
              'user_created_date' => $user_created_date,
              'user_last_login'   => $user_last_login
            ]);
          }
          return view('content.pages.pages-misc-not-authorized');
        }
    }

    public function datatable(Request $request)
    {
        $columns = [
          0 => 'user_id',
          1 => 'user_photo',
          2 => 'user_nik',
          3 => 'user_uniq_name',
          4 => 'user_no_hp',
          5 => 'user_email',
          6 => 'role_id'
        ];

        $search = [];
        // $totalData = User::count();
        $totalData = User::where('user_id', '!=', 2)->count();
        $totalFiltered = $totalData;

        if (!empty($request->input())) {
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
    
            if (empty($request->input('search.value'))) {
                // $order = 'user_id'; 
                // $dir = 'desc';

                $users = User::where('user_id', '!=', 2) 
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
            } else {
                $search = $request->input('search.value');
    
                $users = User::where('user_id', '!=', 2) 
                    ->where('user_nik', 'LIKE', "%{$search}%")
                    ->orWhere('user_uniq_name', 'LIKE', "%{$search}%")
                    ->orWhere('user_no_hp', 'LIKE', "%{$search}%")
                    ->orWhere('user_email', 'LIKE', "%{$search}%")
                    ->orWhereRelation('role', 'role_name', 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
    
                $totalFiltered = User::where('user_id', '!=', 2) 
                    ->where('user_nik', 'LIKE', "%{$search}%")
                    ->orWhere('user_uniq_name', 'LIKE', "%{$search}%")
                    ->orWhere('user_no_hp', 'LIKE', "%{$search}%")
                    ->orWhere('user_email', 'LIKE', "%{$search}%")
                    ->orWhereRelation('role', 'role_name', 'LIKE', "%{$search}%")
                    ->count();
            }
        } else {
            $start = 0;
            // $users = User::all();
            $users = User::where('user_id', '!=', 2)->get();
        }

        $data = [];

        if (!empty($users)) {
          $no = $start;
          foreach ($users as $user) {

            $last_login = Carbon::parse($user->user_last_login);
            $user_last_login = $last_login->format('d F Y \j\a\m H:i:s');

            $nestedData['no']              = ++$no;
            $nestedData['user_id']         = $user->user_id;
            $nestedData['user_photo']      = $user->user_photo;
            $nestedData['user_nik']        = $user->user_nik;
            $nestedData['user_uniq_name']  = $user->user_uniq_name;
            $nestedData['user_no_hp']      = $user->user_no_hp;
            $nestedData['user_email']      = $user->user_email;
            $nestedData['role_name']       = $user->role->role_name;
            $nestedData['user_ref_param']  = $user->user_ref_param;
            $nestedData['user_last_login'] = $user_last_login;
            $nestedData['user_status']     = $user->user_status;

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $validator = Validator::make($request->all(), [
          'role_id'           => 'required',
          'user_nik'          => ['required', 'min:3', 'max:255'],
          'user_uniq_name'    => ['required', 'min:3', 'max:255'],
          'user_no_hp'        => 'required',
          'user_email'        => 'required|user_email:dns|unique:sys_user',
          'user_password'     => 'required|min:8|max:255',
          'user_photo'        => 'nullable|file|image|mimes:jpeg,png,jpg|max:1024',
          'user_province'     => 'required',
          'user_regency'      => 'required',
      ]);

      // dd($request->all());

      if ($validator->fails()) {
        return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
      }

      $status = !empty($request->user_status) && $request->user_status == 'on' ? 2 : 1;

      // Check if a photo is uploaded
      if ($request->hasFile('user_photo')) {
          $image = $request->file('user_photo');

           // Compress the image and save it
           $filename = Carbon::now()->format('Hisu_').'users'.($request->caleg_id).'.'.$image->getClientOriginalExtension();
           $compressedImage = Image::make($image)->resize(300, 300, function ($constraint) {
               $constraint->aspectRatio();
           });
           Storage::disk('public')->put('users_uploads/'.$filename, $compressedImage->encode());
      } else {
          // If no photo is uploaded, use default.jpeg
          $filename = 'default.jpeg';
      }

      $nik_exist = User::where('user_nik', $request->user_nik)->exists();
      if ($nik_exist) {
        return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'NIK already registered on another user!']]);
      }

      $email_exist = User::where('user_email', $request->user_email)->exists();
      if ($email_exist) {
        return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'Email already registered on another user!']]);
      }

      $phone_exist = User::where('user_no_hp', $request->user_no_hp)->exists();
      if ($phone_exist) {
        return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'Phone already registered on another user!']]);
      }

      $newUser = new User();
      $newUser->user_status       = $status;
      $newUser->user_ref_param    = 0; 
      $newUser->user_ref_id       = 0; 
      $newUser->role_id           = $request->role_id;
      $newUser->user_nik          = $request->user_nik;
      $newUser->user_uniq_name    = $request->user_uniq_name;
      $newUser->user_no_hp        = $request->user_no_hp;
      $newUser->user_email        = $request->user_email;
      $newUser->user_password     = Hash::make($request->user_password);
      $newUser->user_photo        = $filename;
      $newUser->user_province     = $request->user_province;
      $newUser->user_regency      = $request->user_regency;
      $newUser->user_district     = $request->user_district;
      $newUser->user_village      = $request->user_village;
      $newUser->user_created_by   = session(('user_id'));
      $newUser->user_created_date = Carbon::now()->format('Y-m-d H:i:s');

      $newUser->save();

      return response()->json(['status' => true, 'message' => ['title' => 'Successfully created!', 'text' => 'User created successfully!']]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $user = User::with('role', 'province', 'regency', 'district', 'village')->where('user_id', $id)->first();

      if($user) {
        return response()->json(['status' => true, 'data' => $user]);
      } else {
        return response()->json(['status' => false, 'data' => []]);
      }
    }

    public function update_status(Request $request)
    {
      $this->validate($request, [
          'user_id'     => 'required',
          'user_status' => 'required',
      ]);

      $user                     = User::where('user_id', $request->user_id)->first();
      $user->user_status        = $request->user_status;
      $user->user_updated_by    = session('user_id');
      $user->user_updated_date  = Carbon::now()->format('Y-m-d H:i:s');
      $user->save();

      if ($request->user_status == 2) {
        return response()->json(['status' => true, 'message' => ['title' => 'User Activated!', 'text' => 'User ' . $user->user_uniq_name . ' status has been activated!']]);
      } else {
        return response()->json(['status' => true, 'message' => ['title' => 'User Deactivated!', 'text' => 'User ' . $user->user_uniq_name . ' status has been deactivated!']]);
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
        $validator = Validator::make($request->all(), [
            'role_id'           => 'required',
            'user_nik'          => ['required', 'min:3', 'max:255'],
            'user_uniq_name'    => ['required', 'min:3', 'max:255'],
            'user_no_hp'        => 'required',
            'user_email'        => 'required',
            'user_photo'        => 'nullable|file|image|mimes:jpeg,png,jpg|max:1024',
            'user_province'     => 'required',
            'user_regency'      => 'required',
        ]);

        // dd($validator->errors());
        // dd($request->all());

        if ($validator->fails()) {
          return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
        }

        $status = !empty($request->user_status) && $request->user_status == 'on' ? 2 : 1;

        $user = User::where('user_id', $id)->first();

        if ($request->hasFile('user_photo')) {
            $image = $request->file('user_photo');

            // Get the old image path from the database
            $oldImage = $user->user_photo;

            // Check if the old image is not the default image and exists in the storage
            if ($oldImage != 'default.jpeg' && Storage::disk('public')->exists('users_uploads/' . $oldImage)) {
                // Unlink (delete) the old image
                Storage::disk('public')->delete('users_uploads/' . $oldImage);
            }
            // Compress the image and save it
            $filename = Carbon::now()->format('Hisu_').'users'.($request->caleg_id).'.'.$image->getClientOriginalExtension();
            $compressedImage = Image::make($image)->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            });
            Storage::disk('public')->put('users_uploads/'.$filename, $compressedImage->encode());

            // Update the user_photo column only if the photo is changed
            $user->user_photo = $filename;
        }

        $nik_exist = User::where('user_nik', $request->user_nik)->where('user_id', '!=', $id)->exists();
        if ($nik_exist) {
          return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'NIK already registered on another user!']]);
        }
        
        $email_exist = User::where('user_email', $request->user_email)->where('user_id', '!=', $id)->exists();
        if ($email_exist) {
          return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'Email already registered on another user!']]);
        }

        $phone_exist = User::where('user_no_hp', $request->user_no_hp)->where('user_id', '!=', $id)->exists();
        if ($phone_exist) {
          return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'Phone already registered on another user!']]);
        }
        
        $user->user_status        = $status;
        $user->user_ref_param     = 0;
        $user->user_ref_id        = 0; 
        $user->role_id            = $request->role_id;
        $user->user_nik           = $request->user_nik;
        $user->user_uniq_name     = $request->user_uniq_name;
        $user->user_no_hp         = $request->user_no_hp;
        $user->user_email         = $request->user_email;
        $user->user_province      = $request->user_province;
        $user->user_regency       = $request->user_regency;
        $user->user_district      = $request->user_district;
        $user->user_village       = $request->user_village;
        $user->user_updated_by    = session('user_id');
        $user->user_updated_date  = Carbon::now()->format('Y-m-d H:i:s');
        $user->save();

        // Update session value for user_photo
        session()->put('user_photo', $user->user_photo);
        session()->save();

        return response()->json([
          'status' => true,
          'message' => ['title' => 'Successfully Updated!', 'text' => 'User ' . $user->user_uniq_name . ' updated successfully!'],
          'newUserPhoto' => asset('storage/users_uploads/' . $user->user_photo), // Kirim URL foto baru
      ]);
    }

    /**
     * Update for user only the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_data_user(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
          'role_id'           => 'required',
          'user_uniq_name'    => ['required', 'min:3', 'max:255'],
          'user_photo'        => 'nullable|file|image|mimes:jpeg,png,jpg|max:1024'
        ]);

        if ($validator->fails()) {
          return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
        }

        // user email check duplicate entry
        $email_exist = User::where('user_email', $request->user_email)->where('user_id', '!=', $id)->exists();
        if ($email_exist) {
          return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'Email already registered on another user!']]);
        }

        $user = User::where('user_id', $id)->first();

        if ($request->hasFile('user_photo')) {
          $image = $request->file('user_photo');

          // Get the old image path from the database
          $oldImage = $user->user_photo;

          // Check if the old image is not the default image and exists in the storage
          if ($oldImage != 'default.jpeg' && Storage::disk('public')->exists('users_uploads/' . $oldImage)) {
              // Unlink (delete) the old image
              Storage::disk('public')->delete('users_uploads/' . $oldImage);
          }
          // Compress the image and save it
          $filename = Carbon::now()->format('Hisu_').'users'.($request->caleg_id).'.'.$image->getClientOriginalExtension();
          $compressedImage = Image::make($image)->resize(300, 300, function ($constraint) {
              $constraint->aspectRatio();
          });
          Storage::disk('public')->put('users_uploads/'.$filename, $compressedImage->encode());

          // Update the user_photo column only if the photo is changed
          $user->user_photo = $filename;
      }
      
        $user->user_status        = 2; // active  
        $user->user_ref_param     = 3; // partner
        $user->role_id            = $request->role_id;
        $user->user_uniq_name     = $request->user_uniq_name;
        $user->user_updated_by    = session('user_id');
        $user->user_updated_date  = Carbon::now()->format('Y-m-d H:i:s');
        $user->save();

        // Perbarui nilai sesi 
        session()->put('user_id', $user->user_id);
        session()->put('user_nik', $user->user_nik);
        session()->put('user_uniq_name', $user->user_uniq_name);
        session()->put('user_no_hp', $user->user_no_hp);
        session()->put('user_email', $user->user_email);
        session()->put('user_photo', $user->user_photo);
        session()->put('user_created_date', $user->user_created_date);
        session()->put('user_last_login', $user->user_last_login);

        return response()->json(['status' => true, 'message' => ['title' => 'Successfully Updated!', 'text' => 'User ' . $user->user_uniq_name . ' updated successfully!']]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
      $user = User::where('user_id', $request->user_id)->first();
      if ($user) {
          $file_path = $user->user_photo;
          Storage::disk('public')->delete('users_uploads/' . $file_path);

          $user->user_status        = '5';
          $user->user_photo         = 'default.jpeg';
          $user->user_deleted_by    = session('user_id');
          $user->user_deleted_date  = Carbon::now()->format('Y-m-d H:i:s');
          $user->save();
          return response()->json(['status' => true, 'message' => ['title' => 'User Deleted!', 'text' => 'User ' . $request->user_uniq_name . ' has been deleted!']]);
      } else {
          return response()->json(['status' => false, 'message' => ['title' => 'User not Deleted!', 'text' => 'User ' . $request->user_uniq_name . ' not deleted!']]);
      }
    }

    public function changePassword(Request $request, $id)
    {
         $this->validate($request, [
            'change_password'     => 'required|min:8|max:255',
          ]);
  
          // dd($request->all());
  
          $changePass = User::where('user_id', $id)->first();
          
          $changePass->user_password          = Hash::make($request->change_password);
          $changePass->user_updated_by        = session('user_id');
          $changePass->user_updated_date      = Carbon::now()->format('Y-m-d H:i:s');
          $changePass->save();

        return response()->json([
            'status' => true,
            'message' => ['title' => 'Successfully created!', 'text' => 'Change Password ' . $request->user_uniq_name . ' updated successfully!'],
        ]);
    }

    public function show_upload_user($user_id)
    {
      $user = User::find($user_id);
      if (!$user) {
        abort(404);
      }

      $file_path = $user->user_photo;

      $path = storage_path('app/public/users_uploads/'.$file_path);
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
