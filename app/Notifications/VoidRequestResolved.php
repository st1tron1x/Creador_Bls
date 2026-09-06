<?php

namespace App\Notifications;

use App\Models\BlVoidRequest;
use Illuminate\Notifications\Notification;

class VoidRequestResolved extends Notification
{
    public function __construct(protected BlVoidRequest $voidRequest, protected bool $approved)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $blNumber = $this->voidRequest->billOfLading->bl_number;
        $resolverName = $this->voidRequest->resolvedBy->name;

        $message = $this->approved
            ? "{$resolverName} aprobó tu solicitud de anulación del BL {$blNumber}"
            : "{$resolverName} rechazó tu solicitud de anulación del BL {$blNumber}";

        return [
            'bill_of_lading_id' => $this->voidRequest->bill_of_lading_id,
            'bl_number' => $blNumber,
            'approved' => $this->approved,
            'reason' => $this->voidRequest->resolution_note ?: ($this->approved ? 'Sin nota adicional.' : 'Sin motivo de rechazo especificado.'),
            'message' => $message,
        ];
    }
}