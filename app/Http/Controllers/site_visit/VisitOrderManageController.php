<?php

namespace App\Http\Controllers\site_visit;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Checklist;
use App\Models\ChecklistAnswer;
use App\Models\ChecklistGroup;
use App\Models\VisitOrder;
use App\Models\VisitOrderHistory;
use App\Models\VisitOrderVisual;
use App\Mail\VisitOrderRevisitMail;
use App\Models\Debtor;
use App\Models\SiteContact;
use Carbon\Carbon;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class VisitOrderManageController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    if (session('role_id') == 1 || session('role_id') == 2) {
      return view('content.site-visit.visit-order-manage');
    } else {
      return view('content.pages.pages-misc-not-authorized');
    }
  }

  /**
   * save data to sys_visit_order_history
   * 
   */
  private function visit_order_history($visitOrderId, $visitOrderStatus, $historyDesc, $createBy)
  {
    $visit_order_history = VisitOrderHistory::create([
      'visit_order_id'                   => $visitOrderId,
      'visit_order_status'               => $visitOrderStatus,
      'visit_order_history_desc'         => $historyDesc,
      'visit_order_history_created_by'   => $createBy,
      'visit_order_history_created_date' => Carbon::now()->format('Y-m-d H:i:s'),
    ]);

    $visit_order_history->save();
  }

  public function datatable(Request $request)
  {
    $columns = [
      0 => 'visit_order_id',
      1 => 'visit_order_number',
      2 => 'visit_order_date',
      3 => 'visit_order_due_date',
      4 => 'debtor_id',
      5 => 'client_id', // client
      6 => 'site_id', // site
      7 => 'visit_order_location',
      8 => 'partner_id', // partner
      9 => 'visit_order_custom_number', // partner
      11 => 'visit_order_status',
    ];

    $search = [];
    $totalData = VisitOrder::where('visit_order_status', '!=', 99)->count();
    $totalFiltered = $totalData;

    if (!empty($request->input())) {
      $limit = $request->input('length');
      $start = $request->input('start');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');

      if (empty($request->input('search.value'))) {
        $visit_order = VisitOrder::with('client', 'site', 'partner', 'debtor')
          ->where('visit_order_status', '!=', 99)
          ->offset($start)
          ->limit($limit)
          ->orderBy($order, $dir)
          ->get();
      } else {
        $search = $request->input('search.value');

        $visit_order = VisitOrder::with('client', 'site', 'partner', 'debtor')
          ->where('visit_order_number', 'LIKE', "%{$search}%")
          ->orWhere('visit_order_location', 'LIKE', "%{$search}%")
          ->orWhere('visit_order_date', 'LIKE', "%{$search}%")
          ->orWhereRelation('client', 'client_name', 'LIKE', "%{$search}%")
          ->orWhereRelation('site', 'site_name', 'LIKE', "%{$search}%")
          ->orWhereRelation('partner', 'partner_name', 'LIKE', "%{$search}%")
          ->orWhereRelation('debtor', 'debtor_name', 'LIKE', "%{$search}%")
          ->where('visit_order_status', '!=', 99)
          ->offset($start)
          ->limit($limit)
          ->orderBy($order, $dir)
          ->get();

        $totalFiltered = VisitOrder::where('visit_order_number', 'LIKE', "%{$search}%")
          ->orWhere('visit_order_location', 'LIKE', "%{$search}%")
          ->orWhere('visit_order_date', 'LIKE', "%{$search}%")
          ->orWhereRelation('client', 'client_name', 'LIKE', "%{$search}%")
          ->orWhereRelation('site', 'site_name', 'LIKE', "%{$search}%")
          ->orWhereRelation('partner', 'partner_name', 'LIKE', "%{$search}%")
          ->orWhereRelation('debtor', 'debtor_name', 'LIKE', "%{$search}%")
          ->where('visit_order_status', '!=', 99)
          ->count();
      }
    } else {
      $start = 0;
      $visit_order = VisitOrder::with('client', 'site', 'partner', 'debtor')->get();
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
        $nestedData['partner_name']         = $order->partner ? $order->partner->partner_name : '-';
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

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Models\Debtor  $debtor
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $validator = Validator::make($request->all(), [
      'client_id'                 => 'required',
      'site_id'                   => 'required',
      'debtor_id'                 => 'required',
      'visit_type_id'             => 'required',
      'partner_id'                => 'required',
      'visit_order_number'        => 'required',
      'visit_order_location'      => 'required|max:255',
      'visit_order_date'          => 'required',
      'visit_order_due_date'      => 'required',
    ]);

    // dd($request->all());
    // dd($validator->errors());

    if ($validator->fails()) {
      return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
    }

    $visitOrder = VisitOrder::where('visit_order_id', $id)->first();
    $visitOrder->client_id                  = $request->client_id;
    $visitOrder->site_id                    = $request->site_id;
    $visitOrder->debtor_id                  = $request->debtor_id;
    $visitOrder->visit_type_id              = $request->visit_type_id;
    $visitOrder->partner_id                 = $request->partner_id;
    $visitOrder->visit_order_number         = $request->visit_order_number;
    $visitOrder->visit_order_custom_number         = $request->visit_order_custom_number;
    $visitOrder->visit_order_location       = $request->visit_order_location;
    $visitOrder->visit_order_location_map   = $request->visit_order_location_map;
    $visitOrder->visit_order_latitude       = $request->visit_order_latitude;
    $visitOrder->visit_order_longitude      = $request->visit_order_longitude;
    $visitOrder->visit_order_date           = $request->visit_order_date;
    $visitOrder->visit_order_due_date       = $request->visit_order_due_date;
    $visitOrder->visit_order_note           = $request->visit_order_note;
    $visitOrder->visit_order_updated_by     = session('user_id');
    $visitOrder->visit_order_updated_date   = Carbon::now()->format('Y-m-d H:i:s');
    $visitOrder->save();

    // Menambahkan log history ketika update visit order
    $visitOrderId     = $visitOrder->visit_order_id;
    $visitOrderStatus = $visitOrder->visit_order_status;
    $historyDesc      = 'Visit Order ' . $request->visit_order_number . ' status has been updated!';
    $createdBy        = session('user_id');

    $this->visit_order_history($visitOrderId, $visitOrderStatus, $historyDesc, $createdBy);

    return response()->json(['status' => true, 'message' => ['title' => 'Successfully Updated!', 'text' => 'Visit Order ' . $request->visit_order_number . ' updated successfully!']]);
  }

  // simpan data ke debtor lewat select2
  public function insert_data_to_debtor(Request $request)
  {
    // Cek apakah debtor_name sudah ada di tabel sys_debtor
    $debtor = Debtor::where('debtor_name', $request->debtor_name)->first();

    // Jika belum ada, simpan data baru ke tabel sys_debtor
    if (!$debtor) {
      $debtor = new Debtor();
      $debtor->debtor_status       = 2; // activated
      $debtor->debtor_name         = $request->debtor_name;
      $debtor->debtor_desc         = 'Generated from visit order - create.';
      $debtor->debtor_created_by   = session('user_id');
      $debtor->debtor_created_date = Carbon::now()->format('Y-m-d H:i:s');
      // Add other debtor data as needed
      $debtor->save();
    } else {
      return response()->json(['status' => false, 'message' => ['title' => 'Debtor Already exist!', 'text' => 'Debtor ' . $request->debtor_name . ' already exist!']]);
    }

    // Return the response with the debtor's ID
    return response()->json(['status' => true, 'debtor_id' => $debtor->debtor_id, 'message' => ['title' => 'Successfully saved!', 'text' => 'Debtor ' . $request->debtor_name . ' saved successfully!']]);
  }

  // simpan data ke site contact lewat autocomplete
  public function insert_data_to_site_contact(Request $request)
  {
    $site_contact = SiteContact::where('site_contact_fullname', $request->site_contact_fullname)->first();

    if (!$site_contact) {
      $site_contact = new SiteContact();
      $site_contact->site_contact_status       = 2; // activated
      $site_contact->site_id                   = $request->site_id;
      $site_contact->site_contact_fullname     = $request->site_contact_fullname;
      $site_contact->site_contact_created_by   = session('user_id');
      $site_contact->site_contact_created_date = Carbon::now()->format('Y-m-d H:i:s');
      $site_contact->save();
    } else {
      return response()->json(['status' => false, 'message' => ['title' => 'Site Contact Already exist!', 'text' => 'Site Contact ' . $request->site_contact_fullname . ' already exist!']]);
    }

    return response()->json(['status' => true, 'site_contact_id' => $site_contact->site_contact_id, 'message' => ['title' => 'Successfully saved!', 'text' => 'Site Contact ' . $request->site_contact_fullname . ' saved successfully!']]);
  }

  public function uploadFile(Request $request, $id)
  {
    $time = Carbon::now();
    $visit = VisitOrder::with('visit_type.visit_visual_type.checklist_visual')->where('visit_order_id', $id)->first();

    //Array untuk validasi input
    $arrValidator = [];

    foreach ($visit->visit_type->visit_visual_type as $each) {
      $arrValidator['image-' . $each->visit_visual_type_id] = 'required|file|image|mimes:jpeg,png,jpg|max:1024';
    }

    $validator = Validator::make($request->all(), $arrValidator);

    if ($validator->fails()) {
      foreach ($validator->errors()->all() as $key => $value) {
        return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => $value]]);
      }
    }

    $visualFile = VisitOrderVisual::where('visit_order_id', $visit->visit_order_id)->get();
    if ($visualFile) {
      foreach ($visualFile as $key => $file) {
        $path = 'storage/visit_order_visual_uploads/' . Carbon::parse($file->visit_order_visual_created_date)->format('Ymd') . '/' . $file->visit_order_visual_file;
        if (file_exists($path)) {
          unlink(public_path($path));
        }
      }
    }
    VisitOrderVisual::where('visit_order_id', $visit->visit_order_id)->delete();

    $timeNow = Carbon::now()->format('Ymd');
    Storage::disk('public')->makeDirectory('visit_order_visual_uploads/' . $timeNow);

    foreach ($visit->visit_type->visit_visual_type as $each) {
      if ($request->hasFile('image-' . $each->visit_visual_type_id)) {
        //Upload image ke storage
        $image = $request->file('image-' . $each->visit_visual_type_id);
        $filename = Carbon::now()->format('Hisu_') . 'visit_order_visual' . ($each->visit_visual_type_id) . '.' . $image->getClientOriginalExtension();
        // $image->storeAs('public/visit_order_visual_uploads/'.$time->format('Ymd'), $filename);
        $img = Image::make($image)->save('./storage/visit_order_visual_uploads/' . $timeNow . '/' . $filename);

        $visitOrderVisual = VisitOrderVisual::create([
          'visit_order_id' => $visit->visit_order_id,
          'visit_visual_type_id' => $each->visit_visual_type_id,
          'visit_order_visual_file' => $filename,
          'visit_order_visual_file_name' => 'Gambar ' . $each->checklist_visual->visit_visual_type_name,
          'visit_order_visual_file_created_by'       => session('user_id'),
          'visit_order_visual_file_created_date'     => $time->format('Y-m-d H:i:s'),
        ]);

        $visitOrderVisual->save();
      }
    }

    return response()->json(['status' => true, 'message' => ['title' => 'Successfully Updated!', 'text' => 'Visit Order ' . $visit->visit_order_number . ' updated successfully!']]);
  }

  public function getChecklists($id)
  {
    $checklistGroups = ChecklistGroup::all();
    $checklists = Checklist::all();

    $checklistAnswers = ChecklistAnswer::where('visit_order_id', $id)->get();

    return response()->json(['status' => 'success', 'checklist_groups' => $checklistGroups, 'checklists' => $checklists, 'checklist_answers' => $checklistAnswers]);
  }

  public function saveChecklist(Request $request)
  {
    // Ambil data dari permintaan
    $visitOrderId = $request->input('visitOrderId');
    $checklists = $request->all()['checklists']; // Ambil nilai checklists dari request

    $checklistGroups = ChecklistGroup::all();

    foreach ($checklistGroups as $groups) {
      $isExist = false;
      foreach ($checklists as $checklist) {
        $check = Checklist::with('checklist_group')->find($checklist['checklistId']);
        if ($check->checklist_group_id == $groups->checklist_group_id) {
          $isExist = true;
        }
      }
      if (!$isExist) {
        return response()->json(['status' => false, 'message' => ['title' => 'Failed Add Checklist!', 'text' => 'Mohon lengkapi checklist terlebih dahulu!']]);
      }
    }

    // Hapus checklist di grup visit order yang sama
    $deleteVisitOrder = ChecklistAnswer::where('visit_order_id', $visitOrderId)->delete();

    // loop melalui daftar checklists yg dipilih
    foreach ($checklists as $checklist) {
      $checklistId = $checklist['checklistId'];
      $checklistText = $checklist['checklistText'];

      // Cek apakah checklist sudah ada di tabel sys_checklist_answer
      $existingAnswer = ChecklistAnswer::where('visit_order_id', $visitOrderId)
        ->where('checklist_id', $checklistId)
        ->first();

      if ($existingAnswer) {
        // update data checklist jika sudah ada
        $existingAnswer->checklist_answer              = $checklistText;
        $existingAnswer->checklist_answer_updated_by   = session('user_id');
        $existingAnswer->checklist_answer_updated_date = Carbon::now()->format('Y-m-d H:i:s');
        $existingAnswer->save();
      } else {
        // Simpan data checklist baru jika belum ada
        $checklist = Checklist::find($checklistId);
        $newAnswer = new ChecklistAnswer();
        $newAnswer->visit_order_id = $visitOrderId;
        $newAnswer->checklist_group_id = $checklist->checklist_group_id;
        $newAnswer->checklist_id = $checklistId;
        $newAnswer->checklist_answer = $checklistText;
        $newAnswer->checklist_answer_created_by = session('user_id');
        $newAnswer->checklist_answer_created_date = Carbon::now()->format('Y-m-d H:i:s');
        $newAnswer->save();
      }
    }
    return response()->json(['status' => true, 'message' => ['title' => 'Successfully created!', 'text' => 'Checklist saved or updated successfully!']]);
  }

  //SET VISITED VISIT ORDER
  public function setVisited(Request $request)
  {
    $visit = VisitOrder::with('visit_type.visit_visual_type')->where('visit_order_id', $request->visit_order_id)->first();

    //Array untuk validasi input
    $arrValidator = [
      'partner_id'     => 'required',
      'visit_order_date'     => 'required',
    ];

    foreach ($visit->visit_type->visit_visual_type as $each) {
      $arrValidator['image-' . $each->visit_visual_type_id] = 'required';
    }
    $validator = Validator::make($request->all(), $arrValidator);

    if ($validator->fails()) {
      return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
    }

    $checklists = json_decode($request->checklists, true);

    // Mengecek apakah checklist ada
    if (!$checklists) {
      return response()->json(['status' => false, 'message' => ['title' => 'Failed Add Checklist!', 'text' => 'Mohon isi checklist terlebih dahulu!']]);
    }

    try {
      DB::beginTransaction();

      // Hapus checklist di grup visit order yang sama
      $deleteVisitOrder = ChecklistAnswer::where('visit_order_id', $request->visit_order_id)->delete();

      // loop melalui daftar checklists yg dipilih
      foreach ($checklists as $checklist) {
        $checklistId = $checklist['checklistId'];
        $checklistText = $checklist['checklistText'];

        // Cek apakah checklist sudah ada di tabel sys_checklist_answer
        $existingAnswer = ChecklistAnswer::where('visit_order_id', $request->visit_order_id)
          ->where('checklist_id', $checklistId)
          ->first();

        if ($existingAnswer) {
          // update data checklist jika sudah ada
          $existingAnswer->checklist_answer              = $checklistText;
          $existingAnswer->checklist_answer_updated_by   = session('user_id');
          $existingAnswer->checklist_answer_updated_date = Carbon::now()->format('Y-m-d H:i:s');
          $existingAnswer->save();
        } else {
          // Simpan data checklist baru jika belum ada
          $checklist = Checklist::find($checklistId);
          $newAnswer = new ChecklistAnswer();
          $newAnswer->visit_order_id = $request->visit_order_id;
          $newAnswer->checklist_group_id = $checklist->checklist_group_id;
          $newAnswer->checklist_id = $checklistId;
          $newAnswer->checklist_answer = $checklistText;
          $newAnswer->checklist_answer_created_by = session('user_id');
          $newAnswer->checklist_answer_created_date = Carbon::now()->format('Y-m-d H:i:s');
          $newAnswer->save();
        }
      }

      $visualFile = VisitOrderVisual::where('visit_order_id', $visit->visit_order_id)->get();
      if ($visualFile) {
        foreach ($visualFile as $key => $file) {
          $path = 'storage/visit_order_visual_uploads/' . Carbon::parse($file->visit_order_visual_created_date)->format('Ymd') . '/' . $file->visit_order_visual_file;
          if (file_exists($path)) {
            unlink(public_path($path));
          }
        }
      }
      VisitOrderVisual::where('visit_order_id', $visit->visit_order_id)->delete();

      $timeNow = Carbon::now()->format('Ymd');
      Storage::disk('public')->makeDirectory('visit_order_visual_uploads/' . $timeNow);

      foreach ($visit->visit_type->visit_visual_type as $each) {
        if ($request->hasFile('image-' . $each->visit_visual_type_id)) {
          //Upload image ke storage
          $image = $request->file('image-' . $each->visit_visual_type_id);
          $filename = Carbon::now()->format('Hisu_') . 'visit_order_visual' . ($each->visit_visual_type_id) . '.' . $image->getClientOriginalExtension();
          // $image->storeAs('visit_order_visual_uploads/'.Carbon::now()->format('Ymd'), $filename);
          Image::make($image)->fit(300, 300)->save('./storage/visit_order_visual_uploads/' . $timeNow . '/' . $filename);

          $visitOrderVisual = VisitOrderVisual::create([
            'visit_order_id' => $visit->visit_order_id,
            'visit_visual_type_id' => $each->visit_visual_type_id,
            'visit_order_visual_file' => $filename,
            'visit_order_visual_file_name' => $each->checklist_visual->visit_visual_type_name,
            'visit_order_visual_file_created_by'       => session('user_id'),
            'visit_order_visual_file_created_date'     => Carbon::now()->format('Y-m-d H:i:s'),
          ]);

          $visitOrderVisual->save();
        }
      }

      $visit->partner_id    = $request->partner_id;
      $visit->visit_order_status    = 5;
      $visit->visit_order_note    = $request->visit_order_note;
      $visit->visit_order_visited_date   = Carbon::now()->format('Y-m-d H:i:s');
      $visit->visit_order_updated_by    = session('user_id');
      $visit->visit_order_updated_date    = Carbon::now()->format('Y-m-d H:i:s');
      $visit->save();

      // Menambahkan log history ketika di set visited = 5
      $visitOrderId = $request->visit_order_id;
      $historyDesc  = $request->visit_order_note;
      $createdBy    = session('user_id');

      $this->visit_order_history($visitOrderId, 5, $historyDesc, $createdBy);

      DB::commit();
      return response()->json(['status' => true, 'message' => ['title' => 'Visit Order Updated!', 'text' => 'Visit order has been Visited']]);
    } catch (\Throwable $th) {
      DB::rollBack();
      return response()->json(['status' => false, 'message' => ['title' => 'Visit Order Fail Updated!', 'text' => 'Visit order has not been Visited']]);
    }
  }

  //SET DOWNLOAD VISIT ORDER
  public function setDownload(Request $request)
  {
    $this->validate($request, [
      'visit_order_id'     => 'required',
    ]);

    $visit_order = VisitOrder::where('visit_order_id', $request->visit_order_id)->first();
    $visit_order->visit_order_downloaded_by    = session('user_id');
    $visit_order->visit_order_downloaded_date    = Carbon::now()->format('Y-m-d H:i:s');
    $visit_order->save();

    return response()->json(['status' => true, 'message' => ['title' => 'Visit Order Updated!', 'text' => 'Your Account Was Set As Downloader']]);
  }

  //STATUS ORDER VISIT CHANGE TO ASSIGN
  public function assign(Request $request)
  {
    $this->validate($request, [
      'visit_order_id'     => 'required',
      'visit_order_status' => 'required',
      'edit_desc' => 'required',
      'partner_id' => 'required',
    ]);

    $visit_order = VisitOrder::where('visit_order_id', $request->visit_order_id)->first();
    $visit_order->visit_order_note = $request->edit_desc;
    $visit_order->partner_id = ($request->partner_id) ? $request->partner_id : $visit_order->partner_id;
    $visit_order->visit_order_status        = $request->visit_order_status;
    $visit_order->visit_order_updated_by    = session('user_id');
    $visit_order->visit_order_updated_date    = Carbon::now()->format('Y-m-d H:i:s');
    $visit_order->save();

    // Menambahkan log history ketika di assign = 2
    $visitOrderId = $visit_order->visit_order_id;
    $historyDesc  = $request->edit_desc;
    $createdBy    = session('user_id');

    $this->visit_order_history($visitOrderId, 2, $historyDesc, $createdBy);

    //Send Email
    $visit_order = VisitOrder::where('visit_order_id', $request->visit_order_id)->first();
    Mail::to($visit_order->partner->partner_email)->send(new VisitOrderRevisitMail("DPI Site Visit Order - " . $visit_order->visit_order_number, $visit_order));

    if ($request->visit_order_status == 2) {
      return response()->json(['status' => true, 'message' => ['title' => 'Visit Order Assigned!', 'text' => 'Visit Order ' . $visit_order->visit_order_number . ' status has been assigned!']]);
    } else {
      return response()->json(['status' => false, 'message' => ['title' => 'Visit Order Not Assigned!', 'text' => 'Visit Order ' . $visit_order->visit_order_number . ' status has not been assigned!']]);
    }
  }

  //STATUS ORDER VISIT CHANGE TO CANCEL
  public function cancel(Request $request)
  {
    $this->validate($request, [
      'visit_order_id'     => 'required',
      'visit_order_status' => 'required',
      'edit_desc' => 'required',
    ]);

    $visit_order = VisitOrder::where('visit_order_id', $request->visit_order_id)->first();
    $visit_order->visit_order_note = $request->edit_desc;
    $visit_order->visit_order_status        = $request->visit_order_status;
    $visit_order->visit_order_visited_date    = null;
    $visit_order->visit_order_updated_by    = session('user_id');
    $visit_order->visit_order_updated_date    = Carbon::now()->format('Y-m-d H:i:s');
    $visit_order->save();

    // Menambahkan log history ketika di cancelled = 3
    $visitOrderId = $visit_order->visit_order_id;
    $historyDesc  = $request->edit_desc;
    $createdBy    = session('user_id');

    $this->visit_order_history($visitOrderId, 3, $historyDesc, $createdBy);

    if ($request->visit_order_status == 3) {
      return response()->json(['status' => true, 'message' => ['title' => 'Visit Order Canceled!', 'text' => 'Visit Order ' . $visit_order->visit_order_number . ' status has been cancelled!']]);
    } else {
      return response()->json(['status' => false, 'message' => ['title' => 'Visit Order Not Canceled!', 'text' => 'Visit Order ' . $visit_order->visit_order_number . ' status has not been cancelled!']]);
    }
  }

  //STATUS ORDER VISIT CHANGE TO Re Visit
  public function reVisit(Request $request)
  {
    $this->validate($request, [
      'visit_order_id'     => 'required',
      'visit_order_status' => 'required',
      'edit_desc' => 'required',
      'partner_id' => 'required',
    ]);

    $visit_order = VisitOrder::where('visit_order_id', $request->visit_order_id)->first();
    $visit_order->partner_id = ($request->partner_id) ? $request->partner_id : $visit_order->partner_id;
    $visit_order->visit_order_note = $request->edit_desc;
    $visit_order->visit_order_status        = 4;
    $visit_order->visit_order_updated_by    = session('user_id');
    $visit_order->visit_order_updated_date    = Carbon::now()->format('Y-m-d H:i:s');
    $visit_order->save();

    // Menambahkan log history ketika di revisit = 4
    $visitOrderId = $visit_order->visit_order_id;
    $historyDesc  = $request->edit_desc;
    $createdBy    = session('user_id');

    $this->visit_order_history($visitOrderId, 4, $historyDesc, $createdBy);

    //Send Email
    $visit_order = VisitOrder::where('visit_order_id', $request->visit_order_id)->first();
    Mail::to($visit_order->partner->partner_email)->send(new VisitOrderRevisitMail("DPI Site Visit Order - " . $visit_order->visit_order_number, $visit_order));

    if ($request->visit_order_status == 4) {
      return response()->json(['status' => true, 'message' => ['title' => 'Visit Order Re Visited!', 'text' => 'Visit Order ' . $visit_order->visit_order_number . ' status has been re visited!']]);
    } else {
      return response()->json(['status' => false, 'message' => ['title' => 'Visit Order Not Re Visited!', 'text' => 'Visit Order ' . $visit_order->visit_order_number . ' status has not been re visited!']]);
    }
  }

  //STATUS ORDER VISIT CHANGE TO DELETE
  public function delete(Request $request)
  {
    $this->validate($request, [
      'visit_order_id'     => 'required',
      'visit_order_status' => 'required',
    ]);

    $visit_order = VisitOrder::where('visit_order_id', $request->visit_order_id)->first();
    $visit_order->visit_order_status        = $request->visit_order_status;
    $visit_order->visit_order_deleted_by    = session('user_id');
    $visit_order->visit_order_deleted_date    = Carbon::now()->format('Y-m-d H:i:s');
    $visit_order->save();

    // Menambahkan log history ketika di delete = 99
    $visitOrderId = $visit_order->visit_order_id;
    $historyDesc  = 'Visit Order ' . $visit_order->visit_order_number . ' status has been deleted!';
    $createdBy    = session('user_id');

    $this->visit_order_history($visitOrderId, 99, $historyDesc, $createdBy);

    if ($request->visit_order_status == 99) {
      return response()->json(['status' => true, 'message' => ['title' => 'Visit Order Deleted!', 'text' => 'Visit Order ' . $visit_order->visit_order_number . ' status has been deleted!']]);
    } else {
      return response()->json(['status' => false, 'message' => ['title' => 'Visit Order Not Deleted!', 'text' => 'Visit Order ' . $visit_order->visit_order_number . ' status has not been deleted!']]);
    }
  }

  //STATUS ORDER VISIT CHANGE TO VALIDATE
  public function validating(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'visit_order_id'     => 'required',
      'visit_order_status' => 'required',
      'edit_desc' => 'required',
    ]);

    if ($validator->fails()) {
      return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
    }

    $visit_order = VisitOrder::where('visit_order_id', $request->visit_order_id)->first();
    $visit_order->visit_order_note = $request->edit_desc;
    $visit_order->visit_order_status        = $request->visit_order_status;
    $visit_order->visit_order_updated_by    = session('user_id');
    $visit_order->visit_order_updated_date    = Carbon::now()->format('Y-m-d H:i:s');
    $visit_order->save();

    // Menambahkan log history ketika di validated = 6
    $visitOrderId = $visit_order->visit_order_id;
    $historyDesc  = $request->edit_desc;
    $createdBy    = session('user_id');

    $this->visit_order_history($visitOrderId, 6, $historyDesc, $createdBy);

    if ($request->visit_order_status == 6) {
      return response()->json(['status' => true, 'message' => ['title' => 'Visit Order Validated!', 'text' => 'Visit Order ' . $visit_order->visit_order_number . ' status has been validated!']]);
    } else {
      return response()->json(['status' => false, 'message' => ['title' => 'Visit Order Not Validated!', 'text' => 'Visit Order ' . $visit_order->visit_order_number . ' status has not been validated!']]);
    }
  }

  //STATUS ORDER VISIT CHANGE TO VALIDATE
  public function cantBilled(Request $request)
  {
    $this->validate($request, [
      'visit_order_id'     => 'required',
      'visit_order_status' => 'required',
      'edit_desc' => 'required',
    ]);

    $visit_order = VisitOrder::where('visit_order_id', $request->visit_order_id)->first();
    $visit_order->visit_order_note = $request->edit_desc;
    $visit_order->visit_order_status        = $request->visit_order_status;
    $visit_order->visit_order_updated_by    = session('user_id');
    $visit_order->visit_order_updated_date    = Carbon::now()->format('Y-m-d H:i:s');
    $visit_order->save();

    // Menambahkan log history ketika di validated = 6
    $visitOrderId = $visit_order->visit_order_id;
    $historyDesc  = $request->edit_desc;
    $createdBy    = session('user_id');

    $this->visit_order_history($visitOrderId, $request->visit_order_status, $historyDesc, $createdBy);

    if ($request->visit_order_status == 7) {
      return response()->json(['status' => true, 'message' => ['title' => 'Visit Order Cant Billed!', 'text' => 'Visit Order ' . $visit_order->visit_order_number . ' can\'t billed!']]);
    } else {
      return response()->json(['status' => false, 'message' => ['title' => 'Visit Order Can Billed!', 'text' => 'Visit Order ' . $visit_order->visit_order_number . ' can billed!']]);
    }
  }

  // Delete visual file
  public function visualDelete(Request $request)
  {
    $visualFile = VisitOrderVisual::where('visit_order_visual_id', Crypt::decrypt($request->visit_order_visual_id))->first();

    if ($visualFile) {
      $path = 'storage/visit_order_visual_uploads/' . Carbon::parse($visualFile->visit_order_visual_created_date)->format('Ymd') . '/' . $visualFile->visit_order_visual_file;
      if (file_exists($path)) {
        unlink(public_path($path));
      }

      $visualFile->delete();
      return response()->json(['status' => true, 'message' => ['title' => 'Visual File Deleted!', 'text' => 'Visual File ' . $visualFile->visit_order_visual . ' has been deleted!']]);
    } else {
      return response()->json(['status' => false, 'message' => ['title' => 'Visual File not Deleted!', 'text' => 'Visual File ' . $visualFile->visit_order_visual . ' not deleted!']]);
    }
  }
}
