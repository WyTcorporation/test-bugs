<?php

namespace Operations\Notification;

use Illuminate\Http\Request;
use Operations\Repositories\SellerRepository;  // Added to work with Seller
use Operations\Repositories\ContractorRepository;  // Added to work with Contractor
use Operations\Repositories\EmployeeRepository;  // Added to work with Employee
use Operations\Services\NotificationService;  // Added to work with Notification

class TsReturnOperation extends ReferencesOperation
{
    private $sellerRepo;
    private $contractorRepo;
    private $employeeRepo;
    private $notificationService;

    public function __construct(
        SellerRepository $sellerRepo,
        ContractorRepository $contractorRepo,
        EmployeeRepository $employeeRepo,
        NotificationService $notificationService
    ) {
        // Dependency Injection for repositories and service
        $this->sellerRepo = $sellerRepo;
        $this->contractorRepo = $contractorRepo;
        $this->employeeRepo = $employeeRepo;
        $this->notificationService = $notificationService;
    }

    public function doOperation(Request $request): array
    {
        // Використання Laravel Request з валідацією
        $data = $request->validate([
            'data.resellerId' => 'required|integer',
            'data.notificationType' => 'required|integer',
            'data.clientId' => 'required|integer',
            'data.creatorId' => 'required|integer',
            'data.expertId' => 'required|integer',
            'data.complaintId' => 'required|integer',
            'data.complaintNumber' => 'required|string',
            'data.consumptionId' => 'required|integer',
            'data.consumptionNumber' => 'required|string',
            'data.agreementNumber' => 'required|string',
            'data.date' => 'required|date',
            'data.differences.from' => 'sometimes|integer',
            'data.differences.to' => 'sometimes|integer'
        ]);

        // Ініціалізація результату
        $result = [
            'notificationEmployeeByEmail' => false,
            'notificationClientByEmail' => false,
            'notificationClientBySms' => [
                'isSent' => false,
                'message' => '',
            ],
        ];

        //Receiving Seller through the repository
        $reseller = $this->sellerRepo->getById($data['resellerId']);
        if (!$reseller) {
            throw new \Exception('Seller not found!', 400);
        }

        // Obtaining a Contractor through a repository with additional validation
        $client = $this->contractorRepo->getById($data['clientId']);
        if (!$client || $client->type !== Contractor::TYPE_CUSTOMER || $client->Seller->id !== $reseller->id) {
            throw new \Exception('Client not found or mismatched seller!', 400);
        }

        //Retrieving Employee through the repository
        $creator = $this->employeeRepo->getById($data['creatorId']);
        $expert = $this->employeeRepo->getById($data['expertId']);

        if (!$creator || !$expert) {
            throw new \Exception('Creator or Expert not found!', 400);
        }

        // Getting differences through a private method
        $differences = $this->getDifferences($data['notificationType'], $data['differences'] ?? []);

        //Forming data for the template
        $templateData = $this->prepareTemplateData($data, $creator, $expert, $client, $differences);

        //Sending notifications through the service
        $this->notificationService->sendNotifications($reseller, $client, $templateData, $result);

        return $result;
    }

    private function getDifferences(int $notificationType, array $differences): string
    {
        //Checking the type of notification and the formation of the message
        if ($notificationType === self::TYPE_NEW) {
            return __('NewPositionAdded');
        } elseif ($notificationType === self::TYPE_CHANGE && !empty($differences)) {
            return __('PositionStatusHasChanged', [
                'FROM' => Status::getName($differences['from']),
                'TO' => Status::getName($differences['to']),
            ]);
        }
        return '';
    }

    private function prepareTemplateData(array $data, $creator, $expert, $client, string $differences): array
    {
        //Preparing data for use in templates
        return [
            'COMPLAINT_ID' => $data['complaintId'],
            'COMPLAINT_NUMBER' => $data['complaintNumber'],
            'CREATOR_ID' => $data['creatorId'],
            'CREATOR_NAME' => $creator->getFullName(),
            'EXPERT_ID' => $data['expertId'],
            'EXPERT_NAME' => $expert->getFullName(),
            'CLIENT_ID' => $data['clientId'],
            'CLIENT_NAME' => $client->getFullName(),
            'CONSUMPTION_ID' => $data['consumptionId'],
            'CONSUMPTION_NUMBER' => $data['consumptionNumber'],
            'AGREEMENT_NUMBER' => $data['agreementNumber'],
            'DATE' => $data['date'],
            'DIFFERENCES' => $differences,
        ];
    }
}
