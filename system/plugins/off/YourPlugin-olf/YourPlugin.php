<?php

// File: plugins/YourPlugin/YourPlugin.php

namespace YourPlugin;

use KaryaKode\Plugins\PluginInterface;

class YourPlugin implements PluginInterface {
  private static $isActive = true;
  private static $name = "Your Plugin";
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
}
