<?php

namespace App\Entity\ValueObject;

class RateLimit
{
    /** @var int */
    private $limitRemaining;

    /** @var int */
    private $limit;

    /** @var \DateTime */
    private $limitReset;

    public function __construct(int $limitRemaining, int $limit, int $limitReset)
    {
        $this->limitRemaining   = $limitRemaining;
        $this->limit            = $limit;
        $this->limitReset       = $this->createDateTimeFromTimestamp($limitReset);
    }

    public function getRemaining(): int
    {
        return $this->limitRemaining;
    }

    private function createDateTimeFromTimestamp($timestamp): \DateTime
    {
        $dateTime = new \DateTime();

        $dateTime->setTimestamp($timestamp);

        return $dateTime;
    }

    public function __toString(): string
    {
        $strResponse = 'GitHub Rate limit: ' . $this->limitRemaining . ' left from ' . $this->limit . '. Reset on ' . $this->limitReset->format('Y-m-d H:i:s');

        return $strResponse;
    }
}