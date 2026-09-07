<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Brocker;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class HomePageController extends Controller
{
    protected $updateUser= ['first_name','last_name','email','phone','age','governce','password','experience_year','qualification'];

    public function homepage(Request $request){
        $user =$request->user();
        $ads= Ad::all();
        $brocker = null;
        if($user->role == 'brocker'){
            $brocker = Brocker::where('user_id',$user->id)->first();
        }
        return response()->json([
            'ads'=>$ads,
            'brocker'=>$brocker
        ]);
    }

    public function profile(Request $request){
        $user = $request->user();
        return response()->json([
            'user'=>$user
        ]);
    }

    public function deleteProfile(Request $request){
        $user_id = $request->user();
        $user_id->delete();
        return response()->json(['message' => 'Profile deleted successfully']);
    }

    public function UpdateProfile(Request $request){
        $user_id = $request->user()->id;
        $user = User::find($user_id);

        $validation = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => [
                'sometimes',
                'string',
                'max:30',
                Rule::unique('users', 'phone')->ignore($user->id),
            ],
            'age' => 'sometimes|nullable|integer',
            'governce' => 'sometimes|nullable|string|max:255',
            'password' => 'sometimes|string|min:6',
            'experience_year' => 'sometimes|nullable|integer',
            'qualification' => 'sometimes|nullable|string|max:255',
        ]);

        if ($validation->fails()) {
            return response()->json(['message' => $validation->errors()], 422);
        }

        $updateprofile = $request->only('first_name','last_name','email','phone','age','governce','password','experience_year','qualification');
        $user->first_name = $updateprofile['first_name']??$user->first_name;
        $user->last_name = $updateprofile['last_name']??$user->last_name;
        $user->email = $updateprofile['email']??$user->email;
        $user->phone = $updateprofile['phone']??$user->phone;
        $user->age = $updateprofile['age']??$user->age;
        $user->governce = $updateprofile['governce']??$user->governce;
        $user->password = $updateprofile['password']??$user->password;
        $user->experience_year = $updateprofile['experience_year']??$user->experience_year;
        $user->qualification = $updateprofile['qualification']??$user->qualification;
        $user->save();

        return response()->json([
            'message'=>'Profile updated successfully',
            'user'=>$user
        ]);

    }
}
