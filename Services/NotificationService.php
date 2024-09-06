<?php

namespace App\Services;

use App\Enums\NotificationEvents;
use App\Helpers\NotificationHelper;
use App\Models\Client;
use App\Models\Seller;

class NotificationService
{
    public function sendEmployeeEmail(array $templateData, Seller $reseller): bool
    {
        $emailFrom = NotificationHelper::getResellerEmailFrom($reseller->id);
        $emails = NotificationHelper::getEmailsByPermit($reseller->id, 'tsGoodsReturn');

        if (!empty($emailFrom) && !empty($emails)) {
            foreach ($emails as $email) {
                MessagesClient::sendMessage([
                    'emailFrom' => $emailFrom,
                    'emailTo'   => $email,
                    'subject'   => __('complaintEmployeeEmailSubject', $templateData, $reseller->id),
                    'message'   => __('complaintEmployeeEmailBody', $templateData, $reseller->id),
                ]);
            }
            return true;
        }

        return false;
    }

    public function sendClientNotifications(array $templateData, Client $client, Seller $reseller): array
    {
        $result = [
            'notificationClientByEmail' => false,
            'notificationClientBySms' => [
                'isSent'  => false,
                'message' => '',
            ],
        ];

        if (!empty($client->email)) {
            MessagesClient::sendMessage([
                'emailFrom' => NotificationHelper::getResellerEmailFrom($reseller->id),
                'emailTo'   => $client->email,
                'subject'   => __('complaintClientEmailSubject', $templateData, $reseller->id),
                'message'   => __('complaintClientEmailBody', $templateData, $reseller->id),
            ]);
            $result['notificationClientByEmail'] = true;
        }

        if (!empty($client->mobile)) {
            $error = '';
            $res = NotificationManager::send(
                $reseller->id,
                $client->id,
                NotificationEvents::CHANGE_RETURN_STATUS,
                $templateData['DIFFERENCES'],
                $templateData,
                $error
            );
            if ($res) {
                $result['notificationClientBySms']['isSent'] = true;
            }
            if (!empty($error)) {
                $result['notificationClientBySms']['message'] = $error;
            }
        }

        return $result;
    }
}
