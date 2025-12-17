<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;

class UpdateLastLogin
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        if ($event->user) {
            // Directly update timestamp to avoid triggering events/observers if not desired, 
            // or just save(). Using save() updates updated_at too, which is fine.
            $event->user->update(['last_login_at' => now()]);
        }
    }
}
