<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Service\ScoreKeeper;
use App\Entity\TournamentScore;
use App\Entity\Game;
use App\Entity\Tournament;
use App\Form\ScoreType;
use App\Repository\TournamentUserRepository;
use App\Repository\TournamentScoreRepository;

/**
 * @Route("/scores")
 */
class TournamentScoreController extends AbstractController
{
    /**
     * @Route("/{game_name}/{tournament_id}/new", name="score_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_USER')")
     * @Entity("tournament", expr="repository.find(tournament_id)")
     * @ParamConverter("game", options={"mapping": {"game_name": "name"}})
     */
    public function new(Request $request, TournamentUserRepository $tournamentUsers, TournamentScoreRepository $tournamentScores, Game $game, Tournament $tournament, ScoreKeeper $scoreKeeper): Response
    {
        $this->denyAccessUnlessGranted('submit_score', $tournament);

        $user = $tournamentUsers->findOneBy([
            'tournament' => $tournament,
            'user' => $this->getUser()
        ]);

        $score = $tournamentScores->findOneBy([
            'user' => $user,
            'game' => $game
        ]);

        if (empty($score)) {
            $score = new TournamentScore();
            $tournament->addScore($score);
            if ($tournament->getFormat() === 'TEAM') {
                $user->getTeam()->addScore($score);
            }
            $game->addTournamentScore($score);
            $user->addScore($score);
            $score->setAutoAssigned(false);
        }

        $form = $this->createForm(ScoreType::class, $score);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Your score was saved!');

            $scoreKeeper->update($score);

            return $this->redirectToRoute('score_show',
                [
                    'tournament_id'=>$score->getTournament()->getId(),
                    'game_name'=>$score->getGame()->getName()
                ]
            );
        }

        return $this->render('score/new.html.twig', [
            'score' => $score,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{game_name}/{tournament_id}", name="score_show")
     * @Entity("tournament", expr="repository.find(tournament_id)")
     * @ParamConverter("game", options={"mapping": {"game_name": "name"}})
     */
    public function show(Tournament $tournament, Game $game, TournamentScoreRepository $tournamentScores)
    {
        $scores = $tournamentScores->findBy([
            'game' => $game,
            'tournament' => $tournament
            ],
            ['points' => 'DESC', 'updated_at' => 'ASC']
        );

        $user_score = null;
        $team_totals = null;

        if ($this->getUser()) {
            $user_score = $tournamentScores->findUserScores($this->getUser(), $tournament, $game);
        }

        if ($tournament->getFormat() === 'TEAM') {
            $team_totals = $tournamentScores->findTeamScores($tournament, $game);
        }

        return $this->render('score/show.html.twig', [
            'scores' => $scores,
            'game' => $game,
            'tournament' => $tournament,
            'team_totals' => $team_totals,
            'user_score' => $user_score
        ]);
    }
}
