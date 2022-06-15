<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;

class SlackService
{
    protected $url = "https://hooks.slack.com/services/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";
    public function sendRequestLog($request)
    {
        $url = $this->url;
        $res = JWTDecode($request->header('authorization'));
        if (isset($res->gdprData)) {
            $gdprData = $res->gdprData;
        }
        $input = array(
            'text' => 'Path: ' . $request->path(),
            "attachments" => [
                [
                    "title" => 'Request data',
                    "fields" => [

                        [
                            "title" => "env",
                            "value" => env('APP_ENV'),
                            "short" => false,
                        ],

                        [
                            "title" => "request method",
                            "value" => $request->method(),
                            "short" => false,
                        ],

                        [
                            "title" => "payload",
                            "value" => json_encode($request->all()),
                            "short" => false,
                        ],


                        [
                            'title' => 'jwt',
                            'value' => $request->header('authorization'),
                            'short' => false,
                        ],
                        [
                            "title" => "decoded response",
                            "value" => json_encode($res),
                            "short" => false,
                        ],
                        [
                            "title" => "gdpr Data",
                            "value" => json_encode($gdprData),
                            "short" => false,
                        ]

                    ],
                ],
            ],
        );

        $response = Curl::to($url)
            ->withData($input)
            ->asJson()
            ->post();
    }


    public function sendErrorToSlack($error)
    {
        $input = array(
            'text' => 'Laravel API General Error',
            "attachments" => [
                [
                    //"title"=> 'Error Description',
                    "fields" => [
                        [
                            'title' => 'Error',
                            'value' => $error->message,
                            'short' => false,
                        ],
                        [
                            "title" => "File",
                            "value" => $error->file,
                            "short" => false,
                        ],
                        [
                            "title" => "Line Number",
                            "value" => $error->lineNo,
                            "short" => false,
                        ],
                        [
                            "title" => "path",
                            "value" => $error->path,
                            "short" => false,
                        ],
                        [
                            "title" => "method",
                            "value" => $error->method,
                            "short" => false,
                        ],
                        [
                            "title" => "Input",
                            "value" => $error->input,
                            "short" => false,
                        ],
                        [
                            "title" => "JWT Token",
                            "value" => $error->authorization,
                            "short" => false,
                        ],
                        [
                            "title" => "Campaign Authkey",
                            "value" => $error->authkey,
                            "short" => false,
                        ],
                    ],
                ],
            ],
        );

        if (isset($error->description)) {
            $input['attachments'][0]['fields'][] = [
                "title" => "Error Description",
                "value" => json_encode($error->description),
                "short" => false,
            ];
        }
        if (isset($error->env)) {
            $input['attachments'][0]['fields'][] = [
                'title' => 'ENV',
                'value' => $error->env,
                'short' => false,
            ];
        }
        if (isset($error->code)) {
            $input['attachments'][0]['fields'][] = [
                'title' => 'Code',
                'value' => $error->code,
                'short' => false,
            ];
        }

        if (in_array(env('APP_ENV'), ['DEV', 'STAGE', 'PROD'])) {
            //            $response = Curl::to('https://hooks.slack.com/services/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')
            $response = Curl::to('https://hooks.slack.com/services/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')
                ->withData($input)
                ->asJson()
                ->post();
        }
    }


    public function sendTemporaryData($data)
    {
        $input = array(
            'text' => json_encode($data)
        );


        $response = Curl::to('https://hooks.slack.com/services/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')
            ->withData($input)
            ->asJson()
            ->post();
    }


    public function sendAPILog($input)
    {
        $input = array(
            'text' => 'Laravel Thirdparty API logs',
            "attachments" => [
                [
                    //"title"=> 'Error Description',
                    "fields" => [
                        [
                            'title' => 'endpoint',
                            'value' => $input->endpoint,
                            'short' => false,
                        ],
                        [
                            "title" => "method",
                            "value" => $input->method,
                            "short" => false,
                        ],
                        [
                            "title" => "payload",
                            "value" => $input->payload,
                            "short" => false,
                        ],
                        [
                            "title" => "Authorization",
                            "value" => $input->authorization,
                            "short" => false,
                        ],
                        // [
                        //     "title" => "Decoded Authorization",
                        //     "value" => $input->decoded_authorization,
                        //     "short" => false,
                        // ],

                        [
                            "title" => "response",
                            "value" => $input->response,
                            "short" => false,
                        ],


                    ],
                ],
            ],
        );

        if (in_array(env('APP_ENV'), ['DEV', 'STAGE', 'PROD'])) {
            //            $response = Curl::to('https://hooks.slack.com/services/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')
            $response = Curl::to('https://hooks.slack.com/services/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')
                ->withData($input)
                ->asJson()
                ->post();
        }
    }
}
