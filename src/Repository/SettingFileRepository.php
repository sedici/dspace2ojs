<?php

namespace App\Repository;

use App\Entity\SettingFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SettingFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method SettingFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method SettingFile[]    findAll()
 * @method SettingFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettingFileRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SettingFile::class);
    }

    // /**
    //  * @return SettingFile[] Returns an array of SettingFile objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SettingFile
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
