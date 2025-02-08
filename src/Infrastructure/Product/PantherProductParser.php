<?php
namespace App\Infrastructure\Product;

use App\Domain\Product\Product;
use App\Domain\Product\ProductParserInterface;
use Symfony\Component\Panther\Client;

class PantherProductParser implements ProductParserInterface
{
    private Client $client;

    public function __construct(string $customUserAgent)
    {
        $this->client = Client::createChromeClient(null, [
            '--headless',
            '--disable-gpu',
            '--no-sandbox',
            '--user-agent=' . $customUserAgent,
        ]);
    }

    public function parse(string $url): Product
    {
        try {
            $this->client->request('GET', $url);

            $crawler = $this->client->waitFor('#detailItem', 5);

            $name = $this->extractName($crawler);
            $price = $this->extractPrice($crawler);
            $photo = $this->extractPhoto($crawler);

            return new Product($name, $price, $photo);
        } finally {
            $this->client->quit();
        }
    }

    private function extractName(\Symfony\Component\DomCrawler\Crawler $crawler): string
    {
        $nameFilter = $crawler->filter('.h1-placeholder');
        if (!$nameFilter->count()) {
            throw new \Exception("Product name not found");
        }
        return trim($nameFilter->text());
    }

    private function extractPrice(\Symfony\Component\DomCrawler\Crawler $crawler): float
    {
        $priceFilter = $crawler->filter('.price-detail__price-box-wrapper .price-box__price');
        if (!$priceFilter->count()) {
            throw new \Exception("Product price not found");
        }
        $priceText = trim($priceFilter->text());
        return (float) bcmul(
            str_replace([' ', ','], ['', '.'], preg_replace('/[^\d, ]/', '', $priceText)),
            '1',
            2
        );
    }

    private function extractPhoto(\Symfony\Component\DomCrawler\Crawler $crawler): string
    {
        $photoFilter = $crawler->filter('.swiper-slide-active img');
        if (!$photoFilter->count()) {
            throw new \Exception("Product image not found");
        }
        return trim($photoFilter->attr('src'));
    }
}
