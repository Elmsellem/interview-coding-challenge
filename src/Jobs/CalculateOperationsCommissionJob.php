<?php

namespace Elmsellem\Jobs;

use Elmsellem\Repositories\OperationRepository;
use Elmsellem\Services\Commission\{CommissionCalculatorService, CommissionRulesRegistry};
use Exception;

class CalculateOperationsCommissionJob implements JobInterface
{
    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $registry = new CommissionRulesRegistry();
        $commissionService = new CommissionCalculatorService($registry);
        $operationRepo = new OperationRepository();

        foreach ($operationRepo->getAll() as $operation) {
            $commission = $commissionService->calculateCommission($operation);

            echo $commission . PHP_EOL;
        }
    }
}
