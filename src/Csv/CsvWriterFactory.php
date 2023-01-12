<?php

namespace App\Csv;

use League\Csv\Writer;

class CsvWriterFactory
{
    public function build()
    {
        return Writer::createFromFileObject(new \SplTempFileObject());
    }
}
