<?php

namespace App\Controller;

use App\Entity\Teams;
use App\Form\TeamsType;
use App\Repository\MatchesRepository;
use App\Repository\RoundRepository;
use App\Repository\TeamsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/teams')]
class TeamsController extends AbstractController
{
    #[Route('/', name: 'app_teams_index', methods: ['GET'])]
    public function index(TeamsRepository $teamsRepository): Response
    {
        return $this->render('teams/index.html.twig', [
            'teams' => $teamsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_teams_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $team = new Teams();
        $form = $this->createForm(TeamsType::class, $team);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            $entityManager->persist($team);
            $entityManager->flush();

            return $this->redirectToRoute('app_teams_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('teams/new.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_teams_delete', methods: ['POST'])]
    public function delete(Request $request,
                           Teams $team,
                           EntityManagerInterface $entityManager,
                           RoundRepository $roundRepository,
                           MatchesRepository $matchesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$team->getId(), $request->request->get('_token'))) {
            $matches = $team->getMatches();

            foreach ($matches as $match) {

                $teams = $match->getTeams();
                foreach($teams as $teamI) {
                    $match->removeTeam($teamI);
                }
                $rounds = $match->getRounds();
                $rounds->removeMatch($match);
            }

            $entityManager->remove($team);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_teams_index', [], Response::HTTP_SEE_OTHER);
    }
}
