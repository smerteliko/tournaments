<?php

namespace App\Controller;

use App\Entity\Bracket;
use App\Entity\Matches;
use App\Entity\Round;
use App\Entity\Tournaments;
use App\Form\TournamentsType;
use App\Repository\RoundRepository;
use App\Repository\TeamsRepository;
use App\Repository\TournamentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tournaments')]
class TournamentsController extends AbstractController
{

    #[Route('/', name: 'app_tournaments_index', methods: ['GET'])]
    public function index(TournamentsRepository $tournamentsRepository): Response
    {
        return $this->render('tournaments/index.html.twig', [
            'tournaments' => $tournamentsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_tournaments_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,TeamsRepository $teamsRepository): Response
    {
        $tournament = new Tournaments();
        $form = $this->createForm(TournamentsType::class, $tournament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveData($form,$tournament, $entityManager, $teamsRepository);


            return $this->redirectToRoute('app_tournaments_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('tournaments/new.html.twig', [
            'tournament' => $tournament,
            'form' => $form,
            'errors' => $form->getErrors()
        ]);
    }

    #[Route('/{slug}', name: 'app_tournaments_bracket', methods: ['GET'])]
    public function bracket( #[MapEntity(expr: 'repository.findOneBy({"Name":slug})')] Tournaments $tournaments, RoundRepository $roundRepository): Response
    {
        $dataObject = [];
        $bracket=$tournaments->getBracket()->getId();
        $rounds=$roundRepository->findBy(['bracket'=>$bracket]);
        foreach ($rounds as $round) {
            $matches = [];
            foreach ($round->getMatches() as $match) {
                $teamNames = [];
                foreach ($match->getTeams()->getValues() as $team) {
                    $teamNames[] = $team->getName();
                }
                $matches[] = $teamNames;
            }
            $dataObject[] = [
                'date' => $round->getDate(),
                'matches'=>$matches
            ];

        }
        return $this->render('tournaments/show.html.twig', [
            'dataObject' => $dataObject,
            'tournamentName' => $tournaments->getName()
        ]);
    }



    #[Route('/{id}', name: 'app_tournaments_delete', methods: ['POST'])]
    public function delete(Request $request, Tournaments $tournament, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tournament->getId(), $request->request->get('_token'))) {
            $entityManager->remove($tournament);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tournaments_index', [], Response::HTTP_SEE_OTHER);
    }


    private function saveData($form ,Tournaments $tournament, EntityManagerInterface $entityManager, TeamsRepository $teamsRepository): void
    {

        $bracketEnt = new Bracket();
        $bracketEnt->setTournament($tournament);
        foreach ($form->getExtraData()['brackets'] as $key => $rounds) {
            $roundEnt = new Round();
            foreach ($rounds['matches'] as $matches) {
                $matchEnt = new Matches();
                $matchEnt->addTeam($teamsRepository->find($matches['team_1']));
                $matchEnt->addTeam($teamsRepository->find($matches['team_2']));

                $entityManager->persist($matchEnt);
                $roundEnt->addMatch($matchEnt);
            }
            $roundEnt->setDate(new \DateTime($rounds['date']));
            $entityManager->persist($roundEnt);
            $bracketEnt->addRound($roundEnt);
            $entityManager->persist($bracketEnt);

        }
        $tournament->setBracket($bracketEnt);
        $entityManager->persist($tournament);

        $entityManager->flush();
    }
}
