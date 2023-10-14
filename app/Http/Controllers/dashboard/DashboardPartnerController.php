<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardPartnerController extends Controller
{
  public function index()
  {
    if (session('role_id') == 3) {
      return view('content.dashboard.dashboards-partner');
    } else {
      return view('content.pages.pages-misc-not-authorized');
    }
  }
}
