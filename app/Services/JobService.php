<?php

namespace App\Services;

use App\Libs\JobLib;
use App\Models\FlowAction;

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

    public function processRunCampaign($input)
    {
        $flow_action = FlowAction::where('id', $input['flow_action']);
        switch ($flow_action->linked_id) {
            case 1:
                $queue = 'run_email_campaigns';
                break;
            case 2:
                $queue = 'run_sms_campaigns';
                break;
            case 3:
                $queue = 'run_otp_campaigns';
                break;
            case 4:
                $queue = 'run_whastapp_campaigns';
                break;
            case 5:
                $queue = 'run_voice_campaigns';
                break;
        }
        $this->lib->enqueue($queue, $input);
    }
}
