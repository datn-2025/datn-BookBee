<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Cart;
use App\Models\Address;
use App\Models\OrderStatus;
use App\Models\PaymentStatus;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $book;
    protected $address;
    protected $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();

        // Tạo dữ liệu cần thiết cho test
        $this->user = User::factory()->create();
        $this->book = Book::factory()->create();
        $this->address = Address::factory()->create(['user_id' => $this->user->id]);
        $this->paymentMethod = PaymentMethod::factory()->create();

        // Tạo trạng thái đơn hàng và thanh toán mặc định
        OrderStatus::factory()->create(['name' => 'pending']);
        PaymentStatus::factory()->create(['name' => 'pending']);
    }

    /** @test */
    public function user_can_view_checkout_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('orders.checkout'));

        $response->assertStatus(200)
            ->assertViewIs('orders.checkout');
    }

    /** @test */
    public function user_cannot_checkout_with_empty_cart()
    {
        $response = $this->actingAs($this->user)
            ->get(route('orders.checkout'));

        $response->assertRedirect(route('cart.index'))
            ->assertSessionHas('error', 'Giỏ hàng trống');
    }

    /** @test */
    public function user_can_create_order()
    {
        // Thêm sách vào giỏ hàng
        Cart::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'quantity' => 1,
            'price' => 100000
        ]);

        $orderData = [
            'address_id' => $this->address->id,
            'payment_method_id' => $this->paymentMethod->id,
            'note' => 'Giao hàng giờ hành chính'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('orders.store'), $orderData);

        $response->assertRedirect()
            ->assertSessionHas('success', 'Đặt hàng thành công');

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'address_id' => $this->address->id,
            'note' => 'Giao hàng giờ hành chính'
        ]);
    }

    /** @test */
    public function order_requires_valid_address()
    {
        $response = $this->actingAs($this->user)
            ->post(route('orders.store'), [
                'payment_method_id' => $this->paymentMethod->id
            ]);

        $response->assertSessionHasErrors('address_id');
    }

    /** @test */
    public function order_requires_valid_payment_method()
    {
        $response = $this->actingAs($this->user)
            ->post(route('orders.store'), [
                'address_id' => $this->address->id
            ]);

        $response->assertSessionHasErrors('payment_method_id');
    }
}
