<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // <--- WAJIB 1
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// WAJIB 2: Tambahkan 'implements ShouldBroadcast'
class NotificationDispatched implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Data yang akan dikirim ke Frontend (Harus Public)
    public $message;
    public $targetUserId;
    public $type; // Misal: 'success', 'info', 'error'
    public $link;

    /**
     * Create a new event instance.
     */
    public function __construct($message, $targetUserId, $type = 'info', $link = '#')
    {
        $this->message = $message;
        $this->targetUserId = $targetUserId;
        $this->type = $type;
        $this->link = $link;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // WAJIB 3: Channel harus dinamis berdasarkan ID target
        // Jadi pesan untuk User A tidak akan nyasar ke User B
        return [
            new PrivateChannel('notifications.' . $this->targetUserId),
        ];
    }

    /**
     * Nama event yang didengar Frontend (Opsional tapi rapi)
     */
    public function broadcastAs()
    {
        return 'notification.dispatched';
    }
}
