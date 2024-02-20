<?php

namespace App\Repository;

use App\Entity\Bracket;
use App\Entity\Matches;
use App\Entity\Round;
use App\Entity\Tournaments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tournaments>
 *
 * @method Tournaments|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tournaments|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tournaments[]    findAll()
 * @method Tournaments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TournamentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tournaments::class);
    }

    /**
     * @throws \Exception
     */
    public function saveData(
        $extraData,
        Tournaments $tournament,
        TeamsRepository $teamsRepository): void
    {
        $tournament->setSlug($extraData['slug']);
        $bracketEnt = new Bracket();
        $bracketEnt->setTournament($tournament);
        foreach ($extraData['brackets'] as $key => $rounds) {
            $roundEnt = new Round();
            foreach ($rounds['matches'] as $matches) {
                $matchEnt = new Matches();
                $matchEnt->addTeam($teamsRepository->find($matches['team_1']));
                $matchEnt->addTeam($teamsRepository->find($matches['team_2']));

                $this->getEntityManager()->persist($matchEnt);
                $roundEnt->addMatch($matchEnt);
            }
            $roundEnt->setDate(new \DateTime($rounds['date']));
            $this->getEntityManager()->persist($roundEnt);
            $bracketEnt->addRound($roundEnt);
            $this->getEntityManager()->persist($bracketEnt);

        }
        $tournament->setBracket($bracketEnt);
        $this->getEntityManager()->persist($tournament);

        $this->getEntityManager()->flush();
    }

//    /**
//     * @return Tournaments[] Returns an array of Tournaments objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Tournaments
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
