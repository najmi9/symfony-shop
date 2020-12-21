<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * find or create a user from google.
     *
     * @param mixed[] $credentials
     */
    public function findOrCreateUserFromGoogle(array $credentials): User
    {
        $user = $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email', $credentials['email'])
            ->getQuery()
            ->getOneOrNullResult();

        if ($user) {
            if (!$user->getGoogleId()) {
                $user->setGoogleId($credentials['sub']);
                $this->getEntityManager()->persist($user);
                $this->getEntityManager()->flush();
            }

            return $user;
        }
        $user = (new User())
            ->setName($credentials['name'])
            ->setEmail($credentials['email'])
            ->setGoogleId($credentials['sub'])
            ->setEnabled(true);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return $user;
    }

    /**
     * @return mixed[]
     */
    public function getNewUsersByMonth(): array
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id) as count, YEAR(u.createdAt) AS year, MONTH(u.createdAt) AS month')
            ->groupBy('year')
            ->addGroupBy('month')
            ->orderBy('year', 'ASC')
            ->addOrderBy('month', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
