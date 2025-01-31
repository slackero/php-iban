<?php

# Engine configuration
#  - first we enable error display
ini_set('display_errors',1);
#  - next we ensure that all errors are displayed
ini_set('error_reporting',E_ALL);

global $_iban_registry;

# include the library itself
require_once dirname(__DIR__) . '/php-iban.php';

# display registry contents
print_r($_iban_registry);
