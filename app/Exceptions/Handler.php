<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (ThrottleRequestsException $e, $request) {
            return response([
                'message' => 'Too many Attempts'
            ], 429);
        });

        $this->renderable(function (NotFoundHttpException  $e, $request) {

            $message = $e->getMessage();
            if (empty($message)) {
                $message = 'Route not found';
            } else {
                if (strpos($message, "App\\Models\\")) {
                    $message = 'Not Found';
                }
            }

            return response([
                'errors' => $message,
                'hasError' => true,
                'status' => 'fail'

            ], 404);
        });

        $this->renderable(function (InvalidRequestException $e, $request) {
            $message = $e->getMessage();
            if (empty($message)) {
                $message = 'Invalid request!';
            }

            return response([
                'errors' => $message,
                'hasError' => true,
                'status' => 'fail'
            ], 401);
        });

        $this->renderable(function (ForbiddenException $e, $request) {
            $message = $e->getMessage();
            if (empty($message)) {
                $message = 'Invalid request!';
            }

            return response([
                'errors' => $message,
                'hasError' => true,
                'status' => 'fail'
            ], 403);
        });

        $this->renderable(function (AttachmentTooLargeException $e, $request) {
            $message = $e->getMessage();
            if (empty($message)) {
                $message = 'Attachment too large!';
            }

            return response([
                'errors' => $message,
                'hasError' => true,
                'status' => 'fail'
            ], 413);
        });






        $this->renderable(function (ValidationException $e, $request) {
            return $this->invalidJson($request, $e);
        });

        $this->renderable(function (\Exception $e, $request) {

            $message = $e->getMessage();
            return response([
                'errors' => $message,
                'hasError' => true,
                'status' => 'fail'
            ]);
        });
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json([
            "status" => "fail",
            "hasError" => true,
            'errors'  => $this->transformErrors($exception),

        ]);
    }

    private function transformErrors(ValidationException $exception)
    {
        $errors = [];

        foreach ($exception->errors() as $field => $message) {
            $errors[] = $message[0];
            /*
           $errors[] = [
               'field' => $field,
               'message' => $message[0],
           ];
           */
        }

        return $errors;
    }
}
