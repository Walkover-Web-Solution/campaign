<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomResource;
use App\Jobs\RabbitMQJob;
use Exception;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Bus\Dispatcher;

class TestingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $values['campaign_id'] = '1';
        $values['mongo_id'] = '2';
        $values['flow_action_id'] = '3';
        $values['action_log_id'] = '4';
        try
        {
            // app(Dispatcher::class)->dispatch(new RabbitMQJob($values));
            // die("here");
            RabbitMQJob::dispatch($values)->onQueue('raghunath');
            // $res = RabbitMQJob::dispatch($values)->onQueue("run_email_campaigns");
            // return new CustomResource($res);
        }
        catch(Exception $ex){
            return $ex;
        }
        try {
            $string = [
                "company" => array(
                    "0" => "",
                    "id" => "452001",
                    "username" => "whozzat",
                    "email" => "paresh@whozzat.com"
                ),
                "need_validation" => false,
                "user" => array(
                    "id" => "452001",
                    "username" => "prasuk",
                    "email" => "prasuk@whozzat.com"
                ),
                "ip" => null
            ];
            $encoded = JWTEncode($string);
            dd($encoded);
            $res = JWTDecode($encoded);
            dd($res->user);
        } catch (\Exception $e) {
            dd($e);
        }
        $key = "example_key";
        $payload = array(
            "iss" => "http://example.org",
            "aud" => "http://example.com",
            "iat" => 1356999524,
            "nbf" => 1357000000
        );


        /**
         * IMPORTANT:
         * You must specify supported algorithms for your application. See
         * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
         * for a list of spec-compliant algorithms.
         */
        $jwt = JWT::encode($payload, $key, 'HS256');
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

        return new CustomResource($decoded);
    }

    public function encodeData(Request $request)
    {
        if (request()->header('testerKey') != 'testerKey') {
            return new CustomResource(["message" => 'invalid request']);
        }
        try {
            $input = $request->all();
            return new CustomResource(["authorization" => JWTEncode($input)]);
        } catch (\Exception $e) {
            return new CustomResource(["message" => $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
