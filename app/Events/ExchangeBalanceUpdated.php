<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExchangeBalanceUpdated implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  array{
     *     included: int,
     *     consumed: int,
     *     purchased: int,
     *     remaining: int,
     *     renewal_date: ?string,
     *     renewal_label: ?string,
     *     has_subscription: bool
     * }  $balance
     */
    public function __construct(
        public int $patientUserId,
        public array $balance,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.'.$this->patientUserId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'exchange.balance.updated';
    }

    public function broadcastWith(): array
    {
        return $this->balance;
    }
}
