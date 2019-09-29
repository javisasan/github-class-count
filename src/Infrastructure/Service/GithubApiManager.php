<?php

namespace App\Infrastructure\Service;

use App\Domain\Service\GithubApiInterface;
use App\Infrastructure\Helper\CurlHelper;

class GithubApiManager implements GithubApiInterface
{
    public function getApiUrl($user, $repo)
    {
        return str_replace(':repo', $repo, str_replace(':user', $user, self::GIT_API_URL));
    }

    public function getContents($user, $repo)
    {
        // Create URL
        $url = $this->getApiUrl($user, $repo);

        // Get contents of base dir
        return CurlHelper::executeCurl($url . '/contents');
    }

    public function getTree($user, $repo, $sha, $recursive)
    {
        // Create URL
        $url = $this->getApiUrl($user, $repo) . '/git/trees/' . $sha . ($recursive ? '?recursive=1' : '');

        // Get contents of base dir
        return CurlHelper::executeCurl($url);
    }

    public function getRateLimit()
    {
        $rateData = CurlHelper::executeCurl('https://api.github.com/rate_limit');

        return $rateData['resources']['core'];
    }
}