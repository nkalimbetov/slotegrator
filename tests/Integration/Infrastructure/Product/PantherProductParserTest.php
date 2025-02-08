<?php

namespace App\Tests\Integration\Infrastructure\Product;

use App\Domain\Product\Product;
use App\Infrastructure\Product\PantherProductParser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PantherProductParserTest extends KernelTestCase
{
    private PantherProductParser $parser;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->parser = new PantherProductParser('Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    }

    public function testParseRealProduct(): void
    {
        // Используем реальный URL продукта с Alza.cz
        $url = 'https://www.alza.cz/imac-24-m3-cz-modry-d8010873.htm';

        $product = $this->parser->parse($url);

        // Проверяем базовую структуру продукта
        $this->assertInstanceOf(Product::class, $product);
        
        // Проверяем, что все необходимые данные получены
        $this->assertNotEmpty($product->getName());
        $this->assertGreaterThan(0, $product->getPrice());
        $this->assertNotEmpty($product->getPhoto());
        
        // Проверяем формат данных
        $this->assertIsString($product->getName());
        $this->assertIsFloat($product->getPrice());
        $this->assertIsString($product->getPhoto());
        
        // Проверяем, что URL фото начинается с https
        $this->assertStringStartsWith('https://', $product->getPhoto());
        
        // Выводим полученные данные для визуальной проверки
        echo sprintf(
            "\nПолученные данные:\nНазвание: %s\nЦена: %.2f\nФото URL: %s",
            $product->getName(),
            $product->getPrice(),
            $product->getPhoto()
        );
    }
} 