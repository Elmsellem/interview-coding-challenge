<?php

namespace Elmsellem\Support\FileSystem;

use Exception;
use Generator;

abstract class AbstractFileReader
{
    protected string $filePath;

    /**
     * @throws Exception
     */
    public function __construct(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new Exception('File not found: ' . $filePath);
        }

        $this->filePath = $filePath;
    }

    abstract public function fetchData(): Generator;
}
