<?php

namespace App\Twig;

use Goutte\Client;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PriceExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('convert', [$this, 'convertPrice']),
        ];
    }

    public function convertPrice(int $price): string|int
    {
        if (!array_key_exists('course', $_COOKIE) || $_COOKIE['course'] === 'BYN') {
            return $price . 'byn';
        }

        if ($_COOKIE['course'] === 'USD') {
            $client = new Client();
            $crawler = $client->request(
                'GET',
                'https://bankdabrabyt.by/export_courses.php'
            );

            $priceInUsd = $crawler->filter('value')->attr('buy');

            return round($price / $priceInUsd, 2) . '$';
        }
    }
}
