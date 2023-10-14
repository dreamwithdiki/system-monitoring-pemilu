<?php

namespace App\Http\Controllers\billings;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Models\VisitOrder;
use App\Models\ClientInvoice;
use App\Models\ClientInvoiceDetail;
use App\Models\ClientInvoiceFile;
use App\Models\ClientInvoiceHistory;
use App\Models\VisitOrderHistory;
use Illuminate\Support\Facades\DB;

class ClientInvoiceController extends Controller
{
  private function generateUniqueCode($prefix)
  {
    $currentYear = Carbon::now()->format('Y');

    // Get the last visit order for the current month
    $lastVisitOrder = ClientInvoice::where('client_invoice_number', 'LIKE', "$prefix/$currentYear/%")
      ->orderBy('client_invoice_id', 'desc')
      ->first();

    if ($lastVisitOrder) {
      // Extract the last number from the last visit order in the current month
      $lastNumber = intval(substr($lastVisitOrder->client_invoice_number, -5));

      $newNumber = $lastNumber + 1;
    } else {
      // If there are no previous visit orders for the current month, start with 00001
      $newNumber = 1;
    }

    $formattedNumber = sprintf("%05d", $newNumber);
    return $prefix . '/' . $currentYear . '/' . $formattedNumber;
  }

  public function index()
  {
    if (session('role_id') == 1 || session('role_id') == 2) {
      return view('content.invoice.client-invoice');
    } else {
      return view('content.pages.pages-misc-not-authorized');
    }
  }

  public function datatable(Request $request)
  {
    $columns = [
      0 => 'client_invoice_id',
      1 => 'client_invoice_number',
      2 => 'client_invoice_name',
      3 => 'site_id',
      4 => 'client_invoice_month',
      5 => 'client_invoice_year',
    ];

    $search = [];
    $totalData = ClientInvoice::where('client_invoice_status', '!=', 5)->count();
    $totalFiltered = $totalData;

    if (!empty($request->input())) {
      $limit = $request->input('length');
      $start = $request->input('start');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');

      if (empty($request->input('search.value'))) {
        $clientInvoice = ClientInvoice::with('site')->where('client_invoice_status', '!=', 5)
          ->offset($start)
          ->limit($limit)
          ->orderBy($order, $dir)
          ->get();
      } else {
        $search = $request->input('search.value');

        $clientInvoice = ClientInvoice::with('site')->where('client_invoice_number', 'LIKE', "%{$search}%")
          ->orWhere('client_invoice_name', 'LIKE', "%{$search}%")
          ->where('client_invoice_status', '!=', 5)
          ->offset($start)
          ->limit($limit)
          ->orderBy($order, $dir)
          ->get();

        $totalFiltered = ClientInvoice::where('client_invoice_number', 'LIKE', "%{$search}%")
          ->orWhere('client_invoice_name', 'LIKE', "%{$search}%")
          ->where('client_invoice_status', '!=', 5)
          ->count();
      }
    } else {
      $start = 0;
      $clientInvoice = ClientInvoice::with('site')->where('client_invoice_status', '!=', 5)->get();
    }

    $data = [];

    if (!empty($clientInvoice)) {
      $no = $start;
      foreach ($clientInvoice as $cli) {
        $nestedData['no']            = ++$no;
        $nestedData['client_invoice_id']     = $cli->client_invoice_id;
        $nestedData['client_invoice_number']   = $cli->client_invoice_number;
        $nestedData['client_invoice_name']   = $cli->client_invoice_name;
        $nestedData['site_name'] = $cli->site->site_name;
        $nestedData['client_invoice_month'] = $cli->client_invoice_month;
        $nestedData['client_invoice_year'] = $cli->client_invoice_year;
        $nestedData['client_invoice_status'] = $cli->client_invoice_status;
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

  public function store(Request $request)
  {
    $time = Carbon::now();
    $validator = Validator::make($request->all(), [
      'client_invoice_name' => 'required',
      'site_id' => 'required',
      'year_month' => 'required',
      'client_invoice_file.*' => 'nullable|file|image|mimes:jpeg,png,jpg|max:1024'
    ]);

    if ($validator->fails()) {
      return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
    }

    $clientInvoiceNumber = $this->generateUniqueCode('CIWO');

    $clientInvoice = ClientInvoice::create([
      'client_invoice_status' => 2,
      'site_id' => $request->site_id,
      'client_invoice_number' => $clientInvoiceNumber,
      'client_invoice_name' => $request->client_invoice_name,
      'client_invoice_month' => explode("-", $request->year_month)[1],
      'client_invoice_year' => explode("-", $request->year_month)[0],
      'client_invoice_desc' => $request->client_invoice_desc,
      'client_invoice_created_by'       => session('user_id'),
      'client_invoice_created_date'     => $time->format('Y-m-d H:i:s'),
    ]);

    // Upload Image
    $timeNow = Carbon::now()->format('Ymd');
    Storage::disk('public')->makeDirectory('client_invoice_uploads/' . $timeNow);

    if ($request->hasFile('client_invoice_file')) {
      foreach ($request->file('client_invoice_file') as $key => $img) {
        //Upload image ke storage
        $filename = Carbon::now()->format('Hisu_') . 'client_invoice' . ($key) . '.' . $img->getClientOriginalExtension();
        $img = Image::make($img)->save('./storage/client_invoice_uploads/' . $timeNow . '/' . $filename);

        $clientInvoiceFile = ClientInvoiceFile::create([
          'client_invoice_id' => $clientInvoice->client_invoice_id,
          'client_invoice_file' => $filename,
          'client_invoice_file_desc' => $request->client_invoice_desc,
          'client_invoice_file_created_by'       => session('user_id'),
          'client_invoice_file_created_date'     => $time->format('Y-m-d H:i:s'),
        ]);
      }
    }
    // End Upload Image

    // Add Client Invoice Detail
    foreach ($request->visit_order as $key => $order) {
      ClientInvoiceDetail::create([
        'client_invoice_id' => $clientInvoice->client_invoice_id,
        'visit_order_id' => $key,
        'client_invoice_detail_created_by'       => session('user_id'),
        'client_invoice_detail_created_date'     => $time->format('Y-m-d H:i:s'),
      ]);
    }
    // End Add Client Invoice Detail

    // Add Client Invoice History
    ClientInvoiceHistory::create([
      'client_invoice_id'               => $clientInvoice->client_invoice_id,
      'client_invoice_history_status'   => 1,
      'client_invoice_history_desc'     => $request->client_invoice_desc,
      'client_invoice_history_created_by'       => session('user_id'),
      'client_invoice_history_created_date'     => $time->format('Y-m-d H:i:s'),
    ]);
    ClientInvoiceHistory::create([
      'client_invoice_id'               => $clientInvoice->client_invoice_id,
      'client_invoice_history_status'   => 2,
      'client_invoice_history_desc'     => $request->client_invoice_desc,
      'client_invoice_history_created_by'       => session('user_id'),
      'client_invoice_history_created_date'     => $time->format('Y-m-d H:i:s'),
    ]);
    // End Add Client Invoice History

    return response()->json(['status' => true, 'message' => ['title' => 'Successfully created!', 'text' => 'Client Invoice ' . $clientInvoiceNumber . ' created successfully!']]);
  }

  public function update(Request $request, $id)
  {
    $time = Carbon::now();
    $clientInvoice = ClientInvoice::where('client_invoice_id', $id)->first();
    $validator = Validator::make($request->all(), [
      'client_invoice_name' => 'required',
      'site_id' => 'required',
      'year_month' => 'required',
      'client_invoice_file.*' => 'nullable|file|image|mimes:jpeg,png,jpg|max:1024'
    ]);

    if ($validator->fails()) {
      return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
    }

    $clientInvoice->update([
      'site_id' => $request->site_id,
      'client_invoice_name' => $request->client_invoice_name,
      'client_invoice_month' => explode("-", $request->year_month)[1],
      'client_invoice_year' => explode("-", $request->year_month)[0],
      'client_invoice_desc' => $request->client_invoice_desc,
      'client_invoice_updated_by'       => session('user_id'),
      'client_invoice_updated_date'     => $time->format('Y-m-d H:i:s'),
    ]);

    // Add Client Invoice Detail
    try {
      DB::beginTransaction();

      ClientInvoiceDetail::where('client_invoice_id', $id)->delete();
      foreach ($request->visit_order as $key => $order) {
        ClientInvoiceDetail::create([
          'client_invoice_id' => $clientInvoice->client_invoice_id,
          'visit_order_id' => $key,
          'client_invoice_detail_created_by'       => session('user_id'),
          'client_invoice_detail_created_date'     => $time->format('Y-m-d H:i:s'),
        ]);
      }
      DB::commit();
    } catch (\Exception $exp) {
      DB::rollBack();
    }
    // End Add Client Invoice Detail

    // Add Client Invoice History
    ClientInvoiceHistory::create([
      'client_invoice_id'               => $clientInvoice->client_invoice_id,
      'client_invoice_history_status'   => $clientInvoice->client_invoice_status,
      'client_invoice_history_desc'     => $request->client_invoice_desc,
      'client_invoice_history_created_by'       => session('user_id'),
      'client_invoice_history_created_date'     => $time->format('Y-m-d H:i:s'),
    ]);
    // End Add Client Invoice History

    return response()->json(['status' => true, 'message' => ['title' => 'Successfully updated!', 'text' => 'Client Invoice ' . $clientInvoice->client_invoice_number . ' updated successfully!']]);
  }

  public function paid(Request $request, $id)
  {
    $clientInvoice = ClientInvoice::where('client_invoice_id', $id)->first();
    $time = Carbon::now();

    // Add Client Invoice Detail
    try {
      DB::beginTransaction();

      ClientInvoiceDetail::where('client_invoice_id', $id)->delete();
      foreach ($request->visit_order as $key => $order) {
        ClientInvoiceDetail::create([
          'client_invoice_id' => $clientInvoice->client_invoice_id,
          'visit_order_id' => $key,
          'client_invoice_detail_created_by'       => session('user_id'),
          'client_invoice_detail_created_date'     => $time->format('Y-m-d H:i:s'),
        ]);
        VisitOrder::where('visit_order_id', $key)->first()->update([
          'client_invoice_id' => $clientInvoice->client_invoice_id,
          'visit_order_status'               => 9,
        ]);
        VisitOrderHistory::create([
          'visit_order_id'                   => $key,
          'visit_order_status'               => 9,
          'visit_order_history_desc'         => "Visit Order has been Paid",
          'visit_order_history_created_by'   => session('user_id'),
          'visit_order_history_created_date' => $time->format('Y-m-d H:i:s'),
        ]);
      }
      DB::commit();
    } catch (\Exception $exp) {
      DB::rollBack();
    }
    // End Add Client Invoice Detail

    $clientInvoice->update([
      'client_invoice_status' => 3,
      'client_invoice_updated_by'       => session('user_id'),
      'client_invoice_updated_date'     => $time->format('Y-m-d H:i:s'),
    ]);

    // Add Client Invoice History
    ClientInvoiceHistory::create([
      'client_invoice_id'               => $clientInvoice->client_invoice_id,
      'client_invoice_history_status'   => 3,
      'client_invoice_history_desc'     => "Client invoice has been Paid",
      'client_invoice_history_created_by'       => session('user_id'),
      'client_invoice_history_created_date'     => $time->format('Y-m-d H:i:s'),
    ]);
    // End Add Client Invoice History

    return response()->json(['status' => true, 'message' => ['title' => 'Successfully updated!', 'text' => 'Client Invoice ' . $clientInvoice->client_invoice_number . ' updated status paid successfully!']]);
  }

  public function show($id)
  {
    Carbon::setLocale('id');
    $cli = ClientInvoice::with('site', 'client_invoice_file', 'visit_order', 'history')->where('client_invoice_id', $id)->first();
    if ($cli) {
      foreach ($cli->client_invoice_file as $file) {
        $file['folder_name'] = Carbon::parse($file->client_invoice_file_created_date)->format('Ymd');
      }

      foreach ($cli->history as $history) {
        $history['date_created_format'] = Carbon::parse($history->client_invoice_history_created_date)->translatedFormat('d F Y, h:m');
      }

      foreach ($cli->visit_order as $key => $order) {
        $order['no'] = $key + 1;
        $order['client_name'] = $order->client->client_name;
        $order['site_name'] = $order->site->site_name;
        $order['partner_name'] = $order->partner->partner_name;
      }
      return response()->json(['status' => true, 'data' => $cli]);
    } else {
      return response()->json(['status' => false, 'data' => []]);
    }
  }

  public function delete(Request $request)
  {
    ClientInvoice::where('client_invoice_id', $request->client_invoice_id)->update([
      'client_invoice_status' => 5,
      'client_invoice_deleted_by'       => session('user_id'),
      'client_invoice_deleted_date'     => Carbon::now()->format('Y-m-d H:i:s'),
    ]);

    return response()->json(['status' => true, 'message' => ['title' => 'Successfully deleted!', 'text' => 'Client Invoice deleted successfully!']]);
  }

  public function uploadFile(Request $request, $id)
  {
    $time = Carbon::now();
    $clientInvoice = ClientInvoice::where('client_invoice_id', $id)->first();
    $validator = Validator::make($request->all(), [
      'client_invoice_file.*' => 'required|file|image|mimes:jpeg,png,jpg|max:1024'
    ]);

    if ($validator->fails()) {
      return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
    }

    // Upload Image
    $timeNow = Carbon::now()->format('Ymd');
    Storage::disk('public')->makeDirectory('client_invoice_uploads/' . $timeNow);

    if ($request->hasFile('client_invoice_file')) {
      foreach ($request->file('client_invoice_file') as $key => $img) {
        //Upload image ke storage
        $filename = Carbon::now()->format('Hisu_') . 'client_invoice' . ($key) . '.' . $img->getClientOriginalExtension();
        $img = Image::make($img)->save('./storage/client_invoice_uploads/' . $timeNow . '/' . $filename);

        $clientInvoiceFile = ClientInvoiceFile::create([
          'client_invoice_id' => $clientInvoice->client_invoice_id,
          'client_invoice_file' => $filename,
          'client_invoice_file_desc' => $request->client_invoice_desc,
          'client_invoice_file_created_by'       => session('user_id'),
          'client_invoice_file_created_date'     => $time->format('Y-m-d H:i:s'),
        ]);
      }
    }
    // End Upload Image

    return response()->json(['status' => true, 'message' => ['title' => 'Successfully updated!', 'text' => 'Client Invoice ' . $clientInvoice->client_invoice_number . ' upload image successfully!']]);
  }

  public function deleteFile($id)
  {
    $clientInvoiceFile = ClientInvoiceFile::where('client_invoice_file_id', $id)->first();

    $path = 'storage/client_invoice_uploads/' . Carbon::parse($clientInvoiceFile->client_invoice_created_date)->format('Ymd') . '/' . $clientInvoiceFile->client_invoice_file;
    if (file_exists($path)) {
      unlink(public_path($path));
    }
    ClientInvoiceFile::where('client_invoice_file_id', $id)->delete();

    return response()->json(['status' => true, 'message' => ['title' => 'Successfully deleted!', 'text' => 'Client Invoice deleted image successfully!']]);
  }

  public function visitOrderDatatable(Request $request)
  {
    $columns = [
      0 => 'visit_order_id',
      1 => 'visit_order_number',
      2 => 'visit_order_date',
      3 => 'visit_order_due_date',
      4 => 'client_id', // client
      5 => 'site_id', // site
      6 => 'visit_order_location',
      7 => 'partner_id', // partner
      9 => 'visit_order_status',
    ];

    $search = [];
    $totalData = VisitOrder::where('visit_order_status', '!=', 99)->count();
    $totalFiltered = $totalData;

    if (!empty($request->input())) {
      $limit = $request->input('length');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');

      if (empty($request->input('search.value'))) {
        $visit_order = VisitOrder::with('client', 'site', 'partner', 'client_invoice')
          ->where('visit_order_status', '!=', 99)
          ->where('site_id', $request->site_id)
          ->where('partner_payment_id', '!=', null)
          ->where('client_invoice_id', null)
          ->where('visit_order_status', 8)
          ->limit($limit)
          ->orderBy($order, $dir)
          ->get();
      } else {
        $search = $request->input('search.value');

        $visit_order = VisitOrder::with('client', 'site', 'partner', 'client_invoice')
          ->where('visit_order_number', 'LIKE', "%{$search}%")
          ->orWhere('visit_order_location', 'LIKE', "%{$search}%")
          ->orWhere('visit_order_date', 'LIKE', "%{$search}%")
          ->orWhereRelation('client', 'client_name', 'LIKE', "%{$search}%")
          ->orWhereRelation('site', 'site_name', 'LIKE', "%{$search}%")
          ->orWhereRelation('partner', 'partner_name', 'LIKE', "%{$search}%")
          ->where('visit_order_status', '!=', 99)
          ->where('site_id', $request->site_id)
          ->where('partner_payment_id', '!=', null)
          ->where('client_invoice_id', null)
          ->where('visit_order_status', 8)
          ->limit($limit)
          ->orderBy($order, $dir)
          ->get();

        $totalFiltered = VisitOrder::where('visit_order_number', 'LIKE', "%{$search}%")
          ->orWhere('visit_order_location', 'LIKE', "%{$search}%")
          ->orWhere('visit_order_date', 'LIKE', "%{$search}%")
          ->orWhereRelation('client', 'client_name', 'LIKE', "%{$search}%")
          ->orWhereRelation('site', 'site_name', 'LIKE', "%{$search}%")
          ->orWhereRelation('partner', 'partner_name', 'LIKE', "%{$search}%")
          ->where('visit_order_status', '!=', 99)
          ->where('site_id', $request->site_id)
          ->where('partner_payment_id', '!=', null)
          ->where('client_invoice_id', null)
          ->where('visit_order_status', 8)
          ->count();
      }
    } else {
      $visit_order = VisitOrder::with('client', 'site', 'partner', 'client_invoice')->get();
    }

    $data = [];

    if (!empty($visit_order)) {
      $no = 0;
      foreach ($visit_order as $order) {
        // Mengecek metode untuk menampilkan data untuk modal add
        if ($request->method == 'add') {
          // Menampilkan data yang tidak punya relasi client_invoice
          if ($order->client_invoice->isEmpty()) {
            $nestedData['no']                   = ++$no;
            $nestedData['visit_order_id']       = $order->visit_order_id;
            $nestedData['visit_order_encrypt_id'] = Crypt::encrypt($order->visit_order_id);
            $nestedData['visit_order_number']   = $order->visit_order_number;
            $nestedData['visit_order_date']     = format_short_local_date($order->visit_order_date);
            $nestedData['visit_order_due_date']     = format_short_local_date($order->visit_order_due_date);
            $nestedData['client_name']          = $order->client->client_name;
            $nestedData['site_name']            = $order->site->site_name;
            $nestedData['visit_order_location'] = $order->visit_order_location;
            $nestedData['partner_name']         = $order->partner ? $order->partner->partner_name : '-';
            $nestedData['download_status']   = ($order->visit_order_status == 5 || $order->visit_order_status == 6) ? (($order->visit_order_downloaded_by && $order->visit_order_downloaded_date) ? "Downloaded" : "Not Downloaded") : "-";
            $nestedData['visit_order_status']   = $order->visit_order_status;
            $nestedData['client_invoice']   = $order->client_invoice;
            $data[] = $nestedData;
          }
        }
        // Mengecek metode untuk menampilkan data untuk modal edit 
        else if ($request->method == 'edit') {
          // Menampilkan data yang tidak punya relasi dan jika punya relasi ambil yang client_invoice_id nya sama
          if ($order->client_invoice->isEmpty()) {
            $nestedData['no']                   = ++$no;
            $nestedData['visit_order_id']       = $order->visit_order_id;
            $nestedData['visit_order_encrypt_id'] = Crypt::encrypt($order->visit_order_id);
            $nestedData['visit_order_number']   = $order->visit_order_number;
            $nestedData['visit_order_date']     = format_short_local_date($order->visit_order_date);
            $nestedData['visit_order_due_date']     = format_short_local_date($order->visit_order_due_date);
            $nestedData['client_name']          = $order->client->client_name;
            $nestedData['site_name']            = $order->site->site_name;
            $nestedData['visit_order_location'] = $order->visit_order_location;
            $nestedData['partner_name']         = $order->partner ? $order->partner->partner_name : '-';
            $nestedData['download_status']   = ($order->visit_order_status == 5 || $order->visit_order_status == 6) ? (($order->visit_order_downloaded_by && $order->visit_order_downloaded_date) ? "Downloaded" : "Not Downloaded") : "-";
            $nestedData['visit_order_status']   = $order->visit_order_status;
            $nestedData['client_invoice']   = $order->client_invoice;
            $data[] = $nestedData;
          } else {
            if ($order->client_invoice[0]->client_invoice_id == $request->client_invoice_id) {
              $nestedData['no']                   = ++$no;
              $nestedData['visit_order_id']       = $order->visit_order_id;
              $nestedData['visit_order_encrypt_id'] = Crypt::encrypt($order->visit_order_id);
              $nestedData['visit_order_number']   = $order->visit_order_number;
              $nestedData['visit_order_date']     = format_short_local_date($order->visit_order_date);
              $nestedData['visit_order_due_date']     = format_short_local_date($order->visit_order_due_date);
              $nestedData['client_name']          = $order->client->client_name;
              $nestedData['site_name']            = $order->site->site_name;
              $nestedData['visit_order_location'] = $order->visit_order_location;
              $nestedData['partner_name']         = $order->partner ? $order->partner->partner_name : '-';
              $nestedData['download_status']   = ($order->visit_order_status == 5 || $order->visit_order_status == 6) ? (($order->visit_order_downloaded_by && $order->visit_order_downloaded_date) ? "Downloaded" : "Not Downloaded") : "-";
              $nestedData['visit_order_status']   = $order->visit_order_status;
              $nestedData['client_invoice']   = $order->client_invoice;
              $data[] = $nestedData;
            }
          }
        } else {
          if ($order->client_invoice->isNotEmpty() && $order->client_invoice[0]->client_invoice_id == $request->client_invoice_id) {
            $nestedData['no']                   = ++$no;
            $nestedData['visit_order_id']       = $order->visit_order_id;
            $nestedData['visit_order_encrypt_id'] = Crypt::encrypt($order->visit_order_id);
            $nestedData['visit_order_number']   = $order->visit_order_number;
            $nestedData['visit_order_date']     = format_short_local_date($order->visit_order_date);
            $nestedData['visit_order_due_date']     = format_short_local_date($order->visit_order_due_date);
            $nestedData['client_name']          = $order->client->client_name;
            $nestedData['site_name']            = $order->site->site_name;
            $nestedData['visit_order_location'] = $order->visit_order_location;
            $nestedData['partner_name']         = $order->partner ? $order->partner->partner_name : '-';
            $nestedData['download_status']   = ($order->visit_order_status == 5 || $order->visit_order_status == 6) ? (($order->visit_order_downloaded_by && $order->visit_order_downloaded_date) ? "Downloaded" : "Not Downloaded") : "-";
            $nestedData['visit_order_status']   = $order->visit_order_status;
            $nestedData['client_invoice']   = $order->client_invoice;
            $data[] = $nestedData;
          }
        }
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

  public function clientInvoiceFileDatatable(Request $request)
  {
    $columns = [
      0 => 'client_invoice_file_id',
      1 => 'client_invoice_file_desc',
    ];

    $search = [];
    $totalData = ClientInvoiceFile::where('client_invoice_id', $request->client_invoice_id)->count();
    $totalFiltered = $totalData;

    if (!empty($request->input())) {
      $limit = $request->input('length');
      $start = $request->input('start');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');

      if (empty($request->input('search.value'))) {
        $client_invoice = ClientInvoiceFile::with('client_invoice')
          ->where('client_invoice_id', $request->client_invoice_id)
          ->offset($start)
          ->limit($limit)
          ->orderBy($order, $dir)
          ->get();
      } else {
        $search = $request->input('search.value');

        $client_invoice = ClientInvoiceFile::with('client_invoice')
          ->where('client_invoice_id', $request->client_invoice_id)
          ->offset($start)
          ->limit($limit)
          ->orderBy($order, $dir)
          ->get();

        $totalFiltered = ClientInvoiceFile::where('client_invoice_id', $request->client_invoice_id)
          ->count();
      }
    } else {
      $client_invoice = ClientInvoiceFile::with('client_invoice')->get();
    }

    $data = [];

    if (!empty($client_invoice)) {
      $no = $start;
      foreach ($client_invoice as $order) {
        $path = 'storage/client_invoice_uploads/' . Carbon::parse($order->client_invoice_file_created_date)->format('Ymd') . '/' . $order->client_invoice_file;
        $nestedData['no']                   = ++$no;
        $nestedData['client_invoice_name']       = $order->client_invoice->client_invoice_name;
        $nestedData['client_invoice_file_id']       = $order->client_invoice_file_id;
        $nestedData['client_invoice_file_desc']       = $order->client_invoice_file_desc;
        $nestedData['check']       = file_exists('/assets/img/no-image-asset.jpg');
        $nestedData['checkc']       = $path;
        if (file_exists($path)) {
          $nestedData['client_invoice_file_url']       = '/'.$path;
        } else {
          $nestedData['client_invoice_file_url']       = '/assets/img/no-image-asset.jpg';
        }
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
}
