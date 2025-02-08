<?php
namespace App\Domain\Product;

class Product
{
    private string $name;
    private float $price;
    private string $photo;

    public function __construct(string $name, float $price, string $photo)
    {
        $this->name = $name;
        $this->price = $price;
        $this->photo = $photo;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getPhoto(): string
    {
        return $this->photo;
    }
}
