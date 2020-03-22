<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Trait EntityTimestamp.
 */
trait EntityTimestamp
{
    /**
     * @var \Datetime|null
     *
     * @ORM\Column(name="created_at", type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    protected $createdAt;

    /**
     * Get creation date.
     *
     * @return \DateTime|null
     */
    final public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param  $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
