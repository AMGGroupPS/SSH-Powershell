#!/usr/bin/php
<?php
namespace amggroup;
/*
* Example of use in integration with Asterisk
* (password reset via phone)
*
* Author: Alisson Pelizaro (alissonpelizaro@hotmail.com)
*/

use amggroup\PowerShell;

require __DIR__.'/ssh_reset/core.php';
require __DIR__.'/phpagi.php';

$host = "172.16.100.140";
$user = "dev";
$pass = "enter@cbs";

$agi=new AGI();
$ssh = new SSH_Conn($host, $user, $pass, false);
$powershell = new PowerShell($ssh);

/*
ARGV[1] = Dialed number
ARGV[2] = User stage
ARGV[3] = User's name in AD (from stage 2)
ARGV[4] = Loopcount for error control
*/

$_stage = $argv[2];
if(!$_stage) $_stage = 1;
$agi->set_variable("STAGERESET", $_stage);
$agi->set_variable("RESETRESPONSE", "We are experiencing issues with this service at the moment. Please try again later.");

switch ($_stage) {
    case '1':
        // Enrollment verification
        $argv[1] = cleanString($argv[1]);
        $user = $powershell->searchUser('HomePhone', $argv[1]);
        if($user){
            $agi->set_variable("RESETRESPONSE","I located your enrollment. Now enter your SSN.");
            $agi->set_variable("RESETUSER", $user->CN);
            $agi->set_variable("STAGERESET", '2');
            $agi->set_variable("loopreset", 1);

        } else {
            $agi->set_variable("RESETRESPONSE","We couldn't find this enrollment. Please try again.");
            $agi->set_variable("loopreset",((int) $argv[4]) + 1);
        }

        // End of enrollment verification
        break;
    case '2':
        // SSN verification
        $argv[3] = cleanString($argv[3]);
        $user = $powershell->getUser($argv[3]);
        if($user && (int) $argv[1] == (int) $user->HomePhone){
            $agi->set_variable("RESETRESPONSE","Okay. Enter 1 to confirm the password reset, or any other number to cancel.");
            $agi->set_variable("RESETCPF", $argv[1]);
            $agi->set_variable("STAGERESET", '3');
            $agi->set_variable("loopreset", 1);
        } else {
            $agi->set_variable("RESETRESPONSE","The provided SSN does not match, please try again.");
            $agi->set_variable("loopreset",((int) $argv[4]) + 1);
        }

        // End of SSN verification
        break;

    case '3':
        // Confirmation
        $argv[3] = cleanString($argv[3]);

        if($argv[1] == '1'){
            $password = rand(100,999);
            $powershell->setExpiredPass($argv[3]);
            $powershell->resetPassword($argv[3], "veracel@".$password);
            $powershell->askNewPassword($argv[3]);

            $agi->set_variable("RESETRESPONSE","Your password has been reset to: veracel@".$password);
            $agi->set_variable("RESETRESPONSEKEYS", $password);
            $agi->set_variable("RESETFINISHED","true");
        } else {
            $agi->set_variable("RESETRESPONSE","The operation has been canceled");
            $agi->set_variable("RESETRESPONSEKEYS","false");
            $agi->set_variable("RESETFINISHED","true");

        }
        // End of SSN verification
        break;
}

function cleanString($string){
    return str_replace("\n", "", $string);
}
