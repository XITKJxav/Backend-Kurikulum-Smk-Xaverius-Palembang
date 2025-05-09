<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateAndSeed extends Command
{
    protected $signature = 'app:migrate-and-seed';
    protected $description = 'Command description';

    public function handle()
    {
        $this->call('migrate:fresh');
        $this->call('db:seed');
        $this->info('Migration and seeding completed!');
    }
}
