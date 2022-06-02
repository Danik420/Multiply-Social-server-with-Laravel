<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();
        //

//        // 토큰 만료시간 설정
//        Passport::tokensExpireIn(now()->addDays(15));
//        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
//        // 리프레시 토큰 만료시간
//        Passport::refreshTokensExpireIn(now()->addDays(30));
    }
}
