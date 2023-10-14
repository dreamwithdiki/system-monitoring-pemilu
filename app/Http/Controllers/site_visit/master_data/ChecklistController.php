<?php

namespace App\Http\Controllers\site_visit\master_data;

use App\Models\Checklist;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ChecklistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(session('role_id') == 1 || session('role_id') == 2){
          return view('content.site-visit.master-data.checklist');
        } else {
          return view('content.pages.pages-misc-not-authorized');
        }
    }

    public function datatable(Request $request)
    {
        $columns = [
          0 => 'checklist_id',
          1 => 'checklist_group_id', // checklist group name
          2 => 'checklist_name',
          3 => 'checklist_desc',
        ];

        $search = [];
        $totalData = Checklist::where('checklist_status', '!=', 5)->count();
        $totalFiltered = $totalData;

        if (!empty($request->input())) {
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
    
            if (empty($request->input('search.value'))) {
              $checklist = Checklist::where('checklist_status', '!=', 5)
                ->with('checklist_group')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            } else {
              $search = $request->input('search.value');
    
              $checklist = Checklist::where('checklist_name', 'LIKE', "%{$search}%")
                ->with('checklist_group')
                ->orWhereRelation('checklist_group', 'checklist_group_name', 'LIKE', "%{$search}%")
                ->where('checklist_status', '!=', 5)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
    
              $totalFiltered = Checklist::where('checklist_name', 'LIKE', "%{$search}%")
                ->orWhereRelation('checklist_group', 'checklist_group_name', 'LIKE', "%{$search}%")
                ->where('checklist_status', '!=', 5)
                ->count();
            }
        } else {
          $start = 0;  
          $checklist = Checklist::with('checklist_group')->get();
        }

        $data = [];

        if (!empty($checklist)) {
          $no = $start;
          foreach ($checklist as $check) {
            $nestedData['no']             = ++$no;
            $nestedData['checklist_id']          = $check->checklist_id;
            $nestedData['checklist_name']        = $check->checklist_name;
            $nestedData['checklist_status']      = $check->checklist_status;
            $nestedData['checklist_group_name']  = $check->checklist_group->checklist_group_name;
            $nestedData['checklist_desc']        = $check->checklist_desc;
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
     * @param  \App\Http\Requests\Response  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'checklist_group_id'  => 'required',
            'checklist_name'      => 'required|max:255'
        ]);
  
        //   dd($request->all());
  
        if ($validator->fails()) {
        return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
        }
        
        // checklist Status
        $status = !empty($request->checklist_status) && $request->checklist_status == 'on' ? 2 : 1;

        $status_is_freetext = !empty($request->checklist_is_freetext) && $request->checklist_is_freetext == 'on' ? 2 : 1;

        Checklist::create([
            'checklist_status'       => $status,
            'checklist_group_id'     => $request->checklist_group_id,
            'checklist_name'         => $request->checklist_name,
            'checklist_is_freetext'  => $status_is_freetext,
            'checklist_desc'         => $request->checklist_desc,
            'checklist_created_by'   => session('user_id'),
            'checklist_created_date' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        return response()->json(['status' => true, 'message' => ['title' => 'Successfully created!', 'text' => 'Checklist created successfully!']]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Checklist  $checklist
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $checklist = Checklist::with('checklist_group')->where('checklist_id', $id)->first();
        if($checklist) {
            return response()->json(['status' => true, 'data' => $checklist]);
        } else {
            return response()->json(['status' => false, 'data' => []]);
        }
    }

    public function statusUpdate(Request $request)
    {
      $this->validate($request, [
          'checklist_id'     => 'required',
          'checklist_status' => 'required',
      ]);

      $checklist = Checklist::where('checklist_id', $request->checklist_id)->first();
      $checklist->checklist_status        = $request->checklist_status;
      $checklist->checklist_updated_by    = session('user_id');
      $checklist->save();

      if ($request->checklist_status == 2) {
        return response()->json(['status' => true, 'message' => ['title' => 'Checklist Activated!', 'text' => 'Checklist ' . $checklist->checklist_name . ' status has been activated!']]);
      } else {
        return response()->json(['status' => true, 'message' => ['title' => 'Checklist Deactivated!', 'text' => 'Checklist ' . $checklist->checklist_name . ' status has been deactivated!']]);
      }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Checklist  $checklist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'checklist_group_id'  => 'required',
            'checklist_name'      => 'required|max:255'
          ]);
  
          // checklist Status
          $status = !empty($request->checklist_status) && $request->checklist_status == 'on' ? 2 : 1;

          $status_is_freetext = !empty($request->checklist_is_freetext) && $request->checklist_is_freetext == 'on' ? 2 : 1;

          $checklist = Checklist::where('checklist_id', $id)->first();
          $checklist->checklist_status            = $status;
          $checklist->checklist_group_id          = $request->checklist_group_id;
          $checklist->checklist_name              = $request->checklist_name;
          $checklist->checklist_is_freetext       = $status_is_freetext;
          $checklist->checklist_desc              = $request->checklist_desc;
          $checklist->checklist_updated_by        = session('user_id');
          $checklist->checklist_updated_date      = Carbon::now()->format('Y-m-d H:i:s');
          $checklist->save();
          
          return response()->json(['status' => true, 'message' => ['title' => 'Successfully Updated!', 'text' => 'Checklist ' . $request->checklist_name . ' updated successfully!']]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Checklist  $checklist
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $visit = Checklist::where('checklist_id', $request->checklist_id)->first();
        $visit->checklist_status        = '5';
        $visit->checklist_deleted_by    = session('user_id');
        $visit->checklist_deleted_date  = Carbon::now()->format('Y-m-d H:i:s');
        $visit->save();

        return response()->json(['status' => true, 'message' => ['title' => 'Checklist Deleted!', 'text' => 'Checklist ' . $request->checklist_name . ' has been deleted!']]);
    }
}
