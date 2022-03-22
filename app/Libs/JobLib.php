<?php

namespace App\Libs;

use App\Jobs\RabbitMQJob;

class JobLib
{
    public function enqueue($queue, $data)
    {
        RabbitMQJob::dispatch($data)->onQueue($queue);
    }
}
