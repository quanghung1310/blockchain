<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @return \Illuminate\Http\Request
     */
    protected function request()
    {
        return app('request');
    }
}
