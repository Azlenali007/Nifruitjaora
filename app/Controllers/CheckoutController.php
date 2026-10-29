<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\Address;
use App\Models\Order;
use App\Models\Setting;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\RazorpayService;
use App\Helpers\Validator;
use App\Helpers\Response;
use Exception;

class CheckoutController {
    public function index(): void {
        $user = auth();
        $couponCode = session('applied_coupon_code');
        $pincode = session('delivery_pincode');

        $cartSummary = CartService::getCartSummary($user['id'] ?? null, session_id(), $couponCode, $pincode);

        if ($cartSummary['total_items'] === 0) {
            flash('warning', 'Your cart is empty. Add fresh coconuts to get started!');
            redirect('/products');
            return;
        }

        $savedAddresses = $user ? Address::getByUserId($user['id']) : [];
        $deliveryZones = Setting::allDeliveryZones();
        $razorpay = new RazorpayService();

        View::render('checkout/index', [
            'title' => 'Express Checkout | N.A Fresh Fruits & Coconuts',
            'cartSummary' => $cartSummary,
            'user' => $user,
            'savedAddresses' => $savedAddresses,
            'deliveryZones' => $deliveryZones,
            'razorpayEnabled' => $razorpay->isEnabled(),
            'razorpayKeyId' => $razorpay->getKeyId(),
            'razorpayMode' => $razorpay->getMode()
        ]);
    }

    public function submit(): void {
        $validator = Validator::make($_POST, [
            'name' => 'required|min:2|max:100',
            'phone' => 'required|phone',
            'address' => 'required|min:10',
            'pincode' => 'required|pincode',
            'email' => 'email'
        ]);

        if ($validator->fails()) {
            if ($this->isAjax()) {
                Response::error($validator->firstError() ?? 'Please correct form errors.', $validator->errors());
                return;
            }
            flash('error', $validator->firstError() ?? 'Please correct form errors.');
            redirect('/checkout');
            return;
        }

        try {
            $customerData = [
                'name' => trim($_POST['name']),
                'phone' => preg_replace('/[^0-9]/', '', $_POST['phone']),
                'email' => !empty($_POST['email']) ? trim($_POST['email']) : null,
                'address' => trim($_POST['address']),
                'pincode' => trim($_POST['pincode']),
                'instructions' => !empty($_POST['instructions']) ? trim($_POST['instructions']) : null
            ];

            // Save address for logged-in user if requested
            $user = auth();
            if ($user && !empty($_POST['save_address'])) {
                Address::create([
                    'user_id' => $user['id'],
                    'recipient_name' => $customerData['name'],
                    'phone' => $customerData['phone'],
                    'address_line1' => $customerData['address'],
                    'pincode' => $customerData['pincode'],
                    'is_default' => 0
                ]);
            }

            $couponCode = session('applied_coupon_code');
            $checkoutResult = OrderService::processCheckout($customerData, $couponCode);

            // Clean coupon from session after successful order initiation
            unset($_SESSION['applied_coupon_code']);

            if ($this->isAjax()) {
                Response::success('Order initiated successfully', $checkoutResult);
                return;
            }

            // Redirect to confirmation or order tracking
            redirect("/checkout/confirmation/{$checkoutResult['order_number']}");
        } catch (Exception $e) {
            if ($this->isAjax()) {
                Response::error($e->getMessage(), [], 400);
                return;
            }
            flash('error', $e->getMessage());
            redirect('/checkout');
        }
    }

    public function confirmation(string $orderNumber): void {
        $order = Order::findByOrderNumber($orderNumber);
        if (!$order) {
            flash('error', 'Order not found.');
            redirect('/');
            return;
        }

        View::render('checkout/confirmation', [
            'title' => "Order #{$order['order_number']} Confirmed | N.A Fresh Fruits",
            'order' => $order
        ]);
    }

    private function isAjax(): bool {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json'));
    }
}
