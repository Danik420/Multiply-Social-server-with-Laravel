<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    // 수정 필요할 수도
    public function verify($id, $hash)
    {
        $user = User::find($id);
        abort_if(!$user, 403);
        abort_if(!hash_equals($hash, sha1($user->getEmailForVerification())), 403);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        return ['message' => 'OK.']; //view('verified-account');
    }

    public function resendNotification(Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return response()->json(['status' => '인증메일 전송 완료']);
    }
}
