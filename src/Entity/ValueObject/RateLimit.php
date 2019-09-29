<?php

namespace App\Entity\ValueObject;

class RateLimit
{
    /** @var int */
    private $limit;

    /** @var int */
    private $limitRemaining;

    /** @var \DateTime */
    private $limitReset;

    public function __construct(int $limit, int $limitRemaining, int $limitReset)
    {
        $this->limit            = $limit;
        $this->limitRemaining   = $limitRemaining;
        $this->limitReset       = $this->createDateTimeFromTimestamp($limitReset);
    }

    private function createDateTimeFromTimestamp($timestamp): \DateTime
    {
        $dateTime = new \DateTime();

        $dateTime->setTimestamp($timestamp);

        return $dateTime;
    }

    public function __toString(): string
    {
        $strResponse = 'GitHub Rate limit: ' . $this->limit . ' left from ' . $this->limitRemaining . '. Reset on ' . $this->limitReset->format('Y-m-d H:i:s');

        return $strResponse;
    }
}