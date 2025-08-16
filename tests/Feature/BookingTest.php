<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $token;
    private $headers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->api_token;

        $this->headers = [
            'Authorization' => "Bearer {$this->token}",
            'Accept' => 'application/json',
        ];
    }

    public function test_create_booking_with_multiple_slots()
    {
        $payload = [
            'slots' => [
                ['start_time' => '2025-08-20 10:00:00', 'end_time' => '2025-08-20 11:00:00'],
                ['start_time' => '2025-08-20 12:00:00', 'end_time' => '2025-08-20 13:00:00'],
            ]
        ];

        $response = $this->postJson('/api/bookings', $payload, $this->headers);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'user_id',
            'slots' => [
                ['start_time', 'end_time']
            ]
        ]);

        $this->assertDatabaseHas('bookings', ['user_id' => $this->user->id]);
        $this->assertDatabaseCount('booking_slots', 2);
    }

    public function test_add_slot_with_conflict_fails()
    {
        $booking = Booking::factory()->create(['user_id' => $this->user->id]);

        $booking->slots()->create([
            'start_time' => '2025-08-21 10:00:00',
            'end_time' => '2025-08-21 11:00:00',
        ]);

        $payload = [
            'start_time' => '2025-08-21 10:30:00',
            'end_time' => '2025-08-21 11:30:00',
        ];

        $response = $this->postJson("/api/bookings/{$booking->id}/slots", $payload, $this->headers);

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Slot conflict']);
    }

    public function test_update_slot_success_and_failure()
    {
        $booking = Booking::factory()->create(['user_id' => $this->user->id]);

        $slot = $booking->slots()->create([
            'start_time' => '2025-08-22 10:00:00',
            'end_time' => '2025-08-22 11:00:00',
        ]);

        $payload = [
            'start_time' => '2025-08-22 11:00:00',
            'end_time' => '2025-08-22 12:00:00',
        ];

        $response = $this->patchJson("/api/bookings/{$booking->id}/slots/{$slot->id}", $payload, $this->headers);

        $response->assertStatus(200);
        $this->assertDatabaseHas('booking_slots', [
            'id' => $slot->id,
            'start_time' => '2025-08-22 11:00:00',
            'end_time' => '2025-08-22 12:00:00',
        ]);

        // Добавляем ещё один слот
        $booking->slots()->create([
            'start_time' => '2025-08-22 12:00:00',
            'end_time' => '2025-08-22 13:00:00',
        ]);

        // Конфликтное обновление
        $payloadConflict = [
            'start_time' => '2025-08-22 12:30:00',
            'end_time' => '2025-08-22 13:30:00',
        ];

        $responseConflict = $this->patchJson("/api/bookings/{$booking->id}/slots/{$slot->id}", $payloadConflict, $this->headers);
        $responseConflict->assertStatus(422);
        $responseConflict->assertJsonFragment(['message' => 'Slot conflict']);
    }

    public function test_unauthorized_request_rejected()
    {
        $response = $this->getJson('/api/bookings');
        $response->assertStatus(401);

        $response = $this->postJson('/api/bookings', []);
        $response->assertStatus(401);
    }

    public function test_invalid_token_rejected()
    {
        $headers = [
            'Authorization' => 'Bearer invalid_token',
            'Accept' => 'application/json',
        ];

        $response = $this->getJson('/api/bookings', $headers);
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Invalid token']);
    }
}
