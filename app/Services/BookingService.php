<?php

namespace App\Services;

use App\DTOs\Booking\BookingDTO;
use App\DTOs\Booking\BookingSlotDTO;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\BookingSlot;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService extends BaseService
{
    public function listUserBookings(): Collection
    {
        return Booking::with('slots')
            ->where('user_id', Auth::id())
            ->get();
    }

    public function createBooking(BookingDTO $bookingDTO): Booking
    {
        return DB::transaction(function () use ($bookingDTO) {
            foreach ($bookingDTO->slots as $slot) {
                if ($this->hasConflict($slot->startTime, $slot->endTime)) {
                    throw ValidationException::withMessages(['message' => 'Slot conflict']);
                }
            }

            $booking = Booking::create(['user_id' => Auth::id()]);

            foreach ($bookingDTO->slots as $slot) {
                $booking->slots()->create([
                    'start_time' => $slot->startTime,
                    'end_time' => $slot->endTime,
                ]);
            }

            $this->logAction('booking_created', [
                'booking_id' => $booking->id,
                'slots' => $bookingDTO->slots->toArray(),
            ]);

            return $booking->load('slots');
        });
    }

    public function updateSlot(Booking $booking, BookingSlot $slot, BookingSlotDTO $slotDTO): BookingSlot
    {
        return $this->withAuthorization($booking, function () use ($booking, $slot, $slotDTO) {
            return DB::transaction(function () use ($booking, $slot, $slotDTO) {
                if ($this->hasConflict($slotDTO->startTime, $slotDTO->endTime, $slot->id)) {
                    throw ValidationException::withMessages(['message' => 'Slot conflict']);
                }

                $slot->update([
                    'start_time' => $slotDTO->startTime,
                    'end_time' => $slotDTO->endTime,
                ]);

                $this->logAction('slot_updated', [
                    'booking_id' => $booking->id,
                    'slot_id' => $slot->id,
                    'start_time' => $slotDTO->startTime,
                    'end_time' => $slotDTO->endTime,
                ]);

                return $slot;
            });
        });
    }

    public function addSlot(Booking $booking, BookingSlotDTO $slotDTO): BookingSlot
    {
        return $this->withAuthorization($booking, function () use ($booking, $slotDTO) {
            return DB::transaction(function () use ($booking, $slotDTO) {
                if ($this->hasConflict($slotDTO->startTime, $slotDTO->endTime)) {
                    throw ValidationException::withMessages(['message' => 'Slot conflict']);
                }

                $slot = $booking->slots()->create([
                    'start_time' => $slotDTO->startTime,
                    'end_time' => $slotDTO->endTime,
                ]);

                $this->logAction('slot_added', [
                    'booking_id' => $booking->id,
                    'slot_id' => $slot->id,
                    'start_time' => $slotDTO->startTime,
                    'end_time' => $slotDTO->endTime,
                ]);

                return $slot;
            });
        });
    }

    public function deleteBooking(Booking $booking): void
    {
        $this->withAuthorization($booking, function () use ($booking) {
            DB::transaction(function () use ($booking) {
                $booking->delete();

                $this->logAction('booking_deleted', [
                    'booking_id' => $booking->id,
                ]);
            });
        });
    }

    private function hasConflict(string $start, string $end, ?int $excludeId = null): bool
    {
        $query = BookingSlot::query();

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->where(function ($q) use ($start, $end) {
            $q->whereBetween('start_time', [$start, $end])
                ->orWhereBetween('end_time', [$start, $end])
                ->orWhere(function ($q2) use ($start, $end) {
                    $q2->where('start_time', '<=', $start)
                        ->where('end_time', '>=', $end);
                });
        })->exists();
    }

    private function logAction(string $action, array $data = []): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'data' => $data,
        ]);
    }
}
