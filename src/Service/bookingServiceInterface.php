<?php

namespace App\Service;

/**
 * Interface bookingServiceInterface
 * @package App\Service
 */
interface bookingServiceInterface
{
    /**
     * @return array
     */
    public function getBookingSlots() : array;

    /**
     * @param array $data
     * @return int|null
     */
    public function addBooking(array $data) : ?int;

    /**
     * @param string $date
     * @return array
     */
    public function getBookings(string $date) : array;
}