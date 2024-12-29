<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Models\User;

class UpdateCartSession
{
    use SerializesModels;

    public $user;
    public $oldSessionId;

    public function __construct(User $user, $oldSessionId)
    {
        $this->user = $user;
        $this->oldSessionId = $oldSessionId;
    }
}
