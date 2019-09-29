<?php

namespace App\Infrastructure\Service;

use App\Domain\Service\GithubApiInterface;
use App\Entity\UserRepositoryEntity;
use App\Entity\ValueObject\ClassWords;
use App\Entity\ValueObject\RateLimit;

class UserRepositoryManager
{
    /** @var GithubApiInterface */
    private $githubApi;

    /** @var UserRepositoryEntity */
    private $userRepositoryEntity;

    /** @var ClassWords */
    private $classWords;

    /** @var  string */
    private $userName;

    /** @var  string */
    private $repositoryName;

    public function __construct(GithubApiInterface $githubApi, string $userName, string $repositoryName)
    {
        $this->githubApi        = $githubApi;
        $this->userName         = $userName;
        $this->repositoryName   = $repositoryName;
        $this->classWords       = new ClassWords();
    }

    public function execute()
    {
        $baseFolder = $this->getGithubSrcFolder();

        foreach ($baseFolder as $item) {
            if (isset($item['name'])) {
                if ($item['name'] === 'src') {
                    $srcFolderSha = $item['sha'];
                }
            }
        }

        if (empty($srcFolderSha)) {
            throw new \Exception('Error: Repository does not contain src directory.');
        }

        $this->processGithubRecursiveTree($srcFolderSha);

        $this->userRepositoryEntity = new UserRepositoryEntity($this->userName, $this->repositoryName, $this->classWords, $this->getRateLimit());

        return $this->userRepositoryEntity;
    }

    private function getGithubSrcFolder()
    {
        return $this->githubApi->getContents($this->userName, $this->repositoryName);
    }

    private function processGithubRecursiveTree($folderSha)
    {
        $apiCallRecursively = false;

        $treeData = $this->githubApi->getTree($this->userName, $this->repositoryName, $folderSha, true);

        if ($treeData['truncated']) {
            $apiCallRecursively = true;
            $treeData = $this->githubApi->getTree($this->userName, $this->repositoryName, $folderSha, false);
        }

        foreach ($treeData['tree'] as $item) {
            switch ($item['type']) {
                case 'blob':
                    if (substr($item['path'], -ClassWords::EXTENSION_LENGTH) === ClassWords::ACCEPTED_EXTENSION) {
                        $this->classWords->addFromFilePath($item['path']);
                    }
                    break;
                case 'tree':
                    if ($apiCallRecursively) {
                        $newSha = $item['sha'];
                        $this->processGithubRecursiveTree($newSha);
                    }
                    break;
            }
        }
    }

    private function getRateLimit(): RateLimit
    {
        $rateLimitArray = $this->githubApi->getRateLimit();
        return new RateLimit($rateLimitArray['remaining'], $rateLimitArray['limit'], $rateLimitArray['reset']);
    }
}