<?php
namespace VariableEncryptor;
use KaryaKode\StringUtil\StringUtil;
use KaryaKode\AppConfig\AppConfig;
use KaryaKode\Plugins\PluginInterface;
class VariableEncryptor implements PluginInterface {
  private static $isActive = false;
  private static $name = "Encrypt Variable Names";
  private static $author = "Karya Kode";
  private static $version = "1.0.0";

  public static function activate() {
      // Code to activate the plugin
      self::$isActive = true;
  }

  public static function deactivate() {
      // Code to deactivate the plugin
      self::$isActive = false;
  }

  public static function isActive() {
      return self::$isActive;
  }

  public static function getName() {
      return self::$name;
  }

  public static function getAuthor() {
      return self::$author;
  }

  public static function getVersion() {
      return self::$version;
  }

  public static function display(){

    return '
    <div class="checkbox">
        <label>
            <input type="checkbox" name="encryptVariable" id="encryptVariable" value="yes">
              '.self::$name.'
            <small class="text-muted">
                (This feature obfuscates the names of variables within the code)
            </small>
        </label>
    </div>';
  }

  public static function encryptVariablesInCode($code, $encryptionKey = 'bismillah') {
      // Ganti kemunculan variabel dengan panggilan fungsi enkripsi
      $code = preg_replace_callback('/(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/', function($matches) use ($encryptionKey) {
          $variable = $matches[0];
          if (preg_match('/^\$/', $variable)) {
              // Pastikan variabel tidak dienkripsi lagi jika sudah dienkripsi sebelumnya
              if (!preg_match('/^' . preg_quote('$_', '/') . '/', $variable)) {
                  // Jika variabel menggunakan $this, kembalikan variabel tanpa enkripsi
                  if ($variable === '$this') {
                      return $variable;
                  } else {
                      // Enkripsi nilai variabel menggunakan kunci enkripsi yang diberikan
                      $encryptedValue = self::encryptVariable($variable, $encryptionKey);
                      // Ganti kemunculan variabel dengan panggilan fungsi enkripsi
                      return $encryptedValue;
                  }
              } else {
                  return $variable;
              }
          }
      }, $code);

      return $code;
  }


  protected static function encryptVariable($variable, $encryptionKey = 'bismillah', $length = 5) {
     $variable = ltrim($variable, '$');
     $encryptedValue = substr(hash('sha256', $variable . $encryptionKey), -32, $length);
     # $encryptedValue = hash('sha256', $variable . $encryptionKey);
     // Memeriksa apakah karakter pertama adalah numerik
     if (ctype_digit(substr($encryptedValue, 0, 1))) {
         $encryptedValue = '_' . $encryptedValue; // Menambahkan garis bawah (_) jika karakter pertama adalah numerik
     }
     if(AppConfig::$isEncryptVariable) {
       if(isActive('Unprintable') && class_exists('Unprintable\Unprintable')) {
         $encryptedValue = \Unprintable\Unprintable::unprintable($encryptedValue);
       }
       return '$' . $encryptedValue;
     }
      return '$' . $variable;
  }

}
?>
