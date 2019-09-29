<?php

namespace App\Controller;

use App\Entity\UserRepositoryEntity;
use App\Infrastructure\Service\GithubApiManager;
use App\Infrastructure\Service\UserRepositoryManager;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{
    public function index(Request $request)
    {
        $userRepositoryEntity = new UserRepositoryEntity('', '', null, null);
        $githubData = [];

        $form = $this->createFormBuilder($userRepositoryEntity)
            ->add('userName', TextType::class, ['label' => 'User name'])
            ->add('repositoryName', TextType::class, ['label' => 'Repository name'])
            ->add('Search', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $userRepositoryManager = new UserRepositoryManager(new GithubApiManager(), $formData->getUserName(), $formData->getRepositoryName());

            $userRepositoryEntity = $userRepositoryManager->execute();

            $githubData = [
                'wordList'  => $userRepositoryEntity->getClassWords(),
                'rateLimit' => $userRepositoryEntity->getRateLimit()->__toString()
            ];
        }

        return $this->render('base.html.twig', [
            'form' => $form->createView(),
            'githubData' => $githubData
        ]);
    }
}