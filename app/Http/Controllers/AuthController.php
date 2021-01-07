<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use  App\User;
// use DB;

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
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        try {

            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->roles = 'NormalUser';
            // $user->roles = 'Admin';
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);
            $user->created_by = 'Self';
            // $user->created_by = 'Admin';

            $user->save();

            //return successful response
            return response()->json(['user' => $user, 'message' => 'CREATED, Kindly Verify your email address'], 201);

        } 
        catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Registration Failed!'], 409);
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
          // validate incoming request 
        // $b = DB::select(DB::raw('select * from password_resets'));
        // print_r(json_encode($b));
        // die;
        // $a = DB::select(DB::raw('select * from users'));
        // print_r(json_encode($a));
        // die;
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
    * Request an email verification email to be sent.
    *
    * @param  Request  $request
    * @return Response
    */
    public function emailRequestVerification(Request $request)
    {
        if ( $request->user()->hasVerifiedEmail() ) {
            return response()->json('Email address is already verified.');
        }
    
        $request->user()->sendEmailVerificationNotification();
    
        return response()->json('Email request verification sent to '. Auth::user()->email);
    }
    /**
    * Verify an email using email and token from email.
    *
    * @param  Request  $request
    * @return Response
    */
    public function emailVerify(Request $request)
    {
        $this->validate($request, [
            'token' => 'required|string',
        ]);
        JWTAuth::getToken();
        JWTAuth::parseToken()->authenticate();
        if ( ! $request->user() ) {
            return response()->json('Invalid token', 401);
        }
        if ( $request->user()->hasVerifiedEmail() ) {
            return response()->json('Email address '.$request->user()->getEmailForVerification().' is already verified.');
        }
        $request->user()->markEmailAsVerified();
        return response()->json('Email address '. $request->user()->email.' successfully verified.');
    }
}