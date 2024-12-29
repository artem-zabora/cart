<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Auth\Events\Registered;
use App\Listeners\UpdateCartUserId;
use Illuminate\Support\Facades\Event;
use App\Events\UpdateCartSession;
use Illuminate\Auth\Events\Authenticated;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            UpdateCartUserId::class,
        ],
        UpdateCartSession::class => [
            UpdateCartUserId::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot()
    {
        parent::boot();

    }
}
