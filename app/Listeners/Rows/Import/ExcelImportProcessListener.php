<?php

namespace App\Listeners\Rows\Import;

use App\Events\Rows\Import\ExcelImportProcessEvent;

class ExcelImportProcessListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\Rows\Import\ExcelImportProcessEvent  $event
     * @return void
     */
    public function handle(ExcelImportProcessEvent $event)
    {
        broadcast($event);
    }
}
