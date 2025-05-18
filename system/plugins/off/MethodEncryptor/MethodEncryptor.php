<?php
namespace MethodEncryptor;
use KaryaKode\StringUtil\StringUtil;
use KaryaKode\AppConfig\AppConfig;
use KaryaKode\Plugins\PluginInterface;

class MethodEncryptor implements PluginInterface {
  private static $isActive = false;
  private static $name = "Encrypt Method Names";
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
            <input type="checkbox" name="encryptMethod" id="encryptMethod" value="yes">
              '.self::$name.'
            <small class="text-muted">
                (This feature obfuscates the names of methods within the code)
            </small>
        </label>
    </div>';
  }

  public static function encryptMethodsInCode($code, $encryptionKey = 'bismillah') {
      // Daftar metode yang telah dienkripsi
      $encryptedMethods = [];

      // Mendeteksi panggilan metode dalam kode
      $methodCalls = self::detectMethodsInCode($code);

      // Daftar kelas yang telah dideklarasikan
      $phpClasses = get_declared_classes();

      // Ganti kemunculan panggilan metode yang belum dienkripsi dengan panggilan fungsi enkripsi
      foreach ($methodCalls as $methodName) {
          // Pastikan metode belum dienkripsi sebelumnya
          $isEncrypted = isset($encryptedMethods[$methodName]);

          // Periksa apakah metode tersebut ada di salah satu kelas yang dideklarasikan
          $methodExists = false;
          foreach ($phpClasses as $phpClass) {
              if (method_exists($phpClass, $methodName)) {
                  $methodExists = true;
                  break;
              }
          }

          if (!$isEncrypted && !$methodExists) {
              // Enkripsi nama metode menggunakan kunci enkripsi yang diberikan
              $encryptedName = self::encryptMethod($methodName, $encryptionKey);
              // Tandai metode sebagai sudah dienkripsi
              $encryptedMethods[$methodName] = true;
              // Ganti kemunculan nama metode dengan panggilan fungsi enkripsi
              $code = preg_replace('/\b' . preg_quote($methodName, '/') . '\b/', $encryptedName, $code);
          }
      }

      return $code;
  }

  public static function encryptMethodsThisInCode($code, $encryptionKey = 'bismillah') {
      // Daftar metode yang telah dienkripsi
      $encryptedMethods = [];

      // Daftar kelas yang telah dideklarasikan
      $phpClasses = get_declared_classes();

      // Mendeteksi panggilan metode dalam kode
      preg_match_all('/->([a-z]\w*)\s*\(/', $code, $matches);
      $methodCalls = array_unique($matches[1]);

      // Ganti kemunculan panggilan metode yang belum dienkripsi dengan panggilan fungsi enkripsi
      foreach ($methodCalls as $methodName) {
          // Pastikan metode belum dienkripsi sebelumnya
          $isEncrypted = isset($encryptedMethods[$methodName]);

          // Periksa apakah metode tersebut ada di salah satu kelas yang dideklarasikan
          $methodExists = false;
          foreach ($phpClasses as $phpClass) {
              if (method_exists($phpClass, $methodName)) {
                  $methodExists = true;
                  break;
              }
          }

          if (!$isEncrypted && !$methodExists) {
              // Enkripsi nama metode menggunakan kunci enkripsi yang diberikan
              $encryptedName = self::encryptMethod($methodName, $encryptionKey);
              // Tandai metode sebagai sudah dienkripsi
              $encryptedMethods[$methodName] = true;
              // Ganti kemunculan nama metode dengan panggilan fungsi enkripsi
              $code = preg_replace('/->' . preg_quote($methodName, '/') . '\b/', '->' . $encryptedName, $code);
          }
      }



      return $code;
  }

  private static function detectMethodsInCode($code) {
      // Pola pencarian untuk mendeteksi deklarasi metode
      $pattern = '/(?:protected|private|public)\s+function\s+([a-z]\w*)\s*\(/';

      // Lakukan pencarian untuk deklarasi metode dalam kode
      preg_match_all($pattern, $code, $matches);

      // Ambil nama-nama metode dari hasil pencarian
      $methodDeclarations = $matches[1];

      // Pola pencarian untuk mendeteksi panggilan metode statis
      $patternStatic = '/\b\w+::(\w+)\s*\(/';

      // Lakukan pencarian untuk panggilan metode statis dalam kode
      preg_match_all($patternStatic, $code, $matchesStatic);

      // Ambil nama-nama metode dari hasil pencarian panggilan metode statis
      $methodCallsStatic = $matchesStatic[1];

      // Pola pencarian untuk mendeteksi panggilan metode non-statis
      $patternNonStatic = '/->([a-z]\w*)\s*\(/';

      // Lakukan pencarian untuk panggilan metode non-statis dalam kode
      preg_match_all($patternNonStatic, $code, $matchesNonStatic);

      // Ambil nama-nama metode dari hasil pencarian panggilan metode non-statis
      $methodCallsNonStatic = $matchesNonStatic[1];

      // Gabungkan hasil dari ketiga pola pencarian
      $methods = array_merge($methodDeclarations, $methodCallsStatic, $methodCallsNonStatic);

      // Hapus duplikat dan kembalikan hasilnya
      return array_unique($methods);
  }

  private static function encryptMethod($variable, $encryptionKey = 'bismillah', $length = 9) {
    $encryptedValue = substr(hash('sha256', $variable . $encryptionKey), 0, $length);
    //$encryptedValue = hash('sha256', $variable . $encryptionKey);
      // Memeriksa apakah karakter pertama adalah numerik
      if (ctype_digit(substr($encryptedValue, 0, 1))) {
          $encryptedValue = '_' . $encryptedValue; // Menambahkan garis bawah (_) jika karakter pertama adalah numerik
      }
      if(AppConfig::$isEncryptMethod) {
        if(isActive('Unprintable') && class_exists('Unprintable\Unprintable')) {
          $encryptedValue = \Unprintable\Unprintable::unprintable($encryptedValue);
        }
        return '_' . $encryptedValue;
      }

      return $variable;
  }

}
?>
