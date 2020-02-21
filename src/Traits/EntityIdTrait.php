<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Trait EntityIdTrait.
 */
trait EntityIdTrait
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    final public function getId()
    {
        return $this->id;
    }
}
