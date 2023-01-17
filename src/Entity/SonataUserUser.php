<?php

namespace App\Entity;

use App\Repository\SonataUserUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseUser;

#[ORM\Table("sonata_user_user")]
#[ORM\Entity(repositoryClass: SonataUserUserRepository::class)]
class SonataUserUser extends BaseUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
