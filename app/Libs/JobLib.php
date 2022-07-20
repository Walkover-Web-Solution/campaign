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
            $campaignLog = CampaignLog::where('id', $data->campaignLogId)->first();
            $checkfailedJob = FailedJob::where('uuid', $data->campaignLogId)->first();
            if (empty($checkfailedJob)) {
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
                $campaignLog->status = "Error - " . $failedJob->id;
            } else {
                $campaignLog->status = "Error - " .  $checkfailedJob->id;
            }
            $campaignLog->retry_status = true;
            $campaignLog->save();
            printLog("Exception in enqueue Main, Message :", ['Stack' => $e->getTrace()]);
            throw new ServerErrorException("Internal Server Error! : [Queue Connection Refused]");
        }
    }
}
