<?php

namespace Elmsellem\Support\FileSystem;

class ReaderFactory
{
    /**
     * @throws \Exception
     */
    public static function createFromPath(string $filePath): AbstractFileReader
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        return match (strtolower($extension)) {
            'csv' => new CSVReader($filePath),
            default => throw new \Exception('Unsupported file type: '.$extension),
        };
    }
}
