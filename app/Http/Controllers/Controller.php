<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    public $successStatus = 100;
    public $errorStatus = 200;
    public $unauthorizedStatus = 401;

    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => 'bearer ' . $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ], 200);
    }
}
