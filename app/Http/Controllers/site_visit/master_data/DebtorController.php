<?php

namespace App\Http\Controllers\site_visit\master_data;

use Carbon\Carbon;
use App\Models\Debtor;
use Illuminate\Http\Request;
use App\Models\DebtorContact;
use App\Models\MasterVillages;
use App\Models\MasterDistricts;
use App\Models\MasterProvinces;
use App\Models\MasterRegencies;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DebtorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(session('role_id') == 1 || session('role_id') == 2){
          return view('content.site-visit.master-data.debtor');
        } else {
          return view('content.pages.pages-misc-not-authorized');
        }
    }

    public function find(Request $request)
    {
      $search = $request->search;
      $debtors = Debtor::orderby('debtor_name','asc')
        ->select('debtor_id','debtor_name')
        ->where('debtor_name', 'like', '%' . $search . '%')
        ->isActive()
        ->get();

      $response = array();
      foreach($debtors as $debtor){
         $response[] = array(
              "id"    => $debtor->debtor_id,
              "value"  => $debtor->debtor_name
         );
      }

      return response()->json($response);
    }

    public function findAllDebtors(Request $request)
    {
        $search = $request->search;
        $debtors = Debtor::orderby('debtor_name', 'asc')
            ->select('debtor_id', 'debtor_name')
            ->where('debtor_name', 'like', '%' . $search . '%')
            ->isActive()
            ->get();

        $response = array();
        foreach ($debtors as $debtor) {
            $response[] = array(
                "id" => $debtor->debtor_id,
                "value" => $debtor->debtor_name,
            );
        }

        return response()->json($response);
    }

    public function datatable(Request $request)
    {
        $columns = [
          0 => 'debtor_id',
          1 => 'debtor_name',
          2 => 'client_name',
          3 => 'debtor_address',
        ];

        $search = [];
        $totalData = Debtor::where('debtor_status', '!=', 5)->count();
        $totalFiltered = $totalData;

        if (!empty($request->input())) {
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
    
            if (empty($request->input('search.value'))) {
              $debtor = Debtor::where('debtor_status', '!=', 5)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            } else {
              $search = $request->input('search.value');
    
              $debtor = Debtor::where('debtor_name', 'LIKE', "%{$search}%")
                ->where('debtor_status', '!=', 5)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
    
              $totalFiltered = Debtor::where('debtor_name', 'LIKE', "%{$search}%")
                ->where('debtor_status', '!=', 5)
                ->count();
            }
        } else {
          $start = 0;  
          $debtor = Debtor::where('debtor_status', '!=', 5)->get();
        }

        $data = [];

        if (!empty($debtor)) {
          $no = $start;
          foreach ($debtor as $debtor) {
            $nestedData['no']            = ++$no;
            $nestedData['debtor_id']     = $debtor->debtor_id;
            $nestedData['debtor_name']   = $debtor->debtor_name;
            $nestedData['debtor_address']= $debtor->debtor_address;
            $nestedData['debtor_status'] = $debtor->debtor_status;
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
          'debtor_name'             => 'required|max:255'
      ]);

      // dd($request->all());

      if ($validator->fails()) {
      return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
      }
      
      // debtor Status
      $status = !empty($request->debtor_status) && $request->debtor_status == 'on' ? 2 : 1;

      Debtor::create([
          'debtor_status'     => $status,
          'debtor_name'       => $request->debtor_name,
          'debtor_address'    => $request->debtor_address,
          'debtor_postal_code'=> $request->debtor_postal_code,
          'debtor_province'   => $request->debtor_province,
          'debtor_regency'    => $request->debtor_regency,
          'debtor_district'   => $request->debtor_district,
          'debtor_village'    => $request->debtor_village,
          'debtor_phone'      => $request->debtor_phone,
          'debtor_fax'        => $request->debtor_fax,
          'debtor_email'      => $request->debtor_email,
          'debtor_desc'       => $request->debtor_desc,
          'debtor_created_by' => session('user_id'),
          'debtor_created_date' => Carbon::now()->format('Y-m-d H:i:s'),
      ]);

      return response()->json(['status' => true, 'message' => ['title' => 'Successfully created!', 'text' => 'Debtor '.$request->debtor_name.' created successfully!']]);
    }

    public function statusUpdate(Request $request)
    {
      $this->validate($request, [
          'debtor_id'     => 'required',
          'debtor_status' => 'required',
      ]);

      $debtor                     = Debtor::where('debtor_id', $request->debtor_id)->first();
      $debtor->debtor_status      = $request->debtor_status;
      $debtor->debtor_updated_by  = session('user_id');
      $debtor->save();

      if ($request->debtor_status == 2) {
        return response()->json(['status' => true, 'message' => ['title' => 'Debtor Activated!', 'text' => 'Debtor ' . $debtor->debtor_name . ' status has been activated!']]);
      } else {
        return response()->json(['status' => true, 'message' => ['title' => 'Debtor Deactivated!', 'text' => 'Debtor ' . $debtor->debtor_name . ' status has been deactivated!']]);
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Debtor  $debtor
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $debtor = Debtor::with('province', 'regency', 'district', 'village')->where('debtor_id', $id)->first();
      if($debtor) {
          return response()->json(['status' => true, 'data' => $debtor]);
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
          'debtor_name'             => 'required|max:255'
      ]);

      // dd($validator->errors());

      if ($validator->fails()) {
          return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
      }
      
      // debtor Status
      $status = !empty($request->debtor_status) && $request->debtor_status == 'on' ? 2 : 1;

      $debtor = Debtor::where('debtor_id', $id)->first();
      $debtor->debtor_status            = $status;
      $debtor->debtor_name              = $request->debtor_name;
      $debtor->debtor_address           = $request->debtor_address;
      $debtor->debtor_postal_code       = $request->debtor_postal_code;
      $debtor->debtor_province          = $request->debtor_province;
      $debtor->debtor_regency           = $request->debtor_regency;
      $debtor->debtor_district          = $request->debtor_district;
      $debtor->debtor_village           = $request->debtor_village;
      $debtor->debtor_phone             = $request->debtor_phone;
      $debtor->debtor_fax               = $request->debtor_fax;
      $debtor->debtor_email             = $request->debtor_email;
      $debtor->debtor_desc              = $request->debtor_desc;
      $debtor->debtor_updated_by        = session('user_id');
      $debtor->debtor_updated_date      = Carbon::now()->format('Y-m-d H:i:s');
      $debtor->save();
      
      return response()->json(['status' => true, 'message' => ['title' => 'Successfully Updated!', 'text' => 'Debtor ' . $request->debtor_name . ' updated successfully!']]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Debtor  $debtor
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
      $debtor = Debtor::where('debtor_id', $request->debtor_id)->first();
      $debtor->debtor_status        = '5';
      $debtor->debtor_deleted_by    = session('user_id');
      $debtor->debtor_deleted_date  = Carbon::now()->format('Y-m-d H:i:s');
      $debtor->save();

      return response()->json(['status' => true, 'message' => ['title' => 'Debtor Deleted!', 'text' => 'Debtor ' . $request->debtor_name . ' has been deleted!']]);
    }

    /**
     * Display data to provinces, regencies, districts and village
     */
    public function getProvinces()
    {
        $provinces = MasterProvinces::all();

        if ($provinces->count() > 0) {
            return response()->json(['status' => true, 'data' => $provinces]);
        } else {
            return response()->json(['status' => false, 'data' => 'Data is empty!']);
        }
    }
    public function getRegencies(Request $request)
    {
        $provinceId = $request->input('provinceId');

        $regencies = MasterRegencies::where('province_id', $provinceId)->get();

        if ($regencies->count() > 0) {
            return response()->json(['status' => true, 'data' => $regencies]);
        } else {
            return response()->json(['status' => false, 'data' => 'Data is empty!']);
        }
    }

    public function getDistricts(Request $request)
    {
        $regencyId = $request->input('regencyId');

        $districts = MasterDistricts::where('regency_id', $regencyId)->get();

        if ($districts->count() > 0) {
            return response()->json(['status' => true, 'data' => $districts]);
        } else {
            return response()->json(['status' => false, 'data' => 'Data is empty!']);
        }
    }

    public function getVillages(Request $request)
    {
        $districtId = $request->input('districtId');

        $villages = MasterVillages::where('district_id', $districtId)->get();

        if ($villages->count() > 0) {
            return response()->json(['status' => true, 'data' => $villages]);
        } else {
            return response()->json(['status' => false, 'data' => 'Data is empty!']);
        }
    }

     /**
     * End Display data to provinces, regencies, districts and village
     */

      /**
     * Contact Debtor
     */

     public function contactDebtorDatatable(Request $request)
     {
         $this->validate($request, [
             'debtor_id' => 'required'
         ]);
 
         $columns = [
           0 => 'debtor_contact_id',
           1 => 'debtor_contact_status',
           2 => 'debtor_contact_fullname',
           3 => 'debtor_contact_email',
           4 => 'debtor_contact_mobile_phone',
           5 => 'debtor_contact_phone'
         ];
 
         $debtor_id = $request->input('debtor_id');
         $search = [];
         $totalData = DebtorContact::where('debtor_contact_status', '!=', 5)->where('debtor_id', $debtor_id)->count();
         $totalFiltered = $totalData;
 
         if (!empty($request->input())) {
             $limit = $request->input('length');
             $start = $request->input('start');
             $order = $columns[$request->input('order.0.column')];
             $dir = $request->input('order.0.dir');
     
             if (empty($request->input('search.value'))) {
               $contacts = DebtorContact::where('debtor_contact_status', '!=', 5)
                 ->where('debtor_id', $debtor_id)
                 ->offset($start)
                 ->limit($limit)
                 ->orderBy($order, $dir)
                 ->get();
             } else {
               $search = $request->input('search.value');
     
               $contacts = DebtorContact::where('debtor_contact_fullname', 'LIKE', "%{$search}%")
                 ->orWhere('debtor_contact_email', 'LIKE', "%{$search}%")
                 ->orWhere('debtor_contact_mobile_phone', 'LIKE', "%{$search}%")
                 ->orWhere('debtor_contact_phone', 'LIKE', "%{$search}%")
                 ->where('debtor_contact_status', '!=', 5)
                 ->where('debtor_id', $debtor_id)
                 ->offset($start)
                 ->limit($limit)
                 ->orderBy($order, $dir)
                 ->get();
     
               $totalFiltered = DebtorContact::where('client_contact_fullname', 'LIKE', "%{$search}%")
                 ->orWhere('debtor_contact_email', 'LIKE', "%{$search}%")
                 ->orWhere('debtor_contact_mobile_phone', 'LIKE', "%{$search}%")
                 ->orWhere('debtor_contact_phone', 'LIKE', "%{$search}%")
                 ->where('debtor_contact_status', '!=', 5)
                 ->where('debtor_id', $debtor_id)
                 ->count();
             }
         } else {
             $contacts = DebtorContact::where('debtor_contact_status', '!=', 5)->where('debtor_id', $debtor_id)->get();
         }
 
         $data = [];
 
         if (!empty($contacts)) {
           $no = $start;
           foreach ($contacts as $contact) {
             $nestedData['no']                        = ++$no;
             $nestedData['debtor_contact_id']           = $contact->debtor_contact_id;
             $nestedData['debtor_contact_fullname']     = $contact->debtor_contact_fullname;
             $nestedData['debtor_contact_email']        = $contact->debtor_contact_email;
             $nestedData['debtor_contact_mobile_phone'] = $contact->debtor_contact_mobile_phone;
             $nestedData['debtor_contact_phone']        = $contact->debtor_contact_phone;
             $nestedData['debtor_contact_status']       = $contact->debtor_contact_status;
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
 
     public function contactDebtorShow($id)
     {
       $debtor_contact_id = DebtorContact::where('debtor_contact_id', $id)->first();
 
       if($debtor_contact_id) {
         return response()->json(['status' => true, 'data' => $debtor_contact_id]);
       } else {
         return response()->json(['status' => false, 'data' => []]);
       }
     }
 
     public function contactDebtorStore(Request $request)
     {
         $this->validate($request, [
            'debtor_contact_fullname'     => 'required|max:100',
            'debtor_contact_email'        => 'required|max:70',
            'debtor_contact_mobile_phone' => 'required|max:20',
            'debtor_contact_phone'        => 'required|max:20'
         ]);

        //  dd($request->all());
 
         // Contact Status
         $status = !empty($request->debtor_contact_status) && $request->debtor_contact_status == 'on' ? 2 : 1;
 
 
         DebtorContact::create([
           'debtor_contact_status'           => $status,
           'debtor_id'                       => $request->debtor_id,
           'debtor_contact_fullname'         => $request->debtor_contact_fullname,
           'debtor_contact_email'            => $request->debtor_contact_email,
           'debtor_contact_mobile_phone'     => $request->debtor_contact_mobile_phone,
           'debtor_contact_phone'            => $request->debtor_contact_phone,
           'debtor_contact_created_by'       => session('user_id'),
           'debtor_contact_created_date'     => Carbon::now()->format('Y-m-d H:i:s'),
         ]);
 
         return response()->json(['status' => true, 'message' => ['title' => 'Successfully Added!', 'text' => 'Contact ' . $request->debtor_contact_fullname . ' saved successfully!']]);
     }
 
     public function contactDebtorUpdate(Request $request, $id)
     {
         $this->validate($request, [
            'debtor_contact_fullname'     => 'required|max:100',
            'debtor_contact_email'        => 'required|max:70',
            'debtor_contact_mobile_phone' => 'required|max:20',
            'debtor_contact_phone'        => 'required|max:20'
         ]);
 
         // Contact Status
         $status = !empty($request->debtor_contact_status) && $request->debtor_contact_status == 'on' ? 2 : 1;
 
         $debtor_contact = DebtorContact::where('debtor_contact_id', $id)->first();
         $debtor_contact->debtor_contact_status        = $status;
         $debtor_contact->debtor_contact_fullname      = $request->debtor_contact_fullname;
         $debtor_contact->debtor_contact_email         = $request->debtor_contact_email;
         $debtor_contact->debtor_contact_mobile_phone  = $request->debtor_contact_mobile_phone;
         $debtor_contact->debtor_contact_phone         = $request->debtor_contact_phone;
         $debtor_contact->debtor_contact_updated_by    = session('user_id');
         $debtor_contact->debtor_contact_updated_date  = Carbon::now()->format('Y-m-d H:i:s');
         $debtor_contact->save();
 
         return response()->json(['status' => true, 'message' => ['title' => 'Successfully Updated!', 'text' => 'Contact ' . $request->debtor_contact_fullname . ' updated successfully!']]);
     }
     
     public function contactDebtorStatusUpdate(Request $request)
     {
       $this->validate($request, [
          'debtor_contact_id'     => 'required',
          'debtor_contact_status' => 'required',
       ]);
 
       $debtor_contact = DebtorContact::where('debtor_contact_id', $request->debtor_contact_id)->first();
       $debtor_contact->debtor_contact_status        = $request->debtor_contact_status;
       $debtor_contact->debtor_contact_updated_by    = session('user_id');
       $debtor_contact->debtor_contact_updated_date  = Carbon::now()->format('Y-m-d H:i:s');
       $debtor_contact->save();
 
       if ($request->debtor_contact_status == 2) {
         return response()->json(['status' => true, 'message' => ['title' => 'Contact Activated!', 'text' => 'Contact ' . $debtor_contact->debtor_contact_fullname . ' status has been activated!']]);
       } else {
         return response()->json(['status' => true, 'message' => ['title' => 'Contact Deactivated!', 'text' => 'Contact ' . $debtor_contact->debtor_contact_fullname . ' status has been deactivated!']]);
       }
     }
}
