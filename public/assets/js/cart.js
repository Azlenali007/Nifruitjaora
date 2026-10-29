// Shopping Cart Interactive Management
document.addEventListener('alpine:init', () => {
  Alpine.data('cartManager', (initialSummary = {}) => ({
    summary: initialSummary,
    couponCode: '',
    loading: false,

    async updateQty(itemId, quantity) {
      if (this.loading) return;
      this.loading = true;

      const fd = new FormData();
      fd.append('item_id', itemId);
      fd.append('quantity', quantity);

      try {
        const res = await fetch('/api/cart/update', {
          method: 'POST',
          body: fd,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.success) {
          this.summary = data.data;
          window.dispatchEvent(new CustomEvent('cart-updated', { detail: { total_items: data.data.total_items } }));
        } else {
          Notify.toast(data.message || 'Error updating quantity', 'error');
        }
      } catch (e) {
        Notify.toast('Network error', 'error');
      } finally {
        this.loading = false;
      }
    },

    async removeItem(itemId) {
      const confirmed = await Notify.confirm('Remove Item?', 'Are you sure you want to remove this item from your cart?');
      if (!confirmed.isConfirmed) return;

      this.loading = true;
      const fd = new FormData();
      fd.append('item_id', itemId);

      try {
        const res = await fetch('/api/cart/remove', {
          method: 'POST',
          body: fd,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.success) {
          this.summary = data.data;
          window.dispatchEvent(new CustomEvent('cart-updated', { detail: { total_items: data.data.total_items } }));
          Notify.toast('Item removed from cart', 'success');
        } else {
          Notify.toast(data.message || 'Error removing item', 'error');
        }
      } catch (e) {
        Notify.toast('Network error', 'error');
      } finally {
        this.loading = false;
      }
    },

    async applyCoupon() {
      if (!this.couponCode.trim()) {
        Notify.toast('Please enter a coupon code', 'warning');
        return;
      }

      this.loading = true;
      const fd = new FormData();
      fd.append('coupon_code', this.couponCode);

      try {
        const res = await fetch('/cart/coupon/apply', {
          method: 'POST',
          body: fd,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.success) {
          this.summary = data.data;
          Notify.toast(data.message || 'Coupon applied!', 'success');
        } else {
          Notify.toast(data.message || 'Invalid coupon', 'error');
        }
      } catch (e) {
        Notify.toast('Error applying coupon', 'error');
      } finally {
        this.loading = false;
      }
    },

    async removeCoupon() {
      this.loading = true;
      try {
        const res = await fetch('/cart/coupon/remove', {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.success) {
          window.location.reload();
        }
      } catch (e) {
        window.location.reload();
      }
    }
  }));
});
