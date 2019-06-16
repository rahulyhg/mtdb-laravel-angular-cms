<?php

namespace App\Console\Commands;

use App\ListModel;
use App\User;
use Illuminate\Console\Command;

class CreateWatchlists extends Command
{
    /**
     * @var string
     */
    protected $signature = 'watchlist:create';

    /**
     * @var string
     */
    protected $description = "Create watchlist for users that don't already have one.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    public function handle()
    {
        $userIds = app(User::class)
            ->whereDoesntHave('watchlist')
            ->pluck('id');

        $userIds->each(function($userId) {
            app(ListModel::class)->create([
                'name' => 'watchlist',
                'user_id' => $userId,
                'system' => 1,
                'public' => 0,
            ]);
        });

        $this->info('Watchlists created');
    }
}
