<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Result;
use App\Form\QuestionType;
use App\Repository\QuestionRepository;
use App\Repository\UserRepository;
use App\Repository\AnswerRepository;
use App\Repository\ResultRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\VarDumper;

#[Route('/questions')]
class QuestionController extends AbstractController
{




    #[Route('/', name: 'question_index', methods: ['GET'])]
    public function index(QuestionRepository $questionRepository): Response
    {

        return $this->render('question/index.html.twig', [
            'questions' => $questionRepository->findAll(),
        ]);
    }

    #[Route('/myquestions', name: 'question_mine', methods: ['GET'])]
    public function myquestions(QuestionRepository $questionRepository): Response
    {

        return $this->render('question/mine.html.twig', [
            'questions' => $questionRepository->findBy(["user" => $this->getUser()->getId()]),
        ]);
    }

    #[Route('/new', name: 'question_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $question->setUser($this->getUser());
            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute('question_index');
        }

        return $this->render('question/new.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'question_show', methods: ['GET'])]
    public function show(Question $question, AnswerRepository $answerRepository): Response
    {
        //$answer = $answerRepository->findBy(['question_id' => $question->getId()]);
        $answer = $question->getAnswers();
        return $this->render('question/show.html.twig', [
            'question' => $question,
            'answers' => $answer,
        ]);
    }

    #[Route('/{id}/edit', name: 'question_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Question $question): Response
    {
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('question_index');
        }

        return $this->render('question/edit.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'question_delete', methods: ['POST'])]
    public function delete(Request $request, Question $question): Response
    {
        if ($this->isCsrfTokenValid('delete' . $question->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($question);
            $entityManager->flush();
        }

        return $this->redirectToRoute('question_index');
    }


    #[Route('/results/{id}', name: 'question_answer', methods: ['GET', 'POST'])]
    public function answer(Request $request, Question $question, AnswerRepository $answertRepository): Response
    {
        if ($answertRepository->haveAlreadyAnswered($question->getId(), $request->getClientIp())) {

            $this->addFlash('error', 'You already answered on this poll');
            return $this->redirectToRoute('questions_results', ['id' => $question->getId()]);
        } else if ($request->getMethod() == "POST") {
            $trucs = $request->request->get('answer');
            $entityManager = $this->getDoctrine()->getManager();
            foreach ($trucs as $answerId) {
                $result = new Result();
                $result->setUser($this->getUser());
                $result->setIp($request->getClientIp());
                $result->setQuestion($question);
                $result->setAnswer($answertRepository->find($answerId));
                $entityManager->persist($result);
                $entityManager->flush();
            }
            $this->addFlash('success', 'result added');
            return $this->redirectToRoute('question_stats', ['id' => $question->getId()]);
        }


        return $this->render('question/answer.html.twig', [
            'question' => $question,
        ]);
    }

    #[Route('/stats/{id}', name: 'question_stats', methods: ['GET', 'POST'])]
    public function stats(Request $request, Question $question, ResultRepository $resultRepository, AnswerRepository $answerRepository): Response
    {

        //  $entityManager = $this->getDoctrine()->getManager();
        $choiceName = [];
        $stats = [];
        $results = $resultRepository->getResultByQuestion($question); //$resultRepository->findBy(array('question' => $question));
        $resultsCount = count($results);
        $choices =  $answerRepository->findBy(["question" => $question]);


        foreach ($results as $result) {
            foreach ($result as $key => $value) {

                if ($key == "answer_id") {
                    //$choiceName[] = $answerRepository->find($value)->getLabel();
                }
                if ($key == "number") {
                    $stats[] = $value;
                }
            }
        }

        foreach ($choices as $choice) {
            $choiceName[] = $choice->getLabel();
        }



        /*  $entityManager->persist($results);
        $entityManager->flush(); */
        return $this->render('question/stats.html.twig', [
            'question' => $question,
            'results' => json_encode($results),
            'resultsCount' => json_encode($resultsCount),
            'choiceName' => json_encode($choiceName),
            'stats' => json_encode($stats),
        ]);
    }
}
/* array(2) { 
    [0]=> array(2) { 
        ["answer_id"]=> string(1) "1" 
        ["number"]=> int(2) 
    } 
    [1]=> array(2) { ["answer_id"]=> string(1) "2" ["number"]=> int(2) } } */
