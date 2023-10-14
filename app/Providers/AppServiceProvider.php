<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    //
  }

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    Validator::extend('user_email', function ($attribute, $value, $parameters, $validator) {
      // Implement your custom email validation logic here
      // You can use $attribute to access the field name ('user_email')
      // $value contains the value entered by the user

      // Example implementation:
      // return strpos($value, 'example.com') !== false;

      // You can also make use of the DNS validation provided by Laravel:
      return Validator::make([$attribute => $value], [
          $attribute => 'email:dns',
      ])->passes();
  });
  }
}
