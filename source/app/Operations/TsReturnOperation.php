<?php

namespace App\Operations;

use App\Services\TsReturnOperationService;
use Exception;

class TsReturnOperation extends ReferencesOperation
{
    protected $service;

    public const TYPE_NEW    = 1;
    public const TYPE_CHANGE = 2;

    public function __construct()
    {

        $this->service = new TsReturnOperationService();
    }

    /**
     * @throws Exception
     */
    public function doOperation(): array
    {
        $data = (array)$this->getRequest('data');
        return $this->service->handle($data);
    }
}
