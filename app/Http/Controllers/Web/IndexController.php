<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

/**
 * Class IndexController
 *
 * @package App\Http\Controllers\Web
 * @author  Nguyen Tri Thanh <adamnguyen.itdn@gmail.com>
 */
class IndexController extends Controller
{
    /**
     * Index
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('web/welcome');
    }
}
