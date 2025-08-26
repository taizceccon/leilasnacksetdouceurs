document.addEventListener('DOMContentLoaded', function () {
    // Mise à jour du panier via AJAX
    document.querySelectorAll('form[action*="cart/update"]').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const input = form.querySelector('input[name="quantity"]');
            const quantity = input.value;
            const action = form.getAttribute('action');

            fetch(action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({ quantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const row = form.closest('tr') || form.closest('.flex.flex-col');
                    const subtotalCell = row?.querySelector('.js-subtotal');
                    if (subtotalCell) {
                        subtotalCell.textContent = `${data.subtotal} €`;
                    }
                    document.querySelectorAll('.js-cart-total').forEach(el => {
                        el.textContent = `Total : ${data.total} €`;
                    });
                } else {
                    alert(data.message || 'Erreur lors de la mise à jour.');
                }
            })
            .catch(() => {
                alert('Une erreur est survenue.');
            });
        });
    });

    // Compteur panier
    document.querySelectorAll('.add-to-cart-button').forEach(button => {
        button.addEventListener('click', () => {
            const countEl = document.getElementById('cart-count');
            let current = parseInt(countEl.textContent) || 0;
            current++;
            countEl.textContent = current;
            countEl.classList.remove('hidden');
        });
    });
});