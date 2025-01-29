<?php

if (!function_exists('config')) {
    function config(string $filename): array
    {
        static $configCache = [];
        if (isset($configCache[$filename])) {
            return $configCache[$filename];
        }

        $configDirectory = dirname(__DIR__) . '/config/';
        $filePath = $configDirectory . $filename . '.php';

        if (!file_exists($filePath)) {
            return [];
        }

        $config = require $filePath;
        $configCache[$filename] = $config ?? [];

        return $config;
    }
}
