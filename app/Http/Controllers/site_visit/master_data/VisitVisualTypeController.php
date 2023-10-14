<?php

namespace App\Http\Controllers\site_visit\master_data;

use App\Models\VisitVisualType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class VisitVisualTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(session('role_id') == 1 || session('role_id') == 2){
          return view('content.site-visit.master-data.visit-visual-type');
        } else {
          return view('content.pages.pages-misc-not-authorized');
        }
    }

    public function datatable(Request $request)
    {
        $columns = [
          0 => 'visit_visual_type_id',
          1 => 'visit_visual_type_sort',
          2 => 'visit_visual_type_name',
          3 => 'visit_visual_type_desc',
        ];

        $search = [];
        $totalData = VisitVisualType::where('visit_visual_type_status', '!=', 5)->count();
        $totalFiltered = $totalData;

        if (!empty($request->input())) {
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
    
            if (empty($request->input('search.value'))) {
              $visit_visual_type = VisitVisualType::where('visit_visual_type_status', '!=', 5)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            } else {
              $search = $request->input('search.value');
    
              $visit_visual_type = VisitVisualType::where('visit_visual_type_name', 'LIKE', "%{$search}%")
                ->where('visit_visual_type_status', '!=', 5)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
    
              $totalFiltered = VisitVisualType::where('visit_visual_type_name', 'LIKE', "%{$search}%")
                ->where('visit_visual_type_status', '!=', 5)
                ->count();
            }
        } else {
          $start = 0;  
          $visit_visual_type = VisitVisualType::where('visit_visual_type_status', '!=', 5)->get();
        }

        $data = [];

        if (!empty($visit_visual_type)) {
          $no = $start;
          foreach ($visit_visual_type as $part) {
            $nestedData['no']             = ++$no;
            $nestedData['visit_visual_type_id']          = $part->visit_visual_type_id;
            $nestedData['visit_visual_type_sort']        = $part->visit_visual_type_sort;
            $nestedData['visit_visual_type_name']        = $part->visit_visual_type_name;
            $nestedData['visit_visual_type_reference']   = $part->visit_visual_type_reference;
            $nestedData['visit_visual_type_desc']        = $part->visit_visual_type_desc;
            $nestedData['visit_visual_type_status']      = $part->visit_visual_type_status;
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
            'visit_visual_type_sort'      => 'required',
            'visit_visual_type_name'      => 'required|max:255',
            'visit_visual_type_reference' => 'required',
        ]);
  
          // dd($request->all());
  
        if ($validator->fails()) {
        return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
        }
        
        // visit type Status
        $status = !empty($request->visit_visual_type_status) && $request->visit_visual_type_status == 'on' ? 2 : 1;

        VisitVisualType::create([
            'visit_visual_type_status'       => $status,
            'visit_visual_type_sort'         => $request->visit_visual_type_sort,
            'visit_visual_type_name'         => $request->visit_visual_type_name,
            'visit_visual_type_reference'    => $request->visit_visual_type_reference,
            'visit_visual_type_desc'         => $request->visit_visual_type_desc,
            'visit_visual_type_created_by'   => session('user_id'),
            'visit_visual_type_created_date' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        return response()->json(['status' => true, 'message' => ['title' => 'Successfully created!', 'text' => 'Visit visual type created successfully!']]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VisitVisualType  $visitVisualType
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $visit = VisitVisualType::where('visit_visual_type_id', $id)->first();
        if($visit) {
            return response()->json(['status' => true, 'data' => $visit]);
        } else {
            return response()->json(['status' => false, 'data' => []]);
        }
    }

    public function statusUpdate(Request $request)
    {
    $this->validate($request, [
        'visit_visual_type_id'     => 'required',
        'visit_visual_type_status' => 'required',
    ]);

      $visit = VisitVisualType::where('visit_visual_type_id', $request->visit_visual_type_id)->first();
      $visit->visit_visual_type_status        = $request->visit_visual_type_status;
      $visit->visit_visual_type_updated_by    = session('user_id');
      $visit->save();

      if ($request->visit_visual_type_status == 2) {
        return response()->json(['status' => true, 'message' => ['title' => 'Visit Visual Type Activated!', 'text' => 'Visit Visual Type ' . $visit->visit_visual_type_name . ' status has been activated!']]);
      } else {
        return response()->json(['status' => true, 'message' => ['title' => 'Visit Visual Type Deactivated!', 'text' => 'Visit Visual Type ' . $visit->visit_visual_type_name . ' status has been deactivated!']]);
      }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\VisitVisualType  $visitVisualType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'visit_visual_type_sort'      => 'required',
            'visit_visual_type_name'      => 'required|max:255',
            'visit_visual_type_reference' => 'required',
          ]);
  
          // visit visual type Status
          $status = !empty($request->visit_visual_type_status) && $request->visit_visual_type_status == 'on' ? 2 : 1;

          $visit = VisitVisualType::where('visit_visual_type_id', $id)->first();
          $visit->visit_visual_type_status            = $status;
          $visit->visit_visual_type_sort              = $request->visit_visual_type_sort;
          $visit->visit_visual_type_name              = $request->visit_visual_type_name;
          $visit->visit_visual_type_reference         = $request->visit_visual_type_reference;
          $visit->visit_visual_type_desc              = $request->visit_visual_type_desc;
          $visit->visit_visual_type_updated_by        = session('user_id');
          $visit->visit_visual_type_updated_date      = Carbon::now()->format('Y-m-d H:i:s');
          $visit->save();
          
          return response()->json(['status' => true, 'message' => ['title' => 'Successfully Updated!', 'text' => 'Visit Visual Type ' . $request->visit_visual_type_name . ' updated successfully!']]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VisitVisualType  $visitVisualType
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $visit = VisitVisualType::where('visit_visual_type_id', $request->visit_visual_type_id)->first();
        $visit->visit_visual_type_status        = '5';
        $visit->visit_visual_type_deleted_by    = session('user_id');
        $visit->visit_visual_type_deleted_date  = Carbon::now()->format('Y-m-d H:i:s');
        $visit->save();

        return response()->json(['status' => true, 'message' => ['title' => 'Visit Visual Type Deleted!', 'text' => 'Visit Type ' . $request->visit_type_name . ' has been deleted!']]);
    }
}
