<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Validator;

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
        // unique field with model
        Validator::extend('unique_field', function ($attribute, $value, $parameters, $validator) {
            $model="\\App\\Models\\" . ucfirst($parameters[0]);
            $where=[];
            $where[]=[$attribute, $value];
            if (isset($parameters[1])) {
                $where[]=[$parameters[1], '!=', $parameters[2]];
            }
            $column=$model::where($where)->first();
            if ($column) {
                return false;
            }
            return true;
        }, "The :attribute must be unique.");
    }
}
