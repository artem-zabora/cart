<x-layout>
    <div class="overflow-x-auto shadow-lg rounded-lg">
        <table class="min-w-full table-auto bg-white dark:bg-zinc-900 border-separate border-spacing-0">
            <thead class="bg-zinc-900 text-white">
            <tr>
                <th class="px-6 py-3 text-left font-medium">{{__('labels.product')}}</th>
                <th class="px-6 py-3 text-left font-medium">{{__('labels.quantity')}}</th>
                <th class="px-6 py-3 text-left font-medium">{{__('labels.price')}}</th>
                <th class="px-6 py-3 text-left font-medium">
                    <!-- Кнопка очистки корзины -->
                    @if ($cartItems->isNotEmpty())
                        <button id="clear-cart" type="button"
                                class="mt-2 focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-3 py-1.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
                            {{__('buttons.clear')}}
                        </button>
                    @endif
                </th>
            </tr>
            </thead>
            <tbody class="bg-zinc-800">
            @forelse ($cartItems as $item)
                <tr class="transition-colors duration-200">
                    <td class="px-6 py-4 border-b border-gray-700 text-white">{{ $item->product->name }}</td>
                    <td class="px-6 py-4 border-b border-gray-700 flex items-center justify-center gap-2">
                        <button
                            class="decrease-quantity text-white bg-gray-700 hover:bg-gray-800 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-full text-xs px-3 py-2 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-900"
                            data-cart-id="{{ $item->id }}"
                            data-csrf="{{ csrf_token() }}"
                        >
                            −
                        </button>
                        <span class="quantity-display text-white">{{ $item->quantity }}</span>
                        <button
                            class="increase-quantity text-white bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-xs px-3 py-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-900"
                            data-cart-id="{{ $item->id }}"
                            data-csrf="{{ csrf_token() }}"
                        >
                            +
                        </button>
                    </td>
                    <td class="total-price px-6 py-4 border-b border-gray-700 text-white">{{ $item->product->price * $item->quantity }} $</td>
                    <td class="place-items-center px-6 py-4 border-b border-gray-700">
                        <button
                            class="remove-from-cart text-[20px] text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-full text-xs p-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900 flex items-center justify-center"
                            data-cart-id="{{ $item->id }}"
                            data-csrf="{{ csrf_token() }}"
                            aria-label="Удалить"
                        >
                            ✕
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">{{__('labels.cart_is_empty')}}</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <!-- Только если корзина не пуста, показываем кнопки для заказа и очистки -->
        @if ($cartItems->isNotEmpty())
            <div class="bg-zinc-800 pt-2 mt-4 text-center flex flex-row justify-center items-center gap-4">
                <strong>{{__('Total price')}}:</strong>
                <span id="total-cart-price" class="text-white">
                    {{ $cartItems->sum(fn($item) => $item->product->price * $item->quantity) }} $
                </span>

                <!-- Кнопка заказать -->
                <button type="button"
                        class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                    {{__('buttons.make_order')}}
                </button>

            </div>
        @endif
    </div>
</x-layout>
