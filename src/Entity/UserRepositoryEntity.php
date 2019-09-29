<?php

namespace App\Entity;

use App\Entity\ValueObject\ClassWords;
use App\Entity\ValueObject\RateLimit;

class UserRepositoryEntity
{
    /** @var  string */
    private $userName;

    /** @var  string */
    private $repositoryName;

    /** @var  ClassWords */
    private $classWords;

    /** @var  RateLimit */
    private $rateLimit;

    public function __construct(string $userName, string $repositoryName, ClassWords $classWords, RateLimit $rateLimit)
    {
        $this->userName         = $userName;
        $this->repositoryName   = $repositoryName;
        $this->classWords       = $classWords;
        $this->rateLimit        = $rateLimit;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getRepositoryName(): string
    {
        return $this->repositoryName;
    }

    public function getClassWords(): array
    {
        return $this->classWords->getClassWords();
    }

    public function getRateLimit(): RateLimit
    {
        return $this->rateLimit;
    }
}