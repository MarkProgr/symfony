<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MainControllerTest extends WebTestCase
{
    public function testUpload()
    {
        $client = static::createClient();
        $file = new UploadedFile(__DIR__ . '/../README.md', 'README.md');
        $client->request('POST', '/main', files: ['file' => $file]);

        $this->assertResponseRedirects();
    }
}
