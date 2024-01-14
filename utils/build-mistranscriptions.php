<?php
/**
 * Create the IBAN mistranscriptions from the config/mistranscriptions.txt file
 * There is no need to deploy this file in production. Run it once
 * after the mistranscriptions.txt was updated.
 */

/**
 * The path to the IBAN registry file
 */
define('IBAN_MISTRANSCRIPT_FILE', dirname(__DIR__) . '/config/mistranscriptions.txt');

/**
 * The path where to store the result of the parsed IBAN registry
 */
define('IBAN_MISTRANSCRIPT_PHP_FILE', dirname(__DIR__) . '/php-iban_mistranscriptions.php');

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
$iban_mistranscriptions = [];

// Load and parse the registry.txt
if (is_file(IBAN_MISTRANSCRIPT_FILE)) {

    if (is_file(IBAN_MISTRANSCRIPT_PHP_FILE) && !is_writable(IBAN_MISTRANSCRIPT_PHP_FILE)) {
        die('Could not write to "' . IBAN_MISTRANSCRIPT_PHP_FILE . '"');
    }

    $data = file_get_contents(IBAN_MISTRANSCRIPT_FILE);
    if ($data === false) {
        die('Could not read "' . IBAN_MISTRANSCRIPT_FILE . '"');
    }

    // Get the lines
    $lines = explode("\n", $data);


    // Loop through lines
    foreach ($lines as $line) {
        // Match lines with ' c-<x> = <something>' where x is a word-like character
        if (preg_match('/^ *c-(\w) = (.*?)$/', $line, $matches)) {
            // Normalize the character to upper case
            $character = strtoupper($matches[1]);
            // Break the possible origins list at '/', strip quotes & spaces
            $chars = explode(' ', str_replace('"', '', preg_replace('/ *?\/ *?/', '', $matches[2])));
            // Assign as possible mistranscriptions for that character
            $iban_mistranscriptions[$character] = $chars;
        }
    }

    // Write the new IBAN registry to a file
    if (count($iban_mistranscriptions)) {
        $php_iban_config  = '<?' . 'php' . "\n";
        $php_iban_config .= '/**' . "\n";
        $php_iban_config .= ' * The php-iban mistranscriptions' . "\n";
        $php_iban_config .= ' * @version ' . date('r') . "\n";
        $php_iban_config .= ' */' . "\n\n";
        $php_iban_config .= '$_iban_mistranscriptions = ';
        $php_iban_config .= var_export($iban_mistranscriptions, true) . ";\n";

        // Try to backup the existing registry php file
        $backup = false;
        $backup_name = IBAN_MISTRANSCRIPT_PHP_FILE . '-' . date('YmdHis') . '.php';
        if (is_file(IBAN_MISTRANSCRIPT_PHP_FILE) && rename(IBAN_MISTRANSCRIPT_PHP_FILE, $backup_name)) {
            $backup = true;
        }

        // In case there is a problem writing the file copy and write the result yourself

        if (file_put_contents($php_iban_config, IBAN_MISTRANSCRIPT_PHP_FILE)) {
            if ($backup) {
                unlink($backup_name);
            }
        } else {
            if ($backup) {
                rename($backup_name, IBAN_MISTRANSCRIPT_PHP_FILE);
            }
            echo $php_iban_config;
            die('The IBAN mistranscriptions could not be written!');
        }

    } else {
        die('The result of parsing the IBAN mistranscriptions seems to be inconsistent. Please proof!');
    }
}
