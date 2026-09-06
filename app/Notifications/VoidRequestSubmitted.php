<?php

namespace App\Notifications;

use App\Models\BlVoidRequest;
use Illuminate\Notifications\Notification;

class VoidRequestSubmitted extends Notification
{
    public function __construct(protected BlVoidRequest $voidRequest)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'bill_of_lading_id' => $this->voidRequest->bill_of_lading_id,
            'bl_number' => $this->voidRequest->billOfLading->bl_number,
            'requested_by_name' => $this->voidRequest->requestedBy->name,
            'reason' => $this->voidRequest->requested_reason,
            'message' => "{$this->voidRequest->requestedBy->name} solicitó anular el BL {$this->voidRequest->billOfLading->bl_number}",
        ];
    }
}
