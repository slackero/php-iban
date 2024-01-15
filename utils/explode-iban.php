<?php
require_once dirname(__DIR__) . '/php-iban.php';

if (empty($argv[1])) {
    print 'Usage: ' . $argv[0] . " <IBAN>\n";
    print "  where <IBAN> is a valid IBAN to display the components of.\n";
    exit(1);
}
$iban = $argv[1];
echo 'Is ' . $iban . ' a valid IBAN: ' . (verify_iban($iban) ? 'Yes' : 'No');
echo PHP_EOL;
$parts = iban_get_parts($iban);
print_r($parts);
exit(0);
