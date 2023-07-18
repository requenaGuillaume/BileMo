<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Trait\SelfDiscoverableTrait;
use App\Repository\ProductRepository;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    use SelfDiscoverableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $brand = null;

    #[ORM\Column(length: 255)]
    private ?string $model = null;

    #[ORM\Column]
    private ?int $pricePreVatInCents = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getPricePreVatInCents(): ?int
    {
        return $this->pricePreVatInCents;
    }

    public function setPricePreVatInCents(int $pricePreVatInCents): static
    {
        $this->pricePreVatInCents = $pricePreVatInCents;

        return $this;
    }
}
