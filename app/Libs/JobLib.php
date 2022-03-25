<?php

namespace App\Libs;

use App\Jobs\RabbitMQJob;

class JobLib
{
    public function enqueue($queue, $data)
    {
        if(env('APP_ENV') == 'local'){
            RabbitMQJob::dispatch($data)->onQueue($queue)->onConnection('rabbitmqlocal');
        }
        else{
            RabbitMQJob::dispatch($data)->onQueue($queue)->onConnection('rabbitmq');
        }
        
    }
}
