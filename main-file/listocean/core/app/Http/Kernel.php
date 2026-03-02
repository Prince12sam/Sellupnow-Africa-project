<?php

namespace App\Http;

use App\Http\Middleware\guestMediaUploadCheck;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // Block /update always; allow /install only when INSTALLER_ENABLED=true
        \App\Http\Middleware\BlockSetupWizards::class,
        // Legacy membership IPN routes are disabled by default unless explicitly enabled.
        \App\Http\Middleware\LockLegacyMembershipIpn::class,
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \Xgenious\Installer\Http\Middleware\InstallerMiddleware::class,
        \App\Http\Middleware\SecurityHeaders::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \App\Http\Middleware\DisableListoceanAdmin::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\CheckUserOnlineStatus::class,
            \App\Http\Middleware\NoStoreForDynamicResponses::class,
           //
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\NoStoreForDynamicResponses::class,
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * Aliases may be used instead of class names to conveniently assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'userEmailVerify' => \App\Http\Middleware\UserEmailVerify::class,
        'globalVariable' => \App\Http\Middleware\GlobalVariableMiddleware::class,
        'inactiveuser' => \App\Http\Middleware\InactiveUser::class,
        'adminglobalVariable' => \App\Http\Middleware\AdminGlobalVariable::class,
        'maintains_mode' => \App\Http\Middleware\MaintainsMode::class,
        'setlang' => \App\Http\Middleware\SetLang::class,
        'role' => RoleMiddleware::class,
        'permission' => PermissionMiddleware::class,
        'guest_media_upload_check' => guestMediaUploadCheck::class,
//        'demo' => \App\Http\Middleware\Demo::class,
    ];
}
