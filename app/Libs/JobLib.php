<?php

namespace App\Libs;

use App\Jobs\RabbitMQJob;

class JobLib
{
    public function enqueue($queue, $data)
    {
        try {
            if (env('APP_ENV') == 'local') {
                RabbitMQJob::dispatch($data)->onQueue($queue)->onConnection('rabbitmqlocal');
            } else {
                RabbitMQJob::dispatch($data)->onQueue($queue);
            }
        } catch (\Exception $e) {
            printLog("Exception in enqueue Main, Message :", ['Stack' => $e->getTrace()]);
        }
    }
}
