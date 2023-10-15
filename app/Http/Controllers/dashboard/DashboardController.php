<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\DataCaleg;
use App\Models\Partner;
use App\Models\User;
use App\Models\VisitOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

  public function index()
  {
    if (session('role_id') == 1) {
      $total_partner_is_active       = DataCaleg::where('caleg_status', 2)->count();
      $total_users_is_active         = User::where('user_status', 2)->count();
      return view('content.dashboard.dashboards-admin', [
        'total_partner_is_active'       => $total_partner_is_active,
        'total_users_is_active'         => $total_users_is_active
      ]);
    } else if (session('role_id') == 2) {
      return view('content.dashboard.dashboards-timses', [
       
      ]);
    } else if (session('role_id') == 3) {
      return view('content.dashboard.dashboards-timdpt', [

      ]);
    } else if (session('role_id') == 4) {
      return view('content.dashboard.dashboards-saksi', [

      ]);
    }else {
      return view('content.pages.pages-misc-not-authorized');
    }
  }

}
