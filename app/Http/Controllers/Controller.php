<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $savedMessage = "Successfully Saved.";
    public $updatedMessage = "Successfully Updated.";
    public $deletedMessage = "Successfully Deleted.";
    public $failedToUpdateMessage = "Failed to update";
    public $notPermittedMessage = "Not permitted for you";
    public $disabledMessage = "Successfully disabled";
    public $enabledMessage = "Successfully enabled";
    public $cannotDisableMessage = "Cannot disable";
    protected function validateRequest(Request $request, array $rules)
    {
        // Perform Validation
        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->messages();

            // crete error message by using key and value
            foreach ($errorMessages as $key => $value) {
                $errorMessages[$key] = $value[0];
            }

            return $errorMessages;
        }

        return true;
    }

    public function sendErrorResponse($errors)
    {
        return response()->json((['status' => 400, 'errors' => $errors]), 400);
    }

    protected function formatError($code, $message)
    {
        return new JsonResponse([
            'status_code' => $code,
            'message' => $message
        ], $code);
    }

    protected function format($code, $message, $data = null)
    {
        if ($data == null) {
            return $this->formatError($code, $message);
        }
        return new JsonResponse([
            'status_code' => $code,
            'message' => $message,
            'data' => $data
        ]);
    }
}
