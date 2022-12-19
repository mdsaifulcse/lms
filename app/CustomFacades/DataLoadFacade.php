<?php
/**
 * Created by PhpStorm.
 * User: mdsaiful
 * Date: 12/23/2019
 * Time: 11:57 AM
 */

namespace App\CustomFacades;
use Illuminate\Support\Facades\Facade;

class DataLoadFacade extends Facade{

    public static function getFacadeAccessor()
    {
        return 'dataload';
    }

}