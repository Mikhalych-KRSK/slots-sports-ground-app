<?php

namespace App\DTOs\Booking;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\DataCollectionOf;

/**
 * @OA\Schema(
 *     schema="BookingDTO",
 *     required={"slots"},
 *     @OA\Property(
 *         property="slots",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/BookingSlotDTO")
 *     )
 * )
 */
class BookingDTO extends Data
{
    #[DataCollectionOf(BookingSlotDTO::class)]
    public DataCollection $slots;

    public function __construct(DataCollection $slots)
    {
        $this->slots = $slots;
    }
}
