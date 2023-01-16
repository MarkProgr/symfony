<?php

namespace App\MessageHandler;

use App\Message\ExportProduct;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ExportProductHandler implements MessageHandlerInterface
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function __invoke(ExportProduct $message)
    {
        $email = new TemplatedEmail();
        $email
            ->from('example@gmail.com')
            ->to('john@doe.com')
            ->subject('New product: ' . $message->getName())
            ->htmlTemplate('emails/export_product.html.twig')
            ->context(
                [
                    'product_name' => $message->getName(),
                    'product_manufacturer' => $message->getManufacturer()
                ]
            );

        $this->mailer->send($email);
    }
}
