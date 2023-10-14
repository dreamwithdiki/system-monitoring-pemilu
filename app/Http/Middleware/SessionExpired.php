<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Session\Store;
use Illuminate\Http\Request;
use Auth;
use Session;

class SessionExpired
{
    protected $session;
    protected $timeout = 7200;
     
    public function __construct(Store $session){
        $this->session = $session;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $isLoggedIn = $request->path() != 'logout';
        if(! session('last_active'))
            $this->session->put('last_active', time());
        elseif(time() - $this->session->get('last_active') > $this->timeout){
            $this->session->forget('last_active');
            $cookie = cookie('intend', $isLoggedIn ? url()->current() : 'dashboard');
            //auth()->logout();
            Session::flush();
            return redirect("login")->withErrors(['user_login' => ['Sesi anda sudah habis, silahkan login ulang']]);
        }
        $isLoggedIn ? $this->session->put('last_active', time()) : $this->session->forget('last_active');
        return $next($request);
    }
}
