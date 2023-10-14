<?php

namespace App\Http\Controllers\billings;

use App\Http\Controllers\Controller;
use App\Models\PartnerPayment;
use App\Models\PartnerPaymentDetail;
use App\Models\PartnerPaymentFile;
use App\Models\PartnerPaymentHistory;
use App\Models\VisitOrder;
use App\Models\VisitOrderHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PartnerPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(session('role_id') == 1 || session('role_id') == 2){
            return view('content.billings.partner-payment', [
                'code_number' => $this->generatePartnerPaymentNumber(),
            ]);
        } else {
            return view('content.pages.pages-misc-not-authorized');
        }
    }

     /**
     * Generate a unique code based on the given prefix and year of creation.
     * The code will be incremented automatically based on the latest code in the database.
     *
     * @param string $prefix The prefix for the code, e.g. PPWO.
     * @return string The generated code.
     */

    private function generatePartnerPaymentNumber()
    {
        $currentYear = Carbon::now()->format('Y');
        $lastPayment = PartnerPayment::where('partner_payment_year', $currentYear)->orderBy('partner_payment_number', 'desc')->first();

        if ($lastPayment) {
            $lastNumber = intval(substr($lastPayment->partner_payment_number, -5));
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
            $partnerPaymentNumber = 'PPWO/' . $currentYear . '/' . $newNumber;
        } else {
            $partnerPaymentNumber = 'PPWO/' . $currentYear . '/00001';
        }

        return $partnerPaymentNumber;
    }

    /**
     * save data to sys_partner_payment_history
     * 
     */
    private function partner_payment_history($partnerPaymentId, $historyDesc, $createBy, $status)
    {
        $partner_payment_history = PartnerPaymentHistory::create([
            'partner_payment_id'                   => $partnerPaymentId,
            'partner_payment_history_status'       => $status,
            'partner_payment_history_desc'         => $historyDesc,
            'partner_payment_history_created_by'   => $createBy,
            'partner_payment_history_created_date' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    
        $partner_payment_history->save();
    }

     public function datatable(Request $request)
     {
         $columns = [
            0 => 'partner_payment_id', 
            1 => 'partner_payment_number', 
            2 => 'partner_payment_name',
            3 => 'partner_payment_month',
            4 => 'partner_payment_year', 
            5 => 'partner_payment_desc'  
         ];
 
         $search = [];
         $totalData = PartnerPayment::where('partner_payment_status', '!=', 5)->count();
         $totalFiltered = $totalData;
 
         if (!empty($request->input())) {
             $limit = $request->input('length');
             $start = $request->input('start');
             $order = $columns[$request->input('order.0.column')];
             $dir = $request->input('order.0.dir');
     
             if (empty($request->input('search.value'))) {
               $partner_payment = PartnerPayment::where('partner_payment_status', '!=', 5)
                 ->offset($start)
                 ->limit($limit)
                 ->orderBy($order, $dir)
                 ->get();
             } else {
               $search = $request->input('search.value');
     
               $partner_payment = PartnerPayment::where('partner_payment_name', 'LIKE', "%{$search}%")
                 ->orWhere('partner_payment_desc', 'LIKE', "%{$search}%")
                 ->where('partner_payment_status', '!=', 5)
                 ->offset($start)
                 ->limit($limit)
                 ->orderBy($order, $dir)
                 ->get();
     
               $totalFiltered = PartnerPayment::where('partner_payment_name', 'LIKE', "%{$search}%")
                 ->orWhere('partner_payment_desc', 'LIKE', "%{$search}%")
                 ->where('visit_order_status', '!=', 5)
                 ->count();
             }
         } else {
           $start = 0;  
           $partner_payment = PartnerPayment::where('partner_payment_status', '!=', 5)->get();
         }
 
         $data = [];
 
         if (!empty($partner_payment)) {
           $no = $start;
           foreach ($partner_payment as $payment) {
                $nestedData['no'] = ++$no;
                $nestedData['partner_payment_id']      = $payment->partner_payment_id;
                $nestedData['partner_payment_status']  = $payment->partner_payment_status;
                $nestedData['partner_payment_number']  = $payment->partner_payment_number;
                $nestedData['partner_payment_name']    = $payment->partner_payment_name;
                $nestedData['partner_payment_month']   = month_name($payment->partner_payment_month);
                $nestedData['partner_payment_year']    = $payment->partner_payment_year;
                $nestedData['partner_payment_desc']    = $payment->partner_payment_desc;

                $data[] = $nestedData;
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePartnerPaymentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $action = $request->input('action');
        $partner_payment_status = ($action === 'save') ? 1 : 2;

        // dd('Action:', $action, 'Status:', $partner_payment_status);

        $partner_payment = PartnerPayment::create([
            'partner_payment_status'       => $partner_payment_status,
            'partner_payment_number'       => $this->generatePartnerPaymentNumber(),
            'partner_payment_name'         => $request->partner_payment_name,
            'partner_payment_month'        => $request->partner_payment_month,
            'partner_payment_year'         => $request->partner_payment_year,
            'partner_payment_desc'         => $request->partner_payment_desc,
            'partner_payment_created_by'   => session('user_id'),
            'partner_payment_created_date' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        $partner_payment->save();

        foreach($request->visit_order as $key => $order){
            PartnerPaymentDetail::create([
              'partner_payment_id' => $partner_payment->partner_payment_id,
              'visit_order_id' => $key,
              'partner_payment_detail_created_by'       => session('user_id'),
              'partner_payment_detail_created_date'     => Carbon::now()->format('Y-m-d H:i:s')
            ]);
          }

        // ini utk upload files
        if ($request->has('partner_payment_files')) {

            foreach ($request->file('partner_payment_files') as $key => $value) {
                $validator = Validator::make($request->all(), [
                    'partner_payment_file.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx,xls,xlsx,ppt,pptx|max:1024',
                    'partner_payment_file_desc.*' => 'nullable|string',
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
                }
                
                $filename = Carbon::now()->format('Hisu_').'partner_payment_file'.($key+1).'.'.$value->getClientOriginalExtension();
                $value->storeAs('partner_payment_file_uploads/'.Carbon::now()->format('Ymd'), $filename);

                // save data to database
                $attachment = new PartnerPaymentFile();
                $attachment->partner_payment_id                = $partner_payment->partner_payment_id;
                $attachment->partner_payment_file              = $filename;
                $attachment->partner_payment_file_desc         = $request->partner_payment_file_desc;
                $attachment->partner_payment_file_created_by   = session('user_id');
                $attachment->partner_payment_file_created_date = Carbon::now()->format('Y-m-d H:i:s');
                $attachment->save();
            }
        } 

        if ($action === 'save') {
          // save history tombol save
          $partnerPaymentId   = $partner_payment->partner_payment_id;
          $partnerPaymendDesc = $request->partner_payment_desc;
          $createdBy          = session('user_id');
      
          $this->partner_payment_history($partnerPaymentId, $partnerPaymendDesc, $createdBy, 1); // Status code 1 for 'Open'
      
        } else {
          // save history tombol save and submit
          $partnerPaymentId   = $partner_payment->partner_payment_id;
          $partnerPaymendDesc = $request->partner_payment_desc;
          $createdBy          = session('user_id');
      
          $this->partner_payment_history($partnerPaymentId, $partnerPaymendDesc, $createdBy, 1); // Status code 1 for 'Open'
          $this->partner_payment_history($partnerPaymentId, $partnerPaymendDesc, $createdBy, 2); // Status code 2 for 'Submitted'
        }

        return response()->json(['status' => true, 'message' => ['title' => 'Successfully created!', 'text' => 'Partner Payment created successfully!']]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PartnerPayment  $partnerPayment
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        Carbon::setLocale('id');
        $partner_payment = PartnerPayment::with('partner_detail', 'partner_payment_file', 'visit_order', 'history')->where('partner_payment_id', $id)->first();
        if($partner_payment) {

            // Fetch visit orders with status 8
            // $visitOrders = VisitOrder::where('visit_order_status', 8)->where('partner_payment_id', $partner_payment->partner_payment_id)->get();

            // // Add visit orders to the partner_payment object
            // $partner_payment->visit_order = $visitOrders;

            foreach ($partner_payment->visit_order as $key => $order) {
              $order['no'] = $key+1;
              $order['client_name'] = $order->client->client_name;
              $order['site_name'] = $order->site->site_name;
              $order['partner_name'] = $order->partner->partner_name;
              $order['visit_order_date']     = format_short_local_date($order->visit_order_date);
              $order['visit_order_due_date'] = format_short_local_date($order->visit_order_due_date);
            }
          
            foreach ($partner_payment->history as $his) {
                $his['date_created_format'] = Carbon::parse($his->partner_payment_history_created_date)->translatedFormat('d F Y, h:m');
            }

            foreach ($partner_payment->partner_payment_file as $file) {
                $file['folder_name'] = Carbon::parse($file->partner_payment_file_created_date)->format('Ymd');
                $file['payment_file_id'] = Crypt::encrypt($file->partner_payment_file_id);
            }
            return response()->json([
                'status' => true,
                'data' => $partner_payment,
            ]);
        } else {
            return response()->json(['status' => false, 'data' => []]);
        }
    }

    /**
     * Display paid the specified resource.
     *
     * @param  \App\Models\PartnerPayment  $partnerPayment
     * @return \Illuminate\Http\Response
     */
    public function show_paid($id)
    {
        Carbon::setLocale('id');
        $partner_payment = PartnerPayment::with('partner_detail', 'partner_payment_file', 'visit_order', 'history')->where('partner_payment_id', $id)->first();
        if($partner_payment) {

            foreach ($partner_payment->visit_order as $key => $order) {
              $order['no'] = $key+1;
              $order['client_name'] = $order->client->client_name;
              $order['site_name'] = $order->site->site_name;
              $order['partner_name'] = $order->partner->partner_name;
              $order['visit_order_date']     = format_short_local_date($order->visit_order_date);
              $order['visit_order_due_date'] = format_short_local_date($order->visit_order_due_date);
            }

            foreach ($partner_payment->history as $his) {
                $his['date_created_format'] = Carbon::parse($his->partner_payment_history_created_date)->translatedFormat('d F Y, h:m');
            }

            foreach ($partner_payment->partner_payment_file as $file) {
                $file['folder_name'] = Carbon::parse($file->partner_payment_file_created_date)->format('Ymd');
                $file['payment_file_id'] = Crypt::encrypt($file->partner_payment_file_id);
            }
            return response()->json([
                'status' => true,
                'data' => $partner_payment,
            ]);
        } else {
            return response()->json(['status' => false, 'data' => []]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\PartnerPayment  $partnerPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // $partner_payment = PartnerPayment::find($id);
        $partner_payment = PartnerPayment::where('partner_payment_id', $id)->first();

        $partner_payment->partner_payment_number        = $request->partner_payment_number;
        $partner_payment->partner_payment_name          = $request->partner_payment_name;
        $partner_payment->partner_payment_month         = $request->partner_payment_month;
        $partner_payment->partner_payment_year          = $request->partner_payment_year;
        $partner_payment->partner_payment_desc          = $request->partner_payment_desc;
        $partner_payment->partner_payment_updated_by    = session('user_id');
        $partner_payment->partner_payment_updated_date  = Carbon::now()->format('Y-m-d H:i:s');
        
        $partner_payment->save();
        
        try {
            DB::beginTransaction();

            PartnerPaymentDetail::where('partner_payment_id', $id)->delete();

            if ($request->has('visit_order')) {
                foreach ($request->visit_order as $key => $order) {
                    $partner_payment_detail = new PartnerPaymentDetail();
                    $partner_payment_detail->partner_payment_id                   = $partner_payment->partner_payment_id;
                    $partner_payment_detail->visit_order_id                       = $key;
                    $partner_payment_detail->partner_payment_detail_created_by    = session('user_id');
                    $partner_payment_detail->partner_payment_detail_created_date  = Carbon::now()->format('Y-m-d H:i:s');
                    $partner_payment_detail->save(); 
                }
            }

            DB::commit();
        } catch (\Exception $exp) {
            DB::rollBack();
        }

         // ini buat history
         $partnerPaymentId   = $partner_payment->partner_payment_id;
         $partnerPaymendDesc = $request->partner_payment_desc;
         $createdBy          = session('user_id');
     
         // Save the history for status 'Open'
         $this->partner_payment_history($partnerPaymentId, $partnerPaymendDesc, $createdBy, 1); // Status code 1 for 'Open'
     
         // Save the history for status 'Valid'
         $this->partner_payment_history($partnerPaymentId, $partnerPaymendDesc, $createdBy, 2); // Status code 2 for 'Valid'

        return response()->json(['status' => true, 'message' => ['title' => 'Successfully Updated!', 'text' => 'Partner Payment ' . $request->partner_payment_name . ' updated successfully!']]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PartnerPayment  $partnerPayment
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $partner_payment = PartnerPayment::where('partner_payment_id', $request->partner_payment_id)->first();
        if ($partner_payment) {
            $partner_payment->partner_payment_status        = '5';
            $partner_payment->partner_payment_deleted_by    = session('user_id');
            $partner_payment->partner_payment_deleted_date  = Carbon::now()->format('Y-m-d H:i:s');
            $partner_payment->save();
            return response()->json(['status' => true, 'message' => ['title' => 'Partner Payment Deleted!', 'text' => 'Partner Payment ' . $request->partner_payment_name . ' has been deleted!']]);
        } else {
            return response()->json(['status' => false, 'message' => ['title' => 'Partner Payment not Deleted!', 'text' => 'Partner Payment ' . $request->partner_payment_name . ' not deleted!']]);
        }
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
          8 => 'visit_order_status', 
          9 => 'partner_payment_id', 
        ];

        $search = [];
        $totalData = VisitOrder::where('visit_order_status', '=', 6)->count();
        $totalFiltered = $totalData;

        if (!empty($request->input())) {
          $limit = $request->input('length');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');
  
          if (empty($request->input('search.value'))) {
            $visit_order = VisitOrder::with('client', 'site', 'partner', 'partner_detail')
                ->where('visit_order_status', 6)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            } else {
              $search = $request->input('search.value');
    
              $visit_order = VisitOrder::with('client', 'site', 'partner', 'partner_detail')
                ->where('visit_order_number', 'LIKE', "%{$search}%")
                ->orWhere('visit_order_location', 'LIKE', "%{$search}%")
                ->orWhere('visit_order_date', 'LIKE', "%{$search}%")
                ->orWhereRelation('client', 'client_name', 'LIKE', "%{$search}%")
                ->orWhereRelation('site', 'site_name', 'LIKE', "%{$search}%")
                ->orWhereRelation('partner', 'partner_name', 'LIKE', "%{$search}%")
                ->where('visit_order_status', 6)                
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
    
              $totalFiltered = VisitOrder::where('visit_order_number', 'LIKE', "%{$search}%")
                ->orWhere('visit_order_location', 'LIKE', "%{$search}%")
                ->orWhere('visit_order_date', 'LIKE', "%{$search}%")
                ->orWhereRelation('client', 'client_name', 'LIKE', "%{$search}%")
                ->orWhereRelation('site', 'site_name', 'LIKE', "%{$search}%")
                ->orWhereRelation('partner', 'partner_name', 'LIKE', "%{$search}%")
                ->where('visit_order_status', 6)               
                ->count();
            }
        } else {
          $visit_order = VisitOrder::with('client', 'site', 'partner', 'partner_detail')->get();
        }
        
        $data = [];

        if (!empty($visit_order)) {
          $no = 0;
          foreach ($visit_order as $order) {
              // Mengecek metode untuk menampilkan data untuk modal add
              if ($request->method == 'add') {
                // Menampilkan data yang tidak punya relasi partner_detail
                if ($order->partner_detail->isEmpty()) {
                  $nestedData['no']                   = ++$no;
                  $nestedData['visit_order_id']       = $order->visit_order_id;
                  $nestedData['visit_order_encrypt_id'] = Crypt::encrypt($order->visit_order_id);
                  $nestedData['visit_order_number']   = $order->visit_order_number;
                  $nestedData['visit_order_date']     = format_short_local_date($order->visit_order_date);
                  $nestedData['visit_order_due_date'] = format_short_local_date($order->visit_order_due_date);
                  $nestedData['client_name']          = $order->client->client_name;
                  $nestedData['site_name']            = $order->site->site_name;
                  $nestedData['visit_order_location'] = $order->visit_order_location;
                  $nestedData['partner_name']         = $order->partner ? $order->partner->partner_name : '-';
                  $nestedData['download_status']      = ($order->visit_order_status == 5 || $order->visit_order_status == 6) ? (($order->visit_order_downloaded_by && $order->visit_order_downloaded_date) ? "Downloaded" : "Not Downloaded") : "-";
                  $nestedData['visit_order_status']   = $order->visit_order_status;
                  $nestedData['partner_detail']       = $order->partner_detail;
                  $nestedData['partner_payment_id']   = $order->partner_payment_id;
                  $data[] = $nestedData;
                }
                
              } else if($request->method == 'edit'){
                // Menampilkan data yang tidak punya relasi dan jika punya relasi ambil yang partner_payment_id nya sama
                if ($order->partner_detail->isEmpty()) {
                    $nestedData['no']                   = ++$no;
                    $nestedData['visit_order_id']       = $order->visit_order_id;
                    $nestedData['visit_order_encrypt_id'] = Crypt::encrypt($order->visit_order_id);
                    $nestedData['visit_order_number']   = $order->visit_order_number;
                    $nestedData['visit_order_date']     = format_short_local_date($order->visit_order_date);
                    $nestedData['visit_order_due_date'] = format_short_local_date($order->visit_order_due_date);
                    $nestedData['client_name']          = $order->client->client_name;
                    $nestedData['site_name']            = $order->site->site_name;
                    $nestedData['visit_order_location'] = $order->visit_order_location;
                    $nestedData['partner_name']         = $order->partner ? $order->partner->partner_name : '-';
                    $nestedData['download_status']      = ($order->visit_order_status == 5 || $order->visit_order_status == 6) ? (($order->visit_order_downloaded_by && $order->visit_order_downloaded_date) ? "Downloaded" : "Not Downloaded") : "-";
                    $nestedData['visit_order_status']   = $order->visit_order_status;
                    $nestedData['partner_detail']       = $order->partner_detail;
                    $nestedData['partner_payment_id']   = $order->partner_payment_id;
                    $data[] = $nestedData;
                } else {
                  if ($order->partner_detail[0]->partner_payment_id == $request->partner_payment_id) {
                    $nestedData['no']                   = ++$no;
                    $nestedData['visit_order_id']       = $order->visit_order_id;
                    $nestedData['visit_order_encrypt_id'] = Crypt::encrypt($order->visit_order_id);
                    $nestedData['visit_order_number']   = $order->visit_order_number;
                    $nestedData['visit_order_date']     = format_short_local_date($order->visit_order_date);
                    $nestedData['visit_order_due_date'] = format_short_local_date($order->visit_order_due_date);
                    $nestedData['client_name']          = $order->client->client_name;
                    $nestedData['site_name']            = $order->site->site_name;
                    $nestedData['visit_order_location'] = $order->visit_order_location;
                    $nestedData['partner_name']         = $order->partner ? $order->partner->partner_name : '-';
                    $nestedData['download_status']      = ($order->visit_order_status == 5 || $order->visit_order_status == 6) ? (($order->visit_order_downloaded_by && $order->visit_order_downloaded_date) ? "Downloaded" : "Not Downloaded") : "-";
                    $nestedData['visit_order_status']   = $order->visit_order_status;
                    $nestedData['partner_detail']       = $order->partner_detail;
                    $nestedData['partner_payment_id']   = $order->partner_payment_id;
                    $data[] = $nestedData;
                  }
                }   
              } else {
                if ($order->partner_detail->isNotEmpty() && $order->partner_detail[0]->partner_payment_id == $request->partner_payment_id) {
                  $nestedData['no']                   = ++$no;
                  $nestedData['visit_order_id']       = $order->visit_order_id;
                  $nestedData['visit_order_encrypt_id'] = Crypt::encrypt($order->visit_order_id);
                  $nestedData['visit_order_number']   = $order->visit_order_number;
                  $nestedData['visit_order_date']     = format_short_local_date($order->visit_order_date);
                  $nestedData['visit_order_due_date'] = format_short_local_date($order->visit_order_due_date);
                  $nestedData['client_name']          = $order->client->client_name;
                  $nestedData['site_name']            = $order->site->site_name;
                  $nestedData['visit_order_location'] = $order->visit_order_location;
                  $nestedData['partner_name']         = $order->partner ? $order->partner->partner_name : '-';
                  $nestedData['download_status']      = ($order->visit_order_status == 5 || $order->visit_order_status == 6) ? (($order->visit_order_downloaded_by && $order->visit_order_downloaded_date) ? "Downloaded" : "Not Downloaded") : "-";
                  $nestedData['visit_order_status']   = $order->visit_order_status;
                  $nestedData['partner_detail']       = $order->partner_detail;
                  $nestedData['partner_payment_id']   = $order->partner_payment_id;
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

     /**
     * Tab Attachment
     */
    public function attachmentDatatable(Request $request)
     {
         $columns = [
           0 => 'partner_payment_file_id',
           1 => 'partner_payment_file',
           2 => 'partner_payment_file_desc',
         ];
 
         $partner_payment_id = $request->input('partner_payment_id');
         $search = [];
         $totalData = PartnerPaymentFile::where('partner_payment_id', $partner_payment_id)->count();
         $totalFiltered = $totalData;
 
         if (!empty($request->input())) {
             $limit = $request->input('length');
             $start = $request->input('start');
             $order = $columns[$request->input('order.0.column')];
             $dir = $request->input('order.0.dir');
     
             if (empty($request->input('search.value'))) {
               $product_attachment = PartnerPaymentFile::where('partner_payment_id', $partner_payment_id)
                 ->offset($start)
                 ->limit($limit)
                 ->orderBy($order, $dir)
                 ->get();
             } else {
               $search = $request->input('search.value');
     
               $product_attachment = PartnerPaymentFile::where('partner_payment_file_desc', 'LIKE', "%{$search}%")
                 ->where('partner_payment_id', $partner_payment_id)
                 ->offset($start)
                 ->limit($limit)
                 ->orderBy($order, $dir)
                 ->get();
     
               $totalFiltered = PartnerPaymentFile::where('partner_payment_file_desc', 'LIKE', "%{$search}%")
                 ->where('partner_payment_id', $partner_payment_id)
                 ->count();
             }
         } else {
             $product_attachment = PartnerPaymentFile::where('partner_payment_id', $partner_payment_id)->get();
         }
 
         $data = [];
 
         if (!empty($product_attachment)) {
           $no = $start;
           foreach ($product_attachment as $attachment) {
             $nestedData['no']                        = ++$no;
             $nestedData['partner_payment_file_id']   = Crypt::encrypt($attachment->partner_payment_file_id);
             $nestedData['partner_payment_file']      = $attachment->partner_payment_file;
             $nestedData['partner_payment_file_desc'] = $attachment->partner_payment_file_desc;
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

    public function attachmentStore(Request $request)
    {
        
        if ($request->has('partner_payment_files')) {

          foreach ($request->file('partner_payment_files') as $key => $value) {
              $validator = Validator::make($request->all(), [
                'partner_payment_file.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx,xls,xlsx,ppt,pptx|max:1024',
                'partner_payment_file_desc.*' => 'nullable|string',
              ]);

              if ($validator->fails()) {
                  return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
              }
            
              $filename = Carbon::now()->format('Hisu_').'partner_payment_file'.($key+1).'.'.$value->getClientOriginalExtension();
              $value->storeAs('partner_payment_file_uploads/'.Carbon::now()->format('Ymd'), $filename);

              // save data to database
              $attachment = new PartnerPaymentFile();
              $attachment->partner_payment_id                = $request->partner_payment_id;
              $attachment->partner_payment_file              = $filename;
              $attachment->partner_payment_file_desc         = $request->partner_payment_file_desc;
              $attachment->partner_payment_file_created_by   = session('user_id');
              $attachment->partner_payment_file_created_date = Carbon::now()->format('Y-m-d H:i:s');
              $attachment->save();
          }
        } else {
            return response()->json(['status' => false, 'message' => ['title' => 'File attachment not found!', 'text' => 'Please select file attachment.']]);
        }
        return response()->json(['status' => true, 'message' => ['title' => 'Successfully created!', 'text' => 'Attachment created successfully!']]);
      
    }

     // show image attachment in dataTable
     public function show_upload_attachment($partner_payment_file_id)
     {
       $partner_payment = PartnerPaymentFile::find(Crypt::decrypt($partner_payment_file_id));
       if (!$partner_payment) {
         abort(404);
       }
 
       $dir = Carbon::parse($partner_payment->partner_payment_file_created_date)->format('Ymd');
       $file_path = $dir.'/'.$partner_payment->partner_payment_file;
 
       $path = storage_path('app/partner_payment_file_uploads/'.$file_path);
       if (!File::exists($path)) {
        $path = public_path('assets/img/no-image-asset.jpg');
       }
 
       $file = File::get($path);
       $type = File::mimeType($path);
       $response = response($file, 200);
       $response->header("Content-Type", $type);
 
       return $response;
     }
 
     // Delete attachment
     public function attachmentDelete(Request $request)
     {
         $PartnerPaymentFile = PartnerPaymentFile::where('partner_payment_file_id', Crypt::decrypt($request->partner_payment_file_id))->first();
 
         if ($PartnerPaymentFile) {
             $dir = Carbon::parse($PartnerPaymentFile->partner_payment_file_created_date)->format('Ymd');
             $file_path = $dir.'/'.$PartnerPaymentFile->partner_payment_file;
             Storage::delete('partner_payment_file_uploads/' . $file_path);
 
             $PartnerPaymentFile->delete();
             return response()->json(['status' => true, 'message' => ['title' => 'File Attachment Deleted!', 'text' => 'File Attachment ' . $PartnerPaymentFile->partner_payment_file . ' has been deleted!']]);
         } else {
             return response()->json(['status' => false, 'message' => ['title' => 'File Attachment not Deleted!', 'text' => 'File Attachment ' . $PartnerPaymentFile->partner_payment_file . ' not deleted!']]);
         }
 
     }

     //satus change to submitted = 2 
    public function submitted(Request $request)
    {
      $this->validate($request, [
          'partner_payment_id'     => 'required',
          'partner_payment_status' => 'required',
      ]);

      $partner_payment = PartnerPayment::where('partner_payment_id', $request->partner_payment_id)->first();
      $partner_payment->partner_payment_status        = $request->partner_payment_status;
      $partner_payment->partner_payment_updated_by    = session('user_id');
      $partner_payment->partner_payment_updated_date  = Carbon::now()->format('Y-m-d H:i:s');
      $partner_payment->save();

      // Menambahkan log history ketika di submitted = 2
      $partnerPaymentId   = $partner_payment->partner_payment_id;
      $partnerPaymendDesc = "Visit Order has been Submitted";
      $createdBy          = session('user_id');

      $this->partner_payment_history($partnerPaymentId, $partnerPaymendDesc, $createdBy, 2); // Status code 2 for 'Submitted'
    
      if ($request->partner_payment_status == 2) {
        return response()->json(['status' => true, 'message' => ['title' => 'Partner Payment Submitted!', 'text' => 'Partner Payment ' . $partner_payment->partner_payment_number . ' status has been submitted!']]);
      } else {
        return response()->json(['status' => false, 'message' => ['title' => 'Partner Payment Not Submitted!', 'text' => 'Partner Payment ' . $partner_payment->partner_payment_number . ' status has not been submitted!']]);
      }
    }

    public function paid(Request $request, $id)
    {
      $partner_payment = PartnerPayment::find($id);
      $time = Carbon::now();

      // Add Partner Payment Detail
      try {
        DB::beginTransaction();

        PartnerPaymentDetail::where('partner_payment_id', $partner_payment->partner_payment_id)->delete();
        foreach($request->visit_order as $key => $order){
          VisitOrder::where('visit_order_id', $key)->first()->update([
            'partner_payment_id' => $partner_payment->partner_payment_id,
            'visit_order_status' => 8, // paid to partner
          ]);
          VisitOrderHistory::create([
            'visit_order_id'                   => $key,
            'visit_order_status'               => 8, // paid to partner
            'visit_order_history_desc'         => "Visit Order has been Paid",
            'visit_order_history_created_by'   => session('user_id'),
            'visit_order_history_created_date' => $time->format('Y-m-d H:i:s'),
          ]);

        }
        DB::commit();
      } catch(\Exception $exp) {
        DB::rollBack();
      }
      // End Add Partner Payment Detail

      $partner_payment->update([
        'partner_payment_status'       => 3, // paid
        'partner_payment_updated_by'   => session('user_id'),
        'partner_payment_updated_date' => $time->format('Y-m-d H:i:s'),
      ]);

       // Menambahkan log payment history 
       $partnerPaymentId   = $partner_payment->partner_payment_id;
       $partnerPaymendDesc = 'Partner Payment has been paid';
       $createdBy          = session('user_id');
 
       $this->partner_payment_history($partnerPaymentId, $partnerPaymendDesc, $createdBy, 3); // Status code 3 for 'Paid'
     
      // End Add log payment history

      return response()->json(['status' => true, 'message' => ['title' => 'Successfully updated!', 'text' => 'Partner Payment '.$partner_payment->partner_payment_number.' updated status paid successfully!']]);
    }

    public function rejected(Request $request)
    {
      $validator = Validator::make($request->all(), [ 
          'partner_payment_id'     => 'required',
          'partner_payment_status' => 'required',
          'partner_payment_desc' => 'required'
      ]);

      if ($validator->fails()) {
          return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
      }

      $partner_payment = PartnerPayment::where('partner_payment_id', $request->partner_payment_id)->first();
      $partner_payment->partner_payment_desc          = $request->partner_payment_desc;
      $partner_payment->partner_payment_status        = $request->partner_payment_status;
      $partner_payment->partner_payment_updated_by    = session('user_id');
      $partner_payment->partner_payment_updated_date  = Carbon::now()->format('Y-m-d H:i:s');
      $partner_payment->save();

      PartnerPaymentDetail::where('partner_payment_id', $request->partner_payment_id)->delete();

      // Menambahkan log history ketika di rejected = 4
      $partnerPaymentId   = $partner_payment->partner_payment_id;
      $partnerPaymendDesc = 'Partner Payment has been rejected';
      $createdBy          = session('user_id');

      $this->partner_payment_history($partnerPaymentId, $partnerPaymendDesc, $createdBy, 4);

      if ($request->partner_payment_status == 4) {
        return response()->json(['status' => true, 'message' => ['title' => 'Partner Payment Rejected!', 'text' => 'Partner Payment ' . $partner_payment->partner_payment_number . ' status has been rejected!']]);
      } else {
        return response()->json(['status' => true, 'message' => ['title' => 'Partner Payment Not Rejected!', 'text' => 'Partner Payment ' . $partner_payment->partner_payment_number . ' status has not been rejected!']]);
      }
    }

}
