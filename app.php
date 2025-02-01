<?php

use Elmsellem\Repositories\OperationRepository;
use Elmsellem\Services\Commission\{CommissionCalculatorService, CommissionRulesRegistry};

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

bcscale(10);
$fileName = $argv[1];
OperationRepository::$filePath = $fileName;

$operationRepo = new OperationRepository();
$registry = new CommissionRulesRegistry();
$commissionService = new CommissionCalculatorService($registry);

$registry->loadRules();

foreach ($operationRepo->getAll() as $operation) {
    $commission = $commissionService->calculateCommission($operation);

    echo $commission . PHP_EOL;
}
