<?php
namespace App\Domain\Product;

interface ProductParserInterface
{
    /**
     * Parses the product page from the provided URL and returns a Product entity.
     *
     * @param string $url
     * @return Product
     * @throws \Exception When parsing fails.
     */
    public function parse(string $url): Product;
}
