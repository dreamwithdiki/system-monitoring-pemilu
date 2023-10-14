<?php

namespace App\Http\Controllers\settings;

use Carbon\Carbon;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $moduleAll = Module::orderby('module_name', 'ASC')
        ->where('module_status', '!=', '5')
        ->where('module_parent_id', '0')
        ->isActive()
        ->get();

        return view('content.settings.module', [
            'module_all' => $moduleAll
        ]);
    }

    public function dropdown()
    {
      $modules = Module::select('module_id', 'module_name')->get();
      return response()->json($modules);
    }

    public function datatable(Request $request)
    {
        $columns = [
          0 => 'module_id',
          1 => 'module_name',
          2 => 'module_title',
          3 => 'module_icon',
          4 => 'module_class',
          5 => 'module_description',
        ];

        $search = [];
        $totalData = Module::where('module_status', '!=', 5)->count();
        $totalFiltered = $totalData;

        if (!empty($request->input())) {
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
    
            if (empty($request->input('search.value'))) {
              $modules = Module::where('module_status', '!=', 5)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            } else {
              $search = $request->input('search.value');
    
              $modules = Module::where('module_name', 'LIKE', "%{$search}%")
                ->orWhere('module_title', 'LIKE', "%{$search}%")
                ->orWhere('module_class', 'LIKE', "%{$search}%")
                ->where('module_status', '!=', 5)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
    
              $totalFiltered = Module::where('module_name', 'LIKE', "%{$search}%")
                ->orWhere('module_title', 'LIKE', "%{$search}%")
                ->orWhere('module_class', 'LIKE', "%{$search}%")
                ->where('module_status', '!=', 5)
                ->count();
            }
        } else {
          $start = 0;  
          $modules = Module::get();
        }

        $data = [];

        if (!empty($modules)) {
          $no = $start;
          foreach ($modules as $module) {
            $nestedData['no'] = ++$no;
            $nestedData['module_id']          = $module->module_id;
            $nestedData['module_name']        = $module->module_name;
            $nestedData['module_title']       = $module->module_title;
            $nestedData['module_icon']        = $module->module_icon;
            $nestedData['module_class']       = $module->module_class;
            $nestedData['module_description'] = $module->module_description;
            $nestedData['module_status']      = $module->module_status;
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
     * @param  \App\Http\Requests\StoreModuleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
          'module_parent_id'    => 'required',
          'module_sort'         => 'required',
          'module_name'         => 'required|max:128',
          'module_title'        => 'required|max:128',
          'module_icon'         => 'required|max:128',
          'module_class'        => 'required|max:128',
          'module_method'       => 'required|max:128'
        ]);

        // dd($request->all());

        if ($validator->fails()) {
          return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
        }
        
        // Module Status
        $status = !empty($request->module_status) && $request->module_status == 'on' ? 2 : 1;
        
        $module_sort = 100;
        if($request->module_sort != '0' || $request->module_sort != '' || $request->module_sort != ' '){
          $module_sort = $request->module_sort;
        }

        Module::create([
            'module_status'         => $status,
            'module_parent_id'      => $request->module_parent_id,
            'module_sort'           => $module_sort,
            'module_name'           => $request->module_name,
            'module_title'          => $request->module_title,
            'module_icon'           => $request->module_icon,
            'module_class'          => $request->module_class,
            'module_method'         => $request->module_method,
            'module_param'          => $request->module_param,
            'module_description'    => $request->module_description,
            'module_created_by'     => session('user_id'),
            'module_created_date'   => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        return response()->json(['status' => true, 'message' => ['title' => 'Successfully created!', 'text' => 'Module created successfully!']]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Module  $module
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
          $module = Module::where('module_id', $id)->first();
          if($module) {
            return response()->json(['status' => true, 'data' => $module]);
          } else {
            return response()->json(['status' => false, 'data' => []]);
          }
    }

    public function statusUpdate(Request $request)
    {
      $this->validate($request, [
          'module_id'     => 'required',
          'module_status' => 'required',
      ]);

      $module = Module::where('module_id', $request->module_id)->first();
      $module->module_status        = $request->module_status;
      $module->module_updated_by    = session('user_id');
      $module->save();

      if ($request->module_status == 2) {
        return response()->json(['status' => true, 'message' => ['title' => 'Module Activated!', 'text' => 'Module ' . $module->module_name . ' status has been activated!']]);
      } else {
        return response()->json(['status' => true, 'message' => ['title' => 'Module Deactivated!', 'text' => 'Module ' . $module->module_name . ' status has been deactivated!']]);
      }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateModuleRequest  $request
     * @param  \App\Models\Module  $module
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
          'module_parent_id'    => 'required',
          'module_sort'         => 'required',
          'module_name'         => 'required|max:128',
          'module_title'        => 'required|max:128',
          'module_icon'         => 'required|max:128',
          'module_class'        => 'required|max:128',
          'module_method'       => 'required|max:128'
        ]);

        // dd($request->all());

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
        }
        
        // Module Status
        $status = !empty($request->module_status) && $request->module_status == 'on' ? 2 : 1;
        
        $module = Module::where('module_id', $id)->first();
        $module->module_status        = $status;
        $module->module_parent_id     = $request->module_parent_id;
        $module->module_sort          = $request->module_sort;
        $module->module_name          = $request->module_name;
        $module->module_title         = $request->module_title;
        $module->module_icon          = $request->module_icon;
        $module->module_class         = $request->module_class;
        $module->module_method        = $request->module_method;
        $module->module_param         = $request->module_param;
        $module->module_description   = $request->module_description;
        $module->module_updated_by    = session('user_id');
        $module->module_updated_date  = Carbon::now()->format('Y-m-d H:i:s');
        $module->save();

        return response()->json(['status' => true, 'message' => ['title' => 'Successfully Updated!', 'text' => 'Module updated successfully!']]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Module  $module
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $module = Module::where('module_id', $request->module_id)->first();
        $module->module_status        = '5';
        $module->module_deleted_by    = session('user_id');
        $module->module_deleted_date  = Carbon::now()->format('Y-m-d H:i:s');
        $module->save();

        return response()->json(['status' => true, 'message' => ['title' => 'Successfully Deleted!', 'text' => 'Module has been deleted!']]);
    }

    // public function hasChild($pohon='', $parent_id=0, $module_id=0, $level=0) {
    //     $data_module = DB::table('sys_module')
    //                     ->where('module_status', '!=', '5')
    //                     ->where('module_parent_id', $module_id)
    //                     ->orderBy('module_name', 'ASC')
    //                     ->get();
    //     if(!empty($data_module)) {
    //         ++$level;
    //         foreach($data_module as $module) {
    //             $separator = $selected ='';
    //             for($i=0;$i<$level;$i++){
    //                 $separator .= '----';
    //             }
    //             if($parent_id == $module->module_id) {
    //                 $selected = 'selected="selected"';
    //             }
    //             $pohon .= "<option ".$selected." value='".$module->module_id."' >".$separator.'&nbsp;'.$module->module_name."</option>";
    //             $pohon .= $this->hasChild('', $parent_id, $module->module_id, $level);
    //         }
    //     }
    //     return $pohon;
    // }
    
}
