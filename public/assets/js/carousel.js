// Premium Interactive Coconut Carousel for N.A Fresh Fruits & Coconuts
// Full touch-swipe, mouse-drag, keyboard navigation, and centered peek cards matching visual reference
document.addEventListener('alpine:init', () => {
  Alpine.data('coconutCarousel', (initialProducts = []) => ({
    products: initialProducts,
    currentIndex: 0,
    quantity: 1,
    isAnimating: false,
    
    // Touch & Drag state
    touchStartX: 0,
    touchStartY: 0,
    touchCurrentX: 0,
    isDragging: false,
    dragOffset: 0,
    swipeThreshold: 45,

    init() {
      // Keyboard navigation
      window.addEventListener('keydown', (e) => {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
        if (e.key === 'ArrowLeft') {
          this.prev();
        } else if (e.key === 'ArrowRight') {
          this.next();
        }
      });
    },

    get total() {
      return this.products.length;
    },

    get currentProduct() {
      return this.products[this.currentIndex] || {};
    },

    get prevIndex() {
      if (this.total <= 1) return null;
      return (this.currentIndex - 1 + this.total) % this.total;
    },

    get nextIndex() {
      if (this.total <= 1) return null;
      return (this.currentIndex + 1) % this.total;
    },

    get prevProduct() {
      if (this.prevIndex === null) return null;
      return this.products[this.prevIndex];
    },

    get nextProduct() {
      if (this.nextIndex === null) return null;
      return this.products[this.nextIndex];
    },

    next() {
      if (this.isAnimating || this.total <= 1) return;
      this.isAnimating = true;
      this.currentIndex = (this.currentIndex + 1) % this.total;
      this.quantity = 1;
      this.dragOffset = 0;
      setTimeout(() => { this.isAnimating = false; }, 380);
    },

    prev() {
      if (this.isAnimating || this.total <= 1) return;
      this.isAnimating = true;
      this.currentIndex = (this.currentIndex - 1 + this.total) % this.total;
      this.quantity = 1;
      this.dragOffset = 0;
      setTimeout(() => { this.isAnimating = false; }, 380);
    },

    goTo(index) {
      if (this.isAnimating || index === this.currentIndex || index < 0 || index >= this.total) return;
      this.isAnimating = true;
      this.currentIndex = index;
      this.quantity = 1;
      this.dragOffset = 0;
      setTimeout(() => { this.isAnimating = false; }, 380);
    },

    incrementQty() {
      const stock = this.currentProduct.stock_quantity;
      if (stock && this.quantity >= stock) {
        if (window.Notify) {
          Notify.toast('Maximum available stock reached', 'info');
        }
        return;
      }
      this.quantity++;
    },

    decrementQty() {
      if (this.quantity > 1) {
        this.quantity--;
      }
    },

    // Touch event handlers
    handleTouchStart(e) {
      if (this.isAnimating) return;
      this.isDragging = true;
      this.touchStartX = e.touches[0].clientX;
      this.touchStartY = e.touches[0].clientY;
      this.touchCurrentX = this.touchStartX;
      this.dragOffset = 0;
    },

    handleTouchMove(e) {
      if (!this.isDragging) return;
      const currentX = e.touches[0].clientX;
      const currentY = e.touches[0].clientY;
      const diffX = currentX - this.touchStartX;
      const diffY = currentY - this.touchStartY;

      // If scrolling vertically more than horizontally, let standard page scroll happen
      if (Math.abs(diffY) > Math.abs(diffX) && Math.abs(diffX) < 15) {
        return;
      }

      this.touchCurrentX = currentX;
      // Dampen drag resistance
      this.dragOffset = diffX * 0.4;
    },

    handleTouchEnd(e) {
      if (!this.isDragging) return;
      this.isDragging = false;
      const diffX = this.touchCurrentX - this.touchStartX;

      if (Math.abs(diffX) > this.swipeThreshold) {
        if (diffX < 0) {
          this.next(); // Swiped left -> advance
        } else {
          this.prev(); // Swiped right -> go back
        }
      } else {
        this.dragOffset = 0;
      }
    },

    // Mouse drag handlers for desktop
    handleMouseDown(e) {
      // Ignore clicks on buttons or links
      if (e.target.closest('button') || e.target.closest('a') || this.isAnimating) return;
      this.isDragging = true;
      this.touchStartX = e.clientX;
      this.touchCurrentX = e.clientX;
      this.dragOffset = 0;
    },

    handleMouseMove(e) {
      if (!this.isDragging) return;
      this.touchCurrentX = e.clientX;
      this.dragOffset = (e.clientX - this.touchStartX) * 0.35;
    },

    handleMouseUp(e) {
      if (!this.isDragging) return;
      this.isDragging = false;
      const diffX = this.touchCurrentX - this.touchStartX;

      if (Math.abs(diffX) > this.swipeThreshold) {
        if (diffX < 0) {
          this.next();
        } else {
          this.prev();
        }
      } else {
        this.dragOffset = 0;
      }
    },

    // Add central product to cart
    async addActiveToCart(orderNow = false) {
      const prod = this.currentProduct;
      if (!prod || !prod.id) return;

      const formData = new FormData();
      formData.append('product_id', prod.id);
      formData.append('quantity', this.quantity);

      try {
        const res = await fetch('/api/cart/add', {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const data = await res.json();
        if (data.success) {
          window.dispatchEvent(new CustomEvent('cart-updated', { detail: data.data }));
          
          if (orderNow) {
            window.location.href = '/checkout';
          } else {
            if (window.Notify) {
              Notify.toast(`Added ${this.quantity}x ${prod.name} to cart!`, 'success');
            }
          }
        } else {
          if (window.Notify) {
            Notify.toast(data.message || 'Could not add to cart.', 'error');
          }
        }
      } catch (err) {
        if (window.Notify) {
          Notify.toast('Network error adding to cart.', 'error');
        }
      }
    },

    // Quick add from peek card
    async addQuickProduct(productId) {
      if (!productId) return;
      const formData = new FormData();
      formData.append('product_id', productId);
      formData.append('quantity', 1);

      try {
        const res = await fetch('/api/cart/add', {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.success) {
          window.dispatchEvent(new CustomEvent('cart-updated', { detail: data.data }));
          if (window.Notify) {
            Notify.toast('Added to cart!', 'success');
          }
        } else {
          if (window.Notify) {
            Notify.toast(data.message || 'Failed to add item.', 'error');
          }
        }
      } catch (err) {
        if (window.Notify) {
          Notify.toast('Network error', 'error');
        }
      }
    }
  }));
});
