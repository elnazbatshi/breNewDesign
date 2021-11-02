<?php

namespace App\Http\Controllers;

use App\Models\wdp_User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function users()
    {
        return view('users');
    }

    public function setUser(Request $request)
    {

        $name = $request->input('name');
        $displayName = $request->input('displayName');
        $email = $request->input('email');
        $pass = $request->input('pass');

        wdp_User::insert([
            'user_login' => $name,
            'user_pass' => Hash::make($pass),
            'user_email' => $email,
            'display_name' => $displayName,
            'user_registered' => Carbon::now(),


            ]);
        return response(['response' => true]);
    }

    public function getUsers(Request $request)
    {
        $query = wdp_User::query();
        if ($request->filter != '') {
            $query->Where('user_login', 'like', '%' . $request->filter . '%')
                ->orWhere('display_name', 'like', '%' . $request->filter . '%');
        }
        $users = $query->get();
        return response(['response' => true, 'users' => $users]);
    }

    public function deleteUser($id)
    {
        $user = wdp_User::where('ID',$id)->delete();
        return response(['response' => true, 'users' => $user]);
    }

    public function updateUser(Request $request, $id)
    {

        $request->validate([
            'name' => 'required|max:255',
            'displayName' => 'required|max:255',
            'email' => 'required|email|max:255',
        ]);
        $user = wdp_User::where('ID',$id);
        $user->timestamps = false;
        $user->update([
            'user_login' => $request->name,
            'user_email' => $request->email,
            'display_name' => $request->displayName,

        ]);
        return response(["status" => true]);
    }

    public function changePass(Request $request,$id)
    {
        $user_pass=$request->input('pass');
        $user = wdp_User::where('ID',$id);
        $user->timestamps = false;
        $user->update([
            'user_pass' => Hash::make($user_pass),
        ]);
        return response(["status" => true]);
    }

}
