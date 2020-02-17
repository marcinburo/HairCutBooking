<?php

namespace App\Repository;

use App\Entity\BookingSlot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @method BookingSlot|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookingSlot|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookingSlot[]    findAll()
 * @method BookingSlot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingSlotRepository extends ServiceEntityRepository
{
    /**
     * BookingSlotRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookingSlot::class);
    }

    /**
     * @return array
     */
    public function getAll() : array
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.id', 'ASC')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

}
