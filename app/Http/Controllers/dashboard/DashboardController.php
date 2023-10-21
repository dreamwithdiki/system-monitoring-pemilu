<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\DataCaleg;
use App\Models\DataDpt;
use App\Models\DataTps;
use App\Models\KecamatanCeklisDpt;
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
      // $total_dpt_is_active       = DataDpt::where('dpt_status', 2)->count();
      $total_dpt_is_active       = KecamatanCeklisDpt::whereIn('kecamatan_id', array(9, 2, 8))->count();
      $total_dpt_tamansari       = KecamatanCeklisDpt::where('kecamatan_id', 9)->count(); // 9 = Tamansari
      $total_dpt_cibereum        = KecamatanCeklisDpt::where('kecamatan_id', 2)->count(); // 2 = Cibereum
      $total_dpt_purbaratu       = KecamatanCeklisDpt::where('kecamatan_id', 8)->count(); // 8 = Purbaratu
      $total_dpt_man             = DataDpt::where('dpt_jenkel', 1)->where('dpt_status', 2)->count();
      $total_dpt_woman           = DataDpt::where('dpt_jenkel', 2)->where('dpt_status', 2)->count();
      $totalSuaraPartai          = DataTps::sum('tps_suara_partai');
      $totalSuaraCaleg           = DataTps::sum('tps_suara_caleg');
      $total_users_is_active     = User::where('user_status', 2)->where('user_id', '!=', 2)->count();
      return view('content.dashboard.dashboards-admin', [
        'total_dpt_is_active'       => $total_dpt_is_active,
        'total_dpt_tamansari'       => $total_dpt_tamansari,
        'total_dpt_cibereum'        => $total_dpt_cibereum,
        'total_dpt_purbaratu'       => $total_dpt_purbaratu,
        'total_dpt_man'             => $total_dpt_man,
        'total_dpt_woman'           => $total_dpt_woman,
        'totalSuaraPartai'          => $totalSuaraPartai,
        'totalSuaraCaleg'           => $totalSuaraCaleg,
        'total_users_is_active'     => $total_users_is_active
      ]);
    } else if (session('role_id') == 2) {
      $total_dpt_is_active       = KecamatanCeklisDpt::whereIn('kecamatan_id', array(9, 2, 8))->count();
      $total_dpt_tamansari       = KecamatanCeklisDpt::where('kecamatan_id', 9)->count(); // 9 = Tamansari
      $total_dpt_cibereum        = KecamatanCeklisDpt::where('kecamatan_id', 2)->count(); // 2 = Cibereum
      $total_dpt_purbaratu       = KecamatanCeklisDpt::where('kecamatan_id', 8)->count(); // 8 = Purbaratu
      $total_dpt_man             = DataDpt::where('dpt_jenkel', 1)->where('dpt_status', 2)->count();
      $total_dpt_woman           = DataDpt::where('dpt_jenkel', 2)->where('dpt_status', 2)->count();
      $totalSuaraPartai          = DataTps::sum('tps_suara_partai');
      $totalSuaraCaleg           = DataTps::sum('tps_suara_caleg');

      return view('content.dashboard.dashboards-timses', [
        'total_dpt_is_active'       => $total_dpt_is_active,
        'total_dpt_tamansari'       => $total_dpt_tamansari,
        'total_dpt_cibereum'        => $total_dpt_cibereum,
        'total_dpt_purbaratu'       => $total_dpt_purbaratu,
        'total_dpt_man'             => $total_dpt_man,
        'total_dpt_woman'           => $total_dpt_woman,
        'totalSuaraPartai'          => $totalSuaraPartai,
        'totalSuaraCaleg'           => $totalSuaraCaleg,
      ]);
    } else if (session('role_id') == 3) {
      $total_dpt_is_active       = KecamatanCeklisDpt::whereIn('kecamatan_id', array(9, 2, 8))->count();
      $total_dpt_tamansari       = KecamatanCeklisDpt::where('kecamatan_id', 9)->count(); // 9 = Tamansari
      $total_dpt_cibereum        = KecamatanCeklisDpt::where('kecamatan_id', 2)->count(); // 2 = Cibereum
      $total_dpt_purbaratu       = KecamatanCeklisDpt::where('kecamatan_id', 8)->count(); // 8 = Purbaratu
      $total_dpt_man             = DataDpt::where('dpt_jenkel', 1)->where('dpt_status', 2)->count();
      $total_dpt_woman           = DataDpt::where('dpt_jenkel', 2)->where('dpt_status', 2)->count();
      $totalSuaraPartai          = DataTps::sum('tps_suara_partai');
      $totalSuaraCaleg           = DataTps::sum('tps_suara_caleg');

      return view('content.dashboard.dashboards-timdpt', [
        'total_dpt_is_active'       => $total_dpt_is_active,
        'total_dpt_tamansari'       => $total_dpt_tamansari,
        'total_dpt_cibereum'        => $total_dpt_cibereum,
        'total_dpt_purbaratu'       => $total_dpt_purbaratu,
        'total_dpt_man'             => $total_dpt_man,
        'total_dpt_woman'           => $total_dpt_woman,
        'totalSuaraPartai'          => $totalSuaraPartai,
        'totalSuaraCaleg'           => $totalSuaraCaleg,
      ]);
    } else if (session('role_id') == 4) {

      $total_dpt_is_active       = KecamatanCeklisDpt::whereIn('kecamatan_id', array(9, 2, 8))->count();
      $total_dpt_tamansari       = KecamatanCeklisDpt::where('kecamatan_id', 9)->count(); // 9 = Tamansari
      $total_dpt_cibereum        = KecamatanCeklisDpt::where('kecamatan_id', 2)->count(); // 2 = Cibereum
      $total_dpt_purbaratu       = KecamatanCeklisDpt::where('kecamatan_id', 8)->count(); // 8 = Purbaratu
      $total_dpt_man             = DataDpt::where('dpt_jenkel', 1)->where('dpt_status', 2)->count();
      $total_dpt_woman           = DataDpt::where('dpt_jenkel', 2)->where('dpt_status', 2)->count();
      $totalSuaraPartai          = DataTps::sum('tps_suara_partai');
      $totalSuaraCaleg           = DataTps::sum('tps_suara_caleg');

      return view('content.dashboard.dashboards-saksi', [
        'total_dpt_is_active'       => $total_dpt_is_active,
        'total_dpt_tamansari'       => $total_dpt_tamansari,
        'total_dpt_cibereum'        => $total_dpt_cibereum,
        'total_dpt_purbaratu'       => $total_dpt_purbaratu,
        'total_dpt_man'             => $total_dpt_man,
        'total_dpt_woman'           => $total_dpt_woman,
        'totalSuaraPartai'          => $totalSuaraPartai,
        'totalSuaraCaleg'           => $totalSuaraCaleg,
      ]);
    }else {
      return view('content.pages.pages-misc-not-authorized');
    }
  }

}
