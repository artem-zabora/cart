<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(): View
    {
        $cartItems = $this->cartService->getCartItems();
        return view('cart', compact('cartItems'));
    }


    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $cartItem = $this->cartService->addOrUpdateCartItem($product, $request->quantity);

        return response()->json(['message' => 'Товар добавлен в корзину', 'cart' => $cartItem]);

    }


    public function remove($id)
    {
        Log::info('remove method works');
        if (!$this->cartService->removeCartItem($id)) {
            return response()->json(['message' => 'Товар не найден'], 404);
        }

        return response()->json(['message' => 'Товар удалён из корзины']);
    }



    public function increaseQuantity(Request $request)
    {
        $request->validate(['cart_id' => 'required|exists:carts,id'], ['cart_id.exists' => 'Товар с таким идентификатором не найден.']);

        try {
            $cartItem = $this->cartService->increaseQuantity($request->cart_id);

            return response()->json([
                'message' => 'Количество увеличено.',
                'cart' => [
                    'id' => $cartItem->id,
                    'quantity' => $cartItem->quantity,
                    'product' => [
                        'price' => $cartItem->product->price,
                        'name' => $cartItem->product->name,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function decreaseQuantity(Request $request)
    {
        $request->validate(['cart_id' => 'required|exists:carts,id'], ['cart_id.exists' => 'Товар с таким идентификатором не найден.']);
        try {
            $cartItem = $this->cartService->decreaseQuantity($request->cart_id);

            return response()->json([
                'message' => 'Количество уменьшено.',
                'cart' => [
                    'id' => $cartItem->id,
                    'quantity' => $cartItem->quantity ?? 0, // Если удалено, возвращаем 0
                    'product' => $cartItem->product ? [
                        'price' => $cartItem->product->price,
                        'name' => $cartItem->product->name,
                    ] : null,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function total()
    {
        $totalPrice = $this->cartService->getTotalPrice();
        return response()->json(['total_price' => "$totalPrice"]);
    }

    public function getCartItemCount()
    {
        $cartItemCount = $this->cartService->getCartItemCount();
        return response()->json(['count' => $cartItemCount]);
    }


    public function clearCart()
    {
        if (Auth::check()) {
            // Очистить корзину авторизованного пользователя
            Cart::where('user_id', Auth::id())->delete();
        } else {
            // Очистить корзину по сессии для гостя
            Cart::where('session_id', Session::getId())->delete();

        }

        return response()->json(['message' => __('messages.cart_cleared')]);
    }

}
