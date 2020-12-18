<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {
   // Matches "/api/register
   // name,email,password and password confirmation is required for register 
   // it registers users to database but user have to first verify his/her email id
   // a verification mail is sent but if user forgot to verify they can verify using the 
   // /api/email/request-verification 
   $router->post('register', 'AuthController@register');

   // Matches "/api/login
   // requires email and password 
   // gives a token
   // if verification is not done then first verify by the verification mail set to email id
   $router->post('login', 'AuthController@login');

   //send verification email to registered email id
   //requires token which we get during login
   $router->post('/email/request-verification', ['as' => 'email.request.verification', 'uses' => 'AuthController@emailRequestVerification']);

   //api/email/verify
   //verify email 
   //requires token which we get in email or click on the link on verification mail
   $router->post('/email/verify', ['as' => 'email.verify', 'uses' => 'AuthController@emailVerify']);

   //matches api/password/reset-request
   //sends reset link to email id which also contain token in the link
   //requires email
   $router->post('/password/reset-request', 'RequestPasswordController@sendResetLinkEmail');

   //matches api/password/reset
   //resets password
   //use token in the link or directly use the link to reset password
   //requires new password and password confirmation
   $router->post('/password/reset', [ 'as' => 'password.reset', 'uses' => 'ResetPasswordController@reset' ]);



});

$router->group(['middleware' => ['auth', 'verified']], function() use ($router){
   
   // Matches "/api/profile
   // requires token which we get during login
   // shows profile of user
   $router->get('/api/profile', 'UserController@profile');
   
   // Matches "/api/users 
   // requires token which we get during login
   // gives list of all users 
   $router->get('/api/users', 'UserController@allUsers');

   // Matches "/api/users/1
   // requires token which we get during login 
   // gives one user by id
   $router->get('/api/users/{id}', 'UserController@singleUser');

   //Matches /api/delete/1
   //requires token for user and id of user which we are going to delete
   $router->get('/api/delete/{id}', 'UserController@deleteUser');

   //matches /api/getUser
   //requires two parameters token and user email
   //different than /api/users/id
   $router->get('/api/getUser', 'UserController@getUserByEmail');

   //matches /api/register/admin
   //requires bearer token of admin and details of new user
   //only admin can create user
   $router->post('/api/register/admin', 'UserController@registerUser');

   //matches /api/restore/{id}
   //only admin can restore user 
   $router->patch('/api/restore/{id}', 'UserController@restoreUser');


   $router->get('/api/getalluser', 'UserController@getalluser');

});
