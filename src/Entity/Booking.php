<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $customer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $customerPhone;

    /**
     * @ORM\Column(type="datetime")
     */
    private $visitTime;

    /**
     * @ORM\Column(type="dateinterval")
     */
    private $visitDuration;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hairDresser;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BookingSlot", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $bookingSlot;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCustomer(): ?string
    {
        return $this->customer;
    }

    public function setCustomer(string $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getCustomerPhone(): ?string
    {
        return $this->customerPhone;
    }

    public function setCustomerPhone(?string $customerPhone): self
    {
        $this->customerPhone = $customerPhone;

        return $this;
    }

    public function getVisitTime(): ?\DateTimeInterface
    {
        return $this->visitTime;
    }

    public function setVisitTime(\DateTimeInterface $visitTime): self
    {
        $this->visitTime = $visitTime;

        return $this;
    }

    public function getVisitDuration(): ?\DateInterval
    {
        return $this->visitDuration;
    }

    public function setVisitDuration(\DateInterval $visitDuration): self
    {
        $this->visitDuration = $visitDuration;

        return $this;
    }

    public function getHairDresser(): ?string
    {
        return $this->hairDresser;
    }

    public function setHairDresser(?string $hairDresser): self
    {
        $this->hairDresser = $hairDresser;

        return $this;
    }

    public function getBookingSlot(): ?BookingSlot
    {
        return $this->bookingSlot;
    }

    public function setBookingSlot(?BookingSlot $bookingSlot): self
    {
        $this->bookingSlot = $bookingSlot;

        return $this;
    }
}
