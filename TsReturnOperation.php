<?php

namespace App\Operations;

use App\Models\Employee;
use App\Models\Seller;
use App\Models\Client;
use App\Services\NotificationService;
use App\Enums\NotificationType;
use Exception;
use App\Services\ClientService;
use App\Services\SellerService;

class TsReturnOperation
{
    private NotificationService $notificationService;
    private ClientService $clientService;
    private SellerService $sellerService;

    public function __construct(
        NotificationService $notificationService,
        ClientService $clientService,
        SellerService $sellerService
    ) {
        $this->sellerService = $sellerService;
        $this->clientService = $clientService;
        $this->notificationService = $notificationService;
    }

    /**
     * @throws Exception
     */
    public function doOperation(array $data): array
    {
        if (!isset($data['resellerId'], $data['clientId'], $data['notificationType'])) {
            throw new Exception('Missing necessary data fields', 400);
        }

        $reseller = $this->sellerService->getSeller((int)$data['resellerId']);
        $client = $this->clientService->getClient((int)$data['clientId'], $reseller->id);

        $templateData = $this->prepareTemplateData($data, $client);
        $result = [
            'notificationEmployeeByEmail' => $this->notificationService->sendEmployeeEmail($templateData, $reseller),
            'notificationClientByEmail' => false,
            'notificationClientBySms' => ['isSent' => false, 'message' => ''],
        ];

        if ((int)$data['notificationType'] === NotificationType::TYPE_CHANGE) {
            $clientResult = $this->notificationService->sendClientNotifications($templateData, $client, $reseller);
            $result = array_merge($result, $clientResult);
        }

        return $result;
    }

    private function prepareTemplateData(array $data, Client $client): array
    {
        return [
            'COMPLAINT_ID'       => (int)$data['complaintId'] ?? 0,
            'COMPLAINT_NUMBER'   => (string)$data['complaintNumber'] ?? '',
            'CREATOR_ID'         => (int)$data['creatorId'] ?? 0,
            'CREATOR_NAME'       => $this->getEmployeeName((int)$data['creatorId']),
            'EXPERT_ID'          => (int)$data['expertId'] ?? 0,
            'EXPERT_NAME'        => $this->getEmployeeName((int)$data['expertId']),
            'CLIENT_ID'          => $client->id,
            'CLIENT_NAME'        => $client->getFullName(),
            'CONSUMPTION_ID'     => (int)$data['consumptionId'] ?? 0,
            'CONSUMPTION_NUMBER' => (string)$data['consumptionNumber'] ?? '',
            'AGREEMENT_NUMBER'   => (string)$data['agreementNumber'] ?? '',
            'DATE'               => (string)$data['date'] ?? '',
            'DIFFERENCES'        => $data['differences'] ?? '',
        ];
    }

    private function getEmployeeName(int $employeeId): string
    {
        $employee = Employee::getById($employeeId);
        return $employee ? $employee->getFullName() : 'Unknown Employee';
    }
}
