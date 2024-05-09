<?php
namespace amggroup;
use amggroup\Log;
use amggroup\Manager;

/**
 * Powershell possibilities and commands class
 * Author: Alisson Pelizaro
 */
class PowerShell {

  private $ssh;
  private $manager;

  private $commandBlock=[];

  private $lastCommandBlock;
  function __construct($ssh){
    $this->ssh = $ssh;
    $this->manager = new Manager;
  }

    /**
     * Creates a new command block
     * This is just an array of commands that
     * we will execute in a single call
     *
     * @param string $BlockName Command Block Name, default blockname is 'run'
     * @param string $Command   Command to start this block
     * @return void
     */
  public function newCommand($BlockName = 'run', $Command=null)
  {
      $this->commandBlock[$BlockName]=[];
      if ($Command != null) {
          $this->commandBlock[$BlockName][]=$Command;
      }
      // Store the last block name used, to make further calls shorter
      $this->lastCommandBlock=$BlockName;
  }

  public function add($BlockName = null, $Command=null)
  {
      if ($BlockName == null) {
          $BlockName=$this->lastCommandBlock;
      }
      \amggroup\Log::create('Appending to :'.$BlockName.': Command :'.$Command.':');
      $this->commandBlock[$BlockName][]=$Command;
  }

  public function run($BlockName = null)
  {
      if ($BlockName == null) {
          $BlockName = $this->lastCommandBlock;
      }
      $Command=implode("\n", $this->commandBlock[$BlockName]);
      return $this->exec($Command);
  }
  /*
  * Searches and returns data of a user from the Server
  */
  public function searchUser($field, $val){
    $user_data = $this->manager->getUser(
        $this->ssh->command('powershell Get-ADUser -filter {'.$field.' -like "'.$val.'"} -properties *')
    );

    if($user_data){
      return $user_data;
    }

    Log::create('User not found by the passed filters ('.$field.', '.$val.')', true);
    return false;
  }

  /*
  * Returns all users from the Server
  */
  public function getUsers(){
    return $this->manager->getUsers(
        $this->ssh->command('powershell Get-ADUser -filter *')
    );
  }

  /*
  * Executes any command passed as parameter
  */
  public function exec($cmd){
    return $this->ssh->command($cmd);
  }

  /*
  * Sets password to never expire
  */
  public function setExpiredPass($user, $stat = false){
    if($stat) $comp = 'true';
    else $comp = 'false';
    return $this->ssh->command('powershell Set-ADUser -Identity '.$user.' -PasswordNeverExpires $'.$comp);
  }

  /*
  * Sets password change at next logon
  */
  public function askNewPassword($user, $stat = true){
    if($stat) $comp = 'true';
    else $comp = 'false';

    return $this->ssh->command('powershell Set-ADUser -Identity '.$user.' -ChangePasswordAtLogon $'.$comp);
  }

  /*
  * Resets a user's password
  */
  public function resetPassword($user, $new_pwd){
    return $this->ssh->command("powershell Set-ADAccountPassword -Identity ".$user." -Reset -NewPassword (ConvertTo-SecureString -AsPlainText '".$new_pwd."' -Force)");
  }

  /*
  * Returns data of a user from the Server
  */
  public function getUser($user){
    $user_data = $this->manager->getUser(
        $this->ssh->command('powershell Get-ADuser '.$user.' -properties *')
    );

    if($user_data){
      return $user_data;
    }

    Log::create('User "'.$user.'" not found', true);
    return false;
  }

}
