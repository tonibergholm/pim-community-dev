<?php

namespace Pim\Bundle\UserBundle\Doctrine\ORM\Repository;

use Doctrine\ORM\EntityRepository;
use Pim\Bundle\UserBundle\Entity\UserInterface;
use Pim\Bundle\UserBundle\Repository\UserRepositoryInterface;

/**
 * User repository
 *
 * @author    Yohan Blain <yohan.blain@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getIdentifierProperties()
    {
        return ['username'];
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByIdentifier($identifier)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->where('u.username = :identifier OR u.email = :identifier')
           ->setParameter(':identifier', $identifier);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByGroupIds(array $groupIds)
    {
        if (empty($groupIds)) {
            return [];
        }

        $qb = $this->createQueryBuilder('u');
        $qb->leftJoin('u.groups', 'g');
        $qb->where($qb->expr()->in('g.id', $groupIds));

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function countAll(): int
    {
        $qb = $this->createQueryBuilder('u');

        return $qb
            ->select('count(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     *
     * @return UserInterface[]
     */
    public function findBySearch($search = null, array $options = [])
    {
        $qb = $this->createQueryBuilder('u');

        if (null !== $search && '' !== $search) {
            $qb->where('u.firstName like :search')->setParameter('search', '%' . $search . '%');
        }

        if (isset($options['identifiers']) && is_array($options['identifiers']) && !empty($options['identifiers'])) {
            $qb->andWhere('u.firstName in (:codes)');
            $qb->setParameter('codes', $options['identifiers']);
        }

        if (isset($options['limit'])) {
            $qb->setMaxResults((int) $options['limit']);
            if (isset($options['page'])) {
                $qb->setFirstResult((int) $options['limit'] * ((int) $options['page'] - 1));
            }
        }

        return $qb->getQuery()->getResult();
    }
}
