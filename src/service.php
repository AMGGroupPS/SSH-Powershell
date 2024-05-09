<?php

/*
* Example file for execution in CLI
*/
namespace amggroup;
use amggroup\Log;
use amggroup\PowerShell;
use \phpseclib3\Net\SSH2;

echo "CWD : ".__DIR__."\n";

require __DIR__.'/core.php';

$host = "remote_host";
$user = "user";
$pass = "password";

/*
 * Check if we have a '.env' file in current or parent directory
 * And if so, we include this.
 * .env file should always be excluded from Git and therefore
 * remains private and slightly more secure
 * .env file is read as a plain php file, so needs to follow
 * those conventions, and should ONLY include variable assignments.
 */
if (file_exists(__DIR__.'/.env')) {
    \amggroup\Log::create("Found config in current folder");
    include_once __DIR__.'/.env';
} elseif (file_exists(__DIR__.'/../.env')) {
    \amggroup\Log::create("Found config in parent folder");
    include_once __DIR__.'/../.env';
}

/*
* Possibility to execute in CLI with the
* "-d" parameter to enable DEBUG mode */
$debug = isset($argv[1]) && strtolower($argv[1]) == '-d' ? true : false;
\amggroup\Log::create('Attempting to connect to : '.$host);
$ssh = new SSH_Conn($host, $user, $pass, $debug);
$powershell = new PowerShell($ssh);
$powershell->newCommand('checkInstaller');
$powershell->add('cd c:\installer');
$powershell->add('ls');
print_r($powershell->run());
// Example to get a list of all users
//print_r($powershell->getUsers());
//print_r($powershell->exec('ls'));

// Example to search for a specific user
//print_r($powershell->getUser('dev'));

// Example to search for a user
//print_r($powershell->searchUser('HomePhone', '4130305525'));

// Example to reset a user's password
//$powershell->resetPassword('diego', 'newPassword123');

Log::create('Process executed successfully');
