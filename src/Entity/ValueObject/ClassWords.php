<?php

namespace App\Entity\ValueObject;

class ClassWords
{
    const ACCEPTED_EXTENSION = '.php';
    const EXTENSION_LENGTH = 4;

    /** @var array  */
    private $classWords = [];

    public function getClassWords(): array
    {
        arsort($this->classWords);
        return $this->classWords;
    }

    public function addFromFilePath($path)
    {
        $fileNamePositionBegin = intval(strrpos($path, DIRECTORY_SEPARATOR));
        $fileNamePositionBegin > 0 ? $fileNamePositionBegin += 1 : $fileNamePositionBegin;

        $fileName = substr($path, $fileNamePositionBegin, -self::EXTENSION_LENGTH);

        $totalMatches = preg_match_all("/[A-Z][a-z]*[^A-Z]/", $fileName, $matches);

        if ($totalMatches) {
            foreach ($matches[0] as $match) {
                if (isset($this->classWords[$match])) {
                    $this->classWords[$match] ++;
                } else {
                    $this->classWords[$match] = 1;
                }
            }
        }
    }
}