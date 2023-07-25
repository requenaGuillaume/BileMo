<?php

namespace App\Entity;

use App\Entity\Company;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use App\TraitClass\SelfDiscoverableTrait;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    use SelfDiscoverableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['showUsers'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2, 
        max: 50,
        minMessage: 'Value must be at least {{ limit }} characters.',
        maxMessage: 'Value must be have a maximum of {{ limit }} characters.'
    )]
    #[Groups(['showUsers'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2, 
        max: 50,
        minMessage: 'Value must be at least {{ limit }} characters.',
        maxMessage: 'Value must be have a maximum of {{ limit }} characters.'
    )]
    #[Groups(['showUsers'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email(message: 'Value is not a valid email.')]
    #[Groups(['showUsers'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 8, 
        max: 15,
        minMessage: 'Value must be at least {{ limit }} characters.',
        maxMessage: 'Value must be have a maximum of {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: '/[(+\d]{1}[\d]+[\d\.) \-]{1}[\d (]{1}[\d]+[\d \.\-)]{1}[ \d]{1}[\d]+[\d \.\-]{1}[\d]+/',
        message: 'Value is not a valid phone number.'
    )]
    #[Groups(['showUsers'])]
    private ?string $phoneNumber = null;

    #[ORM\Column]
    #[Groups(['showUsers'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['showUsers'])]
    private ?Company $company = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }
}
