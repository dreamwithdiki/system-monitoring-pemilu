<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   *
   * @return void
   */
  public function register()
  {
    //
  }

  /**
   * Bootstrap services.
   *
   * @return void
   */
  public function boot()
  {
    $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalMenu.json'));
    $verticalMenuData = json_decode($verticalMenuJson);
    $horizontalMenuJson = file_get_contents(base_path('resources/menu/horizontalMenu.json'));
    $horizontalMenuData = json_decode($horizontalMenuJson);
    $timsesMenuJson = file_get_contents(base_path('resources/menu/timses.json'));
    $verticalMenuTimsesData = json_decode($timsesMenuJson);
    $timdptMenuJson = file_get_contents(base_path('resources/menu/timdpt.json'));
    $verticalMenuTimdptData = json_decode($timdptMenuJson);
    $saksiMenuJson = file_get_contents(base_path('resources/menu/saksi.json'));
    $verticalMenuSaksiData = json_decode($saksiMenuJson);

    // Share all menuData to all the views
    \View::share('menuData', [$verticalMenuData, $horizontalMenuData, $verticalMenuTimsesData, $verticalMenuTimdptData, $verticalMenuSaksiData ]); 
  }
}
