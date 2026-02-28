<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('is_admin', false)->get();
        $products = Product::where('status', 'active')->get();

        foreach ($users as $user) {
            // Create 2-5 orders per user
            for ($i = 0; $i < rand(2, 5); $i++) {
                $orderProducts = $products->random(rand(1, 3));

                $subtotal = 0;
                $items = [];

                foreach ($orderProducts as $product) {
                    $quantity = rand(1, 3);
                    $unitPrice = $product->price;
                    $itemSubtotal = $unitPrice * $quantity;
                    $subtotal += $itemSubtotal;

                    $items[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'subtotal' => $itemSubtotal,
                        'product_data' => json_encode([
                            'name' => $product->name,
                            'sku' => $product->sku,
                            'price' => $product->price,
                        ]),
                    ];
                }

                $tax = round($subtotal * 0.1, 2);
                $shipping = rand(0, 1) ? 10 : 0;
                $discount = rand(0, 20);
                $total = round($subtotal + $tax + $shipping - $discount, 2);

                $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
                $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];
                $paymentMethods = ['credit_card', 'paypal', 'bank_transfer'];
                $shippingMethods = ['standard', 'express', 'overnight'];

                $order = Order::create([
                    'order_number' => Order::generateOrderNumber(),
                    'user_id' => $user->id,
                    'status' => $statuses[array_rand($statuses)],
                    'payment_status' => $paymentStatuses[array_rand($paymentStatuses)],
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'shipping' => $shipping,
                    'discount' => $discount,
                    'total' => $total,
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'transaction_id' => 'TXN-' . strtoupper(bin2hex(random_bytes(8))),
                    'shipping_address' => json_encode([
                        'street' => fake()->streetAddress(),
                        'city' => fake()->city(),
                        'state' => fake()->state(),
                        'zip' => fake()->postcode(),
                        'country' => 'USA',
                    ]),
                    'billing_address' => json_encode([
                        'street' => fake()->streetAddress(),
                        'city' => fake()->city(),
                        'state' => fake()->state(),
                        'zip' => fake()->postcode(),
                        'country' => 'USA',
                    ]),
                    'shipping_method' => $shippingMethods[array_rand($shippingMethods)],
                    'tracking_number' => rand(0, 1) ? 'TRACK-' . strtoupper(bin2hex(random_bytes(6))) : null,
                    'notes' => rand(0, 1) ? fake()->sentence() : null,
                    'shipped_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
                    'delivered_at' => rand(0, 1) ? now()->subDays(rand(1, 15)) : null,
                    'created_at' => now()->subDays(rand(1, 60)),
                ]);

                // Create order items
                foreach ($items as $item) {
                    $order->items()->create($item);
                }
            }
        }
    }
}
