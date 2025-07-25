<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_view_addresses_index()
    {
        $this->actingAs($this->user)
            ->get(route('account.addresses'))
            ->assertStatus(200)
            ->assertViewIs('profile.addresses');
    }

    public function test_can_create_address()
    {
        $addressData = [
            'recipient_name' => $this->faker->name,
            'phone' => '0123456789',
            'address_detail' => $this->faker->streetAddress,
            'city' => 'Hà Nội',
            'district' => 'Hai Bà Trưng',
            'ward' => 'Bạch Mai',
        ];

        $this->actingAs($this->user)
            ->post(route('account.addresses.store'), $addressData)
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('addresses', [
            'user_id' => $this->user->id,
            'recipient_name' => $addressData['recipient_name'],
            'phone' => $addressData['phone'],
            'is_default' => true // First address should be default
        ]);
    }

    public function test_can_update_address()
    {
        $address = Address::factory()->create([
            'user_id' => $this->user->id,
            'is_default' => true
        ]);

        $updatedData = [
            'recipient_name' => 'Updated Name',
            'phone' => '0987654321',
            'address_detail' => 'Updated Address',
            'city' => 'Hồ Chí Minh',
            'district' => 'Quận 1',
            'ward' => 'Phường 1',
        ];

        $this->actingAs($this->user)
            ->put(route('account.addresses.update', $address->id), $updatedData)
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'recipient_name' => 'Updated Name',
            'phone' => '0987654321'
        ]);
    }

    public function test_can_delete_address()
    {
        $address = Address::factory()->create([
            'user_id' => $this->user->id,
            'is_default' => false
        ]);

        $this->actingAs($this->user)
            ->delete(route('account.addresses.destroy', $address->id))
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
    }

    public function test_can_set_default_address()
    {
        $defaultAddress = Address::factory()->create([
            'user_id' => $this->user->id,
            'is_default' => true
        ]);

        $newDefaultAddress = Address::factory()->create([
            'user_id' => $this->user->id,
            'is_default' => false
        ]);

        $this->actingAs($this->user)
            ->post(route('account.addresses.setDefault', $newDefaultAddress->id))
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('addresses', [
            'id' => $newDefaultAddress->id,
            'is_default' => true
        ]);

        $this->assertDatabaseHas('addresses', [
            'id' => $defaultAddress->id,
            'is_default' => false
        ]);
    }

    public function test_cannot_delete_default_address_when_others_exist()
    {
        $defaultAddress = Address::factory()->create([
            'user_id' => $this->user->id,
            'is_default' => true
        ]);

        Address::factory()->create([
            'user_id' => $this->user->id,
            'is_default' => false
        ]);

        $this->actingAs($this->user)
            ->delete(route('account.addresses.destroy', $defaultAddress->id))
            ->assertStatus(400)
            ->assertJson(['success' => false]);
    }

    public function test_user_can_only_access_own_addresses()
    {
        $otherUser = User::factory()->create();
        $otherUserAddress = Address::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $this->actingAs($this->user)
            ->get(route('account.addresses.edit', $otherUserAddress->id))
            ->assertStatus(404);
    }
}
