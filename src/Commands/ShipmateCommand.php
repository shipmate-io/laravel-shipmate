<?php

namespace Shipmate\Shipmate\Commands;

use Illuminate\Console\Command;

class ShipmateCommand extends Command
{
    public $signature = 'laravel-shipmate';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
