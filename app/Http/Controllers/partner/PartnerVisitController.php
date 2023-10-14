<?php

namespace App\Http\Controllers\partner;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\VisitOrder;
use App\Models\VisitVisualType;
use App\Models\VisitOrderVisual;
use App\Models\Checklist;
use App\Models\ChecklistAnswer;
use App\Models\ChecklistGroup;
use App\Models\VisitOrderHistory;
use App\Mail\ReportVisitOrderEmail;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PartnerVisitController extends Controller
{

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

  public function index()
  {
    if (session('role_id') == 3) {
      return view('content.partner.partner-visit');
    } else {
      return view('content.pages.pages-misc-not-authorized');
    }
  }

  public function getVisitOrderList(Request $request)
  {
    $itemsPerPage = 12; // Number of items to display per page
    $search = $request->search;

    $visitOrder = VisitOrder::with('client', 'site', 'partner', 'province', 'regency', 'debtor')
      ->where(function ($query) use ($search) {
        $query->where('visit_order_number', 'LIKE', "%{$search}%")
        ->orWhere('visit_order_location', 'LIKE', "%{$search}%")
        ->orWhere('visit_order_date', 'LIKE', "%{$search}%")
        ->orWhereRelation('client', 'client_name', 'LIKE', "%{$search}%")
        ->orWhereRelation('site', 'site_name', 'LIKE', "%{$search}%")
        ->orWhereRelation('debtor', 'debtor_name', 'LIKE', "%{$search}%");
      })
      ->where(function ($query) {
        $query->where('visit_order_status', 2)
          ->orWhere('visit_order_status', 4)
          ->orWhere('visit_order_status', 5);
      })
      ->whereRelation('partner', 'partner_email', '=', session('user_email'))
      ->paginate($itemsPerPage);

    return response()->json(['status' => true, 'data' => $visitOrder]);
  }

  public function getVisual($id)
  {
    $visitOrderVisual = VisitOrderVisual::where('visit_order_id', $id)->get();
    foreach ($visitOrderVisual as $order) {
      $order['folder_name'] = Carbon::parse($order->visit_order_visual_file_created_date)->format('Ymd');
    }

    return response()->json(['status' => true, 'data' => $visitOrderVisual]);
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
    $visit = VisitOrder::with('client', 'site', 'site_contact', 'debtor', 'history', 'visit_type.visit_visual_type.checklist_visual', 'visit_order_visual')->where('visit_order_id', $id)->first();
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

  public function updateVisual(Request $request, $id)
  {
    $visit = VisitOrder::with('visit_type.visit_visual_type')->where('visit_order_id', $id)->first();

    //Array untuk validasi input
    $arrValidator = [];

    foreach ($visit->visit_type->visit_visual_type as $each) {
      $arrValidator['image-' . $each->visit_visual_type_id] = 'required';
    }

    $validator = Validator::make($request->all(), $arrValidator);

    if ($validator->fails()) {
      return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
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
        $img = Image::make($image)->fit(300, 300)->save('./storage/visit_order_visual_uploads/' . $timeNow . '/' . $filename);

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

    $visit->visit_order_updated_by    = session('user_id');
    $visit->visit_order_updated_date  = Carbon::now()->format('Y-m-d H:i:s');
    $visit->save();

    return response()->json(['status' => true, 'message' => ['title' => 'Successfully Updated!', 'text' => 'Visit Order ' . $visit->visit_order_number . ' updated visit visual successfully!']]);
  }

  public function updateNotes(Request $request, $id)
  {
    $visit = VisitOrder::with('site_contact', 'partner', 'visit_order_visual', 'checklist_answer')->where('visit_order_id', $id)->first();

    //Array untuk validasi input
    $arrValidator = [
      'edit_desc' => 'required',
    ];

    $validator = Validator::make($request->all(), $arrValidator);

    if (count($visit->visit_order_visual) == 0 || count($visit->checklist_answer) == 0) {
      return response()->json(['status' => false, 'message' => ['title' => 'Failed Update', 'text' => 'Silahkan lengkapi file visual dan checklist terlebih dahulu']]);
    }

    $visit->visit_order_note  = $request->edit_desc;
    $visit->visit_order_status = 5;
    $visit->visit_order_visited_date   = Carbon::now()->format('Y-m-d H:i:s');
    $visit->visit_order_updated_by    = session('user_id');
    $visit->visit_order_updated_date  = Carbon::now()->format('Y-m-d H:i:s');
    $visit->save();

    // Menambahkan log history ketika update visit order
    $visitOrderId     = $visit->visit_order_id;
    $visitOrderStatus = $visit->visit_order_status;
    $historyDesc      = 'Visit Order ' . $visit->visit_order_number . ' status has been updated!';
    $createdBy        = session('user_id');

    $this->visit_order_history($visitOrderId, $visitOrderStatus, $historyDesc, $createdBy);

    //Send Email
    if ($visit->site_contact->site_contact_email) {
      Mail::to($visit->site_contact->site_contact_email)->send(new ReportVisitOrderEmail("DPI Site Visit Order - " . $visit->visit_order_number, $visit));
    }

    return response()->json(['status' => true, 'message' => ['title' => 'Successfully Updated!', 'text' => 'Visit Order ' . $visit->visit_order_number . ' updated notes successfully!']]);
  }

  public function getChecklists($id)
  {
    $checklistGroups = ChecklistGroup::all();
    $checklists = Checklist::all();

    $checklistAnswers = ChecklistAnswer::where('visit_order_id', $id)->get();

    return response()->json(['status' => 'success', 'checklist_groups' => $checklistGroups, 'checklists' => $checklists, 'checklist_answers' => $checklistAnswers]);
  }

  public function saveChecklists(Request $request)
  {
    // Ambil data dari permintaan
    $visitOrderId = $request->input('visitOrderId');
    $checklists = $request->all()['checklists'] ?? []; // Ambil nilai checklists dari request

    // Mengecek apakah checklist ada
    if (!$checklists) {
      return response()->json(['status' => false, 'message' => ['title' => 'Failed Add Checklist!', 'text' => 'Mohon isi checklist terlebih dahulu!']]);
    }

    // Mengecek apakah sudah terisi semua checklist group
    // $checklistGroups = ChecklistGroup::all();
    // foreach ($checklistGroups as $groups) {
    //   $isExist = false;
    //   foreach ($checklists as $checklist) {
    //     $check = Checklist::with('checklist_group')->find($checklist['checklistId']);
    //     if ($check->checklist_group_id == $groups->checklist_group_id) {
    //       $isExist = true;
    //     }
    //   }
    //   if (!$isExist) {
    //     return response()->json(['status' => false, 'message' => ['title' => 'Failed Add Checklist!', 'text' => 'Mohon lengkapi checklist terlebih dahulu!']]);
    //   }
    // }

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
}
