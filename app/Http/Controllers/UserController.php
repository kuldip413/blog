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
        // try{
        // return Auth::user()->roles;
        if(Auth::user()->roles == 'Admin'){
            $delete = User::destroy($id);
            // $deletedUser= $user;
            // $user->delete();
            if($delete){
                return response()->json([
                    'Message' => 'delete successfull'
                ],200);
            }
            else{
                return response()->json([
                    'Message' => 'Not Found'
                ]);
            }

            // return response()->json([
            //     'user' => $deletedUser,
            //     'Message' => 'delete successfull'
            // ],200);
        }
        else{
            return response()->json(['message' => 'Only Admin can delete User']);
        }
        // } catch (\Exception $e) {
        //     return response()->json(['message' => 'Unable to delete user']);
        // }
    }


    public function restoreUser($id){
        if(Auth::user()->roles == 'Admin'){
            $recycle =User::onlyTrashed()->find($id);
            if(!is_null($recycle)){
                $recycle->restore();
                return response()->json([
                    'Message' => 'successfully Restored'
                ]);
            }
            else{
                return response()->json([
                    'Message' => 'Not Found'
                ]);
            } 

        }
        else{
            return response()->json(['message' => 'Only Admin can restore User']);
        }
    }

    public function getalluser(){
        if(Auth::user()->roles == 'Admin'){
            return response()->json(['users' =>  User::withTrashed()->get()], 200); 
        }
        else{
            return response()->json(['message' => 'Only Admin can see all users in databse']);
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

    public function registerUser(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        try {
            if(Auth::user()->roles == 'Admin'){
                $user = new User;
                $user->name = $request->input('name');
                $user->email = $request->input('email');
                $user->roles = 'NormalUser';
                $plainPassword = $request->input('password');
                $user->password = app('hash')->make($plainPassword);
                $user->created_by = 'Admin';
                $user->save();

                //return successful response
                return response()->json(['user' => $user, 'message' => 'CREATED'], 201);
            }
            else{
                return response()->json(['message' => 'you do not have the permission to create user']);
            }   

        } 
        catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Registration Failed!'], 409);
        }
    }
}