<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Название товара обязательно для заполнения')]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Название товара должно содержать минимум {{ limit }} символа',
        maxMessage: 'Название товара не может быть длиннее {{ limit }} символов'
    )]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Assert\NotBlank(message: 'Цена обязательна для заполнения')]
    #[Assert\Positive(message: 'Цена должна быть положительным числом')]
    #[Assert\Type(type: 'numeric', message: 'Цена должна быть числом')]
    #[Assert\Regex(
        pattern: '/^\d+(\.\d{0,2})?$/',
        message: 'Цена должна быть числом с не более чем двумя десятичными знаками'
    )]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $price = null;

    #[Assert\NotBlank(message: 'URL фото обязателен для заполнения')]
    #[Assert\Url(message: 'Неверный формат URL фото')]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[Assert\Url(message: 'Неверный формат URL Alza')]
    #[Assert\Regex(
        pattern: '/^https:\/\/www\.alza\.cz\//',
        message: 'URL должен быть с домена alza.cz'
    )]
    private $alzaUrl;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
