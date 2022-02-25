<?php

namespace Qubiqx\QcommerceMenus\Commands;

use Illuminate\Console\Command;

class QcommerceMenusCommand extends Command
{
    public $signature = 'qcommerce-menus';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
