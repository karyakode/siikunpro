<?php
namespace FunctionEncryptor;
use KaryaKode\StringUtil\StringUtil;
use KaryaKode\Plugins\PluginInterface;
use KaryaKode\AppConfig\AppConfig;
class FunctionEncryptor implements PluginInterface {
  private static $isActive = false;
  private static $name = "Encrypt Function Names";
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
            <input type="checkbox" name="encryptFunction" id="encryptFunction" value="yes">
              '.self::$name.'
            <small class="text-muted">
                (This feature obfuscates the names of functions within the code)
            </small>
        </label>
    </div>';
  }

  public static function encryptFunctionsInCode($code, $encryptionKey = 'bismillah') {
      // Daftar fungsi PHP bawaan
      $phpFunctions = get_defined_functions()['internal'];

      // Ganti kemunculan nama fungsi dengan panggilan fungsi enkripsi
      $code = preg_replace_callback('/\b([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*\(/', function($matches) use ($encryptionKey, $phpFunctions) {
          $functionName = $matches[1];

          // Pastikan fungsi tidak termasuk dalam fungsi PHP bawaan
          if (!in_array($functionName, $phpFunctions)) {
              // Pastikan fungsi tersedia sebelum mengenkripsi
              if (function_exists($functionName)) {
                  // Pastikan nama fungsi tidak dienkripsi lagi jika sudah dienkripsi sebelumnya
                  if (!preg_match('/^_/', $functionName)) {
                      // Enkripsi nama fungsi menggunakan kunci enkripsi yang diberikan
                      $encryptedName = self::encryptFunction($functionName, $encryptionKey);
                      // Ganti kemunculan nama fungsi dengan panggilan fungsi enkripsi
                      return $encryptedName . '(';
                  } else {
                      return $matches[0];
                  }
              } else {
                  return $matches[0];
              }
          } else {
              return $matches[0];
          }
      }, $code);

      return $code;
  }


  protected static function encryptFunction($variable, $encryptionKey, $length = 32) {

    # $encryptedValue = substr(hash('sha256', $variable . $encryptionKey), 0, $length);
    $encryptedValue = hash('sha256', $variable . $encryptionKey);
    // Memeriksa apakah karakter pertama adalah numerik
    if (ctype_digit(substr($encryptedValue, 0, 1))) {
        $encryptedValue = '_' . $encryptedValue; // Menambahkan garis bawah (_) jika karakter pertama adalah numerik
    }
      if(AppConfig::$encryptFunction) {
        if(isActive('Unprintable') && class_exists('Unprintable\Unprintable')) {
          $encryptedValue = \Unprintable\Unprintable::unprintable($encryptedValue);
        }
        return $encryptedValue;
      }

      return $variable;
  }

}
?>
