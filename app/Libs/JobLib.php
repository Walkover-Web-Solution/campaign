<?php

namespace App\Libs;

use App\Jobs\RabbitMQJob;

class JobLib
{
    public function enqueue($queue, $data)
    {
        if(env('APP_ENV') == 'prod' || env('APP_ENV') == 'dev' ){
            RabbitMQJob::dispatch($data)->onQueue($queue);
        }
        else{
            RabbitMQJob::dispatch($data)->onQueue($queue)->onConnection('rabbitmqlocal');
        }

    }
}
