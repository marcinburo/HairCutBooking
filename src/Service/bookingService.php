<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\BookingSlot;
use App\Exception\BadRequestParamException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class bookingService
 * @package App\Service
 */
class bookingService implements bookingServiceInterface
{
//  TODO: this should be moved into config (ENV) as we might want to change opening and closing hours for our hair salon without touching code
//  TODO: Add methods for updating/deleting bookings and booking slots to cover complete CRUD
//  TODO: Move setting data into forms, that way improve validation
//  TODO: Extend thrown exceptions and listeners to cover all possible scenarios for various API calls
//  TODO: It would be "nice to have" tests for example written with codeception to ensure all works fine
    const BOOKING_OPEN_TIME = 8;
    const BOOKING_CLOSE_TIME = 20;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * bookingService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     * @return array
     */
    public function getBooking(int $id) : array
    {
        $bookingRepository = $this->entityManager->getRepository(Booking::class);

        return $bookingRepository->findSingleById($id);
    }

    /**
     * @param string $date
     * @return array
     */
    public function getBookings(string $date) : array
    {
        $bookingRepository = $this->entityManager->getRepository(Booking::class);
        $formattedDate = \DateTime::createFromFormat('Y-m-j', $date);

        return $bookingRepository->getAllForDate($formattedDate);
    }

    /**
     * @param string $date
     * @return array
     * @throws \Exception
     */
    public function getAvailableBookingSlots(string $date) : array
    {
        return $this->getAvailableSlots($date);
    }

    /**
     * @return array
     */
    public function getBookingSlots() : array
    {
        $bookingSlotRepository = $this->entityManager->getRepository(BookingSlot::class);

        return $bookingSlotRepository->getAll();
    }

    /**
     * @param array $data
     * @return int|null
     * @throws BadRequestParamException
     */
    public function addBooking(array $data) : ?int
    {
        $booking = new Booking();
        $booking->setCreatedAt(new \DateTime('now'));
        $booking->setCustomer($data['customer']);

        $bookingSlotRepository = $this->entityManager->getRepository(BookingSlot::class);
        $bookingSlot = $bookingSlotRepository->find($data['slotId']);

        $booking->setBookingSlot($bookingSlot);
        $booking->setCustomerPhone($data['customerPhone']);
        $booking->setHairDresser($data['hairDresser']);

        $visitTimeFormatted = \DateTime::createFromFormat('Y-m-j h:i', $data['visitTime']);
        $visitDurationFormatted = new \DateInterval('PT'.$data['visitDuration'].'M');

        $booking->setVisitTime($visitTimeFormatted);
        $booking->setVisitDuration($visitDurationFormatted);

        if(!$this->validateData($data, $bookingSlot, $visitTimeFormatted, $visitDurationFormatted)){
            throw new BadRequestParamException('Bad request parameters: Please verify if all mandatory data is sent, also check if data is valid');
        }

        $availableSlots = $this->checkBookingAvailability($visitTimeFormatted, $visitDurationFormatted);

        if(!$availableSlots[$bookingSlot->getId()]){
            throw new BadRequestParamException('Bad request parameters: requested slot is not available');
        }

        $this->entityManager->persist($booking);
        $this->entityManager->flush();

        return $booking->getId();
    }

    /**
     * @param array $data
     * @param BookingSlot $bookingSlot
     * @param \DateTime $visitTimeFormatted
     * @param \DateInterval $visitDurationFormatted
     * @return bool
     */
    private function validateData(array $data, BookingSlot $bookingSlot, \DateTime $visitTimeFormatted, \DateInterval $visitDurationFormatted) : bool
    {
        $visitTimeFormattedValidate = clone $visitTimeFormatted;

        if(!isset($data['customer']) || empty($data['customer'])){
            return false;
        }

        if(!isset($data['customerPhone']) || empty($data['customerPhone'])){
            return false;
        }

        if(!isset($data['hairDresser']) || empty($data['hairDresser'])){
            return false;
        }

        if(!($visitTimeFormattedValidate instanceof \DateTime)){
            return false;
        }

        if(!($visitDurationFormatted instanceof \DateInterval)){
            return false;
        }

        if(!$bookingSlot){
            return false;
        }

        if((int)$visitTimeFormattedValidate->format('h') < self::BOOKING_OPEN_TIME){
            return false;
        }

        if($visitTimeFormattedValidate->add($visitDurationFormatted)->format('h') > self::BOOKING_CLOSE_TIME){
            return false;
        }

        return true;
    }

    /**
     * @param \DateTime $visitTimeFormatted
     * @param \DateInterval $visitDurationFormatted
     * @return array
     */
    private function checkBookingAvailability(\DateTime $visitTimeFormatted, \DateInterval $visitDurationFormatted) : array
    {
        $bookingSlotRepository = $this->entityManager->getRepository(BookingSlot::class);
        $bookingSlots = $bookingSlotRepository->getAll();
        $availableSlots = [];

        foreach($bookingSlots as $bookingSlot){
            $bookings = $this->entityManager->getRepository(Booking::class)->getByVisitTimeAndSlot($visitTimeFormatted, $bookingSlot);
            if($bookings){
                $availableSlots[$bookingSlot['id']] = $this->checkBookingAvailabilityByBookingSlot($bookings, $visitTimeFormatted, $visitDurationFormatted);
            }else{
                $availableSlots[$bookingSlot['id']] = true;
            }
        }

        return $availableSlots;
    }

    /**
     * @param array $bookings
     * @param \DateTime $visitTimeFormatted
     * @param \DateInterval $visitDurationFormatted
     * @return bool
     */
    private function checkBookingAvailabilityByBookingSlot(array $bookings, \DateTime $visitTimeFormatted, \DateInterval $visitDurationFormatted) : bool
    {
        foreach ($bookings as $booking){
            $visitTimeStart = $booking->getVisitTime();
            $visitTimeEnd = clone $visitTimeStart;
            $visitTimeEnd->add($booking->getVisitDuration());

            $requestTimeStart = $visitTimeFormatted;
            $requestTimeEnd = clone $requestTimeStart;
            $requestTimeEnd = $requestTimeEnd->add($visitDurationFormatted);

            $comp1 = $visitTimeStart <=> $requestTimeStart;
            $comp2 = $visitTimeEnd <=> $requestTimeEnd;
            $comp3 = $visitTimeStart <=> $requestTimeEnd;
            $comp4 = $visitTimeEnd <=> $requestTimeStart;

            if(!(($comp1 == 1 && $comp3 >= 0) || ($comp2 == -1 && $comp4 <= 0))){
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $date
     * @return array
     * @throws \Exception
     */
    public function getAvailableSlots(string $date) : array
    {
        $interval = new \DateInterval('PT30M');
        $formattedDate = \DateTime::createFromFormat('Y-m-j h:i', $date.' 07:30');
        $dayFormatDate = \DateTime::createFromFormat('Y-m-j', $date);

        $bookingSlotRepository = $this->entityManager->getRepository(BookingSlot::class);
        $bookingSlots = $bookingSlotRepository->getAll();
        $result = [];

        foreach($bookingSlots as $bookingSlot){
            $result[$bookingSlot['id']] = [];
            $bookings = $this->entityManager->getRepository(Booking::class)->getByVisitTimeAndSlot($dayFormatDate, $bookingSlot);

            for($i = 0; $i < 24; $i++) {
                $formattedDate->add($interval);
                $formattedDateWithInterval = clone $formattedDate;
                $formattedDateWithInterval->add($interval);

                if($bookings){
                    $isAvailable = $this->checkBookingAvailabilityByBookingSlot($bookings, $formattedDate, $interval);
                    if($isAvailable){
                        $result[$bookingSlot['id']][] = $formattedDate->format('Y-m-j h:i').' - '.$formattedDateWithInterval->format('Y-m-j h:i');
                    }
                }else{
                    $result[$bookingSlot['id']][] = $formattedDate->format('Y-m-j h:i').' - '.$formattedDateWithInterval->format('Y-m-j h:i');
                }
            }

        }

        return $result;
    }
}