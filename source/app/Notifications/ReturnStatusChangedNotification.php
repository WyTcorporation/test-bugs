<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Email;

class ReturnStatusChangedNotification extends Notification
{
    use Queueable;

    protected $templateData;
    protected $reseller;

    public function __construct(array $templateData, $reseller)
    {
        $this->templateData = $templateData;
        $this->reseller = $reseller;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->from(Email::getResellerEmailFrom($this->reseller->id))
            ->subject(__('complaintClientEmailSubject', $this->templateData))
            ->line(__('complaintClientEmailBody', $this->templateData));
    }
}
