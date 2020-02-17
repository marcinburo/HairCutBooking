<?php

namespace App\Controller;

use App\Exception\BadRequestParamException;
use App\Service\bookingServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApiController
 * @package App\Controller
 */
class ApiController extends AbstractController
{
    /**
     * @var bookingServiceInterface
     */
    private $bookingService;

    /**
     * ApiController constructor.
     * @param bookingServiceInterface $bookingService
     */
    public function __construct(bookingServiceInterface $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * @Route("/get_bookings/{date}", name="get_bookings", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getBookings(Request $request): JsonResponse
    {
        $date = $request->attributes->get('date');

        return new JsonResponse(['data' => $this->bookingService->getBookings($date)], Response::HTTP_OK);
    }

    /**
     * @Route("/get_available_booking_slots/{date}", name="get_available_booking_slots", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getAvailableBookingSlots(Request $request): JsonResponse
    {
        $date = $request->attributes->get('date');

        return new JsonResponse(['data' => $this->bookingService->getAvailableBookingSlots($date)], Response::HTTP_OK);
    }

    /**
     * @Route("/get_booking/{id}", name="get_booking", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getBooking(Request $request): JsonResponse
    {
        $id = $request->attributes->get('id');

        return new JsonResponse(['data' => $this->bookingService->getBooking($id)], Response::HTTP_OK);
    }

    /**
     * @Route("/add_booking", name="add_booking", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws BadRequestParamException
     */
    public function addBooking(Request $request): JsonResponse
    {
        $params = $request->getContent();

        if($data = json_decode($params, true)){
            $result = $this->bookingService->addBooking($data);
        }else{
            throw new BadRequestParamException('Bad request parameters: Malformed Json');
        }

        return new JsonResponse(['message' => 'Your booking is confirmed, Your booking ID is: '.$result], Response::HTTP_OK);
    }

    /**
     * @Route("/get_booking_slots", name="get_booking_slots", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getBookingSlots(Request $request): JsonResponse
    {
        return new JsonResponse(['data' => $this->bookingService->getBookingSlots()], Response::HTTP_OK);
    }
}
