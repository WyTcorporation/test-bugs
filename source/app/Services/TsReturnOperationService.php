<?php

namespace App\Services;

use App\Models\Contractor;
use App\Models\Seller;
use App\Models\Employee;
use App\Models\Status;
use App\Notifications\ReturnStatusChangedNotification;
use App\Operations\TsReturnOperation;
use Exception;

class TsReturnOperationService
{
    public function handle(array $data)
    {
        $result = [
            'notificationEmployeeByEmail' => false,
            'notificationClientByEmail'   => false,
            'notificationClientBySms'     => [
                'isSent'  => false,
                'message' => '',
            ],
        ];

        $reseller = Seller::find($data['resellerId']);
        if (!$reseller) {
            throw new Exception('Seller not found!', 400);
        }

        $client = Contractor::find($data['clientId']);
        if (!$client || $client->type !== Contractor::TYPE_CUSTOMER || $client->seller_id !== $reseller->id) {
            throw new Exception('Client not found or invalid!', 400);
        }

        $creator = Employee::find($data['creatorId']);
        if (!$creator) {
            throw new Exception('Creator not found!', 400);
        }

        $expert = Employee::find($data['expertId']);
        if (!$expert) {
            throw new Exception('Expert not found!', 400);
        }

        $this->sendNotifications($data, $result, $reseller, $client, $creator, $expert);

        return $result;
    }

    private function sendNotifications($data, &$result, $reseller, $client, $creator, $expert)
    {
        $templateData = $this->prepareTemplateData($data, $client, $creator, $expert);

        if ($data['notificationType'] === TsReturnOperation::TYPE_CHANGE && !empty($data['differences']['to'])) {
            $client->notify(new ReturnStatusChangedNotification($templateData, $reseller));
            $result['notificationClientByEmail'] = true;
        }
    }

    private function prepareTemplateData($data, $client, $creator, $expert)
    {
        return [
            'COMPLAINT_ID'       => $data['complaintId'],
            'COMPLAINT_NUMBER'   => $data['complaintNumber'],
            'CREATOR_NAME'       => $creator->getFullName(),
            'EXPERT_NAME'        => $expert->getFullName(),
            'CLIENT_NAME'        => $client->getFullName() ?: $client->name,
            'CONSUMPTION_NUMBER' => $data['consumptionNumber'],
            'AGREEMENT_NUMBER'   => $data['agreementNumber'],
            'DATE'               => $data['date'],
            'DIFFERENCES'        => $this->getDifferencesText($data),
        ];
    }

    private function getDifferencesText($data)
    {
        if ($data['notificationType'] === TsReturnOperation::TYPE_NEW) {
            return __('NewPositionAdded');
        }

        if ($data['notificationType'] === TsReturnOperation::TYPE_CHANGE && !empty($data['differences'])) {
            return __('PositionStatusHasChanged', [
                'FROM' => Status::getName($data['differences']['from']),
                'TO'   => Status::getName($data['differences']['to']),
            ]);
        }

        return '';
    }
}
