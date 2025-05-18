<?php
namespace StringEncryptor;
use KaryaKode\ChrConverter\ChrConverter;
use KaryaKode\Plugins\PluginInterface;
use KaryaKode\AppConfig\AppConfig;
class StringEncryptor implements PluginInterface {
  private static $isActive = false;
  private static $name = "Encrypt Strings";
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
            <input type="checkbox" name="stringEncryptor" id="stringEncryptor" value="yes" checked="checked">
                '.self::$name.'
            <small class="text-muted">
                (after encryption no further code or other changes in the file are possible)
            </small>
        </label>
    </div>';
  }

  public static function encryptStringsInCode($input)
  {
      // Regex untuk mendeteksi string yang berada di dalam tanda kutip ganda atau tunggal
      $regex = "/(['\"])(.*?)\\1/";

      // Gunakan preg_replace_callback untuk memproses setiap string yang cocok dengan regex
      return preg_replace_callback($regex, function($matches) {
          // Ambil string yang cocok dari hasil pencarian
          $matchedString = $matches[0];
          // Hapus tanda kutip dari string yang cocok
          $matchedString = str_replace(['"', "'"], '', $matchedString);
          if(empty($matchedString)) return "\"\"";
          // Periksa apakah string yang cocok merupakan ekspresi regex
          if (substr($matchedString, 0, 1) == '/' && substr($matchedString, -1) == '/') {
              // Jika iya, kembalikan string tanpa perubahan
              return "\"{$matchedString}\"";
          } else
          if (substr($matchedString, 0, 1) == '@' && substr($matchedString, -1) == '@') {
              // Jika iya, kembalikan string tanpa perubahan
              return "\"{$matchedString}\"";
          }
           else {

              // Lakukan konversi pada string yang bersih
              $encryptedString = AppConfig::$isEncryptString ? ChrConverter::convert($matchedString) : $matchedString;

              // Format ulang string yang cocok dengan tautan dan tanda kutip yang sesuai
              $replacedString = "\"{$encryptedString}\"";
              return $replacedString;
          }
      }, $input);
  }

}
?>
