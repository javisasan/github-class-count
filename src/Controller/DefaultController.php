<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends AbstractController
{
    const GIT_API_URL = 'https://api.github.com/repos/:user/:repo';

    private $classNames = [];
    private $mostUsedClassWords = [];
    private $rateLimit = 0;
    private $rateLimitRemaining = 0;
    private $rateLimitReset = 0;

    public function index()
    {
        //return $this->render('base.html.twig');


        // METODO 1
        // Primero obtenemos el hash de la carpeta /src del repositorio, despues buscamos el tree recursivo de ese hash
        // CONTENTS: https://developer.github.com/v3/repos/contents/
        // https://api.github.com/repos/javisasan/symfony3-crud/contents/src/JSS/ApiBundle/Controller
        // TREES: https://developer.github.com/v3/git/trees/#get-a-tree
        // https://api.github.com/repos/javisasan/symfony3-crud/git/trees/6c1f0c98e211074c7e8fc9e6ae2454e9131ba40e?recursive=1
        // PROBLEMA: si el report es muy grande, puede devolver TRUNCATE = true

        // METODO 2
        // Conseguimos el hash de /src
        // Apuntamos los ficheros ("*.php")
        // Recursivamente recorremos cada una de las subcarpetas, utilizando su hash, con un tree no recursivo
        // VENTAJA: mas dificil que nos salga el truncate

        /////////////////

        // SYMFONY
        // https://api.github.com/repos/symfony/symfony/contents
        // https://api.github.com/repos/symfony/symfony/git/trees/5edd174d83a250eb3e143153db1fc0376e83ba98?recursive=1

        /*$jsonData = $this->getCurl('https://api.github.com/users/defunkt');
        return new JsonResponse($jsonData);*/

        $user = 'javisasan';
        $repo = 'symfony3-crud';

        $baseFolder = $this->getContents($user, $repo);

        foreach ($baseFolder as $item) {
            if (isset($item['name'])) {
                if ($item['name'] === 'src') {
                    $srcSha = $item['sha'];
                }
            }
        }

        if (empty($srcSha)) {
            echo "Repository does not contain SRC directory";
            die;
        }

        $this->recursiveTree($user, $repo, $srcSha);

        //dump($this->classNames);
        $this->printRateLimit();


        arsort($this->mostUsedClassWords);

        echo "<pre><code>";
        print_r($this->mostUsedClassWords);
        echo "</code></pre>";

        $this->printRateLimit();
        die;
    }

    private function recursiveTree($user, $repo, $sha)
    {
        $recursiveCall = false;

        $treeData = $this->getTree($user, $repo, $sha, true);

        if ($treeData['truncated']) {
            $recursiveCall = true;
            $treeData = $this->getTree($user, $repo, $sha, false);
        }

        foreach ($treeData['tree'] as $item) {
            switch ($item['type']) {
                case 'blob':
                    if (substr($item['path'], -4) === '.php') {
                        $this->addFileName($item['path']);
                    }
                    break;
                case 'tree':
                    if ($recursiveCall) {
                        $newSha = $item['sha'];
                        $this->recursiveTree($user, $repo, $newSha);
                    }
                    break;
                default:
                    echo "UNKNOWN TYPE " . $treeData['type'] . "\n";
                    break;
            }
        }
    }

    private function addFileName($path)
    {
        $extensionLength = 4;
        $fileName = substr($path, strrpos($path, '/')+1, -$extensionLength);
        $this->classNames[] = $fileName;

        $totalMatches = preg_match_all("/[A-Z][a-z]*[^A-Z]/", $fileName, $matches);

        if ($totalMatches) {
            foreach ($matches[0] as $match) {
                if (isset($this->mostUsedClassWords[$match])) {
                    $this->mostUsedClassWords[$match] ++;
                } else {
                    $this->mostUsedClassWords[$match] = 1;
                }
            }
        }


    }

    private function getUrl($user, $repo)
    {
        return str_replace(':repo', $repo, str_replace(':user', $user, self::GIT_API_URL));
    }

    private function getCurl($url)
    {
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        $data = curl_exec($handle);
        curl_close($handle);
        return json_decode($data, true);
    }

    private function getContents($user, $repo)
    {
        // Create URL
        $url = $this->getUrl($user, $repo);

        // Get contents of base dir
        return $this->getCurl($url . '/contents');
    }

    private function getTree($user, $repo, $sha, $recursive)
    {
        // Create URL
        $url = $this->getUrl($user, $repo) . '/git/trees/' . $sha;
        if ($recursive) {
            $url .= '?recursive=1';
        }

        // Get contents of base dir
        return $this->getCurl($url);
    }

    private function printRateLimit()
    {
        $dateTime = new \DateTime();

        $rateData = $this->getCurl('https://api.github.com/rate_limit');

        $dateTime->setTimestamp($rateData['resources']['core']['reset']);

        echo '<div>Rate limit: ' . $rateData['resources']['core']['remaining'] . ' left from ' . $rateData['resources']['core']['limit'] . '. Reset on ' . $dateTime->format('H:i:s') .'</div>';
    }
}