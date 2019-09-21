<?php

namespace App\Entity;

use App\Doctrine\EnumStatusDefaultType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @Groups({"user", "users"})
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"user", "users"})
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @Groups({"user", "users"})
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Token", mappedBy="user", orphanRemoval=true)
     */
    private $tokens;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Customer", mappedBy="author", orphanRemoval=true)
     */
    private $customers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="author", orphanRemoval=true)
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Log", mappedBy="author", orphanRemoval=true)
     */
    private $logs;

    /**
     * @Groups({"user", "users"})
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Groups({"user", "users"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @Groups({"user", "users"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @Groups({"user", "users"})
     * @ORM\Column(type="enum_status_default", length=10)
     */
    private $status = EnumStatusDefaultType::STATUS_NEW;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notification", mappedBy="author")
     */
    private $notifications;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notification", mappedBy="user", orphanRemoval=true)
     */
    private $notificationsUser;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="users")
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="author", orphanRemoval=true)
     */
    private $users;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->tokens = new ArrayCollection();
        $this->customers = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->logs = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->notificationsUser = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->username;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Token[]
     */
    public function getTokens(): Collection
    {
        return $this->tokens;
    }

    /**
     * @param Token $token
     * @return $this
     */
    public function addToken(Token $token): self
    {
        if (!$this->tokens->contains($token)) {
            $this->tokens[] = $token;
            $token->setUser($this);
        }

        return $this;
    }

    /**
     * @param Token $token
     * @return $this
     */
    public function removeToken(Token $token): self
    {
        if ($this->tokens->contains($token)) {
            $this->tokens->removeElement($token);
            // set the owning side to null (unless already changed)
            if ($token->getUser() === $this) {
                $token->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Customer[]
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    /**
     * @param Customer $customer
     * @return $this
     */
    public function addCustomer(Customer $customer): self
    {
        if (!$this->customers->contains($customer)) {
            $this->customers[] = $customer;
            $customer->setAuthor($this);
        }

        return $this;
    }

    /**
     * @param Customer $customer
     * @return $this
     */
    public function removeCustomer(Customer $customer): self
    {
        if ($this->customers->contains($customer)) {
            $this->customers->removeElement($customer);
            // set the owning side to null (unless already changed)
            if ($customer->getAuthor() === $this) {
                $customer->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * @param Product $product
     * @return $this
     */
    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setAuthor($this);
        }

        return $this;
    }

    /**
     * @param Product $product
     * @return $this
     */
    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getAuthor() === $this) {
                $product->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Log[]
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    /**
     * @param Log $log
     * @return $this
     */
    public function addLog(Log $log): self
    {
        if (!$this->logs->contains($log)) {
            $this->logs[] = $log;
            $log->setAuthor($this);
        }

        return $this;
    }

    /**
     * @param Log $log
     * @return $this
     */
    public function removeLog(Log $log): self
    {
        if ($this->logs->contains($log)) {
            $this->logs->removeElement($log);
            // set the owning side to null (unless already changed)
            if ($log->getAuthor() === $this) {
                $log->setAuthor(null);
            }
        }

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
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    /**
     * @param Notification $notification
     * @return $this
     */
    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setAuthor($this);
        }

        return $this;
    }

    /**
     * @param Notification $notification
     * @return $this
     */
    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getAuthor() === $this) {
                $notification->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotificationsUser(): Collection
    {
        return $this->notificationsUser;
    }

    /**
     * @param Notification $notificationsUser
     * @return $this
     */
    public function addNotificationsUser(Notification $notificationsUser): self
    {
        if (!$this->notificationsUser->contains($notificationsUser)) {
            $this->notificationsUser[] = $notificationsUser;
            $notificationsUser->setUser($this);
        }

        return $this;
    }

    /**
     * @param Notification $notificationsUser
     * @return $this
     */
    public function removeNotificationsUser(Notification $notificationsUser): self
    {
        if ($this->notificationsUser->contains($notificationsUser)) {
            $this->notificationsUser->removeElement($notificationsUser);
            // set the owning side to null (unless already changed)
            if ($notificationsUser->getUser() === $this) {
                $notificationsUser->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
        ));
    }


    /**
     * @param $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            ) = unserialize($serialized, array('allowed_classes' => false));
    }

    public function getAuthor(): ?self
    {
        return $this->author;
    }

    public function setAuthor(?self $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setAuthor($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getAuthor() === $this) {
                $user->setAuthor(null);
            }
        }

        return $this;
    }
}