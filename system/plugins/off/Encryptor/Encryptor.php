<?php
namespace Encryptor;

use KaryaKode\AppConfig\AppConfig;
use KaryaKode\CodeCleaner\CodeCleaner;
use KaryaKode\CodeMinifier\CodeMinifier;
use KaryaKode\TextArtGenerator\TextArtGenerator;
use KaryaKode\ChecksumHandler\ChecksumHandler;
use KaryaKode\StringUtil\StringUtil;
use KaryaKode\ChrConverter\ChrConverter;
use KaryaKode\Plugins\PluginInterface;
use KaryaKode\Plugins\PluginLoader as Plugin;

class Encryptor implements PluginInterface {
  private static $isActive = false;
  private static $name = "Encryptor";
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
            <small class="text-muted">
                (This tool encrypts the entire codebase, making it unreadable and securing the code from unauthorized modifications)
            </small>
        </label>
    </div>';
  }


    public static function encrypt($code){
      if(empty(self::isActive())) return $code;

        $md1 = StringUtil::varGen(5, "_");
        $md2 = StringUtil::varGen(5, "_");
        $md3 = StringUtil::varGen(5, "_");
        $md4 = StringUtil::varGen(7, "_");
        $md5 = StringUtil::varGen(7, "_");
        $md6 = StringUtil::varGen(7, "_");
        $md7 = StringUtil::varGen(7, "_");

        $run_reflection = StringUtil::rstr(true, 32);
        $errorHandler = StringUtil::rstr(true, 34);
        $Siikunpro = StringUtil::rstr(true, 15);

        $code = self::encryptContent($code);

        $layer_1 = '
          if(!class_exists("'.$Siikunpro.'")){
          class '.$Siikunpro.'{
              public static function runing(){

                  if(!preg_match("#' . AppConfig::$signature . '#",file_get_contents(' . AppConfig::$localFileIdentifier . '))){
                      exit("'.AppConfig::$errorMessage.'");
                  }
                  if(count(file(' . AppConfig::$localFileIdentifier . '))!="'.ChrConverter::convert(AppConfig::$lineCount).'"){
                      exit("'.AppConfig::$errorMessage.'");
                  }

                  if (dirname('.AppConfig::$localFileIdentifier.') != __DIR__ && '.AppConfig::$localFileIdentifier.' !== __FILE__) {
                    exit("'.AppConfig::$errorMessage.'");
                  }

                  if(function_exists("minify")) ob_start("minify");
                    '.$Siikunpro.'::load(' . AppConfig::$localFileIdentifier . ');
                  if(function_exists("minify")) ob_end_flush();
              }

              protected static function load($s){
                  if(!$s){
                      return false;
                  }

                  $c=explode("'.ChrConverter::convert(AppConfig::$localFileIdentifierName).'",file_get_contents($s));
                  $encryptedData=base64_decode(str_replace("'.ChrConverter::convert(AppConfig::$placeholder).'","",$c[1]));
                  $iv = substr($encryptedData, "'.ChrConverter::convert('0').'", "'.ChrConverter::convert('16').'");
                  $encryptedText = substr($encryptedData, "'.ChrConverter::convert('16').'", "'.ChrConverter::convert('-32').'"); // Exclude IV and HMAC
                  $receivedHmac = substr($encryptedData, "'.ChrConverter::convert('-32').'"); // Last 32 bytes for HMAC

                  $decryptedText = openssl_decrypt($encryptedText, "'.ChrConverter::convert('aes-256-cbc').'", "'.ChrConverter::convert(AppConfig::$encryptionKey).'", OPENSSL_RAW_DATA, $iv);

                  $calculatedHmac = hash_hmac("'.ChrConverter::convert('sha256').'", $encryptedText, "'.ChrConverter::convert(AppConfig::$encryptionKey).'", true);

                  if (hash_equals($receivedHmac, $calculatedHmac)) {
                       eval($decryptedText);
                  } else {
                    exit("'.AppConfig::$errorMessage.'");
                  }
              }
          }


        }
        '.$Siikunpro.'::runing();
        ';

        $layer_1 = self::encryptInCode(CodeMinifier::minifyPhpCode($layer_1, true));

        $layer_2 = '
        if(!function_exists("'.$run_reflection.'")){
          function '.$run_reflection.'(){
            $content = file_get_contents('.AppConfig::$localFileIdentifier.');
            $explodedContent = explode("'.ChrConverter::convert(AppConfig::$exploder).'", preg_replace("'.ChrConverter::convert(AppConfig::$placeholder).'", "", $content));
            $encryptedData = base64_decode($explodedContent[1]);

            $iv = substr($encryptedData, "'.ChrConverter::convert('0').'", "'.ChrConverter::convert('16').'");
            $encryptedText = substr($encryptedData, "'.ChrConverter::convert('16').'", "'.ChrConverter::convert('-32').'"); // Exclude IV and HMAC
            $receivedHmac = substr($encryptedData, "'.ChrConverter::convert('-32').'"); // Last 32 bytes for HMAC

            $decryptedText = openssl_decrypt($encryptedText, "'.ChrConverter::convert('aes-256-cbc').'", "'.ChrConverter::convert(AppConfig::$encryptionKey).'", OPENSSL_RAW_DATA, $iv);

            $calculatedHmac = hash_hmac("'.ChrConverter::convert('sha256').'", $encryptedText, "'.ChrConverter::convert(AppConfig::$encryptionKey).'", true);

            if (hash_equals($receivedHmac, $calculatedHmac)) {
                 return eval($decryptedText);
            } else {
              exit("'.AppConfig::$errorMessage.'");
            }

          }
        }
        call_user_func("'.ChrConverter::convert(''.$run_reflection.'').'");

        ';

        $layer_2 = self::encryptInCode(CodeMinifier::minifyPhpCode($layer_2, true));

        $layer_3 = self::setupFunction(
          $md1 . '=function(' . $md2 . ',' . $md3 . '){
            eval(' . $md2 . '(base64_decode(' . $md3 . ')));
          };
          ' . $md1 . '(hex2bin("' . bin2hex('gzinflate') . '"),
          explode("'.AppConfig::$localFileIdentifierName.'",
          file_get_contents(' . AppConfig::$localFileIdentifier . '))[2]);',
          AppConfig::$encryptionKey
        );

        $layer_3 = self::encryptInCode(CodeMinifier::minifyPhpCode($layer_3, true));


        $layer_4 = '
        if(!function_exists("'.$errorHandler.'")){
          function '.$errorHandler.'($errno, $errstr) {
            exit("'.AppConfig::$errorMessage.' ".$errstr);
         }}';
        $layer_4 .= $md4 . '= function($str="' . str_replace(array('"', '$'), array('\"', '\$'), $layer_3) . '"){
            $str = base64_decode($str);
            $iv = substr($str, "'.ChrConverter::convert('0').'", "'.ChrConverter::convert('16').'");
            $encryptedText = substr($str, "'.ChrConverter::convert('16').'", "'.ChrConverter::convert('-32').'"); // Exclude IV and HMAC
            $receivedHmac = substr($str, "'.ChrConverter::convert('-32').'"); // Last 32 bytes for HMAC

            $decryptedText = openssl_decrypt($encryptedText, "'.ChrConverter::convert('aes-256-cbc').'", "'.ChrConverter::convert(AppConfig::$encryptionKey).'", OPENSSL_RAW_DATA, $iv);

            $calculatedHmac = hash_hmac("'.ChrConverter::convert('sha256').'", $encryptedText, "'.ChrConverter::convert(AppConfig::$encryptionKey).'", true);

            if (hash_equals($receivedHmac, $calculatedHmac)) {
                 $str = $decryptedText;
            } else {
              exit("'.AppConfig::$errorMessage.'");
            }
            $ky=str_replace(chr(32),"","'.ChrConverter::convert(AppConfig::$encryptionKey).'");
            if(strlen($ky)<8)exit();
            $kl=strlen($ky)<32?strlen($ky):32;
            $k=array();
            for($i=0;$i<$kl;$i++){
              $k[$i]=ord($ky[$i])&0x1F;
            }
            $j=0;
            for($i=0;$i<strlen($str);$i++){
              $e=ord($str[$i]);
              $str[$i]=$e&0xE0?chr($e^$k[$j]):chr($e);
              $j++;
              $j=$j==$kl?0:$j;
            }
            return $str;
          };

          set_error_handler("'.$errorHandler.'");
          try {
            eval('  . $md4 . '());
          } catch (Error $e) {
            exit("'.AppConfig::$errorMessage.' {$e}");
          }';

          $layer_4 = self::encryptInCode(CodeMinifier::minifyPhpCode($layer_4, true));

          $outputText = 'eval(base64_decode("'.base64_encode($layer_4).'"));';
          //$outputText = 'eval(gzinflate("'.gzdeflate($layer_4).'"));';
          //$outputText = $layer_4;
          // Output halt
          $outputHalt = '__halt_compiler();';
          // Output Siikunpro
          $outputSIIKUNPRO = strtoupper(AppConfig::$localFileIdentifierName).'.ID::'. AppConfig::$exploder . wordwrap(self::encryptContent($layer_1),50,AppConfig::$placeholder,true) . AppConfig::$exploder .'_'.AppConfig::$localFileIdentifierName.''.wordwrap($code,50,AppConfig::$placeholder,true).AppConfig::$localFileIdentifierName.''.base64_encode(gzdeflate($layer_2));

          $encodeContent = $outputText;
          $encodeContent .= $outputHalt;
          $encodeContent .= $outputSIIKUNPRO;


          return $encodeContent;
    }

    private static function encryptContent($s)
    {
        $iv = openssl_random_pseudo_bytes(16); // Generate a random initialization vector

        $encrypted = openssl_encrypt($s, 'aes-256-cbc', AppConfig::$encryptionKey, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $encrypted, AppConfig::$encryptionKey, true); // Calculate HMAC using the encryption key
        $encryptedData = base64_encode($iv . $encrypted . $hmac);

        return $encryptedData;
    }

    private static function setupFunction($str, $ky = '12345678')
    {
        if ($ky == '') return $str;
        $ky = str_replace(chr(32) , '', $ky);
        if (strlen($ky) < 8) exit();
        $kl = strlen($ky) < 32 ? strlen($ky) : 32;
        $k = array();
        for ($i = 0;$i < $kl;$i++)
        {
            $k[$i] = ord($ky[$i]) & 0x1F;
        }
        $j = 0;
        for ($i = 0;$i < strlen($str);$i++)
        {
            $e = ord($str[$i]);
            $str[$i] = $e & 0xE0 ? chr($e ^ $k[$j]) : chr($e);
            $j++;
            $j = $j == $kl ? 0 : $j;
        }
        return self::encryptContent($str);
    }



    private static function encryptInCode($code){
      if(Plugin::isActive('VariableEncryptor') && class_exists('VariableEncryptor\VariableEncryptor')) {
        $code = \VariableEncryptor\VariableEncryptor::encryptVariablesInCode($code);
      }
      if(Plugin::isActive('FunctionEncryptor') && class_exists('FunctionEncryptor\FunctionEncryptor')) {
        $code = \FunctionEncryptor\FunctionEncryptor::encryptFunctionsInCode($code);
      }
      if(Plugin::isActive('ClassEncryptor') && class_exists('ClassEncryptor\ClassEncryptor')) {
        $code = \ClassEncryptor\ClassEncryptor::encryptClassesInCode($code);
      }
      if(Plugin::isActive('MethodEncryptor') && class_exists('MethodEncryptor\MethodEncryptor')) {
        $code = \MethodEncryptor\MethodEncryptor::encryptMethodsInCode($code);
      }
      if(Plugin::isActive('StringEncryptor') && class_exists('StringEncryptor\StringEncryptor')) {
        $code = \StringEncryptor\StringEncryptor::encryptStringsInCode($code);
      }

      return $code;
    }


}
