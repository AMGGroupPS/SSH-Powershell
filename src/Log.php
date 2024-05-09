<?php
namespace amggroup;
/**
 * Log Constructor Class
 * Author: Alisson Pelizaro
 */
class Log {

  private static $debug;

  /*
  * Generates log content
  */
  public static function create($content, $err = false, $kill = false){
    $content = preg_replace("/\r?\n/","", $content);
    $cat = $err ? 'error' : 'info';
    $content = "[".strtoupper($cat)."][".date('Y-m-d H:i:s')."]: ".$content."\n";
    if(Log::$debug){
      echo $content;
    }
    Log::filePut($content);

    if($kill) {
      Log::create('Process ended');
      die;
    }

  }

  /*
  * Sets the service to run in DEBUG mode or not
  */
  public static function setDebugMode($mode){
    Log::$debug = $mode;
  }

  /*
  * Method to write content to the LOG file
  */
  private static function filePut($content){
    $file = fopen('ssh.log', 'a');
    fwrite($file, $content);
    fclose($file);
  }

}
