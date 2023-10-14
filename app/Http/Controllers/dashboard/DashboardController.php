<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\User;
use App\Models\VisitOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

  public function index()
  {
    if (session('role_id') == 1 || session('role_id') == 2) {

      $list_order_each_status = [];
      $month = [];
      $year = [];
      for ($i = 0; $i < 9; $i++) {
        array_push($list_order_each_status, VisitOrder::where('visit_order_status', $i + 1)->count());
      }
      for ($m = 1; $m <= 12; $m++) {
        $month[] = date('F', mktime(0, 0, 0, $m, 1, date('Y')));
      }
      for ($y = 0; $y <= 5; $y++) {
        $year[] = date('Y') - $y;
      }
      $total_partner_is_active       = Partner::where('partner_status', 2)->count();
      $total_users_is_active         = User::where('user_status', 2)->count();
      $total_visit_order_month_chart = $this->total_visit_order_month_chart(null);
      return view('content.dashboard.dashboards-admin', [
        'list_order_each_status'        => $list_order_each_status,
        'list_month'        => $month,
        'list_year'        => $year,
        'total_visit_order'        => VisitOrder::where('visit_order_status', '!=', 99)->count(),
        'total_partner_is_active'       => $total_partner_is_active,
        'total_users_is_active'         => $total_users_is_active,
        'total_visit_order_month_chart' => $total_visit_order_month_chart
      ]);
    } else {
      if (session('role_id') == 3) {

        $total_visit_order_assign = VisitOrder::where('visit_order_status', 2)->whereHas('partner', function ($q) {
          $q->where('partner_email', session('user_email'));
        })->count();

        $total_visit_order_visited = VisitOrder::where('visit_order_status', '>=', 5)->where('visit_order_status', '!=', 99)
          ->whereHas('partner', function ($q) {
            $q->where('partner_email', session('user_email'));
          })->count();

        return view('content.dashboard.dashboards-partner', [
          'total_visit_order_assign' => $total_visit_order_assign,
          'total_visit_order_visited' => $total_visit_order_visited
        ]);
      }

      return view('content.pages.pages-misc-not-authorized');
    }
  }

  public function filter_year_month($year, $month)
  {
    $list_order_each_status = [];
    $total_visit_order_month_chart = $this->total_visit_order_month_chart($year == 0 ? date('Y') : $year);
    for ($i = 0; $i < 9; $i++) {
      array_push($list_order_each_status, VisitOrder::where('visit_order_status', $i + 1)->whereYear('visit_order_date', ($year == 0) ? '>' : '=', $year)->whereMonth('visit_order_date', ($month == 0) ? '>' : '=', $month)->count());
    }
    return response()->json([
      'list_order_each_status'        => $list_order_each_status,
      'total_visit_order_month_chart' => $total_visit_order_month_chart,
      'total_visit_order'        => VisitOrder::where('visit_order_status', '!=', 99)->whereYear('visit_order_date', ($year == 0) ? '>' : '=', $year)->whereMonth('visit_order_date', ($month == 0) ? '>' : '=', $month)->count(),
    ]);
  }

  private function total_visit_order_month_chart($year)
  {
    $data = VisitOrder::selectRaw("DATE_FORMAT(visit_order_date, '%M - %Y') as month, COUNT(*) as total")
      ->whereYear('visit_order_date', $year ?? date('Y'))
      ->groupBy('month')
      ->orderBy('month')
      ->get();

    return $data;
  }
}
