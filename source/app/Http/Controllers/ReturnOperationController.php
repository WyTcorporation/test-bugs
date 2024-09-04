<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReturnOperationRequest;
use App\Services\TsReturnOperationService;
use Illuminate\Http\JsonResponse;

class ReturnOperationController extends Controller
{
    protected TsReturnOperationService $operationService;

    public function __construct(TsReturnOperationService $operationService)
    {
        $this->operationService = $operationService;
    }

    public function handle(ReturnOperationRequest $request): JsonResponse
    {
        $data = $request->validated();

        $result = $this->operationService->handle($data);

        return response()->json($result);
    }
}
