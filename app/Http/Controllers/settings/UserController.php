<?php

namespace App\Http\Controllers\settings;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
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
        if(session('role_id') == 1 || session('role_id') == 2){
          return view('content.settings.user');
        } else {
          if(session('role_id') == 3) {
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
          2 => 'user_uniq_name',
          3 => 'user_email',
          4 => 'role_id'
        ];

        $search = [];
        $totalData = User::count();
        $totalFiltered = $totalData;

        if (!empty($request->input())) {
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
    
            if (empty($request->input('search.value'))) {
              $users = User::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            } else {
              $search = $request->input('search.value');
    
              $users = User::where('user_uniq_name', 'LIKE', "%{$search}%")
                ->orWhere('user_email', 'LIKE', "%{$search}%")
                ->orWhereRelation('role', 'role_name', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
    
              $totalFiltered = User::where('user_uniq_name', 'LIKE', "%{$search}%")
                ->orWhere('user_email', 'LIKE', "%{$search}%")
                ->orWhereRelation('role', 'role_name', 'LIKE', "%{$search}%")
                ->count();
            }
        } else {
            $start = 0;
            $users = User::all();
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
            $nestedData['user_uniq_name']  = $user->user_uniq_name;
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
      if ($request->role_id == 3) {
          $validator = Validator::make($request->all(), [
              'role_id'           => 'required',
              'user_password'     => 'required|min:8|max:255',
              'user_photo'        => 'nullable|file|image|mimes:jpeg,png,jpg|max:1024',
          ]);
      } else {
          $validator = Validator::make($request->all(), [
              'role_id'           => 'required',
              'user_uniq_name'    => ['required', 'min:3', 'max:255'],
              'user_email'        => 'required|user_email:dns|unique:sys_user',
              'user_password'     => 'required|min:8|max:255',
              'user_photo'        => 'nullable|file|image|mimes:jpeg,png,jpg|max:1024',
          ]);
      }

      // dd($request->all());

      if ($validator->fails()) {
        return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
      }

      $status = !empty($request->user_status) && $request->user_status == 'on' ? 2 : 1;

      // Check if a photo is uploaded
      if ($request->hasFile('user_photo')) {
          $image = $request->file('user_photo');

          // Compress the image and save it
          $filename = Carbon::now()->format('Hisu_').'users'.($request->user_id).'.'.$image->getClientOriginalExtension();
          $compressedImage = Image::make($image)->fit(300, 300);
          Storage::disk('public')->put('users_uploads/'.$filename, $compressedImage->encode());
      } else {
          // If no photo is uploaded, use default.jpeg
          $filename = 'default.jpeg';
      }

      $email_exist = User::where('user_email', $request->user_email)->exists();
      $user_ref_id = null;
      $user_ref_param = 1;
      $email = $request->user_email;
      $name = $request->user_uniq_name;
      if($request->role_id == 3)  {
        $partner = Partner::where('partner_id', $request->user_ref_id)->first(); // ngambil dari partner di lempar ke user_ref_id.
        $name = $partner->partner_name;
        $email = $partner->partner_email;
        $user_ref_id = $partner->partner_id;
        $user_ref_param = 3; // partner
        $email_exist = User::where('user_email', $email)->exists();
      }

      // user email check duplicate entry
      if ($email_exist) {
        return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'Email already registered on another user!']]);
      }

      $newUser = new User();
      $newUser->user_status       = $status;
      $newUser->user_ref_param    = $user_ref_param; 
      $newUser->user_ref_id       = $user_ref_id; 
      $newUser->role_id           = $request->role_id;
      $newUser->user_uniq_name    = $name;
      $newUser->user_email        = $email;
      $newUser->user_password     = Hash::make($request->user_password);
      $newUser->user_photo        = $filename;
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
      $user = User::with('role', 'partner')->where('user_id', $id)->first();

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
        if ($request->role_id == 3) {
            $validator = Validator::make($request->all(), [
                'role_id'           => 'required',
                'user_photo'        => 'nullable|file|image|mimes:jpeg,png,jpg|max:1024',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'role_id'           => 'required',
                'user_uniq_name'    => 'required',
                'user_email'        => 'required',
                'user_photo'        => 'nullable|file|image|mimes:jpeg,png,jpg|max:1024',
            ]);
        }

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
            $filename = Carbon::now()->format('Hisu_').'users'.($request->user_id).'.'.$image->getClientOriginalExtension();
            $compressedImage = Image::make($image)->fit(300, 300);
            Storage::disk('public')->put('users_uploads/'.$filename, $compressedImage->encode());

            // Update the user_photo column only if the photo is changed
            $user->user_photo = $filename;
        }
        
        $email_exist = User::where('user_email', $request->user_email)->where('user_id', '!=', $id)->exists();
        $user_ref_id = null;
        $user_ref_param = 1;
        $email = $request->user_email;
        $name = $request->user_uniq_name;
        if($request->role_id == 3)  {
          $partner = Partner::where('partner_id', $request->user_ref_id)->first(); // ngambil dari partner di lempar ke user_ref_id.
          $name = $partner->partner_name;
          $email = $partner->partner_email;
          $user_ref_id = $partner->partner_id;
          $user_ref_param = 3; // partner
          $email_exist = User::where('user_email',  $email)->where('user_id', '!=', $id)->exists();
        }

        // user email check duplicate entry
        if ($email_exist) {
          return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'Email already registered on another user!']]);
        }
        
        $user->user_status        = $status;
        $user->user_ref_param     = $user_ref_param;
        $user->user_ref_id        = $user_ref_id; 
        $user->role_id            = $request->role_id;
        $user->user_uniq_name     = $name;
        $user->user_email         = $email;
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
          'user_uniq_name'    => 'required',
          'user_photo'        => 'nullable|file|image|mimes:jpeg,png,jpg|max:1024',
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
          $filename = Carbon::now()->format('Hisu_').'users'.($request->user_id).'.'.$image->getClientOriginalExtension();
          $compressedImage = Image::make($image)->fit(300, 300);
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
        session()->put('user_uniq_name', $user->user_uniq_name);
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

}
