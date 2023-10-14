<?php

namespace App\Http\Controllers\report;

use App\Exports\ReportExcelExport;
use App\Exports\VisitOrderExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Controller;
use App\Models\VisitOrder;
use App\Models\Client;
use App\Models\Site;
use App\Models\Partner;
use App\Models\VisitOrderVisual;
use App\Models\ChecklistGroup;
use App\Models\ChecklistAnswer;
use App\Models\VisitOrderHistory;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReportVisitOrderController extends Controller
{
  public function index()
  {
    return view('content.report.report-visit-order');
  }

  public function findClient(Request $request)
  {
    $search = $request->search;
    $clients = Client::orderby('client_name', 'asc')
      ->select('client_id', 'client_name', 'client_code')
      ->where('client_name', 'like', '%' . $search . '%')
      ->orWhere('client_code', 'like', '%' . $search . '%')
      ->isActive()
      ->get();

    $response = array();
    $response[] = array(
      "id"    => 0,
      "text"  => "Get All"
    );
    foreach ($clients as $client) {
      $response[] = array(
        "id"    => $client->client_id,
        "text"  => $client->client_code . ' - ' . $client->client_name
      );
    }

    return response()->json($response);
  }

  public function findSite(Request $request)
  {
    $search = $request->search;
    $sites = Site::orderby('site_name', 'asc')
      ->select('site_id', 'site_name', 'site_code')
      ->where('site_name', 'like', '%' . $search . '%')
      ->orWhere('site_code', 'like', '%' . $search . '%')
      ->isActive()
      ->get();

    $response = array();
    $response[] = array(
      "id"    => 0,
      "text"  => "Get All"
    );
    foreach ($sites as $site) {
      $response[] = array(
        "id"    => $site->site_id,
        "text"  => $site->site_code . ' - ' . $site->site_name
      );
    }

    return response()->json($response);
  }

  public function findPartner(Request $request)
  {
    $search = $request->search;
    $partners = Partner::orderby('partner_name', 'asc')
      ->select('partner_id', 'partner_name')
      ->where('partner_name', 'like', '%' . $search . '%')
      ->isActive()
      ->get();

    $response = array();
    $response[] = array(
      "id"    => 0,
      "text"  => "Get All"
    );
    foreach ($partners as $partner) {
      $response[] = array(
        "id"    => $partner->partner_id,
        "text"  => $partner->partner_name
      );
    }

    return response()->json($response);
  }

  public function datatable(Request $request)
  {
    $columns = [
      0 => 'visit_order_id',
      1 => 'visit_order_number',
      2 => 'visit_order_date',
      3 => 'visit_order_due_date',
      4 => 'debtor_id', // debtor name
      5 => 'client_id', // client name
      6 => 'site_id', // site name
      7 => 'visit_order_province',
      8 => 'visit_order_regency',
      9 => 'visit_order_location',
      10 => 'partner_id',
      11 => 'visit_order_custom_number',
      13 => 'visit_order_status', // partner name
    ];

    $startDate = date('Y-m-d', strtotime($request->input('start_date')));
    $endDate = date('Y-m-d', strtotime($request->input('end_date')));

    $search = [];
    $clientName = ($request->input('client_id') && $request->input('client_id') != 0) ? Client::where('client_id', $request->input('client_id'))->first()->client_name : '';
    $siteName = ($request->input('site_id') && $request->input('site_id') != 0) ? Site::where('site_id', $request->input('site_id'))->first()->site_name : '';
    $partnerName = ($request->input('partner_id') && $request->input('partner_id') != 0) ? ((Partner::where('partner_id', $request->input('partner_id'))->first()->partner_name) ? Partner::where('partner_id', $request->input('partner_id'))->first()->partner_name : '') : '';
    $statusName = ($request->input('status_id')) ? $request->input('status_id') : 0;

    $totalData = VisitOrder::where(function ($query) use ($startDate, $endDate, $request) {
      if ($request->input('time_id') == 1) {
        $query->where('visit_order_date', '>=', $startDate)->where('visit_order_date', '<=', $endDate);
      } else {
        $query->where('visit_order_visited_date', '>=', $startDate)->where('visit_order_visited_date', '<=', $endDate);
      }
    })
      ->where('visit_order_status', ($statusName != 0) ? "=" : '>=', $statusName)
      ->where(function ($query) use ($partnerName) {
        if ($partnerName) {
          $query->whereRelation('partner', 'partner_name', 'LIKE', "%{$partnerName}%");
        } else {
          $query;
        }
      })
      ->whereHas('client', function ($q) use ($clientName) {
        $q->where('client_name', 'LIKE', "%{$clientName}%");
      })
      ->whereHas('site', function ($q) use ($siteName) {
        $q->where('site_name', 'LIKE', "%{$siteName}%");
      })
      ->count();
    $totalFiltered = $totalData;

    if (!empty($request->input())) {
      $limit = $request->input('length');
      $start = $request->input('start');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');

      if (empty($request->input('search.value'))) {
        $visit_order = VisitOrder::with('province', 'regency', 'client', 'site', 'partner', 'debtor')
          ->where(function ($query) use ($startDate, $endDate, $request) {
            if ($request->input('time_id') == 1) {
              $query->where('visit_order_date', '>=', $startDate)->where('visit_order_date', '<=', $endDate);
            } else {
              $query->where('visit_order_visited_date', '>=', $startDate)->where('visit_order_visited_date', '<=', $endDate);
            }
          })
          ->where('visit_order_status', ($statusName != 0) ? "=" : '>=', $statusName)
          ->where(function ($query) use ($partnerName) {
            if ($partnerName) {
              $query->whereRelation('partner', 'partner_name', 'LIKE', "%{$partnerName}%");
            } else {
              $query;
            }
          })
          ->whereHas('client', function ($q) use ($clientName) {
            $q->where('client_name', 'LIKE', "%{$clientName}%");
          })
          ->whereHas('site', function ($q) use ($siteName) {
            $q->where('site_name', 'LIKE', "%{$siteName}%");
          })
          ->offset($start)
          ->limit($limit)
          ->orderBy($order, $dir)
          ->get();
      } else {
        $search = $request->input('search.value');

        $visit_order = VisitOrder::with('province', 'regency', 'client', 'site', 'partner', 'debtor')
          ->where(function ($query) use ($search) {
            $query->where('visit_order_number', 'LIKE', "%{$search}%")
              ->orWhere('visit_order_location', 'LIKE', "%{$search}%")
              ->orWhereRelation('debtor', 'debtor_name', 'LIKE', "%{$search}%");
          })
          ->where(function ($query) use ($startDate, $endDate, $request) {
            if ($request->input('time_id') == 1) {
              $query->where('visit_order_date', '>=', $startDate)->where('visit_order_date', '<=', $endDate);
            } else {
              $query->where('visit_order_visited_date', '>=', $startDate)->where('visit_order_visited_date', '<=', $endDate);
            }
          })
          ->where('visit_order_status', ($statusName != 0) ? "=" : '>=', $statusName)
          ->where(function ($query) use ($partnerName) {
            if ($partnerName) {
              $query->whereRelation('partner', 'partner_name', 'LIKE', "%{$partnerName}%");
            } else {
              $query;
            }
          })
          ->whereHas('client', function ($q) use ($clientName) {
            $q->where('client_name', 'LIKE', "%{$clientName}%");
          })
          ->whereHas('site', function ($q) use ($siteName) {
            $q->where('site_name', 'LIKE', "%{$siteName}%");
          })
          ->offset($start)
          ->limit($limit)
          ->orderBy($order, $dir)
          ->get();

        $totalFiltered = VisitOrder::with('province', 'regency', 'client', 'site', 'partner', 'debtor')
          ->where(function ($query) use ($search) {
            $query->where('visit_order_number', 'LIKE', "%{$search}%")
              ->orWhere('visit_order_location', 'LIKE', "%{$search}%")
              ->orWhereRelation('debtor', 'debtor_name', 'LIKE', "%{$search}%");
          })
          ->where(function ($query) use ($startDate, $endDate, $request) {
            if ($request->input('time_id') == 1) {
              $query->where('visit_order_date', '>=', $startDate)->where('visit_order_date', '<=', $endDate);
            } else {
              $query->where('visit_order_visited_date', '>=', $startDate)->where('visit_order_visited_date', '<=', $endDate);
            }
          })
          ->where('visit_order_status', ($statusName != 0) ? "=" : '>=', $statusName)
          ->where(function ($query) use ($partnerName) {
            if ($partnerName) {
              $query->whereRelation('partner', 'partner_name', 'LIKE', "%{$partnerName}%");
            } else {
              $query;
            }
          })
          ->whereHas('client', function ($q) use ($clientName) {
            $q->where('client_name', 'LIKE', "%{$clientName}%");
          })
          ->whereHas('site', function ($q) use ($siteName) {
            $q->where('site_name', 'LIKE', "%{$siteName}%");
          })
          ->count();
      }
    }

    $data = [];

    if (!empty($visit_order)) {
      $no = $start;
      foreach ($visit_order as $order) {
        $nestedData['no']                   = ++$no;
        $nestedData['visit_order_id']       = $order->visit_order_id;
        $nestedData['visit_order_encrypt_id'] = Crypt::encrypt($order->visit_order_id);
        $nestedData['visit_order_number']   = $order->visit_order_number;
        $nestedData['visit_order_custom_number']   = $order->visit_order_custom_number ?? '-';
        $nestedData['visit_order_date']     = format_short_local_date($order->visit_order_date);
        $nestedData['visit_order_due_date']     = format_short_local_date($order->visit_order_due_date);
        $nestedData['debtor_name']          = $order->debtor->debtor_name;
        $nestedData['client_name']          = $order->client->client_name;
        $nestedData['site_name']            = $order->site->site_name;
        $nestedData['visit_order_location'] = $order->visit_order_location;
        $nestedData['partner_name']         = ($order->partner) ? $order->partner->partner_name : "-";
        $nestedData['province_name']         = $order->province->name;
        $nestedData['regency_name']         = $order->regency->name;
        $nestedData['download_status']   = ($order->visit_order_status == 5 || $order->visit_order_status == 6) ? (($order->visit_order_downloaded_by && $order->visit_order_downloaded_date) ? "Downloaded" : "Not Downloaded") : "-";
        $nestedData['visit_order_status']   = $order->visit_order_status;
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
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    Carbon::setLocale('id');
    $visit = VisitOrder::with('client', 'site', 'site_contact', 'history.user', 'debtor', 'partner', 'province', 'regency', 'district', 'village', 'visit_type.visit_visual_type.checklist_visual', 'visit_order_visual')->where('visit_order_id', $id)->first();
    if ($visit) {
      foreach ($visit->visit_order_visual as $order) {
        $path = 'storage/visit_order_visual_uploads/' . Carbon::parse($order->visit_order_visual_file_created_date)->format('Ymd') . "/" . $order->visit_order_visual_file;
        if (file_exists($path)) {
          $order['visual_image_url'] = '/' . $path;
        } else {
          $order['visual_image_url'] = '/assets/img/no-image-asset.jpg';
        }
      }
      foreach ($visit->history as $order) {
        $order['date_created_format'] = Carbon::parse($order->visit_order_history_created_date)->translatedFormat('d F Y, H:i');
      }
      return response()->json(['status' => true, 'data' => $visit]);
    } else {
      return response()->json(['status' => false, 'data' => []]);
    }
  }

  public function pdf($id)
  {
    Carbon::setLocale('id');
    $decryptId = Crypt::decrypt($id);
    $visit_order = VisitOrder::with('debtor', 'site', 'visit_order_visual', 'site_contact')->where('visit_order_id', $decryptId)->first();
    $visit_order_visual_first = VisitOrderVisual::where('visit_order_id', $decryptId)->first();
    $latest_visit_order_history = Carbon::parse(VisitOrderHistory::where('visit_order_id', $decryptId)->where('visit_order_status', 5)->latest('visit_order_history_created_date')->first()->visit_order_history_created_date)->translatedFormat('d F Y') ?? '-';
    $date = '';
    if ($visit_order_visual_first) {
      $date = Carbon::parse($visit_order_visual_first->visit_order_visual_file_created_date)->format('Ymd');
    }
    $checklistGroup = ChecklistGroup::with('checklist_active')->get();
    $checklistAnswer = ChecklistAnswer::where('visit_order_id', $decryptId)->get();
    $pdf = Pdf::loadview('content.report.pdf.report-visit-order-pdf', ['visit_order' => $visit_order, 'date' => $date, 'checklistGroup' => $checklistGroup, 'checklistAnswer' => $checklistAnswer, 'latest_visit_order_history' => $latest_visit_order_history])->setPaper('a4', 'landscape');
    return $pdf->stream();
  }

  public function excel($id)
  {
    Carbon::setLocale('id');
    $visit_order = VisitOrder::with('debtor', 'site', 'visit_order_visual', 'site_contact')->where('visit_order_id', $id)->first();
    $visit_order_visual_first = VisitOrderVisual::where('visit_order_id', $id)->first();
    $latest_visit_order_history = Carbon::parse(VisitOrderHistory::where('visit_order_id', $id)->where('visit_order_status', 5)->latest('visit_order_history_created_date')->first()->visit_order_history_created_date)->translatedFormat('d F Y') ?? '-';
    $date = '';
    if ($visit_order_visual_first) {
      $date = Carbon::parse($visit_order_visual_first->visit_order_visual_file_created_date)->format('Ymd');
    }
    $checklistGroup = ChecklistGroup::with('checklist_active')->get();
    $checklistAnswer = ChecklistAnswer::where('visit_order_id', $id)->get();
    return Excel::download(new ReportExcelExport($visit_order, $date, $checklistGroup, $checklistAnswer, $latest_visit_order_history), 'excel_visit_order.xlsx');
  }

  public function dataExcel(Request $request)
  {
    $clientName = ($request->input('client_id') && $request->input('client_id') != 0) ? Client::where('client_id', $request->input('client_id'))->first()->client_name : '';
    $siteName = ($request->input('site_id') && $request->input('site_id') != 0) ? Site::where('site_id', $request->input('site_id'))->first()->site_name : '';
    $partnerName = ($request->input('partner_id') && $request->input('partner_id') != 0) ? ((Partner::where('partner_id', $request->input('partner_id'))->first()->partner_name) ? Partner::where('partner_id', $request->input('partner_id'))->first()->partner_name : '') : '';
    $statusName = ($request->input('status_id')) ? $request->input('status_id') : 0;
    $startDate = date('Y-m-d', strtotime($request->input('start_date')));
    $endDate = date('Y-m-d', strtotime($request->input('end_date')));

    $visitOrder = VisitOrder::with('client', 'site', 'site_contact', 'history', 'debtor', 'partner', 'province', 'regency')
      ->where(function ($query) use ($request, $startDate, $endDate) {
        if ($request->time_id == 1) {
          $query->where('visit_order_date', '>=', $startDate)->where('visit_order_date', '<=', $endDate);
        } else {
          $query->whereHas('history', function ($q) use ($startDate, $endDate) {
            $q->where('visit_order_status', 5)->where('visit_order_history_created_date', '>=', $startDate)->where('visit_order_history_created_date', '<=', $endDate);
          });
        }
      })
      ->where('visit_order_status', ($statusName != 0) ? "=" : '>=', $statusName)
      ->where('visit_order_status', '!=', 99)
      ->whereRelation('partner', 'partner_name', 'LIKE', "%{$partnerName}%")
      ->whereHas('client', function ($q) use ($clientName) {
        $q->where('client_name', 'LIKE', "%{$clientName}%");
      })
      ->whereHas('site', function ($q) use ($siteName) {
        $q->where('site_name', 'LIKE', "%{$siteName}%");
      })
      ->get();
    return Excel::download(new VisitOrderExport($visitOrder, $startDate . ' - ' . $endDate, Carbon::now()), 'report_visit_order.xlsx');
  }
}
