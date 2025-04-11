<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Winner;
use Illuminate\Console\Command;

class DeclareWinner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'declare:winner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Declare the top user as winner if no tie.';

    /**
     * Execute the console command.
     */

     public function handle()
    {
        $topUsers = User::query()->orderBy('points', 'desc')->take(2)->get();

        if ($topUsers->count() === 1 || $topUsers[0]->points !== $topUsers[1]->points) {
            Winner::query()->create([
                'user_id' => $topUsers[0]->id,
                'points' => $topUsers[0]->points,
                'declared_at' => now(),
            ]);
            $this->info('Winner declared!');
        } else {
            $this->info('Tie detected. No winner declared.');
        }
    }
}
