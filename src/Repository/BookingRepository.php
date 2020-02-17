<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * @param \DateTime $date
     * @return array
     */
    public function getAllForDate(\DateTime $date): array
    {
        $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        $emConfig->addCustomDatetimeFunction('DAY', 'DoctrineExtensions\Query\Mysql\Day');
        $emConfig->addCustomDatetimeFunction('HOUR', 'DoctrineExtensions\Query\Mysql\Hour');
        $emConfig->addCustomDatetimeFunction('MIN', 'DoctrineExtensions\Query\Mysql\Minute');

        return $this->createQueryBuilder('b')
            ->select('b')
            ->addSelect('bs')
            ->join('b.bookingSlot', 'bs')
            ->orderBy('b.id', 'ASC')
            ->where('YEAR(b.visitTime) = :year')
            ->andWhere('MONTH(b.visitTime) = :month')
            ->andWhere('DAY(b.visitTime) = :day')
            ->setParameter('year', $date->format('Y'))
            ->setParameter('month', $date->format('m'))
            ->setParameter('day', $date->format('j'))
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * @param $id
     * @return array
     * @throws NonUniqueResultException
     */
    public function findSingleById($id) : array
    {
        try{
            return $this->createQueryBuilder('b')
                ->orderBy('b.id', 'ASC')
                ->where('b.id = '.$id)
                ->getQuery()
                ->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }catch(NoResultException $e){
//          TODO: Here we should throw our own exception (api no result with message booking for given id does not exist) instead of returning empty array as this is not logically correct
//          TODO: For above we should have event listener for out thrown exception that would render json response 
            return [];
        }
    }

    /**
     * @param $visitTimeFormatted
     * @param $bookingSlot
     * @return array
     */
    public function getByVisitTimeAndSlot($visitTimeFormatted, $bookingSlot) : array
    {
        $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        $emConfig->addCustomDatetimeFunction('DAY', 'DoctrineExtensions\Query\Mysql\Day');

        return $this->createQueryBuilder('b')
            ->select('b')
            ->addSelect('bs')
            ->join('b.bookingSlot', 'bs')
            ->orderBy('b.id', 'ASC')
            ->where('bs.id = :bookingSlotId')
            ->andWhere('YEAR(b.visitTime) = :year')
            ->andWhere('MONTH(b.visitTime) = :month')
            ->andWhere('DAY(b.visitTime) = :day')
            ->setParameter('year', $visitTimeFormatted->format('Y'))
            ->setParameter('month', $visitTimeFormatted->format('m'))
            ->setParameter('day', $visitTimeFormatted->format('j'))
            ->setParameter('bookingSlotId', $bookingSlot['id'])
            ->getQuery()
            ->getResult();
    }
}
