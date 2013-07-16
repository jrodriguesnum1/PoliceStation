<?php

namespace policestation\utils;

class ConfigUtils {

//http://stackoverflow.com/questions/5900077/does-php-allow-properties-file-as-in-java
public static function parse_properties($txtProperties) {
 $result = array();

 $lines = split("\n", $txtProperties);
 $key = "";

 $isWaitingOtherLine = false;
 foreach($lines as $i=>$line) {

 if(empty($line) || (!$isWaitingOtherLine && strpos($line,"#") === 0)) continue;

 if(!$isWaitingOtherLine) {
 $key = substr($line,0,strpos($line,'='));
 $value = substr($line,strpos($line,'=') + 1, strlen($line));
 }
 else {
 $value .= $line;
 }

 /* Check if ends with single '\' */
 if(strrpos($value,"\\") === strlen($value)-strlen("\\")) {
 $value = substr($value, 0, strlen($value)-1)."\n";
 $isWaitingOtherLine = true;
 }
 else {
 $isWaitingOtherLine = false;
 }

 $result[$key] = $value;
 unset($lines[$i]);
 }

 return $result;
}

}

?>
