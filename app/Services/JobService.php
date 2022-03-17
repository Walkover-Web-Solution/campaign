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

    public function processRunCampaign($input, $campaign)
    {
        $input = array(
            'input' => $input,
            'campaign_id' => $campaign->id
        );

        switch ($campaign->campaign_type_id) {
            case 1:
                $queue = 'run_email_campaigns';
                break;

            case 2:
                $queue = 'run_sms_campaigns';
                break;
            case 4:
                $queue = 'run_voice_campaigns'; // initializeing the queue name for the rabbhit mq
                break;
        }
        $this->lib->enqueue($queue, $input);
    }
}
