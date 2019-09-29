<?php

namespace App\Controller;

use App\Infrastructure\Service\GithubApiManager;
use App\Infrastructure\Service\UserRepositoryManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    public function index()
    {
        $user = 'javisasan';
        $repo = 'symfony3-crud';

        $userRepositoryManager = new UserRepositoryManager(new GithubApiManager(), $user, $repo);

        $userRepositoryEntity = $userRepositoryManager->execute();

        dump($userRepositoryEntity->getClassWords());

        echo $userRepositoryEntity->getRateLimit()->__toString();

        die;

    }
}