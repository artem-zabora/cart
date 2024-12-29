<x-layout>
    <div class="container mx-auto px-4">

        <!-- Сетка товаров -->
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 lg:gap-8">
            @foreach($products as $product)
                <section class="flex flex-col gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]">
                    <!-- Изображение товара -->
                    <div class="flex justify-center">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-40 w-40 object-cover rounded-lg">
                    </div>

                    <!-- Информация о товаре -->
                    <div class="flex flex-col flex-grow">
                        <!-- Название товара -->
                        <h2 class="text-xl font-semibold text-black dark:text-white text-center">{{ $product->name }}</h2>

                        <!-- Описание товара -->
                        <p class="mt-4 text-sm text-gray-700 dark:text-gray-300">
                            {{ $product->description }}
                        </p>

                        <!-- Цена -->
                        <p class="pt-4 text-lg font-bold text-center">$ {{ $product->price }}</p>
                    </div>

                    <!-- Управление количеством и кнопка добавления -->
                    <div class="flex items-center gap-4 mt-4 justify-center">



                        <!-- Поле ввода количества -->
                        <input
                            name="quantity"
                            type="number"
                            min="1"
                            value="1"
                            aria-describedby="helper-text-explanation"
                            class="w-12 quantity-input bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            required
                            data-product-id="{{ $product->id }}"
                        />
                        <!-- Кнопка добавления товара в корзину -->
                        <button
                            id="add-to-cart"
                            data-route="{{ route('cart.add') }}"
                            data-csrf="{{ csrf_token() }}"
                            class="add-to-cart text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
                            data-product-id="{{ $product->id }}"
                        >
                            {{__('buttons.add_to_cart')}}
                        </button>
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</x-layout>
