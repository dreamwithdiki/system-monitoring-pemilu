<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\DataCaleg;
use App\Models\DataDpt;
use App\Models\DataTps;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DashboardController extends Controller
{

  public function index()
  {
    if (session('role_id') == 1) {

      $data_chart_bar_tps = DataTps::with('province', 'regency', 'district', 'village')->get();
      // Mengambil daftar kecamatan yang ada dalam data TPS
      $data_districts             = $data_chart_bar_tps->pluck('district.name')->unique();

      $total_dpt_is_active       = DataDpt::whereIn('dpt_district', array(3278020, 3278030, 3278031))->where('dpt_status', 2)->count();
      $total_dpt_is_deactive     = DataDpt::whereIn('dpt_district', array(3278020, 3278030, 3278031))->where('dpt_status', 1)->count();
      $total_dpt_tamansari       = DataDpt::where('dpt_district', 3278020)->where('dpt_status', 2)->count(); // 3278020 = Tamansari
      $total_dpt_cibereum        = DataDpt::where('dpt_district', 3278030)->where('dpt_status', 2)->count(); // 3278030 = Cibereum
      $total_dpt_purbaratu       = DataDpt::where('dpt_district', 3278031)->where('dpt_status', 2)->count(); // 3278031 = Purbaratu
      $total_dpt_man             = DataDpt::where('dpt_jenkel', 1)->where('dpt_status', 2)->count();
      $total_dpt_woman           = DataDpt::where('dpt_jenkel', 2)->where('dpt_status', 2)->count();
      $total_caleg_is_active     = DataCaleg::where('caleg_status', 2)->count();
      $totalSuaraPartai          = DataTps::sum('tps_suara_partai');
      $totalSuaraCaleg           = DataTps::sum('tps_suara_caleg');
      $total_users_is_active     = User::where('user_status', 2)->where('user_id', '!=', 2)->count();
      return view('content.dashboard.dashboards-admin', [
        'total_dpt_is_active'       => $total_dpt_is_active,
        'total_dpt_is_deactive'     => $total_dpt_is_deactive,
        'total_dpt_tamansari'       => $total_dpt_tamansari,
        'total_dpt_cibereum'        => $total_dpt_cibereum,
        'total_dpt_purbaratu'       => $total_dpt_purbaratu,
        'total_dpt_man'             => $total_dpt_man,
        'total_dpt_woman'           => $total_dpt_woman,
        'totalSuaraPartai'          => $totalSuaraPartai,
        'totalSuaraCaleg'           => $totalSuaraCaleg,
        'total_users_is_active'     => $total_users_is_active,
        'total_caleg_is_active'     => $total_caleg_is_active,
        'data_chart_bar_tps'        => $data_chart_bar_tps,
        'data_districts'            => $data_districts,  
      ]);
    } else if (session('role_id') == 2) {
      $data_chart_bar_tps = DataTps::with('province', 'regency', 'district', 'village')->get();
      // Mengambil daftar kecamatan yang ada dalam data TPS
      $data_districts             = $data_chart_bar_tps->pluck('district.name')->unique();

      $total_dpt_is_active       = DataDpt::whereIn('dpt_district', array(3278020, 3278030, 3278031))->where('dpt_status', 2)->count();
      $total_dpt_is_deactive     = DataDpt::whereIn('dpt_district', array(3278020, 3278030, 3278031))->where('dpt_status', 1)->count();
      $total_dpt_tamansari       = DataDpt::where('dpt_district', 3278020)->where('dpt_status', 2)->count(); // 3278020 = Tamansari
      $total_dpt_cibereum        = DataDpt::where('dpt_district', 3278030)->where('dpt_status', 2)->count(); // 3278030 = Cibereum
      $total_dpt_purbaratu       = DataDpt::where('dpt_district', 3278031)->where('dpt_status', 2)->count(); // 3278031 = Purbaratu
      $total_dpt_man             = DataDpt::where('dpt_jenkel', 1)->where('dpt_status', 2)->count();
      $total_dpt_woman           = DataDpt::where('dpt_jenkel', 2)->where('dpt_status', 2)->count();
      $total_caleg_is_active     = DataCaleg::where('caleg_status', 2)->count();
      $totalSuaraPartai          = DataTps::sum('tps_suara_partai');
      $totalSuaraCaleg           = DataTps::sum('tps_suara_caleg');
      $total_users_is_active     = User::where('user_status', 2)->where('user_id', '!=', 2)->count();

      return view('content.dashboard.dashboards-timses', [
        'total_dpt_is_active'       => $total_dpt_is_active,
        'total_dpt_is_deactive'     => $total_dpt_is_deactive,
        'total_dpt_tamansari'       => $total_dpt_tamansari,
        'total_dpt_cibereum'        => $total_dpt_cibereum,
        'total_dpt_purbaratu'       => $total_dpt_purbaratu,
        'total_dpt_man'             => $total_dpt_man,
        'total_dpt_woman'           => $total_dpt_woman,
        'totalSuaraPartai'          => $totalSuaraPartai,
        'totalSuaraCaleg'           => $totalSuaraCaleg,
        'total_users_is_active'     => $total_users_is_active,
        'total_caleg_is_active'     => $total_caleg_is_active,
        'data_chart_bar_tps'        => $data_chart_bar_tps,
        'data_districts'            => $data_districts, 
      ]);
    } else if (session('role_id') == 3) {
      $data_chart_bar_tps = DataTps::with('province', 'regency', 'district', 'village')->get();
      // Mengambil daftar kecamatan yang ada dalam data TPS
      $data_districts             = $data_chart_bar_tps->pluck('district.name')->unique();

      $total_dpt_is_active       = DataDpt::whereIn('dpt_district', array(3278020, 3278030, 3278031))->where('dpt_status', 2)->count();
      $total_dpt_is_deactive     = DataDpt::whereIn('dpt_district', array(3278020, 3278030, 3278031))->where('dpt_status', 1)->count();
      $total_dpt_tamansari       = DataDpt::where('dpt_district', 3278020)->where('dpt_status', 2)->count(); // 3278020 = Tamansari
      $total_dpt_cibereum        = DataDpt::where('dpt_district', 3278030)->where('dpt_status', 2)->count(); // 3278030 = Cibereum
      $total_dpt_purbaratu       = DataDpt::where('dpt_district', 3278031)->where('dpt_status', 2)->count(); // 3278031 = Purbaratu
      $total_dpt_man             = DataDpt::where('dpt_jenkel', 1)->where('dpt_status', 2)->count();
      $total_dpt_woman           = DataDpt::where('dpt_jenkel', 2)->where('dpt_status', 2)->count();
      $total_caleg_is_active     = DataCaleg::where('caleg_status', 2)->count();
      $totalSuaraPartai          = DataTps::sum('tps_suara_partai');
      $totalSuaraCaleg           = DataTps::sum('tps_suara_caleg');
      $total_users_is_active     = User::where('user_status', 2)->where('user_id', '!=', 2)->count();

      return view('content.dashboard.dashboards-timdpt', [
        'total_dpt_is_active'       => $total_dpt_is_active,
        'total_dpt_is_deactive'     => $total_dpt_is_deactive,
        'total_dpt_tamansari'       => $total_dpt_tamansari,
        'total_dpt_cibereum'        => $total_dpt_cibereum,
        'total_dpt_purbaratu'       => $total_dpt_purbaratu,
        'total_dpt_man'             => $total_dpt_man,
        'total_dpt_woman'           => $total_dpt_woman,
        'totalSuaraPartai'          => $totalSuaraPartai,
        'totalSuaraCaleg'           => $totalSuaraCaleg,
        'total_users_is_active'     => $total_users_is_active,
        'total_caleg_is_active'     => $total_caleg_is_active,
        'data_chart_bar_tps'        => $data_chart_bar_tps,
        'data_districts'            => $data_districts, 
      ]);
    } else if (session('role_id') == 4) {
      $data_chart_bar_tps = DataTps::with('province', 'regency', 'district', 'village')->get();
      // Mengambil daftar kecamatan yang ada dalam data TPS
      $data_districts             = $data_chart_bar_tps->pluck('district.name')->unique();

      $total_dpt_is_active       = DataDpt::whereIn('dpt_district', array(3278020, 3278030, 3278031))->where('dpt_status', 2)->count();
      $total_dpt_is_deactive     = DataDpt::whereIn('dpt_district', array(3278020, 3278030, 3278031))->where('dpt_status', 1)->count();
      $total_dpt_tamansari       = DataDpt::where('dpt_district', 3278020)->where('dpt_status', 2)->count(); // 3278020 = Tamansari
      $total_dpt_cibereum        = DataDpt::where('dpt_district', 3278030)->where('dpt_status', 2)->count(); // 3278030 = Cibereum
      $total_dpt_purbaratu       = DataDpt::where('dpt_district', 3278031)->where('dpt_status', 2)->count(); // 3278031 = Purbaratu
      $total_dpt_man             = DataDpt::where('dpt_jenkel', 1)->where('dpt_status', 2)->count();
      $total_dpt_woman           = DataDpt::where('dpt_jenkel', 2)->where('dpt_status', 2)->count();
      $total_caleg_is_active     = DataCaleg::where('caleg_status', 2)->count();
      $totalSuaraPartai          = DataTps::sum('tps_suara_partai');
      $totalSuaraCaleg           = DataTps::sum('tps_suara_caleg');
      $total_users_is_active     = User::where('user_status', 2)->where('user_id', '!=', 2)->count();

      return view('content.dashboard.dashboards-saksi', [
        'total_dpt_is_active'       => $total_dpt_is_active,
        'total_dpt_is_deactive'     => $total_dpt_is_deactive,
        'total_dpt_tamansari'       => $total_dpt_tamansari,
        'total_dpt_cibereum'        => $total_dpt_cibereum,
        'total_dpt_purbaratu'       => $total_dpt_purbaratu,
        'total_dpt_man'             => $total_dpt_man,
        'total_dpt_woman'           => $total_dpt_woman,
        'totalSuaraPartai'          => $totalSuaraPartai,
        'totalSuaraCaleg'           => $totalSuaraCaleg,
        'total_users_is_active'     => $total_users_is_active,
        'total_caleg_is_active'     => $total_caleg_is_active,
        'data_chart_bar_tps'        => $data_chart_bar_tps,
        'data_districts'            => $data_districts, 
      ]);
    } else if (session('role_id') == 5) {
      $data_chart_bar_tps = DataTps::with('province', 'regency', 'district', 'village')->get();
      // Mengambil daftar kecamatan yang ada dalam data TPS
      $data_districts             = $data_chart_bar_tps->pluck('district.name')->unique();

      $total_dpt_is_active       = DataDpt::whereIn('dpt_district', array(3278020, 3278030, 3278031))->where('dpt_status', 2)->count();
      $total_dpt_is_deactive     = DataDpt::whereIn('dpt_district', array(3278020, 3278030, 3278031))->where('dpt_status', 1)->count();
      $total_dpt_tamansari       = DataDpt::where('dpt_district', 3278020)->where('dpt_status', 2)->count(); // 3278020 = Tamansari
      $total_dpt_cibereum        = DataDpt::where('dpt_district', 3278030)->where('dpt_status', 2)->count(); // 3278030 = Cibereum
      $total_dpt_purbaratu       = DataDpt::where('dpt_district', 3278031)->where('dpt_status', 2)->count(); // 3278031 = Purbaratu
      $total_dpt_man             = DataDpt::where('dpt_jenkel', 1)->where('dpt_status', 2)->count();
      $total_dpt_woman           = DataDpt::where('dpt_jenkel', 2)->where('dpt_status', 2)->count();
      $total_caleg_is_active     = DataCaleg::where('caleg_status', 2)->count();
      $totalSuaraPartai          = DataTps::sum('tps_suara_partai');
      $totalSuaraCaleg           = DataTps::sum('tps_suara_caleg');
      $total_users_is_active     = User::where('user_status', 2)->where('user_id', '!=', 2)->count();

      return view('content.dashboard.dashboards-monitoring', [
        'total_dpt_is_active'       => $total_dpt_is_active,
        'total_dpt_is_deactive'     => $total_dpt_is_deactive,
        'total_dpt_tamansari'       => $total_dpt_tamansari,
        'total_dpt_cibereum'        => $total_dpt_cibereum,
        'total_dpt_purbaratu'       => $total_dpt_purbaratu,
        'total_dpt_man'             => $total_dpt_man,
        'total_dpt_woman'           => $total_dpt_woman,
        'totalSuaraPartai'          => $totalSuaraPartai,
        'totalSuaraCaleg'           => $totalSuaraCaleg,
        'total_users_is_active'     => $total_users_is_active,
        'total_caleg_is_active'     => $total_caleg_is_active,
        'data_chart_bar_tps'        => $data_chart_bar_tps,
        'data_districts'            => $data_districts, 
      ]);
    } else {
      return view('content.pages.pages-misc-not-authorized');
    }
  }

  public function getLatestCaleg()
  {
      $latestCaleg = DataCaleg::where('caleg_status', 2)
          ->orderBy('caleg_created_date', 'desc')
          ->first();

      return response()->json($latestCaleg);
  }

  public function show_upload_caleg($caleg_id)
    {
      $caleg = DataCaleg::find($caleg_id);
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

    public function show_upload_caleg_partai($caleg_partai_id)
    {
      $caleg_partai = DataCaleg::find($caleg_partai_id);
      if (!$caleg_partai) {
        abort(404);
      }

      $dir_file = Carbon::parse($caleg_partai->caleg_created_date)->format('Ymd');
      $file_path_partai = $dir_file.'/'.$caleg_partai->caleg_photo_partai;

      $path_partai = storage_path('app/public/caleg_partai_uploads/'.$file_path_partai);
      if (!File::exists($path_partai)) {
        $path_partai = public_path('assets/upload/user/default.jpeg');
      }

      $file = File::get($path_partai);
      $type = File::mimeType($path_partai);
      $response = response($file, 200);
      $response->header("Content-Type", $type);

      return $response;
    }


}
