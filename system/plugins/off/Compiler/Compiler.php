<?php

// File: plugins/YourPlugin/YourPlugin.php

namespace Compiler;

use KaryaKode\Plugins\PluginInterface;

class Compiler implements PluginInterface {
  private static $isActive = false;
  private static $name = "Compiler";
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
        <label for="compiler">Compiler &mdash;
            <i><a target="_blank" class="textlink" href="https://codecanyon.net/collections/5408927-php-encoder-plugins/?ref=adilbo">PLUGIN</a></i>
        </label>
        <select class="form-control optional" name="plugin[compiler]" id="compiler">
            <option></option>
            <option value="">&hellip;</option>
            <option value="OK">OK</option>
        </select>
        <small class="form-text text-muted">
            Compile full file only! &mdash; You must copy the complete compiled file from the Repository!<br>
            Bullet proof protection, <b style="color:crimson">can\'t be mixed with other plugins</b> and will increase the load size a little bit!
        </small>
    </div>';
  }

}
