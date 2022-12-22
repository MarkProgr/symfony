<?php

namespace App\Filesystem;

use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileUploader
{
    private string $uploadPath;

    private string $env;

    public function __construct(string $uploadPath, string $env)
    {
        $this->uploadPath = $uploadPath;
        $this->env = $env;
    }

    public function isTest(): bool
    {
        return 'test' === $this->env;
    }

    public function uploadFile(string $name, mixed $fileResource): void
    {
        $base = $this->uploadPath;
        if ($this->isTest()) {
            $base = __DIR__.'/../../'.$this->uploadPath;
            if (!is_dir($base)) {
                mkdir($base, recursive: true);
            }
        }

        if (false === file_put_contents($base.'/'.$name, $fileResource)) {
            throw new FileException('Failed to upload file');
        }
    }
}
