<?php

namespace App\Events\Rows\Import;

use Illuminate\Broadcasting\Channel;
use Illuminate\Support\Facades\Redis;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ExcelImportProcessEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $count;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($key)
    {
        $this->count = Redis::get($key);
    }

    public function broadcastOn()
    {
        return new Channel("RowsImpotExcelChannel");
    }

    public function broadcastWith()
    {
        return [
            'count' => $this->count,
        ];
    }
}
