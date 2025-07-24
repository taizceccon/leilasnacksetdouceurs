<?php

namespace App\Entity;

use App\Entity\User;
use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?float $total = 0.0;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    // Relation OneToMany pour récupérer les items associés à cette commande
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, cascade: ['persist'])]
    private Collection $orderItems;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
        $this->total = 0.0;
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

    public function getTotal(): ?float
    {
        return $this->total;
    }

    // Calculate total price based on order items
    public function setTotal(): static
    {
        $this->total = 0.0;
        foreach ($this->orderItems as $orderItem) {
            $this->total += $orderItem->getSubtotal();
        }
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function addItem(OrderItem $item): self
    {
        if (!$this->orderItems->contains($item)) {
            $this->orderItems[] = $item;
            $item->setOrder($this);
        }

        // Recalculate total whenever an item is added
        $this->setTotal();

        return $this;
    }

    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }
}