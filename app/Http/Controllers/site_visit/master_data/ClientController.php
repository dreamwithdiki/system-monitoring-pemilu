<?php

namespace App\Http\Controllers\site_visit\master_data;

use Carbon\Carbon;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\VisitOrder;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
  public function index()
  {
    if (session('role_id') == 1 || session('role_id') == 2) {
      return view('content.site-visit.master-data.client');
    } else {
      return view('content.pages.pages-misc-not-authorized');
    }
  }

  public function find(Request $request)
  {
    $search = $request->search;
    $clients = Client::orderby('client_name', 'asc')
      ->select('client_id', 'client_name', 'client_code')
      ->where('client_name', 'like', '%' . $search . '%')
      ->orWhere('client_code', 'like', '%' . $search . '%')
      ->isActive()
      ->get();

    $response = array();
    foreach ($clients as $client) {
      $response[] = array(
        "id"    => $client->client_id,
        "text"  => $client->client_code . ' - ' . $client->client_name
      );
    }

    return response()->json($response);
  }

  public function datatable(Request $request)
  {
    $columns = [
      0 => 'client_id',
      1 => 'client_code',
      2 => 'client_name',
      3 => 'client_address',
      4 => 'total_order',
    ];

    $search = [];
    $totalData = Client::where('client_status', '!=', 5)->count();
    $totalFiltered = $totalData;

    if (!empty($request->input())) {
      $limit = $request->input('length');
      $start = $request->input('start');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');

      if (empty($request->input('search.value'))) {
        $client = Client::with('client_order')
          ->where('client_status', '!=', 5)
          ->offset($start)
          ->limit($limit)
          ->orderBy($order, $dir)
          ->get();
      } else {
        $search = $request->input('search.value');

        $client = Client::with('client_order')
          ->where('client_code', 'LIKE', "%{$search}%")
          ->orWhere('client_name', 'LIKE', "%{$search}%")
          ->where('client_status', '!=', 5)
          ->offset($start)
          ->limit($limit)
          ->orderBy($order, $dir)
          ->get();

        $totalFiltered = Client::with('client_order')
          ->where('client_code', 'LIKE', "%{$search}%")
          ->orWhere('client_name', 'LIKE', "%{$search}%")
          ->where('client_status', '!=', 5)
          ->count();
      }
    } else {
      $start = 0;
      $client = Client::where('client_status', '!=', 5)->get();
    }

    $data = [];

    if (!empty($client)) {
      $no = $start;
      foreach ($client as $cli) {
        $clientId = $cli->client_id;
        $nestedData['no']            = ++$no;
        $nestedData['client_id']     = $clientId;
        $nestedData['client_code']   = $cli->client_code;
        $nestedData['client_name']   = $cli->client_name;
        $nestedData['client_address'] = $cli->client_address;

        $nestedData['total_order'] = VisitOrder::where(function ($q) use ($clientId) {
            $q->where('client_id', $clientId)
              ->where('visit_order_status', '!=', 3)
              ->where('visit_order_status', '!=', 99);
          })
          ->count();

        $nestedData['client_status'] = $cli->client_status;
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
   * @param  \App\Http\Requests\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'client_code'             => 'required|max:50',
      'client_name'             => 'required|max:255',
    ]);

    // dd($request->all());

    if ($validator->fails()) {
      return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
    }

    // client Status
    $status = !empty($request->client_status) && $request->client_status == 'on' ? 2 : 1;

    // client code check duplicate entry
    $client_code_exist = Client::where('client_code', $request->client_code)->first();
    if ($client_code_exist) {
      if ($client_code_exist->client_status == 5) {
        return response()->json(['status' => false, 'message' => ['title' => 'Wrong Code', 'text' => 'Code already used by deleted client!']]);
      }
      return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'Code already used by another client!']]);
    }

    Client::create([
      'client_status'     => $status,
      'client_param'      => $request->client_param,
      'client_code'       => $request->client_code,
      'client_name'       => $request->client_name,
      'client_phone'      => $request->client_phone,
      'client_fax'        => $request->client_fax,
      'client_email'      => $request->client_email,
      'client_address'    => $request->client_address,
      'client_desc'       => $request->client_desc,
      'client_created_by' => session('user_id'),
      'client_created_date' => Carbon::now()->format('Y-m-d H:i:s'),
    ]);

    return response()->json(['status' => true, 'message' => ['title' => 'Successfully created!', 'text' => 'Client created successfully!']]);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Client  $client
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $type = Client::where('client_id', $id)->first();
    if ($type) {
      return response()->json(['status' => true, 'data' => $type]);
    } else {
      return response()->json(['status' => false, 'data' => []]);
    }
  }

  public function statusUpdate(Request $request)
  {
    $this->validate($request, [
      'client_id'     => 'required',
      'client_status' => 'required',
    ]);

    $type = Client::where('client_id', $request->client_id)->first();
    $type->client_status        = $request->client_status;
    $type->client_updated_by    = session('user_id');
    $type->save();

    if ($request->client_status == 2) {
      return response()->json(['status' => true, 'message' => ['title' => 'Client Activated!', 'text' => 'Client ' . $type->client_name . ' status has been activated!']]);
    } else {
      return response()->json(['status' => true, 'message' => ['title' => 'Client Deactivated!', 'text' => 'Client ' . $type->client_name . ' status has been deactivated!']]);
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Models\Client  $client
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $this->validate($request, [
      'client_code'             => 'required|max:50',
      'client_name'             => 'required|max:255',
    ]);

    // Client Status
    $status = !empty($request->client_status) && $request->client_status == 'on' ? 2 : 1;

    // Client code check duplicate entry
    $client_code_exist = Client::where('client_code', $request->client_code)->where('client_id', '!=', $id)->first();
    if ($client_code_exist) {
      if ($client_code_exist->client_status == 5) {
        return response()->json(['status' => false, 'message' => ['title' => 'Wrong Code', 'text' => 'Code already used by deleted client!']]);
      }
      return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'Code already used by another client!']]);
    }

    $type = Client::where('client_id', $id)->first();
    $type->client_status            = $status;
    $type->client_param             = $request->client_param;
    $type->client_code              = $request->client_code;
    $type->client_name              = $request->client_name;
    $type->client_phone             = $request->client_phone;
    $type->client_fax               = $request->client_fax;
    $type->client_email             = $request->client_email;
    $type->client_address           = $request->client_address;
    $type->client_desc              = $request->client_desc;
    $type->client_updated_by        = session('user_id');
    $type->client_updated_date      = Carbon::now()->format('Y-m-d H:i:s');
    $type->save();

    return response()->json(['status' => true, 'message' => ['title' => 'Successfully Updated!', 'text' => 'Client ' . $request->client_name . ' updated successfully!']]);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Client  $client
   * @return \Illuminate\Http\Response
   */
  public function delete(Request $request)
  {
    $type = Client::where('client_id', $request->client_id)->first();
    $type->client_status        = '5';
    $type->client_deleted_by    = session('user_id');
    $type->client_deleted_date  = Carbon::now()->format('Y-m-d H:i:s');
    $type->save();

    return response()->json(['status' => true, 'message' => ['title' => 'Client Deleted!', 'text' => 'Client ' . $request->client_name . ' has been deleted!']]);
  }
}
