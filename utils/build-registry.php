<?php
/**
 * Create the IBAN registry from the config/registry.txt file
 * There is no need to deploy this file in production. Run it once
 * after the registry.txt was updated.
 */

/**
 * The path to the IBAN registry file
 */
define('IBAN_REGISTRY_FILE', dirname(__DIR__) . '/config/registry.txt');

/**
 * The path where to store the result of the parsed IBAN registry
 */
define('IBAN_REGISTRY_PHP_FILE', dirname(__DIR__) . '/php-iban_registry.php');

/**
 * Set this to false to avoid spreading errors,
 * you should know what it means
 */
const REPORT_ERRORS = true;

if (!REPORT_ERRORS) {
    error_reporting(0);
}

/**
 * The IBAN registry
 */
$iban_registry = [];

// Load and parse the registry.txt
if (is_file(IBAN_REGISTRY_FILE)) {

    if (is_file(IBAN_REGISTRY_PHP_FILE) && !is_writable(IBAN_REGISTRY_PHP_FILE)) {
        die('Could not write to "' . IBAN_REGISTRY_PHP_FILE . '"');
    }

    $data = file_get_contents(IBAN_REGISTRY_FILE);
    if ($data === false) {
        die('Could not read "' . IBAN_REGISTRY_FILE . '"');
    }

    // Get the lines
    $lines = explode("\n", $data);

    // Drop leading description line
    array_shift($lines);

    // Loop through lines
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line !== '') {

            // Split the line
            list(
                $country,
                $country_name,
                $domestic_example,
                $bban_example,
                $bban_format_swift,
                $bban_format_regex,
                $bban_length,
                $iban_example,
                $iban_format_swift,
                $iban_format_regex,
                $iban_length,
                $bban_bankid_start_offset,
                $bban_bankid_stop_offset,
                $bban_branchid_start_offset,
                $bban_branchid_stop_offset,
                $registry_edition,
                $country_sepa,
                $country_swift_official,
                $bban_checksum_start_offset,
                $bban_checksum_stop_offset,
                $country_iana,
                $country_iso3166,
                $parent_registrar,
                $currency_iso4217,
                $central_bank_url,
                $central_bank_name,
                $membership
            ) = explode('|', $line);

            // Assign the country IBAN settings to the registry
            $iban_registry[$country] = [
                'country' => $country,
                'country_name' => $country_name,
                'country_sepa' => $country_sepa,
                'domestic_example' => $domestic_example,
                'bban_example' => $bban_example,
                'bban_format_swift' => $bban_format_swift,
                'bban_format_regex' => $bban_format_regex,
                'bban_length' => $bban_length,
                'iban_example' => $iban_example,
                'iban_format_swift' => $iban_format_swift,
                'iban_format_regex' => $iban_format_regex,
                'iban_length' => $iban_length,
                'bban_bankid_start_offset' => $bban_bankid_start_offset,
                'bban_bankid_stop_offset' => $bban_bankid_stop_offset,
                'bban_branchid_start_offset' => $bban_branchid_start_offset,
                'bban_branchid_stop_offset' => $bban_branchid_stop_offset,
                'registry_edition' => $registry_edition,
                'country_swift_official' => $country_swift_official,
                'bban_checksum_start_offset' => $bban_checksum_start_offset,
                'bban_checksum_stop_offset' => $bban_checksum_stop_offset,
                'country_iana' => $country_iana,
                'country_iso3166' => $country_iso3166,
                'parent_registrar' => $parent_registrar,
                'currency_iso4217' => $currency_iso4217,
                'central_bank_url' => $central_bank_url,
                'central_bank_name' => $central_bank_name,
                'membership' => $membership
            ];
        }
    }

    // Write the new IBAN registry to a file
    if (count($iban_registry) === count($lines) - 1) {
        $php_iban_config  = '<?' . 'php' . "\n";
        $php_iban_config .= '/**' . "\n";
        $php_iban_config .= ' * The php-iban registry' . "\n";
        $php_iban_config .= ' * @version ' . date('r') . "\n";
        $php_iban_config .= ' */' . "\n\n";
        $php_iban_config .= '$_iban_registry = ';
        $php_iban_config .= var_export($iban_registry, true) . ";\n";

        // Try to backup the existing registry php file
        $backup = false;
        $backup_name = IBAN_REGISTRY_PHP_FILE . '-' . date('YmdHis') . '.php';
        if (is_file(IBAN_REGISTRY_PHP_FILE) && rename(IBAN_REGISTRY_PHP_FILE, $backup_name)) {
            $backup = true;
        }

        // In case there is a problem writing the file copy and write the result yourself

        if (file_put_contents($php_iban_config, IBAN_REGISTRY_PHP_FILE)) {
            if ($backup) {
                unlink($backup_name);
            }
        } else {
            if ($backup) {
                rename($backup_name, IBAN_REGISTRY_PHP_FILE);
            }
            echo $php_iban_config;
            die('The IBAN Registry could not be written!');
        }

    } else {
        die('The result of parsing the IBAN Registry seems to be inconsistent. Please proof!');
    }
}
