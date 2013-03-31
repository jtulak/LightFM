<?php
/**
 * This script will compile all less files in this directory to correspondent css
 * @author: Jan Ťulak <jan@tulak.me>
 */
require "lessc.inc.php";

$less = new lessc;

// included path
$params = file_get_contents('params.less');


foreach (scandir('.') as $file){
  if(is_file($file) && preg_match("/\.less$/",$file) != FALSE){
    try {
      $newName = preg_replace("/\.less$/",".css",$file);
      echo "compiling $file to $newName\n";
      // join with params
     // $data = $params."\n".file_get_contents($file);
      // compile
      //$compiled =  $less->compile($data);
      //file_put_contents($newName,);
      $less->checkedCompile($file, $newName);
      
      
    }catch (Exception $e){
      echo "An error occured... ".$e->getMessage()."\n";
    }
  }
}
echo "all done\n";