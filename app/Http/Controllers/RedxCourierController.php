<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Codeboxr\RedxCourier\Facade\RedxCourier;




class RedxCourierController extends Controller
{
    public function get_area_list()
    {
        // dd('get_area_list');
        // dd(RedxCourier::area()->list());
        // $area_list = RedxCourier::area()->list();
        return  RedxCourier::area()->list();
    }

}
