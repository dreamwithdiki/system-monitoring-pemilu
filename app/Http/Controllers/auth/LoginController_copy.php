<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Token;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function index()
    {
        $pageConfigs = ['myLayout' => 'blank'];
        return view('content.auth.login', ['pageConfigs' => $pageConfigs]);
    }

    public function doLogin(Request $request)
    {
        $this->validate($request, [
            // 'user_email'    => 'required|email',
            'user_identity' => 'required',
            'user_password' => 'required|min:8',
        ]);

        $check_email = User::where("user_email", $request->user_email)->first();

		if(!$check_email){
            return redirect("login")->withErrors(['user_login' => ['Email tersebut tidak ditemukan']]);
		}else{
            $user = User::where("user_email", $request->user_email)->first();

            if (!$user || !Hash::check($request->user_password, $user->user_password)) {
                return redirect("login")->withErrors(['user_login' => ['Email atau sandi yang Anda masukkan salah']]);
            }
            else {
                if($user->user_status != 2){
                    return redirect("login")->withErrors(['user_login' => ['Status akun anda sudah tidak aktif, silahkan menghubungi Administrator jika anda ingin mengaktifkan akun anda kembali']]);
                }
                // create token data
                $check_tokey = Token::where("user_id", $user->user_id)
                                    ->where("token_param", 1)
                                    ->where("token_status", 2)
                                    ->orderBy('sys_token.token_id','DESC')
                                    ->first();
                $unique_1 = Str::random($strlentgh = 32);
                $unique_2 = md5(Carbon::now()->format('Y-m-d H:i:s'));
                $tokey = $user->user_id . $unique_1 . $unique_2;
                if (!$check_tokey) {
                    // CREATE NEW TOKEY
                    $data = new Token();
                    $data->user_id              = $user->user_id;
                    $data->token_key            = $tokey;
                    $data->token_param         	= '1';
                    $data->token_status         = '2';
                    $data->token_expired_date   = Carbon::now()->addDays(3)->format('Y-m-d H:i:s');
                    $data->token_created_date   = Carbon::now()->format('Y-m-d H:i:s');
                    $data->save();
                    // ---------- //
                }else{
                    // UPDATE TOKEY
                    $check_tokey->token_key             = $tokey;
                    $check_tokey->token_param         	= '1';
                    $check_tokey->token_status         	= '2';
                    $check_tokey->token_expired_date    = Carbon::now()->addDays(3)->format('Y-m-d H:i:s');
                    $check_tokey->token_updated_date    = Carbon::now()->format('Y-m-d H:i:s');
                    $check_tokey->save();
                    // ---------- //
                }

                // update user last login
                $user->user_last_login  = Carbon::now()->format('Y-m-d H:i:s');
                $user->save(); 

                // create session data
                session()->put('user_id', $user->user_id);
                session()->put('user_ref_param', $user->user_ref_param);
                session()->put('user_status', $user->user_status);
                session()->put('user_ref_id', $user->user_ref_id);
                session()->put('user_uniq_name', $user->user_uniq_name); 
                session()->put('role_id', $user->role_id); 
                session()->put('role_name', $user->role->role_name);
                session()->put('user_email', $user->user_email);
                session()->put('user_photo', $user->user_photo);
                session()->put('user_created_date', $user->user_created_date);
                session()->put('user_last_login', $user->user_last_login);
                session()->put('token_key', $tokey);
                session()->save();
                session()->regenerate();

                return redirect("/dashboard");
                
                // if (session('role_id') == 1 || session('role_id') == 2) {
                //    return redirect("/"); // key
                // } else {
                //    return redirect("/dashboard"); // partner
                // }
			}
		}
    }

    public function change_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password'      => 'required|min:8',
            'change_password'       => 'required|confirmed|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill and follow the rules within all fields']]);
        }

        $user = User::find(session('user_id'));

        if (!$user) {
            return response()->json(['status' => false, 'message' => ['title' => 'Error', 'text' => 'Please re-login and change your password again.']]);
        } else {
            // Check current password
            if (Hash::check($request->current_password, $user->user_password)) {
                $user->user_password = Hash::make($request->change_password);
                $user->save();

                return response()->json(['status' => true, 'message' => ['title' => 'Change password success', 'text' => 'New password has been saved, please re-login.']]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => [
                        'title' => 'Wrong current password',
                        'text' => "If you don't remember your current password, you can use the forgot password feature on the login page."
                    ]
                ]);
            }
        }
    }

    
    public function logout(Request $request)
    {
        // session()->flush();
        // Auth::logout();
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
