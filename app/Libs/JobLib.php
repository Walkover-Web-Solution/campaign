<?php

namespace App\Libs;

use App\Jobs\RabbitMQJob;
use Carbon\Carbon;

class JobLib
{
    public function enqueue($queue, $data, $delayTime = 0)
    {
        // Count set to zero when job first created
        $data->failedCount = 0;
        try {
            if (env('APP_ENV') == 'local') {
                RabbitMQJob::dispatch($data)->onQueue($queue)->delay(Carbon::now()->addSeconds($delayTime))->onConnection('rabbitmqlocal');
            } else {
                RabbitMQJob::dispatch($data)->onQueue($queue)->delay(Carbon::now()->addSeconds($delayTime));
            }
        } catch (\Exception $e) {
            printLog("Exception in enqueue Main, Message :", ['Stack' => $e->getTrace()]);
        }
    }
}
