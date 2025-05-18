<?php

// File: plugins/YourPlugin/YourPlugin.php

namespace Looper;

use KaryaKode\Plugins\PluginInterface;

class Looper implements PluginInterface {
  private static $isActive = false;
  private static $name = "Looper";
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


  public static  function display() {

    return '
    <div class="form-group">
        <label for="looper">Looper &mdash;
            <i><a target="_blank" class="textlink" href="https://codecanyon.net/collections/5408927-php-encoder-plugins/?ref=adilbo">PLUGIN</a></i>
        </label>
        <select class="form-control optional" name="plugin[looper]" id="looper">
            <option></option>
            <option value="">&hellip;</option>
            <option value="1">1 x &mdash; plus compressed</option>
            <option value="7">7 x</option>
            <option value="12">12 x</option>
            <option value="32">32 x</option>
            <option value="64">64 x</option>
            <option value="128">128 x</option>
            <option value="Random">Random &mdash; between 3 and 128</option>
        </select>
        <small class="form-text text-muted">
            Select how many times the result should be encrypted; or use the random option.<br>
            Not bullet proof protection, can be mixed with other functions and will increase the load size!
        </small>
    </div>';
  }

}
