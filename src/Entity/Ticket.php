<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TicketRepository;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\ManyToOne(targetEntity: Annonce::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $annonce;

    #[ORM\Column(type:"datetime")]
    private $purchasedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getAnnonce(): ?Annonce
    {
        return $this->annonce;
    }

    public function setAnnonce(Annonce $annonce): self
    {
        $this->annonce = $annonce;
        return $this;
    }

    public function getPurchasedAt(): ?\DateTimeInterface
    {
        return $this->purchasedAt;
    }

    public function setPurchasedAt(\DateTimeInterface $date): self
    {
        $this->purchasedAt = $date;
        return $this;
    }
}
