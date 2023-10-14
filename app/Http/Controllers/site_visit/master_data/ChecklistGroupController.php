<?php

namespace App\Http\Controllers\site_visit\master_data;

use App\Models\ChecklistGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ChecklistGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(session('role_id') == 1 || session('role_id') == 2){
          return view('content.site-visit.master-data.checklist-group');
        } else {
          return view('content.pages.pages-misc-not-authorized');
        }
    }

    public function find(Request $request)
    {
      $search = $request->search;
      $checklist_group = ChecklistGroup::orderby('checklist_group_name','asc')
        ->select('checklist_group_id','checklist_group_name','checklist_group_code')
        ->where('checklist_group_name', 'like', '%' . $search . '%')
        ->orWhere('checklist_group_code', 'like', '%' . $search . '%')
        ->isActive()
        ->get();

      $response = array();
      foreach($checklist_group as $checklist){
         $response[] = array(
              "id"    => $checklist->checklist_group_id,
              "text"  => $checklist->checklist_group_code . ' - ' . $checklist->checklist_group_name
         );
      }

      return response()->json($response);
    }

    public function datatable(Request $request)
    {
        $columns = [
          0 => 'checklist_group_id',
          1 => 'checklist_group_sort',
          2 => 'checklist_group_code',
          3 => 'checklist_group_name',
          4 => 'checklist_group_desc',
        ];

        $search = [];
        $totalData = ChecklistGroup::where('checklist_group_status', '!=', 5)->count();
        $totalFiltered = $totalData;

        if (!empty($request->input())) {
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
    
            if (empty($request->input('search.value'))) {
              $checklist_group = ChecklistGroup::where('checklist_group_status', '!=', 5)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            } else {
              $search = $request->input('search.value');
    
              $checklist_group = ChecklistGroup::where('checklist_group_code', 'LIKE', "%{$search}%")
                ->orWhere('checklist_group_name', 'LIKE', "%{$search}%")
                ->where('checklist_group_status', '!=', 5)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
    
              $totalFiltered = ChecklistGroup::where('checklist_group_code', 'LIKE', "%{$search}%")
                ->orWhere('checklist_group_name', 'LIKE', "%{$search}%")
                ->where('checklist_group_status', '!=', 5)
                ->count();
            }
        } else {
          $start = 0;  
          $checklist_group = ChecklistGroup::where('checklist_group_status', '!=', 5)->get();
        }

        $data = [];

        if (!empty($checklist_group)) {
          $no = $start;
          foreach ($checklist_group as $group) {
            $nestedData['no']             = ++$no;
            $nestedData['checklist_group_id']     = $group->checklist_group_id;
            $nestedData['checklist_group_sort']   = $group->checklist_group_sort;
            $nestedData['checklist_group_code']   = $group->checklist_group_code;
            $nestedData['checklist_group_name']   = $group->checklist_group_name;
            $nestedData['checklist_group_desc']   = $group->checklist_group_desc;
            $nestedData['checklist_group_status'] = $group->checklist_group_status;
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
            'checklist_group_sort'             => 'required',
            'checklist_group_code'             => 'required|max:50',
            'checklist_group_name'             => 'required|max:255',
        ]);
  
        //   dd($request->all());
  
        if ($validator->fails()) {
        return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
        }
        
        // checklist group Status
        $status = !empty($request->checklist_group_status) && $request->checklist_group_status == 'on' ? 2 : 1;
        
        // checklist group code check duplicate entry
        $checklist_group_code_exist = ChecklistGroup::where('checklist_group_code', $request->checklist_group_code)->first();
        if ($checklist_group_code_exist) {
        if ($checklist_group_code_exist->checklist_group_status == 5) {
            return response()->json(['status' => false, 'message' => ['title' => 'Wrong Code', 'text' => 'Code already used by deleted checklist group!']]);
        }
        return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'Code already used by another checklist group!']]);
        }

        ChecklistGroup::create([
            'checklist_group_status'       => $status,
            'checklist_group_sort'         => $request->checklist_group_sort,
            'checklist_group_code'         => $request->checklist_group_code,
            'checklist_group_name'         => $request->checklist_group_name,
            'checklist_group_desc'         => $request->checklist_group_desc,
            'checklist_group_created_by'   => session('user_id'),
            'checklist_group_created_date' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        return response()->json(['status' => true, 'message' => ['title' => 'Successfully created!', 'text' => 'Checklist group created successfully!']]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ChecklistGroup  $checklistGroup
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = ChecklistGroup::where('checklist_group_id', $id)->first();
        if($group) {
            return response()->json(['status' => true, 'data' => $group]);
        } else {
            return response()->json(['status' => false, 'data' => []]);
        }
    }

    public function statusUpdate(Request $request)
    {
      $this->validate($request, [
        'checklist_group_id'     => 'required',
        'checklist_group_status' => 'required',
      ]);

      $group = ChecklistGroup::where('checklist_group_id', $request->checklist_group_id)->first();
      $group->checklist_group_status        = $request->checklist_group_status;
      $group->checklist_group_updated_by    = session('user_id');
      $group->save();

      if ($request->checklist_group_status == 2) {
        return response()->json(['status' => true, 'message' => ['title' => 'Checklist Group Activated!', 'text' => 'Checklist Group ' . $group->visit_type_name . ' status has been activated!']]);
      } else {
        return response()->json(['status' => true, 'message' => ['title' => 'Checklist Group Deactivated!', 'text' => 'Checklist Group ' . $group->visit_type_name . ' status has been deactivated!']]);
      }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\ChecklistGroup  $checklistGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'checklist_group_sort'             => 'required',
            'checklist_group_code'             => 'required|max:50',
            'checklist_group_name'             => 'required|max:255',
          ]);
  
          // checklist group Status
          $status = !empty($request->checklist_group_status) && $request->checklist_group_status == 'on' ? 2 : 1;
          
          // checklist group code check duplicate entry
          $checklist_group_code_exist = ChecklistGroup::where('checklist_group_code', $request->checklist_group_code)->where('checklist_group_id', '!=', $id)->first();
          if ($checklist_group_code_exist) {
            if ($checklist_group_code_exist->checklist_group_status == 5) {
              return response()->json(['status' => false, 'message' => ['title' => 'Wrong Code', 'text' => 'Code already used by deleted checklist group!']]);
            }
            return response()->json(['status' => false, 'message' => ['title' => 'Duplicate Entry', 'text' => 'Code already used by another checklist group!']]);
          }
  
          $group = ChecklistGroup::where('checklist_group_id', $id)->first();
          $group->checklist_group_status            = $status;
          $group->checklist_group_sort              = $request->checklist_group_sort;
          $group->checklist_group_code              = $request->checklist_group_code;
          $group->checklist_group_name              = $request->checklist_group_name;
          $group->checklist_group_desc              = $request->checklist_group_desc;
          $group->checklist_group_updated_by        = session('user_id');
          $group->checklist_group_updated_date      = Carbon::now()->format('Y-m-d H:i:s');
          $group->save();
          
          return response()->json(['status' => true, 'message' => ['title' => 'Successfully Updated!', 'text' => 'Checklist Group ' . $request->checklist_group_name . ' updated successfully!']]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ChecklistGroup  $checklistGroup
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $group = ChecklistGroup::where('checklist_group_id', $request->checklist_group_id)->first();
        $group->checklist_group_status        = '5';
        $group->checklist_group_deleted_by    = session('user_id');
        $group->checklist_group_deleted_date  = Carbon::now()->format('Y-m-d H:i:s');
        $group->save();

        return response()->json(['status' => true, 'message' => ['title' => 'Checklist Group Deleted!', 'text' => 'Checklist Group ' . $request->checklist_group_name . ' has been deleted!']]);
    }
}
