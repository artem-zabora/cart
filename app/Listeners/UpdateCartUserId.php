<?php

namespace App\Listeners;

use App\Events\UpdateCartSession;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;
use App\Models\Cart;

class UpdateCartUserId
{
    /**
     * Обработка события обновления корзины после смены сессии.
     */
    public function handle(UpdateCartSession $event): void
    {
        $user = $event->user;
        $oldSessionId = $event->oldSessionId;
        $newSessionId = session()->getId();
        $userId = $user->id;

        $this->updateCartSessionAndUserId($oldSessionId, $newSessionId, $userId);
    }

    /**
     * Обработка события регистрации.
     */
    public function handleRegistration(Registered $event): void
    {
        $user = $event->user;
        $this->updateCartUserId($user);
    }

    /**
     * Привязка корзины к пользователю после авторизации или регистрации.
     */
    protected function updateCartUserId($user): void
    {
        $sessionId = session()->getId();
        $userId = $user->id;

        Log::info("Привязка корзины для сессии {$sessionId} к user_id {$userId}");

        $cartItems = Cart::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->get();

        if ($cartItems->isEmpty()) {
            Log::info("Корзина для сессии {$sessionId} пуста.");
            return;
        }

        foreach ($cartItems as $item) {
            $item->user_id = $userId;
            $item->save();
        }

        Log::info("Корзина для сессии {$sessionId} успешно привязана к user_id {$userId}");
    }

    /**
     * Обновление корзины для смены сессии и привязки к пользователю.
     */
    protected function updateCartSessionAndUserId(string $oldSessionId, string $newSessionId, int $userId): void
    {
        Log::info("Обновление корзины для смены session_id: {$oldSessionId} → {$newSessionId}, user_id: {$userId}");

        $cartItems = Cart::where('session_id', $oldSessionId)
            ->whereNull('user_id')
            ->get();

        if ($cartItems->isEmpty()) {
            Log::info("Корзина для session_id {$oldSessionId} пуста.");
            return;
        }

        foreach ($cartItems as $item) {
            $item->user_id = $userId;
            $item->session_id = $newSessionId;
            $item->save();
        }

        Log::info("Корзина для session_id {$oldSessionId} успешно обновлена и привязана к user_id {$userId}");
    }
}
