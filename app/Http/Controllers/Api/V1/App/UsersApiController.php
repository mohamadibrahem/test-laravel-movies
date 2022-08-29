<?php

namespace App\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\Admin\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UsersApiController extends Controller
{
    use MediaUploadingTrait;

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => ['These credentials do not match our records.']
            ], 404);
        }

        if(!$user->verified)
        {
            $response = [
                'message' => 'يرجى تأكيد حسابك أولا'
            ];
        
            //return response($response, 404);
        }
    
        $token = $user->createToken('app')->accessToken;
    
        $response = [
            'user' => $user,
            'token' => $token,
            'message' => 'تم تسجيل الدخول بنجاح'
        ];
    
        return response($response, 201);
    }


    public function register(Request $request)
    {
        request()->validate([
            'name' => 'required|min:3|max:50',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|max:20',                
            'password_confirmation' => 'required|min:6|max:20|same:password',
        ], [
            'name.required' => 'Name is required',
            'name.min' => 'Name must be at least 2 characters.',
            'name.max' => 'Name should not be greater than 50 characters.',
        ]);
    
        
        $user = User::create($request->all());
        $user->roles()->sync(2);

        if ($request->input('photo', false)) {
            $user->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
        }

        return (new UserResource($user,['message' => 'done']))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

}
