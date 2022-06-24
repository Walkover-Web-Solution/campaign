<?php

namespace App\Http\Controllers\API;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomResource;
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
}
