<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use  App\User;

class UserController extends Controller
{
     /**
     * Instantiate a new UserController instance.
     *
     * return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get the authenticated User.
     *
     * return Response
     */
    public function profile()
    {
        // return response()->json(['user' => Auth::user()], 200);
        // if(Auth::user()->email == 'amartya@gmail.com'){
        //     return Auth::user();   
        // }
        return Auth::user();
    }

    /**
     * Get all User.
     *
     * return Response
     */
    public function allUsers()
    {
         return response()->json(['users' =>  User::all()], 200);
    }

    /**
     * Get one user.
     *
     * return Response
     */
    public function singleUser($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json(['user' => $user], 200);

        } catch (\Exception $e) {

            return response()->json(['message' => 'user not found!'], 404);
        }

    }


    public function deleteUser($id)
    {
        try{
            // return Auth::user()->roles;
            if(Auth::user()->roles == 'Admin'){
                $user = User::findOrFail($id);
                $deletedUser= $user;
                $user->delete();

                return response()->json([
                    'user' => $deletedUser,
                    'Message' => 'delete successfull'
                ],200);
            }
            else{
                return response()->json(['message' => 'Only Admin can delete User']);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unable to delete user']);
        }
    }

    // public function getUserByEmail($email){
    //     $user = User::where('email',$email)->first();
    //     return response()->json(['user' => $user],200);
    // }
    public function getUserByEmail(Request $request)
    {
        try {
            $email =$request->input('email');
            $user = User::where('email', $email)->first();

            return response()->json(['user' => $user], 200);

        } catch (\Exception $e) {

            return response()->json(['message' => 'user not found!'], 404);
        }

    }
}