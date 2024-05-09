<?php
namespace amggroup;
/*
* PowerShell data manipulation class
* Author: Alisson Pelizaro
*/
class Manager {

  /*
  * Converts the getUsers command structure in PowerShell to an object
  */
  public function getUsers($response){
    $response = explode("\n", $response);
    $ind = 0;
    $users[0] = (object) array();

    foreach ($response as $line_num => $line) {
      if($line_num > 1){
        $contents = explode(' : ', $line);
        if(isset($contents[1])){
          @$users[$ind]->{$this->cleanString($contents[0])} = $contents[1];
        } else {
          $ind++;
          $users[$ind] = (object) array();
        }
      }
    }
    return $users;
  }

  /*
  * Converts the getUser command structure in PowerShell to an object
  */
  public function getUser($response){
    $response = explode("\n", $response);
    $user = (object) array();

    foreach ($response as $line_num => $line) {
      $contents = explode(' : ', $line);
      if(isset($contents[1])){
        @$user->{$this->cleanString($contents[0])} = $contents[1];
      }
    }

    if(!isset($user->CN)){
      return false;
    }

    return $user;
  }

  /*
  * Cleans string
  */
  private function cleanString($string){
    $string = iconv( "UTF-8" , "ASCII//TRANSLIT//IGNORE" , $string );
    $string = preg_replace(
        array('/[ ]/' , '/[^A-Za-z0-9\-]/'),
        array('', ''),
        $string
    );

    $what = array(
        '-','(',')',',',';',':','|','!','"','#','$',
        '%','&','/','=','?','~','^','>','<','ª','º'
    );
    $by   = array(
        '_','_','_','_','_','_','_','_','_','_','_',
        '_','_','_','_','_','_','_','_','_','_','_'
    );

    return str_replace($what, $by, $string);
  }

}
