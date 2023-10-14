<?php

namespace App\Helpers;

use DB;
use Config;
use Illuminate\Support\Str;

class Helpers
{
  public static function appClasses()
  {

    $data = config('custom.custom');


    // default data array
    $DefaultData = [
      'myLayout' => 'vertical',
      'myTheme' => 'theme-default',
      'myStyle' => 'light',
      'myRTLSupport' => true,
      'myRTLMode' => true,
      'hasCustomizer' => true,
      'showDropdownOnHover' => true,
      'displayCustomizer' => true,
      'menuFixed' => true,
      'menuCollapsed' => false,
      'navbarFixed' => true,
      'footerFixed' => false,
      'menuFlipped' => false,
      // 'menuOffcanvas' => false,
      'customizerControls' => [
        'rtl',
        'style',
        'layoutType',
        'showDropdownOnHover',
        'layoutNavbarFixed',
        'layoutFooterFixed',
        'themes',
      ],
      //   'defaultLanguage'=>'en',
    ];

    // if any key missing of array from custom.php file it will be merge and set a default value from dataDefault array and store in data variable
    $data = array_merge($DefaultData, $data);

    // All options available in the template
    $allOptions = [
      'myLayout' => ['vertical', 'horizontal', 'blank'],
      'menuCollapsed' => [true, false],
      'hasCustomizer' => [true, false],
      'showDropdownOnHover' => [true, false],
      'displayCustomizer' => [true, false],
      'myStyle' => ['light', 'dark'],
      'myTheme' => ['theme-default', 'theme-bordered', 'theme-semi-dark'],
      'myRTLSupport' => [true, false],
      'myRTLMode' => [true, false],
      'menuFixed' => [true, false],
      'navbarFixed' => [true, false],
      'footerFixed' => [true, false],
      'menuFlipped' => [true, false],
      // 'menuOffcanvas' => [true, false],
      'customizerControls' => [],
      // 'defaultLanguage'=>array('en'=>'en','fr'=>'fr','de'=>'de','pt'=>'pt'),
    ];

    //if myLayout value empty or not match with default options in custom.php config file then set a default value
    foreach ($allOptions as $key => $value) {
      if (array_key_exists($key, $DefaultData)) {
        if (gettype($DefaultData[$key]) === gettype($data[$key])) {
          // data key should be string
          if (is_string($data[$key])) {
            // data key should not be empty
            if (isset($data[$key]) && $data[$key] !== null) {
              // data key should not be exist inside allOptions array's sub array
              if (!array_key_exists($data[$key], $value)) {
                // ensure that passed value should be match with any of allOptions array value
                $result = array_search($data[$key], $value, 'strict');
                if (empty($result) && $result !== 0) {
                  $data[$key] = $DefaultData[$key];
                }
              }
            } else {
              // if data key not set or
              $data[$key] = $DefaultData[$key];
            }
          }
        } else {
          $data[$key] = $DefaultData[$key];
        }
      }
    }
    //layout classes
    $layoutClasses = [
      'layout' => $data['myLayout'],
      'theme' => $data['myTheme'],
      'style' => $data['myStyle'],
      'rtlSupport' => $data['myRTLSupport'],
      'rtlMode' => $data['myRTLMode'],
      'textDirection' => $data['myRTLMode'],
      'menuCollapsed' => $data['menuCollapsed'],
      'hasCustomizer' => $data['hasCustomizer'],
      'showDropdownOnHover' => $data['showDropdownOnHover'],
      'displayCustomizer' => $data['displayCustomizer'],
      'menuFixed' => $data['menuFixed'],
      'navbarFixed' => $data['navbarFixed'],
      'footerFixed' => $data['footerFixed'],
      'menuFlipped' => $data['menuFlipped'],
      // 'menuOffcanvas' => $data['menuOffcanvas'],
      'customizerControls' => $data['customizerControls'],
    ];

    // sidebar Collapsed
    if ($layoutClasses['menuCollapsed'] == true) {
      $layoutClasses['menuCollapsed'] = 'layout-menu-collapsed';
    }

    // Menu Fixed
    if ($layoutClasses['menuFixed'] == true) {
      $layoutClasses['menuFixed'] = 'layout-menu-fixed';
    }

    // Navbar Fixed
    if ($layoutClasses['navbarFixed'] == true) {
      $layoutClasses['navbarFixed'] = 'layout-navbar-fixed';
    }

    // Footer Fixed
    if ($layoutClasses['footerFixed'] == true) {
      $layoutClasses['footerFixed'] = 'layout-footer-fixed';
    }

    // Menu Flipped
    if ($layoutClasses['menuFlipped'] == true) {
      $layoutClasses['menuFlipped'] = 'layout-menu-flipped';
    }

    // Menu Offcanvas
    // if ($layoutClasses['menuOffcanvas'] == true) {
    //   $layoutClasses['menuOffcanvas'] = 'layout-menu-offcanvas';
    // }

    // RTL Supported template
    if ($layoutClasses['rtlSupport'] == true) {
      $layoutClasses['rtlSupport'] = '/rtl';
    }

    // RTL Layout/Mode
    if ($layoutClasses['rtlMode'] == true) {
      $layoutClasses['rtlMode'] = 'rtl';
      $layoutClasses['textDirection'] = 'rtl';
    } else {
      $layoutClasses['rtlMode'] = 'ltr';
      $layoutClasses['textDirection'] = 'ltr';
    }

    // Show DropdownOnHover for Horizontal Menu
    if ($layoutClasses['showDropdownOnHover'] == true) {
      $layoutClasses['showDropdownOnHover'] = 'true';
    } else {
      $layoutClasses['showDropdownOnHover'] = 'false';
    }

    // To hide/show display customizer UI, not js
    if ($layoutClasses['displayCustomizer'] == true) {
      $layoutClasses['displayCustomizer'] = 'true';
    } else {
      $layoutClasses['displayCustomizer'] = 'false';
    }

    return $layoutClasses;
  }

  public static function updatePageConfig($pageConfigs)
  {
    $demo = 'custom';
    if (isset($pageConfigs)) {
      if (count($pageConfigs) > 0) {
        foreach ($pageConfigs as $config => $val) {
          Config::set('custom.' . $demo . '.' . $config, $val);
        }
      }
    }
  }

  /**
   * Untuk parent module.
   *
   */
  public static function hasChild($pohon='', $parent_id=0, $module_id=0, $level=0)
    {
        $data_module = DB::table('sys_module')
                        ->where('module_status', '!=', '5')
                        ->where('module_parent_id', $module_id)
                        ->orderBy('module_name', 'ASC')
                        ->get();

        if (!empty($data_module)) {
            ++$level;
            foreach ($data_module as $module) {
                $separator = $selected ='';
                for ($i = 0; $i < $level; $i++) {
                    $separator .= '----';
                }
                if ($parent_id == $module->module_id) {
                    $selected = 'selected="selected"';
                }
                $pohon .= "<option ".$selected." value='".$module->module_id."' >".$separator.'&nbsp;'.$module->module_name."</option>";
                $pohon .= self::hasChild('', $parent_id, $module->module_id, $level);
            }
        }
        return $pohon;
    }


    /**
     * Untuk assign module.
     *
     */
    public static function hasModule($pohon = '', $role_id = 0, $module_id = 0)
    {
        $data_module = DB::table('sys_module')->where('module_status', '!=', '5')->where('module_parent_id', $module_id)->get();
        if (!$data_module->isEmpty()) {
            $pohon .= "<ul style='padding-left: 20px;list-style: none;'>";
            foreach ($data_module as $module) {
                if (!empty($role_id)) {
                    $data = DB::table('sys_role_module')
                        ->join('sys_module', 'sys_role_module.module_id', '=', 'sys_module.module_id')
                        ->where('sys_role_module.role_module_status', '!=', '5')
                        ->where('sys_role_module.role_id', $role_id)
                        ->where('sys_module.module_id', $module->module_id)
                        ->first();
                    $data_parent = DB::table('sys_role_module')
                        ->join('sys_module', 'sys_role_module.module_id', '=', 'sys_module.module_id')
                        ->where('sys_role_module.role_module_status', '!=', '5')
                        ->where('sys_role_module.role_id', $role_id)
                        ->where('sys_module.module_id', $module->module_parent_id)
                        ->first();
                }
                $checked = (!empty($data)) ? "checked='checked'" : '';
                $show = (empty($data) && empty($data_parent)) ? "style='display:none'" : '';
                $pohon .= "<li ".$show." data-id='".$module->module_id."'>";
                $onchange = "$(_module(this,'".$module->module_id."'))";
                $pohon .= '<div class="form-check form-check-primary mt-3"><input '.$checked.' type="checkbox" onchange="'.$onchange.'" name="modules[]" class="case_'.$module->module_parent_id.' form-check-input checkbox-item" value="'.$module->module_id.'" required><label class="form-check-label">'.$module->module_name."</label></div>";
                
                $pohon .= self::hasModule('', $role_id, $module->module_id);
                $pohon .= "</li>";
            }
            $pohon .= "</ul>";
        }
        return $pohon;
    }


}
