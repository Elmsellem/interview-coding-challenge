<?php

use Elmsellem\Jobs\CalculateOperationsCommissionJob;
use Elmsellem\Repositories\OperationRepository;

require __DIR__ . '/vendor/autoload.php';

$fileName = $argv[1];
if (empty($fileName)) {
    throw new InvalidArgumentException('File name cannot be empty');
}

OperationRepository::$filePath = $fileName;

$commissionJob = new CalculateOperationsCommissionJob();
$commissionJob->handle();
