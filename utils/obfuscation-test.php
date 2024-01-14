<?php
require_once dirname(__DIR__) . '/php-iban.php';

$example_iban_files = glob(__DIR__ . '/example-ibans/*');
if (count($example_iban_files) == 0) {
    echo 'No example IBAN files found in "' . __DIR__ . '/example-ibans"' . "\n";
    exit(1);
}

$ibans = [];
foreach ($example_iban_files as $file) {
    if (is_readable($file)) {
        $ibans += file($file);
    }
}
foreach($ibans as $iban) {
    $iban = iban_to_machine_format($iban);
    echo iban_to_human_format($iban) . "\n";
    echo iban_to_obfuscated_format($iban) . "\n";
}
