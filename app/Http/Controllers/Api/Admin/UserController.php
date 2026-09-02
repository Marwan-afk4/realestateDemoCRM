<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function getUsers()
    {
        $users = User::all();
        return response()->json(['users' => $users]);
    }

    public function deleteuser($id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function getAdmins()
    {
        $admins = User::where('role', 'admin')->get();
        return response()->json(['admins' => $admins]);
    }

    public function addAdmin(Request $request){
        $validation=Validator::make(request()->all(),[
            'first_name'=>'required',
            'last_name'=>'required',
            'email'=>'required|email|unique:users',
            'phone'=>'required|unique:users',
            'password'=>'required|min:6',
        ]);
        if($validation->fails()){
            return response()->json(['error'=>$validation->errors()],401);
        }
        $admin=User::create([
            'first_name' =>$request->first_name,
            'last_name'=>$request->last_name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'password'=>Hash::make($request->password),
            'role'=>'admin'
        ]);
        return response()->json(['message'=>'Admin Added Successfully','admin'=>$admin]);

    }

    public function deleteadmin($id){
        $user = User::find($id);
        $user->delete();
        return response()->json(['message' => 'Admin deleted successfully']);
    }
}
