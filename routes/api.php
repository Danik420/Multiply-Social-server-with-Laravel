<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserAuthController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\VerificationController;
use App\Http\Controllers\API\PasswordResetController;
use App\Http\Controllers\API\NewPasswordController;

use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::prefix('/user')->group(function () {
    // 회원 가입(메일 전송)
    Route::post('/register', [UserAuthController::class, 'register'])->name('user.register');
    // 인증 버튼
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['signed'])->name('user.verification.verify');
    // 메일 재전송
    Route::post('/email/verification-notification', [VerificationController::class, 'resendNotification'])
        ->name('user.verification.send');
    // 비밀번호 잊어버리셨나요
    Route::post('/forgot-password', [PasswordResetController::class, 'forgot'])
        ->name('user.password.reset');
    // 비밀번호 변경
    Route::post('/reset-password', [NewPasswordController::class, 'reset'])
        ->name('user.password.update');
    // 로그인
    Route::post('/login', [UserAuthController::class, 'login'])->name('user.login');

    // 포스팅 및 댓글 기능 및 my auth:api 미들웨어로 옮길 예정 @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    // 포스팅 CRUD
    Route::prefix('/post')->group(function () {
        Route::get('/', [PostController::class, 'index']);
        Route::post('/', [PostController::class, 'store']);
        Route::delete('/{id}', [PostController::class, 'destroy']);
        Route::put('/{id}', [PostController::class, 'update']);
    });
    // 댓글 CRUD
    Route::prefix('/comment')->group(function () {
        Route::get('/{id}', [CommentController::class, 'index']);
        Route::post('/{id}', [CommentController::class, 'store']);
        Route::delete('/{id}', [CommentController::class, 'destroy']);
        Route::put('/{id}', [CommentController::class, 'update']);
    });



    // 리프레시 토큰 - 개발하는 정대리 https://www.youtube.com/watch?v=HHBkRb-Aclw
    // Route::post('/token-refresh', [AuthController::class, 'tokenRefresh'])->name('user.token-refresh');

    // 인증 처리가 된
    Route::middleware('auth:api')->group(function () {

        Route::prefix('/my')->group(function () {
            // 유저 확인
            Route::get('/', [UserAuthController::class, 'currentUserInfo'])->name('user.info');
            // 내가 쓴 글
            Route::get('/post/{id}', [PostController::class, 'myPost']);
            // 내가 쓴 댓글
            Route::get('/comment/{id}', [CommentController::class, 'myComment']);
        });

        // 로그아웃
        Route::post('/logout', [UserAuthController::class, 'logout'])->name('user.logout');
        // 유저 업데이트
        Route::post('/update/{id}', [UserAuthController::class, 'update'])->name('user.update');
        // 프로필 사진 삭제
        Route::delete('/delete/profile_image/{id}', [UserAuthController::class, 'deleteProfileImage'])->name('user.deleteProfileImage');
        // 유저 목록
        Route::get('/all', [UserAuthController::class, 'fetchUsers'])->name('user.all');
    });
});

/*
Route::middleware('auth:api', 'verified')->get('user', function (Request $request) {
    return $request->user();
});

// 포스팅 관련
Route::get('post', [PostController::class, 'index']);//->middleware(['auth', 'verified']);
Route::post('post', [PostController::class, 'store']);
Route::delete('post/{id}', [PostController::class, 'destroy']);
Route::put('post/{id}', [PostController::class, 'update']);




// 회원 가입(메일 전송)
Route::post('register', [UserAuthController::class, 'register']);

// 인증 버튼
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed'])->name('verification.verify');

// 메일 재전송
Route::post('/email/verification-notification', [VerificationController::class, 'resendNotification'])
    ->name('verification.send');

// 로그인 로그아웃
Route::post('login', [UserAuthController::class, 'login']);
Route::post('logout', [UserAuthController::class, 'logout']);
*/

