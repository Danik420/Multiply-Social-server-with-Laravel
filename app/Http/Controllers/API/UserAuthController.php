<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;

class UserAuthController extends Controller
{
    /**
     * Registration Req
     */

    // 회원가입
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|min:2|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        // 필수입력값들에 대한 유효성 검사
//        if ($data->fails()) {
//            return response()->json([
//                'error' => $data->errors()->all()
//            ], \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
//        }

        // 사용자 생성
        $data['password'] = bcrypt($request->password);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $data['password'],
            'profile_image_url' => $request->profileImageUrl,
            'phone_Number' => $request->phoneNumber,
            //'email_token' => mt_rand(100000, 999999),
        ]);

        // event(new Registered($user));

        //

        $token = $user->createToken('API Token')->accessToken;

        Auth::login($user);

        return
        response()->json(
            [
                'user' => auth()->user(),
                'token' => $token
// 이런 식으로도 할 수 있는데 로그인이랑 양식 통일시켜야 Swift에서 Response 하나로 받기 편함
//                'name' => $user->name,
//                'email' => $user->email,
//                'token' => $token
            ], 200);

    }



    /**
     * Login Req
     */

    // 로그인
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        $user = User::whereEmail($request->email)->first();

        if (!Auth::attempt($data)){
            return response()->json([
                'message' => '유효하지 않은 로그인 정보입니다.'
            ], Response::HTTP_UNAUTHORIZED);
        }
//        if (!auth()->attempt($data)) {
//            return response(['error_message' => 'Incorrect Details.
//            Please try again'], 401);
//        }

        $token = $user->createToken('API Token')->accessToken;

        Auth::login($user);

        return response()->json(
            [
                'user' => auth()->user(),
                'token' => $token
            ], 200);

//        다른 방법
//        if (auth()->attempt($data)) {
//            $token = auth()->user()->createToken('API Token')->accessToken;
//            return response()->json(['token' => $token], 200);
//        } else {
//            return response()->json(['error' => 'Unauthorised'], 401);
//        }
    }

    // 로그아웃
    public function logout(Request $request)
    {
        $token = Auth::user()->token();
        $token->revoke();
        return response()->json([ 'message' => 'Good bye', ], 200);
    }

    // 전체 유저 확인
    public function fetchUsers(){
        return User::all();
    }

    // 현재 유저 확인
    public function currentUserInfo()
    {
        return response()->json([
            'user' => auth()->user()
        ], Response::HTTP_OK);
    }

    // 유저 정보 수정
    public function update(Request $request, $id)
    {

        $user = User::find($id);

        if ($request->hasfile('profile_image_url')) {
            $uploadFile = $request->file('profile_image_url');
            $fileHashName = time() . '-' . 'profile_image_' . 'of_user:' . $user->id . '.png';
            // $uploadFile->getClientOriginalName();
            $filePath = $uploadFile->storeAs('public/profile_images', $fileHashName);
            $profile_image_url = Storage::disk('local')->url($filePath);
            $user->profile_image_url = $profile_image_url ?? '';
        }

//        $user->update($request->all());
        $user->profile_image_url = $profile_image_url;
        $user->save();

        return response()->json([
            'status'=>200,
            'data'=>$user
        ]);
    }
}



/*
DB::beginTransacion();

try {
    $user = $this->create($request->all());
    // After creating the user send an email with the random token generated in the create method above
    $email = $user(['email_token' => $user->email_token, 'name' => $user->name]);
    Mail::to($user->email)->send($email);
    DB::commit();

    //Flash::message('Thanks for signing up! Please check your email.');
} catch (Exception $e) {
    DB::rollback();
    return back();
}

return response()->json(
    [
        'message' => 'User Registered, need verification',
        'user' => $user
    ], 200
);
}

public function verify($token)
{
    //chained to model
    try {
        User::where('email_token', $token)->firstOrFail()->verified();
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return Response::make('Not found', 404);
    }

    return redirect('post');
*/
