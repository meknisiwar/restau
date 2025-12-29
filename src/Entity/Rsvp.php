<?php

namespace App\Entity;

use App\Repository\RsvpRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RsvpRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_user_event', columns: ['user_id','event_id'])]
class Rsvp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'rsvps')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Event $event = null;

    #[ORM\Column]
    private int $guests = 1;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;
        return $this;
    }

    public function getGuests(): int
    {
        return $this->guests;
    }

    public function setGuests(int $guests): static
    {
        $this->guests = max(1, $guests);
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
}
