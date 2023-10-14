<?php

namespace App\Http\Controllers\settings;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\Module;
use App\Models\RoleModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $data_module = Module::orderby('module_name', 'ASC')->isActive()->get();

    return view('content.settings.role', [
      'data_module' => $data_module
    ]);
  }

  public function find(Request $request)
  {
    $search = $request->search;
    $roles = Role::orderby('role_name', 'asc')
      ->select('role_id', 'role_name')
      ->where('role_name', 'like', '%' . $search . '%')
      ->where(function($query){
        if (session('role_id') == 1) {
          $query;
        } else if (session('role_id') == 2){
          $query->where('role_id', '=', 3);
        }
      })
      ->isActive()
      ->get();

    $response = array();
    foreach ($roles as $role) {
      $response[] = array(
        "id"    => $role->role_id,
        "text"  => $role->role_name
      );
    }

    return response()->json($response);
  }


  public function datatable(Request $request)
  {
    $columns = [
      0 => 'role_id',
      1 => 'role_name',
      2 => 'role_description',
    ];

    $search = [];
    $totalData = Role::where('role_status', '!=', 5)->count();
    $totalFiltered = $totalData;

    if (!empty($request->input())) {
      $limit = $request->input('length');
      $start = $request->input('start');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');

      if (empty($request->input('search.value'))) {
        $roles = Role::where('role_status', '!=', 5)
          ->offset($start)
          ->limit($limit)
          ->orderBy($order, $dir)
          ->get();
      } else {
        $search = $request->input('search.value');

        $roles = Role::where('role__name', 'LIKE', "%{$search}%")
          ->orWhere('role_description', 'LIKE', "%{$search}%")
          ->where('role_status', '!=', 5)
          ->offset($start)
          ->limit($limit)
          ->orderBy($order, $dir)
          ->get();

        $totalFiltered = Role::where('role__name', 'LIKE', "%{$search}%")
          ->orWhere('role_description', 'LIKE', "%{$search}%")
          ->where('role_status', '!=', 5)
          ->count();
      }
    } else {
      $start = 0;
      $roles = Role::where('role_status', '!=', 5)->get();
    }

    $data = [];

    if (!empty($roles)) {
      $no = $start;
      foreach ($roles as $role) {
        $nestedData['no'] = ++$no;
        $nestedData['role_id'] = $role->role_id;
        $nestedData['role_name'] = $role->role_name;
        $nestedData['role_description'] = $role->role_description;
        $nestedData['role_status'] = $role->role_status;
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
   * @param  \App\Http\Requests\StoreRoleRequest  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'role_name'   => 'required|max:255',
      'modules'     => 'required|array|min:1',
      'modules.*'   => 'integer', // Jika modul harus berupa angka, tambahkan aturan ini
    ]);

    if ($validator->fails()) {
      return response()->json(['status' => false, 'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the field']]);
    }

    // Role Status
    $status = !empty($request->role_status) && $request->role_status == 'on' ? 2 : 1;

    $data = Role::create([
      'role_status'      => $status,
      'role_name'        => $request->role_name,
      'role_description' => $request->role_description,
      'role_created_by'  => session('user_id'),
      'role_created_date'  => Carbon::now()->format('Y-m-d H:i:s'),
    ]);
    $data->save();

    if ($data) {
      $modules = $request->modules;
      if (!empty($modules)) {
        foreach ($modules as $module) {
          $data_module = Module::where('module_id', $module)->first();
          if (!empty($data_module)) {
            $role_module_values = [
              'role_id'                   => $data->role_id,
              'role_module_status'        => 2,
              'module_id'                 => $data_module->module_id,
              'role_module_created_by'    => session('user_id'),
              'role_module_created_date'  => Carbon::now()->format('Y-m-d H:i:s')
            ];
          }
          DB::table('sys_role_module')->insert($role_module_values);
        }
      }
    }

    return response()->json(['status' => true, 'message' => ['title' => 'Successfully created!', 'text' => 'Role created successfully!']]);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Role  $role
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $role = Role::where('role_id', $id)->first();
    if ($role) {
      $role->assign_module = DB::table('sys_role_module')
        ->join('sys_module', 'sys_role_module.module_id', '=', 'sys_module.module_id')
        ->where('sys_role_module.role_id', $role->role_id)
        ->where('sys_module.module_status', '!=', '5')
        ->select('sys_module.*')
        ->get();

      return response()->json(['status' => true, 'data' => $role]);
    } else {
      return response()->json(['status' => false, 'data' => []]);
    }
  }

  public function statusUpdate(Request $request)
  {
    $this->validate($request, [
      'role_status' => 'required',
    ]);

    $role = Role::where('role_id', $request->role_id)->first();
    $role->role_status      = $request->role_status;
    $role->role_updated_by  = session('user_id');
    $role->save();

    if ($request->role_status == 2) {
      return response()->json(['status' => true, 'message' => ['title' => 'Role Activated!', 'text' => 'Role ' . $role->role_name . ' status has been activated!']]);
    } else {
      return response()->json(['status' => true, 'message' => ['title' => 'Role Deactivated!', 'text' => 'Role ' . $role->role_name . ' status has been deactivated!']]);
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\UpdateRoleRequest  $request
   * @param  \App\Models\Role  $role
   * @return \Illuminate\Http\Response
   */

  public function update(Request $request, $id)
  {
    $data_role = DB::table('sys_role')
      ->where('role_id', '=', $id)
      ->first();

    if (!$_POST) {
      return view('content.settings.role', [
        'data_role' => $data_role,
      ]);
    } else {
      $validator = Validator::make($request->all(), [
        'role_name'   => 'required|max:255',
        'modules'     => 'required|array|min:1',
        'modules.*'   => 'integer',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'status' => false,
          'message' => ['title' => 'Validation data required', 'text' => 'Please fill all the fields']
        ]);
      }

      $status = !empty($request->role_status) && $request->role_status == 'on' ? 2 : 1;

      $role = Role::findOrFail($id);
      $role->role_status = $status;
      $role->role_name = $request->role_name;
      $role->role_description = $request->role_description;
      $role->role_updated_by = session('user_id');
      $role->role_updated_date = now()->format('Y-m-d H:i:s');
      $role->save();

      if ($role) {
        $modules = $request->modules;

        // Menghapus data peran-modul yang tidak dipilih kembali
        RoleModule::where('role_id', $id)->whereNotIn('module_id', $modules)->delete();

        // Menyimpan atau memperbarui data peran-modul yang dipilih kembali
        foreach ($modules as $module) {
          $data_module = Module::where('module_id', $module)->first();
          if (!empty($data_module)) {
            $role_module_values = [
              'role_id' => $role->role_id,
              'role_module_status' => 2,
              'module_id' => $data_module->module_id,
              'role_module_created_by' => session('user_id'),
              'role_module_created_date' => now()->format('Y-m-d H:i:s')
            ];
            RoleModule::updateOrCreate([
              'role_id' => $role->role_id,
              'module_id' => $data_module->module_id
            ], $role_module_values);
          }
        }
      }

      return response()->json([
        'status' => true,
        'message' => ['title' => 'Successfully Updated!', 'text' => 'Role ' . $request->role_name . ' updated successfully!']
      ]);
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Role  $role
   * @return \Illuminate\Http\Response
   */
  public function delete(Request $request)
  {
    $role = Role::where('role_id', $request->role_id)->first();
    $role->role_status        = '5';
    $role->role_deleted_by    = session('user_id');
    $role->role_deleted_date  = Carbon::now()->format('Y-m-d H:i:s');
    $role->save();

    return response()->json(['status' => true, 'message' => ['title' => 'Successfully Deleted!', 'text' => 'Role has been deleted!']]);
  }

  /**
   * Untuk assign module. note di pindahkan ke helpers
   *
   */
  //  public function hasModule($pohon = '', $role_id = 0, $module_id = 0)
  //   {
  //       $data_module = DB::table('sys_module')->where('module_status', '!=', '5')->where('module_parent_id', $module_id)->get();
  //       if (!$data_module->isEmpty()) {
  //           $pohon .= "<ul style='padding-left: 20px;list-style: none;'>";
  //           foreach ($data_module as $module) {
  //               $data = null;
  //               $data_parent = null;
  //               if (!empty($role_id)) {
  //                   $data = DB::table('sys_role_module')->where('role_module_status', '!=', '5')->where('role_id', $role_id)->where('module_id', $module->module_id)->first();
  //                   $data_parent = DB::table('sys_role_module')->where('role_module_status', '!=', '5')->where('role_id', $role_id)->where('module_id', $module->module_parent_id)->first();
  //               }
  //               $checked = (!empty($data)) ? "checked='checked'" : '';
  //               $show = (empty($data) && empty($data_parent)) ? "style='display:none'" : '';
  //               $pohon .= "<li ".$show." data-id='".$module->module_id."'>";
  //               $onchange = "$(_module(this,'".$module->module_id."'))";
  //               $pohon .= '<div class="form-check form-check-primary mt-3"><input '.$checked.' type="checkbox" onchange="'.$onchange.'" name="modules[]" class="case_'.$module->module_parent_id.'" value="'.$module->module_id.'">'.$module->module_name."</div>";

  //               $pohon .= $this->hasModule('', $role_id, $module->module_id);
  //               $pohon .= "</li>";
  //           }
  //           $pohon .= "</ul>";
  //       }
  //       return $pohon;
  //   }

}
