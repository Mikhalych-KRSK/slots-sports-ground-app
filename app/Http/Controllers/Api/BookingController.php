<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Booking\BookingDTO;
use App\DTOs\Booking\BookingSlotDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Http\Requests\BookingSlotRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\BookingSlotResource;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Bookings",
 *     description="Операции с бронированиями"
 * )
 */
class BookingController extends Controller
{
    public function __construct(private BookingService $service) {}

    /**
     * @OA\Get(
     *     path="/api/bookings",
     *     summary="Получить список бронирований текущего пользователя",
     *     tags={"Bookings"},
     *     security={{"api_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Список бронирований",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/BookingDTO"))
     *     ),
     *     @OA\Response(response=401, description="Неавторизован")
     * )
     */
    public function index(): JsonResponse
    {
        $bookings = $this->service->listUserBookings();

        return response()->json(BookingResource::collection($bookings));
    }

    /**
     * @OA\Post(
     *     path="/api/bookings",
     *     summary="Создать бронирование с несколькими слотами",
     *     tags={"Bookings"},
     *     security={{"api_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BookingDTO")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Бронирование создано",
     *         @OA\JsonContent(ref="#/components/schemas/BookingDTO")
     *     ),
     *     @OA\Response(response=422, description="Конфликт слотов")
     * )
     */
    public function store(BookingRequest $request): JsonResponse
    {
        $dto = BookingDTO::from($request->validated());
        $booking = $this->service->createBooking($dto);

        return response()->json(new BookingResource($booking), 201);
    }

    /**
     * @OA\Patch(
     *     path="/api/bookings/{booking}/slots/{slot}",
     *     summary="Обновить слот бронирования",
     *     tags={"Bookings"},
     *     security={{"api_token":{}}},
     *     @OA\Parameter(name="booking", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="slot", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BookingSlotDTO")
     *     ),
     *     @OA\Response(response=200, description="Слот обновлён", @OA\JsonContent(ref="#/components/schemas/BookingSlotDTO")),
     *     @OA\Response(response=403, description="Доступ запрещён"),
     *     @OA\Response(response=422, description="Конфликт слотов")
     * )
     */
    public function updateSlot(BookingSlotRequest $request, Booking $booking, BookingSlot $slot): JsonResponse
    {
        $slotDto = BookingSlotDTO::from($request->validated());
        $updated = $this->service->updateSlot($booking, $slot, $slotDto);

        return response()->json(new BookingSlotResource($updated));
    }

    /**
     * @OA\Post(
     *     path="/api/bookings/{booking}/slots",
     *     summary="Добавить слот в бронирование",
     *     tags={"Bookings"},
     *     security={{"api_token":{}}},
     *     @OA\Parameter(name="booking", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BookingSlotDTO")
     *     ),
     *     @OA\Response(response=201, description="Слот добавлен", @OA\JsonContent(ref="#/components/schemas/BookingSlotDTO")),
     *     @OA\Response(response=422, description="Конфликт слотов")
     * )
     */
    public function addSlot(BookingSlotRequest $request, Booking $booking): JsonResponse
    {
        $slotDto = BookingSlotDTO::from($request->validated());
        $slot = $this->service->addSlot($booking, $slotDto);

        return response()->json(new BookingSlotResource($slot), 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/bookings/{booking}",
     *     summary="Удалить бронирование",
     *     tags={"Bookings"},
     *     security={{"api_token":{}}},
     *     @OA\Parameter(name="booking", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Бронирование удалено", @OA\JsonContent(@OA\Property(property="message", type="string", example="Booking deleted"))),
     *     @OA\Response(response=403, description="Доступ запрещён"),
     *     @OA\Response(response=404, description="Бронирование не найдено")
     * )
     */
    public function destroy(Booking $booking): JsonResponse
    {
        $this->service->deleteBooking($booking);

        return response()->json(['message' => 'Booking deleted']);
    }
}
