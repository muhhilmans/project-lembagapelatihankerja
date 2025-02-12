<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
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
            //
        });
    }

    public function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please log in first.'
            ], 401);
        }

        return redirect()->guest(route('login'));
    }

    public function render($request, \Throwable $exception)
    {
        // Tangani error dari JWT
        if ($request->expectsJson()) {
            if ($exception instanceof UnauthorizedHttpException) {
                $previousException = $exception->getPrevious();

                if ($previousException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Token not provided. Please log in first.'
                    ], 401);
                }

                // Jika UnauthorizedHttpException tetapi tidak ada token
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please log in first.'
                ], 401);
            }
        }

        return parent::render($request, $exception);
    }
}
