document.addEventListener('DOMContentLoaded', function () {
    const burgerBtn = document.getElementById('burger-button');
    const mobileMenu = document.getElementById('mobile-menu');

    if (burgerBtn && mobileMenu) {
        burgerBtn.addEventListener('click', () => {
            const expanded = burgerBtn.getAttribute('aria-expanded') === 'true';
            burgerBtn.setAttribute('aria-expanded', !expanded);
            mobileMenu.classList.toggle('hidden');
        });
    }
});