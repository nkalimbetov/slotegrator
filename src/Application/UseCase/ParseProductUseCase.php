<?php
namespace App\Application\UseCase;

use App\Domain\Product\Product;
use App\Domain\Product\ProductParserInterface;

class ParseProductUseCase
{
    private ProductParserInterface $parser;

    public function __construct(ProductParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Parses a product from the given URL.
     *
     * @param string $url
     * @return Product
     * @throws \Exception
     */
    public function execute(string $url): Product
    {
        return $this->parser->parse($url);
    }
}
