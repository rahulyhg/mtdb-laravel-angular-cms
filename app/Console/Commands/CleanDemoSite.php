<?php

namespace App\Console\Commands;

use App\User;
use Common\Localizations\Localization;
use Hash;
use Illuminate\Console\Command;

class CleanDemoSite extends Command
{
    /**
     * @var string
     */
    protected $signature = 'demo:clean';

    /**
     * @var string
     */
    protected $description = 'Reset demo site.';

    /**
     * @var User
     */
    private $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        parent::__construct();
        $this->user = $user;
    }

    /**
     * @return void
     */
    public function handle()
    {
        // reset admin user
        $this->cleanAdminUser('admin@admin.com');

        // delete localizations
        app(Localization::class)->get()->each(function(Localization $localization) {
            if (strtolower($localization->name) !== 'english') {
                $localization->delete();
            }
        });
    }

    private function cleanAdminUser($email)
    {
        $admin = $this->user
            ->where('email', $email)
            ->first();

        if ( ! $admin) return;

        $admin->avatar = null;
        $admin->username = 'admin';
        $admin->first_name = 'Demo';
        $admin->last_name = 'Admin';
        $admin->password = Hash::make('admin');
        $admin->permissions = ['admin' => 1, 'superAdmin' => 1];
        $admin->save();
    }
}
