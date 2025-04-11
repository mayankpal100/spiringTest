<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ResetUserScores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:reset-scores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rest all user to 0';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        User::query()->update(['points' => 0]);
        $this->info('All user scores reset.');
    }
}
