<?php

// File: plugins/YourPlugin/YourPlugin.php

namespace Unprintable;

use KaryaKode\Plugins\PluginInterface;
use KaryaKode\AppConfig\AppConfig;

class Unprintable implements PluginInterface {
  private static $isActive = false;
  private static $name = "Unprin table Chars";
  private static $author = "Karya Kode";
  private static $version = "1.0.0";

  public static function activate() {
      // Code to activate the plugin
      self::$isActive = true;
  }

  public static  function deactivate() {
      // Code to deactivate the plugin
      self::$isActive = false;
  }

  public static  function isActive() {
      return self::$isActive;
  }

  public static  function getName() {
      return self::$name;
  }

  public static  function getAuthor() {
      return self::$author;
  }

  public static  function getVersion() {
      return self::$version;
  }


  public static function display(){

    return '
    <div class="checkbox">
        <label>
            <input type="checkbox" name="unprintableChars" id="unprintableChars" value="yes">
              '.self::$name.'
            <small class="text-muted">
                (This feature replaces characters with their unprintable counterparts to obfuscate the code further)
            </small>
        </label>
    </div>';
  }


  public static function unprintable($string){

    if(AppConfig::$isUnprintableChars) {
      // Mengubah karakter-karakter cetak menjadi karakter unprintable
      $string = str_replace(range('a', 'z'), range(chr(128), chr(255)), $string);
      $string = str_replace(range('A', 'Z'), range(chr(128), chr(255)), $string);
      $string = str_replace(range('0', '9'), range(chr(128), chr(255)), $string);
    }



    return $string;
  }
}
