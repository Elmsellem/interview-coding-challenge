<?php

namespace Elmsellem\Jobs;

interface JobInterface
{
    public function handle(): void;
}