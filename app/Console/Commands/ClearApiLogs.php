<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ApiLog;

class ClearApiLogs extends Command
{
    protected $signature = 'logs:clear {days=30}';
    protected $description = 'Remove logs antigos da tabela logs.api_logs';

    public function handle()
    {
        $days = $this->argument('days');
        $count = ApiLog::where('created_at', '<', now()->subDays($days))->delete();

        $this->info("$count logs removidos com mais de $days dias.");
    }
}
