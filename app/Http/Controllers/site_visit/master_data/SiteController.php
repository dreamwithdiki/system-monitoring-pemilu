<?php

namespace App\Http\Controllers\site_visit\master_data;

use App\Models\Site;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MasterDistricts;
use App\Models\MasterProvinces;
use App\Models\MasterRegencies;
use App\Models\MasterVillages;
use App\Models\SiteContact;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(session('role_id') == 1 || session('role_id') == 2){
          return view('content.site-visit.master-data.site');
        } else {
          return view('content.pages.pages-misc-not-authorized');
        }
    }

    public function find(Request $request)
    {
      $search = $request->search;
      $sites = Site::orderby('site_name','asc')
        ->select('site_id','site_name','site_code')
        ->where('site_name', 'like', '%' . $search . '%')
        ->orWhere('site_code', 'like', '%' . $search . '%')
        ->isActive()
        ->get();

      $response = array();
      foreach($sites as $site){
         $response[] = array(
              "id"    => $site->site_id,
              "text"  => $site->site_code . ' - ' . $site->site_name
         );
      }

      return response()->json($response);
    }

    public function findById(Request $request, $id)
    {
        $search = $request->search;
        $sites = Site::orderBy('site_name', 'asc')
            ->select('site_id', 'site_name', 'site_code')
            ->where(function ($query) use ($search) {
                $query->where('site_name', 'like', '%' . $search . '%')
                    ->orWhere('site_code', 'like', '%' . $search . '%');
            })
            ->where('client_id', $id)
            ->isActive()
            ->get();

        $response = [];
        foreach ($sites as $site) {
            $response[] = [
                "id" => $site->site_id,
                "text" => $site->site_code . ' - ' . $site->site_name
            ];
        }

        return response()->json($response);
    }


    public function datatable(Request $request)
    {
        $columns = [
          0 => 'site_id',
          1 => 'site_code',
          2 => 'site_name',
          3 => 'client_name',
          4 => 'site_address',
        ];

        $search = [];
        $totalData = Site::where('site_status', '!=', 5)->count();
        $totalFiltered = $totalData;

        if (!empty($request->input())) {
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
    
            if (empty($request->input('search.value'))) {
              $site = Site::where('site_status', '!=', 5)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            } else {
              $search = $request->input('search.value');
    
              $site = Site::where('site_code', 'LIKE', "%{$search}%")
                ->orWhere('site_name', 'LIKE', "%{$search}%")
                ->orWhereRelation('client', 'client_name', 'LIKE', "%{$search}%")
                ->where('site_status', '!=', 5)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
    
              $totalFiltered = Site::where('site_code', 'LIKE', "%{$search}%")
                ->orWhere('site_name', 'LIKE', "%{$search}%")
                ->orWhereRelation('client', 'client_name', 'LIKE', "%{$search}%")
                ->where('site_status', '!=', 5)
                ->count();
            }
        } else {
          $start = 0;  
          $site = Site::with('client')->get();
        }

        $data = [];

        if (!empty($site)) {
          $no = $start;
          foreach ($site as $site) {
            $nestedData['no'] = ++$no;
            $nestedData['site_id'] = $site->site_id;
            $nestedData['site_code'] = $site->site_code;
            $nestedData['site_name'] = $site->site_name;
            $nestedData['site_address'] = $site->site_address;
            $nestedData['client_name'] = $site->client->client_name;
            $nestedData['site_status'] = $site->site_status;
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
            'client_id'             => 'required',
            'site_code'             => 'required|max:50',
            'site_name'             => 'required|max:255',
            'site_address'          => 'required|max:255',
            'site_postal_code'      => 'required|max:100',
        ]);
  
          // dd($request->all());
  
        if ($validator->fails()) {
        return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
        }
        
        // site Status
        $status = !empty($request->site_status) && $request->site_status == 'on' ? 2 : 1;
        
        // site code check duplicate entry
        $site_code_exist = Site::where('site_code', $request->site_code)->first();
        if ($site_code_exist) {
        if ($site_code_exist->site_status == 5) {
            return response()->json(['status' => false, 'message' => ['title' => 'Wrong Code', 'text' => 'Code already used by deleted site!']]);
        }
        return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'Code already used by another site!']]);
        }

        Site::create([
            'site_status'     => $status,
            'client_id'       => $request->client_id,
            'site_code'       => $request->site_code,
            'site_name'       => $request->site_name,
            'site_address'    => $request->site_address,
            'site_postal_code'=> $request->site_postal_code,
            'site_province'   => $request->site_province,
            'site_regency'    => $request->site_regency,
            'site_district'   => $request->site_district,
            'site_village'    => $request->site_village,
            'site_phone'      => $request->site_phone,
            'site_fax'        => $request->site_fax,
            'site_desc'       => $request->site_desc,
            'site_created_by' => session('user_id'),
            'site_created_date' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        return response()->json(['status' => true, 'message' => ['title' => 'Successfully created!', 'text' => 'Site '.$request->site_name.' created successfully!']]);
    }

    public function statusUpdate(Request $request)
    {
      $this->validate($request, [
          'site_id'     => 'required',
          'site_status' => 'required',
      ]);

      $site                   = Site::where('site_id', $request->site_id)->first();
      $site->site_status      = $request->site_status;
      $site->site_updated_by  = session('user_id');
      $site->save();

      if ($request->site_status == 2) {
        return response()->json(['status' => true, 'message' => ['title' => 'Site Activated!', 'text' => 'Site ' . $site->site_name . ' status has been activated!']]);
      } else {
        return response()->json(['status' => true, 'message' => ['title' => 'Site Deactivated!', 'text' => 'Site ' . $site->site_name . ' status has been deactivated!']]);
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $site = Site::with('client', 'province', 'regency', 'district', 'village')->where('site_id', $id)->first();
        if($site) {
            return response()->json(['status' => true, 'data' => $site]);
        } else {
            return response()->json(['status' => false, 'data' => []]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'client_id'             => 'required',
            'site_code'             => 'required|max:50',
            'site_name'             => 'required|max:255',
            'site_address'          => 'required|max:255',
            'site_postal_code'      => 'required|max:100',
        ]);

        // dd($validator->errors());

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
        }
        
        // Site Status
        $status = !empty($request->site_status) && $request->site_status == 'on' ? 2 : 1;

         // Site code check duplicate entry
         $site_code_exist = Site::where('site_code', $request->site_code)->where('site_id', '!=', $id)->first();
         if ($site_code_exist) {
           if ($site_code_exist->site_status == 5) {
             return response()->json(['status' => false, 'message' => ['title' => 'Wrong Code', 'text' => 'Code already used by deleted site!']]);
           }
           return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'Code already used by another site!']]);
         }
 

        $site = Site::where('site_id', $id)->first();
        $site->site_status            = $status;
        $site->client_id              = $request->client_id;
        $site->site_code              = $request->site_code;
        $site->site_name              = $request->site_name;
        $site->site_address           = $request->site_address;
        $site->site_postal_code       = $request->site_postal_code;
        $site->site_province          = $request->site_province;
        $site->site_regency           = $request->site_regency;
        $site->site_district          = $request->site_district;
        $site->site_village           = $request->site_village;
        $site->site_phone             = $request->site_phone;
        $site->site_fax               = $request->site_fax;
        $site->site_desc              = $request->site_desc;
        $site->site_updated_by        = session('user_id');
        $site->site_updated_date      = Carbon::now()->format('Y-m-d H:i:s');
        $site->save();
        
        return response()->json(['status' => true, 'message' => ['title' => 'Successfully Updated!', 'text' => 'Site ' . $request->site_name . ' updated successfully!']]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $site = Site::where('site_id', $request->site_id)->first();
        $site->site_status        = '5';
        $site->site_deleted_by    = session('user_id');
        $site->site_deleted_date  = Carbon::now()->format('Y-m-d H:i:s');
        $site->save();

        return response()->json(['status' => true, 'message' => ['title' => 'Site Deleted!', 'text' => 'Site ' . $request->site_name . ' has been deleted!']]);
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
     * Contact Site
     */

     public function contactSiteDatatable(Request $request)
     {
         $this->validate($request, [
             'site_id' => 'required'
         ]);
 
         $columns = [
           0 => 'site_contact_id',
           1 => 'site_contact_status',
           2 => 'site_contact_fullname',
           3 => 'site_contact_email',
           4 => 'site_contact_mobile_phone',
           5 => 'site_contact_phone'
         ];
 
         $site_id = $request->input('site_id');
         $search = [];
         $totalData = SiteContact::where('site_contact_status', '!=', 5)->where('site_id', $site_id)->count();
         $totalFiltered = $totalData;
 
         if (!empty($request->input())) {
             $limit = $request->input('length');
             $start = $request->input('start');
             $order = $columns[$request->input('order.0.column')];
             $dir = $request->input('order.0.dir');
     
             if (empty($request->input('search.value'))) {
               $contacts = SiteContact::where('site_contact_status', '!=', 5)
                 ->where('site_id', $site_id)
                 ->offset($start)
                 ->limit($limit)
                 ->orderBy($order, $dir)
                 ->get();
             } else {
               $search = $request->input('search.value');
     
               $contacts = SiteContact::where('site_contact_fullname', 'LIKE', "%{$search}%")
                 ->orWhere('site_contact_email', 'LIKE', "%{$search}%")
                 ->orWhere('site_contact_mobile_phone', 'LIKE', "%{$search}%")
                 ->orWhere('site_contact_phone', 'LIKE', "%{$search}%")
                 ->where('site_contact_status', '!=', 5)
                 ->where('site_id', $site_id)
                 ->offset($start)
                 ->limit($limit)
                 ->orderBy($order, $dir)
                 ->get();
     
               $totalFiltered = SiteContact::where('client_contact_fullname', 'LIKE', "%{$search}%")
                 ->orWhere('site_contact_email', 'LIKE', "%{$search}%")
                 ->orWhere('site_contact_mobile_phone', 'LIKE', "%{$search}%")
                 ->orWhere('site_contact_phone', 'LIKE', "%{$search}%")
                 ->where('site_contact_status', '!=', 5)
                 ->where('site_id', $site_id)
                 ->count();
             }
         } else {
             $contacts = SiteContact::where('site_contact_status', '!=', 5)->where('site_id', $site_id)->get();
         }
 
         $data = [];
 
         if (!empty($contacts)) {
           $no = $start;
           foreach ($contacts as $contact) {
             $nestedData['no']                        = ++$no;
             $nestedData['site_contact_id']           = $contact->site_contact_id;
             $nestedData['site_contact_fullname']     = $contact->site_contact_fullname;
             $nestedData['site_contact_email']        = $contact->site_contact_email;
             $nestedData['site_contact_mobile_phone'] = $contact->site_contact_mobile_phone;
             $nestedData['site_contact_phone']        = $contact->site_contact_phone;
             $nestedData['site_contact_status']       = $contact->site_contact_status;
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
 
     public function contactSiteShow($id)
     {
       $site_contact_id = SiteContact::where('site_contact_id', $id)->first();
 
       if($site_contact_id) {
         return response()->json(['status' => true, 'data' => $site_contact_id]);
       } else {
         return response()->json(['status' => false, 'data' => []]);
       }
     }
 
     public function contactSiteStore(Request $request)
     {
         $this->validate($request, [
            'site_contact_fullname'     => 'required|max:50'
         ]);

        //  dd($request->all());
 
         // Contact Status
         $status = !empty($request->site_contact_status) && $request->site_contact_status == 'on' ? 2 : 1;
 
 
         SiteContact::create([
           'site_contact_status'           => $status,
           'site_id'                       => $request->site_id,
           'site_contact_fullname'         => $request->site_contact_fullname,
           'site_contact_email'            => $request->site_contact_email,
           'site_contact_mobile_phone'     => $request->site_contact_mobile_phone,
           'site_contact_phone'            => $request->site_contact_phone,
           'site_contact_created_by'       => session('user_id'),
           'site_contact_created_date'     => Carbon::now()->format('Y-m-d H:i:s'),
         ]);
 
         return response()->json(['status' => true, 'message' => ['title' => 'Successfully Added!', 'text' => 'Contact ' . $request->site_contact_fullname . ' saved successfully!']]);
     }
 
     public function contactSiteUpdate(Request $request, $id)
     {
         $this->validate($request, [
            'site_contact_fullname'     => 'required|max:50'
         ]);
 
         // Contact Status
         $status = !empty($request->site_contact_status) && $request->site_contact_status == 'on' ? 2 : 1;
 
         $site_contact = SiteContact::where('site_contact_id', $id)->first();
         $site_contact->site_contact_status        = $status;
         $site_contact->site_contact_fullname      = $request->site_contact_fullname;
         $site_contact->site_contact_email         = $request->site_contact_email;
         $site_contact->site_contact_mobile_phone  = $request->site_contact_mobile_phone;
         $site_contact->site_contact_phone         = $request->site_contact_phone;
         $site_contact->site_contact_updated_by    = session('user_id');
         $site_contact->site_contact_updated_date  = Carbon::now()->format('Y-m-d H:i:s');
         $site_contact->save();
 
         return response()->json(['status' => true, 'message' => ['title' => 'Successfully Updated!', 'text' => 'Contact ' . $request->site_contact_fullname . ' updated successfully!']]);
     }
     
     public function contactSiteStatusUpdate(Request $request)
     {
       $this->validate($request, [
          'site_contact_id'     => 'required',
          'site_contact_status' => 'required',
       ]);
 
       $site_contact = SiteContact::where('site_contact_id', $request->site_contact_id)->first();
       $site_contact->site_contact_status        = $request->site_contact_status;
       $site_contact->site_contact_updated_by    = session('user_id');
       $site_contact->site_contact_updated_date  = Carbon::now()->format('Y-m-d H:i:s');
       $site_contact->save();
 
       if ($request->site_contact_status == 2) {
         return response()->json(['status' => true, 'message' => ['title' => 'Contact Activated!', 'text' => 'Contact ' . $site_contact->site_contact_fullname . ' status has been activated!']]);
       } else {
         return response()->json(['status' => true, 'message' => ['title' => 'Contact Deactivated!', 'text' => 'Contact ' . $site_contact->site_contact_fullname . ' status has been deactivated!']]);
       }
     }
}
