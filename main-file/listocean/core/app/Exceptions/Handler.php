<?php

namespace App\Exceptions;

use App\Models\Backend\Menu;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($this->isHttpException($exception)) {
            if ($exception->getStatusCode() == 404) {
                if ($request->is('api/v1/*')){
                    return response()->error(['message' => __('nothing found')]);
                }

                $primary_menu = Menu::where(['status' => 'default'])->first()?->id;
                return response()->view('frontend.pages.404', compact('primary_menu'));
            }

            if ($exception->getStatusCode() == 500 ) {
                if ($request->is('api/v1/*')){
                    return response()->error(['message' => __('server error')]);
                }
            }
        }

        if(str_contains($exception->getMessage() , 'Route [login]' )){
            return redirect()->to(route('user.login'))->with(['msg' => __('Cookie expired, Please login'),'type' => 'danger']);
        }

        return parent::render($request, $exception);
    }
}
