<?php

namespace App\DTOs\Booking;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;

/**
 * @OA\Schema(
 *     schema="BookingSlotDTO",
 *     required={"start_time","end_time"},
 *     @OA\Property(property="start_time", type="string", format="date-time", example="2025-06-25T12:00:00"),
 *     @OA\Property(property="end_time", type="string", format="date-time", example="2025-06-25T13:00:00")
 * )
 */
class BookingSlotDTO extends Data
{
    #[MapName('start_time')]
    public string $startTime;

    #[MapName('end_time')]
    public string $endTime;

    public function __construct(string $startTime, string $endTime)
    {
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }
}
