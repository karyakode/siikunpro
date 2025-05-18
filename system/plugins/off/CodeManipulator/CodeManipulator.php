<?php

namespace CodeManipulator;
use KaryaKode\TextArtGenerator\TextArtGenerator;
use KaryaKode\AppConfig\AppConfig;
use KaryaKode\ChecksumHandler\ChecksumHandler;
use KaryaKode\CodeMinifier\CodeMinifier;
use KaryaKode\Plugins\PluginInterface;

class CodeManipulator implements PluginInterface {
  private static $isActive = false;
  private static $name = "Code Manipulator";
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
            <input type="checkbox" name="encryption" id="encryption" value="yes" checked="checked">
                Use complete, single File Encryption
            <small class="text-muted">
                (after encryption no further code or other changes in the file are possible)
            </small>
        </label>
    </div>';
  }

  public static function isClass($code){
    // Cari posisi akhir dari tanda kurung kurawal penutup `}` pada kelas
    $endOfClassPosition = strrpos($code, '}');
    // Jika tidak ada tanda kurung kurawal penutup, kembalikan kelas tanpa perubahan
    if ($endOfClassPosition === false) {
        return false;
    }

    // Pisahkan kelas menjadi bagian sebelum dan sesudah tanda kurung kurawal penutup
    $classBeforeInsertion = substr($code, 0, $endOfClassPosition);
    // Periksa apakah ada definisi kelas dalam kode
    $matches = [];
    preg_match('/\bclass\s+(\w+)\s*\{/', $classBeforeInsertion, $matches);
    $className = isset($matches[1]) ? $matches[1] : '';

    // Jika tidak ada definisi kelas, kembalikan kelas tanpa perubahan
    if (empty($className)) {
        return false;
    }

    return true;
  }

  public static function insertCodeIntoClass($code, $codeToInsert) {
      // Temukan posisi sebelum deklarasi visibilitas dari method terakhir dalam kelas
      $beforeLastMethodVisibilityPosition = self::findBeforeLastMethodVisibilityPosition($code);
      if (!$beforeLastMethodVisibilityPosition) {
          return $code;
      }

      // Pisahkan kelas menjadi bagian sebelum dan sesudah posisi tersebut
      $classBeforeInsertion = substr($code, 0, $beforeLastMethodVisibilityPosition);
      $classAfterInsertion = substr($code, $beforeLastMethodVisibilityPosition);

      // Periksa apakah ada definisi kelas dalam kode
      $matches = [];
      preg_match('/\bclass\s+(\w+)\s*\{/', $classBeforeInsertion, $matches);
      $className = isset($matches[1]) ? $matches[1] : '';

      // Jika tidak ada definisi kelas, kembalikan kelas tanpa perubahan
      if (empty($className)) {
          return $code;
      }

      // Cari semua metode dalam kelas
      preg_match_all('/\b(private|public|protected|static)\s+function\s+(\w+)\s*\(/', $classBeforeInsertion, $methodMatches);
      $methods = isset($methodMatches[2]) ? $methodMatches[2] : [];

      // Temukan apakah metode dalam $codeToInsert bersifat statis
      if (preg_match('/\bstatic\s+function\s+(\w+)\s*\(/', $codeToInsert, $matches)) {
          // Jika metode adalah statis, gunakan self::
          $newMethodName = isset($matches[1]) ? $matches[1] : '';
          $newMethodCall = "self::$newMethodName();\n";
      } elseif (preg_match('/\b(private|public|protected)\s+function\s+(\w+)\s*\(/', $codeToInsert, $matches)) {
          // Jika metode tidak statis, gunakan $this->
          $newMethodName = isset($matches[1]) ? $matches[1] : '';
          $newMethodCall = '$instance = new self();';
          $newMethodCall .= '$instance->'.$newMethodName.'();'."\n";
      }

      // Masukkan kode baru di dalam setiap metode yang ditemukan
      foreach ($methods as $method) {
          $classBeforeInsertion = self::insertCodeIntoMethod($method, $newMethodCall, $classBeforeInsertion);
      }

      // Gabungkan kode yang dimasukkan di antara kedua bagian
      $modifiedClass = $classBeforeInsertion . $codeToInsert . $classAfterInsertion;

      return $modifiedClass;
  }

  private static function insertCodeIntoMethod($methodName, $codeToInsert, $classCode) {
      // Pola ekspresi reguler untuk mencocokkan definisi metode
      $pattern = '/\b(private|public|protected|static)\s+function\s+' . $methodName . '\s*\([^)]*\)\s*{/';

      // Menggunakan preg_replace_callback untuk menyisipkan kode baru
      $newCode = preg_replace_callback($pattern, function($matches) use ($codeToInsert) {
          // Memasukkan kode baru setelah tanda kurung kurawal buka {
          return $matches[0] . "\n" . $codeToInsert;
      }, $classCode);

      return $newCode;
  }

  private static function findBeforeLastMethodVisibilityPosition($code) {
      // Temukan posisi terakhir dari method dalam kode
      $lastMethodPosition = self::findLastMethodPosition($code);

      // Jika tidak ada method dalam kode, kembalikan false
      if (!$lastMethodPosition) {
          return false;
      }

      // Pisahkan kode sebelum posisi terakhir dari method
      $codeBeforeLastMethod = substr($code, 0, $lastMethodPosition);

      // Temukan posisi terakhir dari deklarasi visibilitas method sebelum method terakhir
      $lastVisibilityPosition = strrpos($codeBeforeLastMethod, "\n");

      return $lastVisibilityPosition;
  }

  public static function findLastMethodPosition($code) {
      // Temukan posisi penutup kurawal terakhir dari method dalam kelas
      preg_match_all('/\b(private|public|protected|static)\s+function\s+(\w+)\s*\([^)]*\)\s*{/', $code, $matches, PREG_OFFSET_CAPTURE);

      // Jika tidak ada method dalam kode, kembalikan false
      if (empty($matches[0])) {
          return false;
      }

      // Ambil posisi terakhir dari method dalam kode
      $lastMatch = end($matches[0]);
      $lastMethodPosition = $lastMatch[1];

      return $lastMethodPosition;
  }




  public static function insertCodeIntoFunction($code, $codeToInsert) {
      // Temukan posisi pembukaan kurawal dari semua fungsi dalam kode
      $functionPositions = self::findAllFunctionPositions($code);

      // Jika tidak ada fungsi dalam kode, kembalikan kode tanpa perubahan
      if (empty($functionPositions)) {
          return $code;
      }

      // Pisahkan kode menjadi bagian sebelum dan sesudah posisi pembukaan kurawal
      $modifiedCode = $code;
      foreach ($functionPositions as $position) {
          $codeBeforeInsertion = substr($modifiedCode, 0, $position);
          $codeAfterInsertion = substr($modifiedCode, $position);

          // Ambil definisi fungsi dari kode sebelum penyisipan
          $functionDefinition = self::extractFunctionDefinition($codeBeforeInsertion);

          // Cek apakah fungsi dimulai dengan kata kunci visibilitas
          if (!self::startsWithVisibilityKeyword($functionDefinition)) {
              // Menggunakan preg_replace_callback untuk menyisipkan kode baru
              $pattern = '/\bfunction\s+(\w+)\s*\([^)]*\)\s*{/';
              $newCode = preg_replace_callback($pattern, function($matches) use ($codeToInsert) {
                  // Memasukkan kode baru setelah tanda kurung kurawal buka {
                  return $matches[0] . "\n" . $codeToInsert;
              }, $codeBeforeInsertion);

              //$modifiedCode = $newCode . $codeAfterInsertion;
          }
      }

      return $modifiedCode;
  }

  private static function extractFunctionDefinition($code) {
      // Ekstrak definisi fungsi dari kode
      preg_match('/\bfunction\s+(\w+)\s*\([^)]*\)\s*{/', $code, $matches);
      return isset($matches[0]) ? $matches[0] : '';
  }

  private static function startsWithVisibilityKeyword($functionDefinition) {
      // Periksa apakah fungsi dimulai dengan kata kunci visibilitas
      return preg_match('/\b(private|public|protected|static)\s+function/', $functionDefinition);
  }

  private static function findAllFunctionPositions($code) {
      // Temukan posisi pembukaan kurawal dari semua fungsi dalam kode
      preg_match_all('/\bfunction\s+(\w+)\s*\([^)]*\)\s*{/', $code, $matches, PREG_OFFSET_CAPTURE);

      // Ambil semua posisi pembukaan kurawal dari fungsi dalam kode
      $functionPositions = [];
      foreach ($matches[0] as $match) {
          $functionPositions[] = $match[1];
      }

      return $functionPositions;
  }



}
