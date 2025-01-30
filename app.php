<?php

use Elmsellem\Jobs\CalculateOperationsCommissionJob;
use Elmsellem\Repositories\OperationRepository;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

bcscale(10);
$fileName = $argv[1];
OperationRepository::$filePath = $fileName;

$commissionJob = new CalculateOperationsCommissionJob();
$commissionJob->handle();
