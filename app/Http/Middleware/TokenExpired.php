<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Token;
use Illuminate\Contracts\Auth\Factory as Auth;
use Carbon\Carbon;

class TokenExpired
{
    protected $auth;
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {

        if ($this->auth->guard($guard)->guest()) {
            if (session('token_key')) {
                $token = session('token_key');
                $check_token = Token::where('token_key', $token)->first();
                
                if ($check_token == null) {
                    return redirect("login")->withErrors(['user_login' => ['Sesi anda sudah habis, silahkan login ulang']]);
                } else {
                    $datetime1 = Carbon::now();
                    $datetime2 = Carbon::createFromFormat('Y-m-d H:i:s', $check_token->token_expired_date);
                    $check_token_diff = $datetime1->gte($datetime2);
                    
                    if ($check_token_diff == true) {
                        return redirect("login")->withErrors(['user_login' => ['Token anda sudah tidak berlaku, silahkan login ulang']]);
                    }
                }
            } else {
                return redirect("login")->withErrors(['user_login' => ['Silahkan login terlebih dahulu']]);
            }
        }
        
        return $next($request);
    }
}
