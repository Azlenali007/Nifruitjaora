// Global App JavaScript
document.addEventListener('DOMContentLoaded', () => {
  // Listen for cart update events
  window.addEventListener('cart-updated', (e) => {
    const badge = document.getElementById('cart-badge');
    if (badge && e.detail && typeof e.detail.total_items !== 'undefined') {
      badge.textContent = e.detail.total_items;
      badge.classList.remove('hidden');
      if (e.detail.total_items === 0) {
        badge.classList.add('hidden');
      }
    }
  });
});
