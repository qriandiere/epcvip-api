<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    /**
     * @Groups({"product", "products"})
     * @ORM\Id()
     * @ORM\Column(type="string", length=13)
     */
    private $issn;

    /**
     * @Groups({"product", "products"})
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Groups({"product", "products"})
     * @ORM\Column(type="enum_status_extended", length=10)
     */
    private $status;

    /**
     * @Groups({"product", "products"})
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Groups({"product", "products"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @Groups({"product", "products"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @Groups({"customer"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer", inversedBy="products")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="uuid")
     */
    private $customer;

    /**
     *  @Groups({"product", "products"})
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notification", mappedBy="product")
     */
    private $notifications;

    public function __construct()
    {
        $this->notifications = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->getIssn();
    }

    /**
     * @return string|null
     */
    public function getIssn(): ?string
    {
        return $this->issn;
    }

    /**
     * @param string $issn
     * @return $this
     */
    public function setIssn(string $issn): self
    {
        $this->issn = $issn;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTimeInterface|null $updatedAt
     * @return $this
     */
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTimeInterface|null $deletedAt
     * @return $this
     */
    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return Customer|null
     */
    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    /**
     * @param Customer|null $customer
     * @return $this
     */
    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setProduct($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getProduct() === $this) {
                $notification->setProduct(null);
            }
        }

        return $this;
    }
}