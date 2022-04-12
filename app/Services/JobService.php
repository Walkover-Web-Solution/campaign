<?php

namespace App\Services;

use App\Libs\JobLib;

/**
 * Class JobService
 * @package App\Services
 */
class JobService
{
    protected $lib;
    public function __construct(JobLib $lib)
    {
        $this->lib = $lib;
    }

    public function processRunCampaign($campLog)
    {
        // setting object to be send with job
        $input = new \stdClass();
        $input->campaignLogId = $campLog->id;
        $count=$campLog->no_of_records;

        if($count<=1000){
            $queue = '1k_data_queue';
        }
        else if($count<=10000){
            $queue = '10k_data_queue';
        }
        else{
            $queue = 'bulk_data_queue';
        }
        $this->lib->enqueue($queue, $input);
    }
}
