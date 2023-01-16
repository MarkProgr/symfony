<?php

namespace App\Tests\MessageHandler;

use App\Message\ExportProduct;
use App\MessageHandler\ExportProductHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;

class ExportProductHandlerTest extends TestCase
{
    private $mailerMock;
    protected function setUp(): void
    {
        $this->mailerMock = $this->createMock(MailerInterface::class);
    }

    public function testHandle()
    {
        $handler = new ExportProductHandler($this->mailerMock);

        $message = new ExportProduct('123123', '123123');

        $this->mailerMock->expects($this->once())->method('send');

        $handler($message);
    }
}
