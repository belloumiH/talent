<?php

declare(strict_types=1);

namespace App\Entity;

use App\Helper\Cryptor;
use App\Traits\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Candidate.
 *
 * @ORM\Table(name="candidate")
 * @ORM\Entity(repositoryClass="App\Repository\CandidateRepository")
 */
class Candidate
{
    use EntityIdTrait;

    /**
     * @var string|null
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mail", type="string", length=255, nullable=true)
     */
    private $mail;

    /**
     * @var string|null
     *
     * @ORM\Column(name="file", type="string", length=255, nullable=true)
     */
    private $file;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var Offer|null
     * @ORM\ManyToOne(targetEntity = "Offer")
     * @ORM\JoinColumn(name="offer_id", referencedColumnName="id", nullable=true)
     */
    private $offer;

    public function getFirstName(): ?string
    {
        return Cryptor::decrypt($this->firstName);
    }

    public function setFirstName(?string $firstName): Candidate
    {
        $this->firstName = Cryptor::encrypt($firstName);

        return $this;
    }

    public function getLastName(): ?string
    {
        return Cryptor::decrypt($this->lastName);
    }

    public function setLastName(?string $lastName): Candidate
    {
        $this->lastName = Cryptor::encrypt($lastName);

        return $this;
    }

    public function getPhone(): ?string
    {
        return Cryptor::decrypt($this->phone);
    }

    public function setPhone(?string $phone): Candidate
    {
        $this->phone = Cryptor::encrypt($phone);

        return $this;
    }

    public function getMail(): ?string
    {
        return Cryptor::decrypt($this->mail);
    }

    public function setMail(?string $mail): Candidate
    {
        $this->mail = Cryptor::encrypt($mail);

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): Candidate
    {
        $this->comment = $comment;

        return $this;
    }

    public function getFile(): ?string
    {
        return Cryptor::decrypt($this->file);
    }

    public function setFile(?string $file): Candidate
    {
        $this->file = Cryptor::encrypt($file);

        return $this;
    }

    public function getOffer(): ?Offer
    {
        return $this->offer;
    }

    public function setOffer(?Offer $offer): Candidate
    {
        $this->offer = $offer;

        return $this;
    }
}
