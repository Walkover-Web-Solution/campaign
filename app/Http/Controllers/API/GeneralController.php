<?php

namespace App\Http\Controllers\API;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomResource;
use App\Models\ActionLog;
use App\Models\FailedJob;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GeneralController extends Controller
{
    public function encodeData(Request $request)
    {
        if (request()->header('testerKey') != 'testerKey') {
            throw new InvalidRequestException('Invalid Request');
        }
        try {
            $input = $request->all();
            return new CustomResource(["authorization" => JWTEncode($input)]);
        } catch (\Exception $e) {
            return new CustomResource(["message" => $e->getMessage()]);
        }
    }

    public function getFailedJobs(Request $request)
    {
        if (request()->header('failedKey') != 'thisisfailedkey') {
            throw new InvalidRequestException('Invalid Request');
        }
        $failedJobs = FailedJob::select()->where(function ($query) use ($request) {
            if ($request->has('log_id')) {
                $query->where('log_id', $request->log_id);
            }
            if ($request->has('queue')) {
                $query->where('queue', $request->queue);
            }
        })->get();
        return new CustomResource($failedJobs);
    }

    public function getUnits(Request $request)
    {
        if (!$request->has('unit')) {
            throw new NotFoundHttpException('Invalid Unit!');
        }
        $unit = $request->unit;

        $units = [
            "time" => [
                "seconds", "minutes", "hours", "days"
            ]
        ];

        if (empty($units[$unit])) {
            throw new NotFoundHttpException('Invalid Unit!');
        }

        return new CustomResource($units[$unit]);
    }

    public function getStatus(Request $request)
    {
        if (!$request->has('field')) {
            throw new NotFoundHttpException('Invalid Field!');
        }
        $field = $request->field;

        $status = [
            "campaignLog" => ['Running', 'Paused', 'Stopped', 'Completed', 'Error'],
            "actionLog" => ['Pending', 'Completed', 'Stopped']
        ];

        if (empty($status[$field])) {
            throw new NotFoundHttpException('Invalid Field!');
        }
        return new CustomResource($status[$field]);
    }

    public function runSeed(Request $request)
    {
        $limit = 1000;
        if(isset($request->limit)){
            $limit = $request->limit;
        }
        $actionLogs = ActionLog::where('ref_id', '!=', "")->limit($limit)->get();

        collect($actionLogs)->map(function ($actionLog) {
            $actionLog->ref_id()->create([
                'ref_id' => $actionLog->ref_id,
                'status' => $actionLog->status,
                'response' => $actionLog->response,
                'no_of_records' => $actionLog->no_of_records
            ]);
            $actionLog->ref_id = "";
            $actionLog->response = "";
            $actionLog->save();
        });
    }

    public function runSeedFailed(Request $request)
    {
        $limit = 1000;
        if(isset($request->limit)){
            $limit = $request->limit;
        }
        $actionLogs = ActionLog::where('ref_id', '!=', "")->limit($limit)->get();

        collect($actionLogs)->map(function ($actionLog) {
            $actionLog->ref_id()->create([
                'ref_id' => $actionLog->ref_id,
                'status' => $actionLog->status,
                'response' => $actionLog->response,
                'no_of_records' => $actionLog->no_of_records
            ]);
            $actionLog->ref_id = "";
            $actionLog->response = "";
            $actionLog->save();
        });
    }
}
