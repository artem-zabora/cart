<?php

namespace App\Services;

use App\Models\Cart;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Collection;

class CartService
{
    public function getCartItems(): Collection
    {
        if (auth()->check()) {
            return Cart::with('product')->where('user_id', auth()->id())->get();
        }

        return Cart::with('product')->where('session_id', Session::getId())->get();
    }

    public function getCartItemById($cartId): Cart
    {
        if (auth()->check()) {
            return Cart::where('id', $cartId)->where('user_id', auth()->id())->first();
        }

        return Cart::where('id', $cartId)->where('session_id', Session::getId())->first();
    }

    public function addOrUpdateCartItem($product, $quantity): Cart
    {
        $userId = auth()->id(); // Получаем ID пользователя, если он авторизован
        $sessionId = Session::getId(); // Получаем ID сессии

        // Ищем существующий элемент корзины
        $cartItem = Cart::where('product_id', $product->id)
            ->where(function ($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->first();

        if ($cartItem) {
            // Если элемент найден, увеличиваем количество
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            // Если элемент не найден, создаем новый
            $cartItem = Cart::create([
                'session_id' => $sessionId,
                'user_id' => $userId, // Для неавторизованных будет null
                'product_id' => $product->id,
                'price' => $product->price,
                'quantity' => $quantity,
            ]);
        }

        return $cartItem;
    }

    public function removeCartItem($id): bool
    {
        $cartItem = $this->getCartItemById($id);

        if ($cartItem) {
            $cartItem->delete();
            return true;
        }

        return false;
    }

    public function getTotalPrice(): float
    {
        $cartItems = $this->getCartItems();
        return $cartItems->sum(fn($item) => $item->product->price * $item->quantity);
    }

    public function getCartItemCount(): int
    {
        if (auth()->check()) {
            return Cart::where('user_id', auth()->id())->sum('quantity');
        }

        return Cart::where('session_id', Session::getId())->sum('quantity');
    }


    public function increaseQuantity($cartId): Cart
    {
        $userId = auth()->id();
        $sessionId = Session::getId();

        // Поиск товара в корзине
        $cartItem = Cart::where('id', $cartId)
            ->where(function ($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->first();

        if (!$cartItem) {
            throw new \Exception('Товар не найден в корзине.');
        }

        // Увеличиваем количество
        $cartItem->increment('quantity');

        return $cartItem;
    }


    public function decreaseQuantity($cartId): Cart
    {
        $userId = auth()->id();
        $sessionId = Session::getId();

        // Поиск товара в корзине
        $cartItem = Cart::where('id', $cartId)
            ->where(function ($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->first();

        if (!$cartItem) {
            throw new \Exception('Товар не найден в корзине.');
        }

        if ($cartItem->quantity > 1) {
            $cartItem->decrement('quantity');
        } else {
            $cartItem->delete();
        }

        return $cartItem;
    }
}
