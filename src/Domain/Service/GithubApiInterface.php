<?php

namespace App\Domain\Service;

interface GithubApiInterface
{
    const GIT_API_URL = 'https://api.github.com/repos/:user/:repo';

    function getApiUrl($user, $repo);

    function getContents($user, $repo);

    function getTree($user, $repo, $sha, $recursive);

    function getRateLimit();
}