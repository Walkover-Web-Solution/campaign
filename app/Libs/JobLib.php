<?php

namespace App\Libs;

use App\Exceptions\ServerErrorException;
use App\Jobs\Campaign\RabbitMQJob;
use App\Models\CampaignLog;
use App\Models\FailedJob;
use Carbon\Carbon;

class JobLib
{
    public function enqueue($queue, $data, $delayTime = 0)
    {
        // Count set to zero when job first created
        $data->failedCount = 0;
        try {

            if (env('APP_ENV') == 'local') {
                $job = (new RabbitMQJob($data))->onQueue($queue)->delay(Carbon::now()->addSeconds($delayTime))->onConnection('rabbitmqlocal');
                dispatch($job);
            } else {
                $job = (new RabbitMQJob($data))->onQueue($queue)->delay(Carbon::now()->addSeconds($delayTime));
                dispatch($job);
            }
        } catch (\Exception $e) {
            $input = [
                'connection' => $job->connection,
                'uuid' => $data->campaignLogId,
                'queue' => $queue,
                'payload' => $data,
                'exception' => $e->__toString(),
                'failed_at' => Carbon::now(),
                'log_id' => $data->campaignLogId
            ];
            $failedJob = FailedJob::create($input);
            $campaignLog = CampaignLog::where('id', $data->campaignLogId)->first();
            $campaignLog->status = "Error - " . $failedJob->id;
            $campaignLog->save();
            printLog("Exception in enqueue Main, Message :", ['Stack' => $e->getTrace()]);
            throw new ServerErrorException("Internal Server Error! : [RabbitMQ Connection Refused]");
        }
    }
}
