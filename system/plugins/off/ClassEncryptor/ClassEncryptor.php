<?php
namespace ClassEncryptor;
use KaryaKode\StringUtil\StringUtil;
use KaryaKode\AppConfig\AppConfig;
use KaryaKode\Plugins\PluginInterface;

class ClassEncryptor implements PluginInterface {
  private static $isActive = false;
  private static $name = "Encrypt Class Names";
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
            <input type="checkbox" name="encryptClass" id="encryptClass" value="yes">
            '.self::$name.'
            <small class="text-muted">
                (This feature obfuscates the names of classes within the code)
            </small>
        </label>
    </div>';
  }


  protected static function detectClassesInCode($code) {
      $classNames = [];

      // Pola untuk mendeteksi deklarasi kelas
      if(preg_match_all('/\bclass\s+([\p{L}_\x7f-\xff][\p{L}\d_\x7f-\xff]*)\b/', $code, $matches)){
        $classNames = array_merge($classNames, $matches[1]);
      }

      // Pola untuk mendeteksi penciptaan objek
      if(preg_match_all('/\bnew\s+([\p{L}_\x7f-\xff][\p{L}\d_\x7f-\xff]*)\s*\(/', $code, $matches)){
        $classNames = array_merge($classNames, $matches[1]);
      }

      // Pola untuk mendeteksi pemanggilan metode statis
      if(preg_match_all('/\b([\p{L}_\x7f-\xff][\p{L}\d_\x7f-\xff]*)::\w+\s*\(/', $code, $matches)){
        $classNames = array_merge($classNames, $matches[1]);
      }

      // Menghilangkan duplikat
      $classNames = array_unique($classNames);

      // Filter kelas yang benar-benar didefinisikan
      $classNames = array_filter($classNames, function($className) {
          return $className;
      });

      return $classNames;
  }



  public static function encryptClassesInCode($code, $encryptionKey = 'bismillah') {
      // Daftar kelas PHP bawaan
      $phpClasses = get_declared_classes();
      $phpClasses = array_merge(['self','std'], $phpClasses);
      // Daftar kelas yang telah dienkripsi
      $encryptedClasses = [];

      // Mendeteksi kelas dalam kode
      $classNames = self::detectClassesInCode($code);

      // Ganti kemunculan nama kelas yang belum dienkripsi dengan panggilan fungsi enkripsi
      foreach ($classNames as $className) {
          // Pastikan kelas bukan merupakan kelas bawaan PHP
          if (!in_array($className, $phpClasses)) {
              // Pastikan kelas belum dienkripsi sebelumnya
              //if (!isset($encryptedClasses[$className])) {
                  // Enkripsi nama kelas menggunakan kunci enkripsi yang diberikan
                  $encryptedName = self::encryptClass($className, $encryptionKey);
                  // Tandai kelas sebagai sudah dienkripsi
                  $encryptedClasses[$className] = true;
                  // Ganti kemunculan nama kelas dengan panggilan fungsi enkripsi
                  $code = preg_replace('/\b' . preg_quote($className, '/') . '\b/', $encryptedName, $code);
              //}
          }
      }

      return $code;
  }


  protected static function encryptClass($variable, $encryptionKey, $length = 8) {
     $encryptedValue = substr(hash('sha256', $variable . $encryptionKey), 0, $length);
    // Memeriksa apakah karakter pertama adalah numerik
    if (ctype_digit(substr($encryptedValue, 0, 1))) {
        $encryptedValue = '_' . $encryptedValue; // Menambahkan garis bawah (_) jika karakter pertama adalah numerik
    }
      if(AppConfig::$isEncryptClass) {
        if(isActive('Unprintable') && class_exists('Unprintable\Unprintable')) {
          $encryptedValue = \Unprintable\Unprintable::unprintable($encryptedValue);
        }
        return '_' . $encryptedValue;
      }

      return $variable;
  }

}
?>
