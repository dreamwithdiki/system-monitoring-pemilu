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
    $partnerMenuJson = file_get_contents(base_path('resources/menu/partner.json'));
    $verticalMenuPartnerData = json_decode($partnerMenuJson);
    $adminMenuJson = file_get_contents(base_path('resources/menu/admin.json'));
    $verticalMenuAdminData = json_decode($adminMenuJson);

    // Share all menuData to all the views
    \View::share('menuData', [$verticalMenuData, $horizontalMenuData, $verticalMenuAdminData, $verticalMenuPartnerData ]); 
  }
}
