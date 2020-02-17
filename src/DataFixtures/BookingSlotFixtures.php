<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\BookingSlot;

/**
 * Class BookingSlotFixtures
 * @package App\DataFixtures
 */
class BookingSlotFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $bookingSlotBlack = new BookingSlot();
        $bookingSlotBlack->setName('Black chair');
        $manager->persist($bookingSlotBlack);

        $bookingSlotWhite = new BookingSlot();
        $bookingSlotWhite->setName('White chair');
        $manager->persist($bookingSlotWhite);

        $manager->flush();
    }
}
