<?php
/**
 * Created by PhpStorm.
 * User: skings
 * Date: 11/25/17
 * Time: 1:47 PM
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function getUser(){
        $user = Auth::user();
        if(empty($user)){
            return response()->json(['success' => 403, 'message' => 'forbiden']);
        }else{
            return response()->json(['success' => 200, 'user' => $user]);
        }

    }
}