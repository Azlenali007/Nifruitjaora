// Razorpay Checkout Integration
document.addEventListener('alpine:init', () => {
  Alpine.data('checkoutHandler', (config = {}) => ({
    isSubmitting: false,
    selectedAddressId: '',
    pincode: config.pincode || '',
    name: config.name || '',
    phone: config.phone || '',
    email: config.email || '',
    address: config.address || '',
    instructions: '',
    saveAddress: true,

    selectSavedAddress(addr) {
      this.selectedAddressId = addr.id;
      this.name = addr.recipient_name;
      this.phone = addr.phone;
      this.address = `${addr.address_line1} ${addr.address_line2 || ''} ${addr.landmark || ''}`.trim();
      this.pincode = addr.pincode;
    },

    async submitOrder() {
      if (this.isSubmitting) return;

      if (!this.name.trim() || !this.phone.trim() || !this.address.trim() || !this.pincode.trim()) {
        Notify.toast('Please fill all mandatory delivery details', 'warning');
        return;
      }

      this.isSubmitting = true;
      const form = document.getElementById('checkoutForm');
      const formData = new FormData(form);

      try {
        const response = await fetch('/checkout/submit', {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const result = await response.json();
        if (!result.success) {
          Notify.toast(result.message || 'Error creating order.', 'error');
          this.isSubmitting = false;
          return;
        }

        const orderData = result.data;
        this.openRazorpayModal(orderData);
      } catch (err) {
        Notify.toast('Network error creating order. Please retry.', 'error');
        this.isSubmitting = false;
      }
    },

    openRazorpayModal(orderData) {
      if (typeof Razorpay === 'undefined') {
        // Fallback for environments where third-party script is blocked or offline
        this.handleSandboxPayment(orderData);
        return;
      }

      const options = {
        key: orderData.razorpay_key_id,
        amount: Math.round(orderData.total_amount * 100),
        currency: 'INR',
        name: 'N.A Fresh Fruits & Coconuts',
        description: `Order #${orderData.order_number}`,
        image: '/assets/images/nariyal-pani.webp',
        order_id: orderData.razorpay_order_id,
        prefill: {
          name: orderData.customer.name,
          email: orderData.customer.email,
          contact: orderData.customer.phone
        },
        theme: {
          color: '#16a34a' // Tropical emerald
        },
        handler: async (response) => {
          await this.verifyPaymentOnServer(response, orderData.order_number);
        },
        modal: {
          ondismiss: () => {
            this.isSubmitting = false;
            Notify.toast('Payment was cancelled. You can retry anytime.', 'info');
          }
        }
      };

      try {
        const rzp = new Razorpay(options);
        rzp.on('payment.failed', async (resp) => {
          this.isSubmitting = false;
          await this.reportPaymentFailure(resp.error, orderData.razorpay_order_id);
          Notify.toast(`Payment failed: ${resp.error.description}`, 'error');
        });
        rzp.open();
      } catch (e) {
        this.handleSandboxPayment(orderData);
      }
    },

    async handleSandboxPayment(orderData) {
      // Sandbox interactive prompt
      const confirmed = await Notify.confirm(
        'Razorpay Test Mode',
        `Simulate test payment for Order #${orderData.order_number} (₹${orderData.total_amount})?`,
        'Authorize Test Payment'
      );

      if (confirmed.isConfirmed) {
        const mockResponse = {
          razorpay_order_id: orderData.razorpay_order_id,
          razorpay_payment_id: 'pay_test_' + Math.random().toString(36).substring(2, 12),
          razorpay_signature: 'sandbox_sig_' + Math.random().toString(36).substring(2, 16)
        };
        await this.verifyPaymentOnServer(mockResponse, orderData.order_number);
      } else {
        this.isSubmitting = false;
        Notify.toast('Payment cancelled.', 'info');
      }
    },

    async verifyPaymentOnServer(paymentResponse, orderNumber) {
      Notify.toast('Verifying payment securely on server...', 'info');

      const fd = new FormData();
      fd.append('razorpay_order_id', paymentResponse.razorpay_order_id);
      fd.append('razorpay_payment_id', paymentResponse.razorpay_payment_id);
      fd.append('razorpay_signature', paymentResponse.razorpay_signature);
      fd.append('order_number', orderNumber);

      try {
        const verifyRes = await fetch('/api/payments/razorpay/verify', {
          method: 'POST',
          body: fd,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const verifyData = await verifyRes.json();
        if (verifyData.success) {
          Notify.toast('Payment confirmed! Redirecting...', 'success');
          setTimeout(() => {
            window.location.href = verifyData.data.redirect_url;
          }, 600);
        } else {
          this.isSubmitting = false;
          Notify.toast(verifyData.message || 'Payment signature verification failed.', 'error');
        }
      } catch (err) {
        this.isSubmitting = false;
        Notify.toast('Error contacting server to verify payment.', 'error');
      }
    },

    async reportPaymentFailure(errorData, razorpayOrderId) {
      const fd = new FormData();
      fd.append('razorpay_order_id', razorpayOrderId);
      fd.append('error_code', errorData.code || 'PAYMENT_FAILED');
      fd.append('error_description', errorData.description || 'Payment rejected');

      try {
        await fetch('/api/payments/razorpay/failed', {
          method: 'POST',
          body: fd,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
      } catch (e) {
        console.error('Failed to report failure log:', e);
      }
    }
  }));
});
