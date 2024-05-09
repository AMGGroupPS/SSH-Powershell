<?php
namespace amggroup;
/*
* SSH command manipulation class
* Author: Alisson Pelizaro
*/

use amggroup\Log;
use \phpseclib3\Crypt;
use \phpseclib3\Net\SSH2;

class SSH_Conn {

  private $ssh;

  function __construct($host, $user, $key, $debug = false){

    Log::setDebugMode($debug);
    Log::create('New call');
    if ($key=='') {
        $CurrentUser=posix_getpwuid(posix_geteuid());
        $private_key=(file_get_contents($CurrentUser['dir'].'/.ssh/id_rsa'));
        $key=$key = \phpseclib3\Crypt\PublicKeyLoader::load($private_key);
    }
    $this->ssh =  new SSH2($host);
    $this->login($user, $key);
  }

  /*
  * Performs authentication on the remote server
  */
  public function login($user, $key){
    if($this->ssh->login($user, $key)){
      return Log::create('Authenticated successfully');
    }
    Log::create('Authentication failed', true, true);
  }

  /*
  * Executes a command on the remote server
  */
  public function command($cmd){
    if($this->ssh){
      Log::create("Executed command (".$cmd.")");
      return $this->ssh->exec($cmd);
    }
    return false;
  }

}
