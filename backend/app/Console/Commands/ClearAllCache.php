<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearAllCache extends Command
{
    protected $signature = 'cache:clear-all';
    protected $description = 'Clear all Laravel caches';

    public function handle()
    {
        $this->call('config:clear');
        $this->call('route:clear');  
        $this->call('cache:clear');
        $this->call('view:clear');
        
        $this->info('✅ All caches cleared!');
        return Command::SUCCESS;
    }
}