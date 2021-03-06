<?php

declare(strict_types=1);

namespace App\Repository;

/**
 * OfferRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OfferRepository extends \Doctrine\ORM\EntityRepository
{
    public function getDistinctAddress()
    {
        $qb = $this->createQueryBuilder('o');

        return $qb->select('o.address')
            ->distinct()
            ->where('o.enabled = :enabledValue')
            ->setParameter('enabledValue', 1)
            ->getQuery()
            ->getResult();
    }

    public function getByFilter($data, $limit = 0)
    {
        $qb = $this->createQueryBuilder('o');
        $qb->select()
            ->where('o.enabled = :enabledValue')
            ->setParameter('enabledValue', 1);
        if (true === isset($data['address-filter']) and '' !== $data['address-filter']) {
            $qb->andWhere('o.address = :address')
                ->setParameter('address', $data['address-filter']);
        }
        if (true === isset($data['skill-filter']) and 0 < count($data['skill-filter'])) {
            $skillsImplode = implode("','", $data['skill-filter']);
            $arraySkills = "('".$skillsImplode."')";
            $qb->innerJoin('App\Entity\OfferSkill', 's', 'WITH', 'o.id = s.offer')
                ->andWhere('s.label IN '.$arraySkills);
        }
        if (2 === (int) $data['time-filter']) {
            $qb->orderBy('o.createdAt', 'ASC');
        } else {
            $qb->orderBy('o.createdAt', 'DESC');
        }
        $qb->setFirstResult(0);
        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }
}
