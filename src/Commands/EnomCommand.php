<?php

namespace Sensson\Enom\Commands;

use Illuminate\Console\Command;

class EnomCommand extends Command
{
    public $signature = 'laravel-enom';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
