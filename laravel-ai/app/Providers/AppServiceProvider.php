<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        RateLimiter::for('admin-login', function (Request $request): Limit {
            $email = Str::lower((string) $request->input('email', 'guest'));

            return Limit::perMinute(5)
                ->by($email.'|'.$request->ip());
        });

        // API login: rate limit per email (không per-IP) để load test từ 1 máy hoạt động đúng.
        // Mỗi email chỉ được login 10 lần/phút — bảo vệ credential stuffing mà không block load test.
        RateLimiter::for('api-login', function (Request $request): Limit {
            $email = Str::lower((string) $request->input('email', 'guest'));

            return Limit::perMinute(10)->by('login|'.$email);
        });

        // Authenticated API: rate limit per user ID (không per-IP)
        RateLimiter::for('api', function (Request $request): Limit {
            return $request->user()
                ? Limit::perMinute(120)->by('user|'.$request->user()->id)
                : Limit::perMinute(30)->by('guest|'.$request->ip());
        });
    }
}
