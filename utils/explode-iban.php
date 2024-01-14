<?php
require_once dirname(__DIR__) . '/php-iban.php';

if (!isset($argv[1])) {
    print 'Usage: ' . $argv[0] . " <IBAN>\n";
    print "  where <IBAN> is a valid IBAN to display the components of.\n";
    exit(1);
}
$iban = $argv[1];
$parts = iban_get_parts($argv[1]);
print_r($parts);
exit(0);
