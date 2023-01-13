<?php

namespace App\Message;

final class ExportProduct
{
    private $name;

    private $manufacturer;

    public function __construct(string $name, string $manufacturer)
    {
        $this->name = $name;
        $this->manufacturer = $manufacturer;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getManufacturer(): string
    {
        return $this->manufacturer;
    }
}
