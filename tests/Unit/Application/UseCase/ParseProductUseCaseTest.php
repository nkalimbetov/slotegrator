<?php

namespace App\Tests\Unit\Application\UseCase;

use App\Application\UseCase\ParseProductUseCase;
use App\Domain\Product\Product;
use App\Domain\Product\ProductParserInterface;
use PHPUnit\Framework\TestCase;

class ParseProductUseCaseTest extends TestCase
{
    private ProductParserInterface $parser;
    private ParseProductUseCase $useCase;

    protected function setUp(): void
    {
        $this->parser = $this->createMock(ProductParserInterface::class);
        $this->useCase = new ParseProductUseCase($this->parser);
    }

    public function testExecuteWithValidUrl(): void
    {
        $url = 'https://www.alza.cz/product';
        $expectedProduct = new Product('Test Product', 99.99, 'https://example.com/photo.jpg');

        $this->parser
            ->expects($this->once())
            ->method('parse')
            ->with($url)
            ->willReturn($expectedProduct);

        $result = $this->useCase->execute($url);
        
        $this->assertSame($expectedProduct, $result);
    }

    public function testExecuteWithInvalidUrl(): void
    {
        $url = 'invalid-url';
        $exception = new \Exception('Invalid URL');

        $this->parser
            ->expects($this->once())
            ->method('parse')
            ->with($url)
            ->willThrowException($exception);

        $this->expectException(\Exception::class);
        $this->useCase->execute($url);
    }
} 