<?php

// File: plugins/YourPlugin/YourPlugin.php

namespace Obfuscator\Variabel;

use KaryaKode\Plugins\PluginInterface;

class Variabel implements PluginInterface {
  private static $isActive = true;
  private static $name = "Variabel Plugin";
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
            <input type="checkbox" name="encryptor" id="encryptor" value="yes" checked="checked">
            '.self::$name.'
            <!--small class="text-muted">
                (This tool encrypts the entire codebase, making it unreadable and securing the code from unauthorized modifications)
            </small-->
        </label>
    </div>';
  }

  public static function javascript(){

    return '<script> </script>';
  }



}
