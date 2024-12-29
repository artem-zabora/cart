import $ from 'jquery';
window.$ = window.jQuery = $;


// Додавння товару до кошика
$(document).on('click', '.add-to-cart', function () {
    const addUrl = window.routes.cartAdd;
    const csrfToken = $(this).data('csrf');
    const productId = $(this).data('product-id');
    const quantity = $(this).closest('div').find('.quantity-input').val();

    //Перевірка даних перед відправкою
    if (!quantity || quantity < 1) {
        alert('Вкажіть коректну кількість товару.');
        return;
    }

    $.ajax({
        url: addUrl,
        method: 'POST',
        data: {
            product_id: productId,
            quantity: quantity,
            _token: csrfToken
        },
        success: function (response) {
            // alert(response.message);
            document.dispatchEvent(new Event('cartUpdated')); // Оновленняя кількості в кошику для навігації

        },
        error: function (xhr) {
            alert('Помилка: ' + xhr.responseJSON.message);
        }
    });
});


// Видалення товару з кошика
$(document).on('click', '.remove-from-cart', function () {
    const cartId = $(this).data('cart-id');
    const csrfToken = $(this).data('csrf');
    $.ajax({
        url: '/cart/' + cartId,
        method: 'DELETE',
        data: {
            _token: csrfToken
        },
        success: function (response) {
            const row = $(`[data-cart-id="${cartId}"]`).closest('tr');
            if (response.cart) {
                // Оновлення кількості та ціни
                row.find('.quantity-display').text(response.cart.quantity);
                row.find('.total-price').text((response.cart.quantity * response.cart.product.price).toFixed(2) + ' $');
            } else {
                // Видалення строки якщо товар повністю виделен
                row.remove();
            }
            // Если корзина пуста, перезагружаем страницу
            if ($('tbody tr').length === 0) {
                location.reload();
            } else {
                document.dispatchEvent(new Event('cartUpdated'));
                updateTotalPrice();
            }
        },
        error: function (xhr) {
            alert('Ошибка: ' + xhr.responseJSON.message);
        }
    });
});


// + Кількість
$(document).on('click', '.increase-quantity', function () {
    const cartId = $(this).data('cart-id');
    const csrfToken = $(this).data('csrf');
    const increaseUrl = window.routes.cartIncrease;
    $.ajax({
        url: increaseUrl,
        method: 'POST',
        data: {
            cart_id: cartId,
            _token: csrfToken
        },
        success: function (response) {
            // Знаходимо елемент відображаючий кількість товару
            const cartCountElement = document.querySelector('.cart-count');
            cartCountElement.textContent = response.cartItemCount; // response.cartItemCount — значение, возвращаемое сервером

            const row = $(`[data-cart-id="${cartId}"]`).closest('tr');
            row.find('.quantity-display').text(response.cart.quantity);
            row.find('.total-price').text(response.cart.quantity * response.cart.product.price + ' $');
            updateTotalPrice(); // Обновляем общую стоимость
            document.dispatchEvent(new Event('cartUpdated'));
        },
        error: function (xhr) {
            alert('Ошибка: ' + xhr.responseJSON.error);
        }
    });
});


// - Кількість
$(document).on('click', '.decrease-quantity', function () {
    const cartId = $(this).data('cart-id');
    const csrfToken = $(this).data('csrf');
    const decreaseUrl = window.routes.cartDecrease;
    const quantityDisplay = $(`[data-cart-id="${cartId}"]`).closest('tr').find('.quantity-display');
    const currentQuantity = parseInt(quantityDisplay.text(), 10);
    // Перевірк щоб не було меньше 1
    if (currentQuantity <= 1) {
        alert('Меньше 1 не можна.');
        return;
    }
    $.ajax({
        url: decreaseUrl,
        method: 'POST',
        data: {
            cart_id: cartId,
            _token: csrfToken
        },
        success: function (response) {
            const row = $(`[data-cart-id="${cartId}"]`).closest('tr');
            if (response.cart) {
                const newQuantity = response.cart.quantity;
                quantityDisplay.text(newQuantity); // Обновляем количество в интерфейсе
                row.find('.total-price').text(newQuantity * response.cart.product.price + ' $'); // Обновляем цену
                // Робимо кнопку - не активною, якщо кількість 1
                // if (newQuantity === 1) {
                //     row.find('.decrease-quantity').addClass('disabled');
                // }
            } else {
                row.remove();
            }
            updateTotalPrice(); // Обновляем общую стоимость
            document.dispatchEvent(new Event('cartUpdated'));
        },
        error: function (xhr) {
            alert('Ошибка: ' + xhr.responseJSON.error);
        }
    });
});

// Оновлення загальної вартості
function updateTotalPrice() {
    const cartTotalUrl = window.routes.cartTotal;
    $.ajax({
        url: cartTotalUrl,
        method: 'GET',
        success: function (response) {
            $('#total-cart-price').text(response.total_price + ' $');
        },
        error: function () {
            alert('Ошибка при обновлении общей стоимости.');
        }
    });
}


// Динамічне оновлення кількості товарів в кошику:
document.addEventListener('DOMContentLoaded', () => {
    const cartItemCountUrl = window.routes.cartItemCount;
    const updateCartCount = () => {
        fetch(cartItemCountUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                const cartCountElement = document.querySelector('.cart-count');
                if (data.count > 0) {
                    cartCountElement.textContent = data.count;
                    cartCountElement.style.display = 'flex';
                } else {
                    cartCountElement.style.display = 'none';
                }
            })
            .catch(error => console.error('Ошибка при обновлении количества товаров:', error));
    };
    updateCartCount();
    document.addEventListener('cartUpdated', updateCartCount);
});

// Динамічне оновлення кількості товарів в кошику:
document.addEventListener('cartUpdated', () => {
    const cartItemCountUrl = window.routes.cartItemCount;
    fetch(cartItemCountUrl, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(response => response.json())
        .then(data => {
            const cartCountElement = document.querySelector('.cart-count');
            if (data.count > 0) {
                cartCountElement.textContent = data.count;
                cartCountElement.style.display = 'flex';
            } else {
                cartCountElement.style.display = 'none';
            }
        })
        .catch(error => console.error('Ошибка при обновлении количества товаров:', error));
});


document.getElementById('clear-cart').addEventListener('click', function () {
    fetch('/cart/clear', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
        .then(response => response.json())
        .then(data => {
            alert(data.message); // Отображаем сообщение
            location.reload(); // Перезагрузка страницы для обновления корзины
        })
        .catch(error => console.error('Ошибка:', error));
});






