<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\Score;
use App\Form\ScoreType;


/**
 * @Route("/score")
 */
class ScoreController extends AbstractController
{
    /**
     * @Route("/new/{game}/{tournament}", name="score_new", methods={"GET","POST"})
     */
    public function new(Request $request, $game, $tournament): Response
    {
        if (
            $score = $this->getDoctrine()
                ->getRepository('App\Entity\Score')
                ->findOneBy([
                    'tournament' => $tournament,
                    'game' => $game
                ])
        ) {
            return $this->redirectToRoute('score_edit', [
                'id' => $score->getId()
            ]);
        } else {
            $game = $this->getDoctrine()
                ->getRepository('App\Entity\Game')
                ->find($game);

            $tournament = $this->getDoctrine()
                ->getRepository('App\Entity\Tournament')
                ->find($tournament);

            $score = new Score($game, $tournament, $this->getUser());
            $form = $this->createForm(ScoreType::class, $score);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($score);
                $entityManager->flush();

                return $this->redirectToRoute('tournament_scores',
                    [
                        'id'=>$tournament->getId(),
                        'game'=>$game->getId()
                    ]
                );
            }

            return $this->render('score/new.html.twig', [
                'score' => $score,
                'form' => $form->createView()
            ]);
        }
    }

    /**
     * @Route("/{id}/edit", name="score_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Score $score): Response
    {
        $form = $this->createForm(ScoreType::class, $score);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tournament_show', ['id'=>$score->getTournament()->getId()]);
        }

        return $this->render('score/edit.html.twig', [
            'score' => $score,
            'form' => $form->createView(),
        ]);
    }
}