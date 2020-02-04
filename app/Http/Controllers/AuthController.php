<?php

namespace App\Http\Controllers;

use App\models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {
        //validate incoming request
        $validate = Validator::make($request->all(),[
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);
        if ($validate->fails()) {
            $res = [
                'message'       => $validate->messages(),
                'response_code' => $this->errorStatus,
            ];
            return response()->json($res);
        }

        try {
            $user = new User;
            $user->first_name = $request->input('first_name');
            $user->middle_name = $request->input('middle_name');
            $user->last_name = $request->input('last_name');
            $user->email = $request->input('email');
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);

            $user->save();

            //return successful response
            return response()->json(['user' => $user, 'message' => 'New user registered successfully!', 'response_code' => $this->successStatus]);

        } catch (\Exception $e) {
            //return error message
            Log::error($e->getMessage());
            return response()->json(['message' => 'User Registration Failed!', 'response_code' => $this->errorStatus]);
        }

    }

    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Email and password do not match!', 'response_code' => $this->unauthorizedStatus]);
        }

        return $this->respondWithToken($token);
    }

    public function checkValidEmail(Request $request){
        $validate = Validator::make($request->all(),[
            'email' => 'required|unique:users'
        ]);
        if ($validate->fails()) {
            $res = [
                'message'       => $validate->messages(),
                'response_code' => $this->errorStatus,
            ];
            return response()->json($res);
        }
        return response()->json(['response_code', $this->successStatus]);
    }
}
