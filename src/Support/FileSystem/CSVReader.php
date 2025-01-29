<?php

namespace Elmsellem\Support\FileSystem;

use Exception;
use Generator;

class CSVReader extends AbstractFileReader
{
    /**
     * @throws Exception
     */
    public function fetchData(): Generator
    {
        $handle = fopen($this->filePath, 'r');
        if (!$handle) {
            throw new Exception('Unable to open file: ' . $this->filePath);
        }

        try {
            while (($data = fgetcsv($handle)) !== false) {
                yield $data;
            }
        } finally {
            fclose($handle);
        }
    }
}
