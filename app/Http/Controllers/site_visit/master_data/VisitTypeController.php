<?php

namespace App\Http\Controllers\site_visit\master_data;

use App\Models\VisitType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ChecklistGroup;
use App\Models\VisitTypeChecklistGroup;
use App\Models\VisitTypeVisitVisualType;
use App\Models\VisitVisualType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class VisitTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(session('role_id') == 1 || session('role_id') == 2){
          $checklistGroup = ChecklistGroup::orderby('checklist_group_name', 'ASC')->isActive()->get();
          $checklistVisual = VisitVisualType::orderby('visit_visual_type_name', 'ASC')->isActive()->get();
          
          return view('content.site-visit.master-data.visit-type', [
            'checklistGroup' => $checklistGroup,
            'checklistVisual'=> $checklistVisual
          ]);
        } else {
          return view('content.pages.pages-misc-not-authorized');
        }
    }

    public function find(Request $request)
    {
      $search = $request->search;
      $visit_type = VisitType::orderby('visit_type_name','asc')
        ->select('visit_type_id','visit_type_name','visit_type_code')
        ->where('visit_type_name', 'like', '%' . $search . '%')
        ->orWhere('visit_type_code', 'like', '%' . $search . '%')
        ->isActive()
        ->get();

      $response = array();
      foreach($visit_type as $type){
         $response[] = array(
              "id"    => $type->visit_type_id,
              "text"  => $type->visit_type_code . ' - ' . $type->visit_type_name
         );
      }

      return response()->json($response);
    }

    public function datatable(Request $request)
    {
        $columns = [
          0 => 'visit_type_id',
          1 => 'visit_type_code',
          2 => 'visit_type_name',
          3 => 'visit_type_desc',
        ];

        $search = [];
        $totalData = VisitType::where('visit_type_status', '!=', 5)->count();
        $totalFiltered = $totalData;

        if (!empty($request->input())) {
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
    
            if (empty($request->input('search.value'))) {
              $visit_type = VisitType::where('visit_type_status', '!=', 5)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            } else {
              $search = $request->input('search.value');
    
              $visit_type = VisitType::where('visit_type_code', 'LIKE', "%{$search}%")
                ->orWhere('visit_type_name', 'LIKE', "%{$search}%")
                ->where('visit_type_status', '!=', 5)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
    
              $totalFiltered = VisitType::where('visit_type_code', 'LIKE', "%{$search}%")
                ->orWhere('visit_type_name', 'LIKE', "%{$search}%")
                ->where('visit_type_status', '!=', 5)
                ->count();
            }
        } else {
          $start = 0;  
          $visit_type = VisitType::where('visit_type_status', '!=', 5)->get();
        }

        $data = [];

        if (!empty($visit_type)) {
          $no = $start;
          foreach ($visit_type as $visit) {
            $nestedData['no']             = ++$no;
            $nestedData['visit_type_id']     = $visit->visit_type_id;
            $nestedData['visit_type_code']   = $visit->visit_type_code;
            $nestedData['visit_type_name']   = $visit->visit_type_name;
            $nestedData['visit_type_desc']   = $visit->visit_type_desc;
            $nestedData['visit_type_status'] = $visit->visit_type_status;
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
            'visit_type_code'             => 'required|max:50',
            'visit_type_name'             => 'required|max:255',
        ]);
  
        //   dd($request->all());
  
        if ($validator->fails()) {
        return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
        }
        
        // visit type Status
        $status = !empty($request->visit_type_status) && $request->visit_type_status == 'on' ? 2 : 1;
        
        // visit type code check duplicate entry
        $visit_type_code_exist = VisitType::where('visit_type_code', $request->visit_type_code)->first();
        if ($visit_type_code_exist) {
        if ($visit_type_code_exist->visit_type_status == 5) {
            return response()->json(['status' => false, 'message' => ['title' => 'Wrong Code', 'text' => 'Code already used by deleted visit type!']]);
        }
        return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'Code already used by another visit type!']]);
        }

        VisitType::create([
            'visit_type_status'       => $status,
            'visit_type_code'         => $request->visit_type_code,
            'visit_type_name'         => $request->visit_type_name,
            'visit_type_desc'         => $request->visit_type_desc,
            'visit_type_created_by'   => session('user_id'),
            'visit_type_created_date' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        return response()->json(['status' => true, 'message' => ['title' => 'Successfully created!', 'text' => 'Visit Type created successfully!']]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VisitType  $visitType
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $visit = VisitType::where('visit_type_id', $id)->first();
        if($visit) {
            return response()->json(['status' => true, 'data' => $visit]);
        } else {
            return response()->json(['status' => false, 'data' => []]);
        }
    }


    public function statusUpdate(Request $request)
    {
      $this->validate($request, [
        'visit_type_id'     => 'required',
        'visit_type_status' => 'required',
      ]);

      $visit = VisitType::where('visit_type_id', $request->visit_type_id)->first();
      $visit->visit_type_status        = $request->visit_type_status;
      $visit->visit_type_updated_by    = session('user_id');
      $visit->save();

      if ($request->visit_type_status == 2) {
        return response()->json(['status' => true, 'message' => ['title' => 'Visit Type Activated!', 'text' => 'Visit Type ' . $visit->visit_type_name . ' status has been activated!']]);
      } else {
        return response()->json(['status' => true, 'message' => ['title' => 'Visit Type Deactivated!', 'text' => 'Visit Type ' . $visit->visit_type_name . ' status has been deactivated!']]);
      }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\VisitType  $visitType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'visit_type_code'             => 'required|max:50',
            'visit_type_name'             => 'required|max:255',
          ]);
  
          // visit type Status
          $status = !empty($request->visit_type_status) && $request->visit_type_status == 'on' ? 2 : 1;
          
          // visit type code check duplicate entry
          $visit_type_code_exist = VisitType::where('visit_type_code', $request->visit_type_code)->where('visit_type_id', '!=', $id)->first();
          if ($visit_type_code_exist) {
            if ($visit_type_code_exist->visit_type_status == 5) {
              return response()->json(['status' => false, 'message' => ['title' => 'Wrong Code', 'text' => 'Code already used by deleted visit type!']]);
            }
            return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'Code already used by another visit type!']]);
          }
  
          $visit = VisitType::where('visit_type_id', $id)->first();
          $visit->visit_type_status            = $status;
          $visit->visit_type_code              = $request->visit_type_code;
          $visit->visit_type_name              = $request->visit_type_name;
          $visit->visit_type_desc              = $request->visit_type_desc;
          $visit->visit_type_updated_by        = session('user_id');
          $visit->visit_type_updated_date      = Carbon::now()->format('Y-m-d H:i:s');
          $visit->save();
          
          return response()->json(['status' => true, 'message' => ['title' => 'Successfully Updated!', 'text' => 'Visit Type ' . $request->visit_type_name . ' updated successfully!']]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VisitType  $visitType
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $visit = VisitType::where('visit_type_id', $request->visit_type_id)->first();
        $visit->visit_type_status        = '5';
        $visit->visit_type_deleted_by    = session('user_id');
        $visit->visit_type_deleted_date  = Carbon::now()->format('Y-m-d H:i:s');
        $visit->save();

        return response()->json(['status' => true, 'message' => ['title' => 'Visit Type Deleted!', 'text' => 'Visit Type ' . $request->visit_type_name . ' has been deleted!']]);
    }

    /**
     * Checklist Group
     */
    public function saveOrUpdateVisitTypeChecklistGroup(Request $request)
    {
     $validator = Validator::make($request->all(), [
         'checklist_group'     => 'required|array|min:1',
         'checklist_group.*'   => 'integer',
     ]);

    //  dd($request->all());
    // dd($validator->errors());

     if ($validator->fails()) {
         return response()->json([
             'status' => false,
             'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the fields']
         ]);
     }

     $checklist_group_data = $request->checklist_group;
     $visit_type_id     = $request->visit_type_id;

     // Menghapus data checklist group yang tidak dipilih kembali
     VisitTypeChecklistGroup::where('visit_type_id', $visit_type_id)->whereNotIn('checklist_group_id', $checklist_group_data)->delete();

     // Menyimpan atau memperbarui data checklist group yang dipilih kembali
     foreach ($checklist_group_data as $check) {
         $checklist_group_object = ChecklistGroup::where('checklist_group_id', $check)->first();
         if (!empty($checklist_group_object)) {
             $checklist_group_data = [
                 'visit_type_id'                           => $visit_type_id,
                 'checklist_group_id'                      => $checklist_group_object->checklist_group_id,
                 'visit_type_checklist_group_created_by'   => session('user_id'),
                 'visit_type_checklist_group_created_date' => now()->format('Y-m-d H:i:s')
             ];
             
             VisitTypeChecklistGroup::updateOrCreate(
                 [
                     'visit_type_id'      => $visit_type_id,
                     'checklist_group_id' => $checklist_group_object->checklist_group_id,
                 ],
                 $checklist_group_data
             );
           
         }
     }

     return response()->json([
         'status' => true,
         'message' => ['title' => 'Successfully created!', 'text' => 'Checklist group created successfully!'],
         'data' => $checklist_group_object
     ]);
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\Models\VisitType 
    * @return \Illuminate\Http\Response
    */
   public function VisitTypeChecklistGroupShow($id)
   {
       $checklist_group = VisitType::where('visit_type_id', $id)->first();

       if ($checklist_group) {
         $checklist_group->detail = VisitTypeChecklistGroup::with('checklist_group')->where('visit_type_id', '=', $id)->get();
           return response()->json(['status' => true, 'data' => $checklist_group]);
       } else {
           return response()->json(['status' => false, 'data' => []]);
       }
       
   }


   /**
     * Visual Type
     */
    public function saveOrUpdateVisitTypeVisualType(Request $request)
    {
     $validator = Validator::make($request->all(), [
         'visual_type'     => 'required|array|min:1',
         'visual_type.*'   => 'integer',
     ]);

    //  dd($request->all());

     if ($validator->fails()) {
         return response()->json([
             'status' => false,
             'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the fields']
         ]);
     }

     $visual_type_data = $request->visual_type;
     $visit_type_id     = $request->visit_type_id;

     // Menghapus data checklist visual yang tidak dipilih kembali
     VisitTypeVisitVisualType::where('visit_type_id', $visit_type_id)->whereNotIn('visit_visual_type_id', $visual_type_data)->delete();

     // Menyimpan atau memperbarui data checklist visual yang dipilih kembali
     foreach ($visual_type_data as $check) {
         $checklist_visual_object = VisitVisualType::where('visit_visual_type_id', $check)->first();
         if (!empty($checklist_visual_object)) {
             $checklist_group_data = [
                 'visit_type_id'                             => $visit_type_id,
                 'visit_visual_type_id'                      => $checklist_visual_object->visit_visual_type_id,
                 'visit_type_visit_visual_type_created_by'   => session('user_id'),
                 'visit_type_visit_visual_type_created_date' => now()->format('Y-m-d H:i:s')
             ];
             
             VisitTypeVisitVisualType::updateOrCreate(
                 [
                     'visit_type_id'      => $visit_type_id,
                     'visit_visual_type_id' => $checklist_visual_object->visit_visual_type_id,
                 ],
                 $checklist_group_data
             );
           
         }
     }

     return response()->json([
         'status' => true,
         'message' => ['title' => 'Successfully created!', 'text' => 'Checklist visual created successfully!'],
         'data' => $checklist_visual_object
     ]);
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\Models\VisitType 
    * @return \Illuminate\Http\Response
    */
   public function VisitTypeVisualTypeShow($id)
   {
       $checklist_visual = VisitType::where('visit_type_id', $id)->first();

       if ($checklist_visual) {
         $checklist_visual->detail = VisitTypeVisitVisualType::with('checklist_visual')->where('visit_type_id', '=', $id)->get();
           return response()->json(['status' => true, 'data' => $checklist_visual]);
       } else {
           return response()->json(['status' => false, 'data' => []]);
       }
       
   }
}
