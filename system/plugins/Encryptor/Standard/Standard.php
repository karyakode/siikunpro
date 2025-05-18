<?php

// File: plugins/YourPlugin/YourPlugin.php

namespace Encryptor\Standard;

use KaryaKode\Plugins\PluginInterface;

class Standard implements PluginInterface {
  private static $isActive = true;
  private static $name = "Default Encoder";
  private static $author = "Karya Kode";
  private static $version = "1.0.0";
  private static $id = "standard";

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

  public static  function getId() {
      return self::$id;
  }

  public static function display(){

    return '
    <div class="checkbox">
        <label>
            <input type="checkbox" name="encryptor" id="encryptor" value="yes" checked="checked">
            '.self::$name.'
            <!--small class="text-muted">
                (This tool encrypts the entire codebase, making it unreadable and securing the code from unauthorized modifications)
            </small-->
        </label>
    </div>';
  }

  public static function javascript(){

    return '<script> </script>';
  }


  public static function encrypt($sourceFile, $targetFile, $append){

    $obfuscatedSource = \KaryaKode\PhpObfuscator\Obfuscator::obfuscateContent($sourceFile);
    $obfuscatedSource = Encoder::encode($obfuscatedSource);
    if ($obfuscatedSource === false) {
        //throw new \Exception("Gagal membaca file: {$sourceFilePath}");
    }
    if (!\KaryaKode\Analysis\File::writeContent($targetFile, $obfuscatedSource, $append)) {
        //throw new \Exception("Gagal menulis file: {$destinationFilePath}");
    }

  }

}


class Encoder {



    /**
  	 * @var array
  	 */
  	private $memVar = [];

    /**
     * @var string
     */
    private static $key = 'bismillah';

    public static $hashType = 'sha1';

    /**
     * @var array
     */
    private $func = [];
    public static $base64Encode = false;

    public static $fileName;


      private static function base64Encode($code){
        $code = gzdeflate($code);
        $obfCode = self::$base64Encode ? base64_encode($code) : self::escape($code);

        return $obfCode;
      }

      public static function encode(string $code, int $noiseLevel = 1) : string {
          $code = self::removePHPTags($code);
          $obfCode = self::base64Encode(strrev($code));
          $obfCode = self::escape(self::encrypt(strrev($code), self::$key, self::$base64Encode));

          $number=round((mt_rand(1,mt_rand(32,64))*mt_rand(1,10))/mt_rand(1,10));
          $string = self::$base64Encode ? "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ" : self::UNPRINT_ELI_CHARS;
          $generateDecryptorName = self::rstr(true, 10, $string);


          $randLenFront = rand(44 * $noiseLevel, 100 * $noiseLevel);
          //$randLenFront = round((mt_rand(1,mt_rand(44,100))*mt_rand(1,10))/mt_rand(1,10));//rand(44 * $noiseLevel, 100 * $noiseLevel);
          $randFront = self::rstr(false, $randLenFront, $string);
          $randLenBack = rand(22 * $noiseLevel, 67 * $noiseLevel);
          //$randLenBack = round((mt_rand(1,mt_rand(22,67))*mt_rand(1,10))/mt_rand(1,10));//rand(22 * $noiseLevel, 67 * $noiseLevel);
          $randBack = self::rstr(false, $randLenBack, $string);

          $decodeFuncName = self::rstr(true, 15, $string);

          //$numEvals = rand($noiseLevel + 1, ($noiseLevel + 1) * 2);
          $numEvals = $noiseLevel;

          $obfCode = str_repeat("eval(", $numEvals) . $decodeFuncName . "(\"" . $randFront . $obfCode . $randBack . "\")" . str_repeat(")", $numEvals) . "";

          return self::generateCode($obfCode, $decodeFuncName, $randLenFront, $randLenBack, $generateDecryptorName);
          //$vqriable = self::escape(self::gen2(4096 * 3));
          //return "\${\"{$vqriable}\"}";
      }

      private static function getFileName() {

        if(empty(self::$fileName)) return false;
        $nameFile = str_replace('.php','', self::$fileName);
        $nameFile = str_replace('-','', $nameFile);
        $nameFile = str_replace(' ','', $nameFile);
        $nameFile = str_replace('_','', $nameFile);

        //return '';
        return strrev($nameFile);
      }

      private static function generateCode(string $obfCode, string $decodeFuncName, int $frontLen, int $backLen, string $generateDecryptorName) : string {
          $randomVarOne = self::randomVariable().''.self::getFileName();
          $randomVarTwo = self::randomVariable().''.self::getFileName();
          $randomVarThree = self::randomVariable().''.self::getFileName();
          $base64Decode = self::randomVariable().''.self::getFileName();


          $substrFuncName = "_" . self::randomString(rand(5, 9)).''.self::getFileName();
          $substrFunc ="if(!function_exists('{$substrFuncName}')){";
          $substrFunc .= "function " . $substrFuncName . "(" . $randomVarThree . "){" . $randomVarThree
                      . "=substr(" . $randomVarThree . ",(int)(hex2bin(\"" . self::convert(bin2hex($frontLen)) . "\")));"
                      . $randomVarThree . "=substr(" . $randomVarThree . ",(int)(hex2bin(\"" . self::convert(bin2hex(0)) . "\")),(int)(hex2bin(\""
                      . self::convert(bin2hex(-1 * $backLen)) . "\")));return " . $randomVarThree . ";}";
          $substrFunc .= "}";
          $decodeFunc ="if(!function_exists('{$decodeFuncName}')){";
            if(empty(self::$base64Encode)) {
              //$decodeFunc .= "function ". $decodeFuncName . "(" . $randomVarOne . "){return strrev(gzinflate(" . $substrFuncName . "(" . $randomVarOne . ")));}";
            } else {
              //$decodeFunc .= "function ". $decodeFuncName . "(" . $randomVarOne . "){return strrev(gzinflate(base64_decode(" . $substrFuncName . "(" . $randomVarOne . "))));}";
            }

            $decodeFunc .= "function ". $decodeFuncName . "(" . $randomVarOne . "){return strrev({$generateDecryptorName}(" . $substrFuncName . "(" . $randomVarOne . ")));}";

          $decodeFunc .= "}";
          //$midObfCode = "eval(gzinflate(\"".self::escape(gzdeflate($substrFunc. $decodeFunc))."\"));" . $obfCode;

          $generateDecryptor = self::generateDecryptor($generateDecryptorName);


          /*
          $obfuscatedSource = self::escape(gzdeflate($substrFunc. $decodeFunc));
          $midObfCode = "eval(gzinflate(\"".$obfuscatedSource."\"));" . $generateDecryptor.$obfCode;


          $obfuscatedSource = self::escape(gzdeflate($substrFunc. $decodeFunc));
          $midObfCode = "eval(gzinflate(\"".$obfuscatedSource."\"));" .$obfCode;
          */

          $generateDecryptor = self::escape(gzdeflate($generateDecryptor));
          $generateDecryptor = "eval(gzinflate(\"".$generateDecryptor."\"));";

          $obfuscatedSource = self::escape(self::encrypt($substrFunc. $decodeFunc, self::$key, self::$base64Encode));
          $midObfCode = "eval({$generateDecryptorName}(\"".$obfuscatedSource."\"));" .$obfCode;


          $lastEvaluation = '__halt_compiler();';
          $footerSign = '';//$lastEvaluation.self::escape(self::encrypt($substrFunc. $decodeFunc, self::$key, self::$base64Encode));
          $TextArtGenerator = \KaryaKode\TextArtGenerator\TextArtGenerator::generateTextArt();
          /*$fullyObfCode = "<?php\n eval(base64_decode('" . base64_encode($midObfCode) . "'));\n?>";*/
  				$fullyObfCode = "<?php\n {$TextArtGenerator}\n{$generateDecryptor}{$midObfCode};{$footerSign}?>";

          return $fullyObfCode;
      }



      /**
    	 * @param string $string
    	 * @param string $key
    	 * @param bool	 $binarySafe
    	 * @return string
    	 */
    	private static function encrypt(string $string, string $key, bool $binarySafe = false): string
    	{
    		$string = gzdeflate($string, 9);
    		$slen = strlen($string);
    		$klen = strlen($key);
    		$r = $newKey = "";
    		$salt = self::saltGenerator();
    		$cost = 1;
    		for($i = $j = 0;$i < $klen; $i++) {
    			$newKey .= chr(ord($key[$i]) ^ ord($salt[$j++]));
    			if ($j === 5) {
    				$j = 0;
    			}
    		}
    		$newKey = hash(self::$hashType,$newKey);
    		for($i = $j = $k = 0; $i < $slen; $i++) {
    			$r .= chr(
    				ord($string[$i]) ^ ord($newKey[$j++]) ^ ord($salt[$k++]) ^ ($i << $j) ^ ($k >> $j) ^
    				($slen % $cost) ^ ($cost >> $j) ^ ($cost >> $i) ^ ($cost >> $k) ^
    				($cost ^ ($slen % ($i + $j + $k + 1))) ^ (($cost << $i) % 2) ^ (($cost << $j) % 2) ^
    				(($cost << $k) % 2) ^ (($cost * ($i+$j+$k)) % 3)
    			);
    			$cost++;
    			if ($j === $klen) {
    				$j = 0;
    			}
    			if ($k === 5) {
    				$k = 0;
    			}
    		}
    		$r .= $salt;
    		if ($binarySafe) {
    			return strrev(base64_encode($r));
    		} else {
    			return $r;
    		}
    	}


      /**
    	 * @param string $decryptorName
    	 * @return string
    	 */
    	private static function generateDecryptor($decryptorName): string
    	{
    		$rc = range(chr(128), chr(255));
    		$var = [
    			"string" => "\$".self::rstr(true, 32, self::UNPRINT_ELI_CHARS),
    			"key" => "\$".self::rstr(true, 32, self::UNPRINT_ELI_CHARS),
    			"binary" => "\$".self::rstr(true, 32, self::UNPRINT_ELI_CHARS),
    			"slen" => "\$".self::rstr(true, 32, self::UNPRINT_ELI_CHARS),
    			"salt" => "\$".self::rstr(true, 32, self::UNPRINT_ELI_CHARS),
    			"klen" => "\$".self::rstr(true, 32, self::UNPRINT_ELI_CHARS),
    			"new" => "\$".self::rstr(true, 32, self::UNPRINT_ELI_CHARS),
    			"r" => "\$".self::rstr(true, 32, self::UNPRINT_ELI_CHARS),
    			"cost" => "\$".self::rstr(true, 32, self::UNPRINT_ELI_CHARS),
    			"i" => "\$".self::rstr(true, 32, self::UNPRINT_ELI_CHARS),
    			"j" => "\$".self::rstr(true, 32, self::UNPRINT_ELI_CHARS),
    			"k" => "\$".self::rstr(true, 32, self::UNPRINT_ELI_CHARS)
    		];

    		$decryptorFunc ="if(!function_exists('{$decryptorName}')){";
    		$decryptorFunc .= 'function '.$decryptorName.'('.$var["string"].', '.$var["key"].' = "'.self::convert(self::$key).'", '.$var["binary"].' = '.(self::$base64Encode ? true : 0).') { if ('.$var["binary"].') { '.$var["string"].' = base64_decode(strrev('.$var["string"].')); } '.$var["slen"].' = strlen('.$var["string"].'); '.$var["salt"].' = substr('.$var["string"].', '.$var["slen"].' - 5); '.$var["string"].' = substr('.$var["string"].', 0, ('.$var["slen"].' = '.$var["slen"].' - 5)); '.$var["klen"].' = strlen('.$var["key"].'); '.$var["new"].' = '.$var["r"].' = ""; '.$var["cost"].' = 1; for('.$var["i"].'='.$var["j"].'=0;'.$var["i"].'<'.$var["klen"].';'.$var["i"].'++) { '.$var["new"].' .= chr(ord('.$var["key"].'['.$var["i"].']) ^ ord('.$var["salt"].'['.$var["j"].'++])); if ('.$var["j"].' === 5) { '.$var["j"].' = 0; } } '.$var["new"].' = hash("'.self::$hashType.'",'.$var["new"].'); for('.$var["i"].'='.$var["j"].'='.$var["k"].'=0;'.$var["i"].'<'.$var["slen"].';'.$var["i"].'++) { '.$var["r"].' .= chr( ord('.$var["string"].'['.$var["i"].']) ^ ord('.$var["new"].'['.$var["j"].'++]) ^ ord('.$var["salt"].'['.$var["k"].'++]) ^ ('.$var["i"].' << '.$var["j"].') ^ ('.$var["k"].' >> '.$var["j"].') ^ ('.$var["slen"].' % '.$var["cost"].') ^ ('.$var["cost"].' >> '.$var["j"].') ^ ('.$var["cost"].' >> '.$var["i"].') ^ ('.$var["cost"].' >> '.$var["k"].') ^ ('.$var["cost"].' ^ ('.$var["slen"].' % ('.$var["i"].' + '.$var["j"].' + '.$var["k"].' + 1))) ^ (('.$var["cost"].' << '.$var["i"].') % 2) ^ (('.$var["cost"].' << '.$var["j"].') % 2) ^ (('.$var["cost"].' << '.$var["k"].') % 2) ^ (('.$var["cost"].' * ('.$var["i"].'+'.$var["j"].'+'.$var["k"].')) % 3) ); '.$var["cost"].'++; if ('.$var["j"].' === '.$var["klen"].') { '.$var["j"].' = 0; } if ('.$var["k"].' === 5) { '.$var["k"].' = 0; } } return gzinflate('.$var["r"].'); }';
    		$decryptorFunc .= "}";

    		return $decryptorFunc;
    	}

      private static function removePHPTags(string $code) : string {
          $code = trim($code);

          $startTags = [ "<?php", "<?hh", "<?" ];
          foreach ($startTags as $startTag) {
              if (substr($code, 0, strlen($startTag)) == $startTag) {
                  $code = substr($code, strlen($startTag));

                  if ($startTag == "<?") {
                      $nextChar = substr($code, 0, 1);

                      if ($nextChar == "=") {
                          $code = "echo " . substr($code, 1);
                      }
                  }

                  break;
              }
          }

          if (substr($code, -2) == "?>") {
              $code = substr($code, 0, -2);
          }

          return $code;
      }

      private static function randomVariable($generate = false) : string {
          if(empty($generate)) return "\$_" . self::randomString(rand(5, 9));
          $veriable = self::escape(self::gen2(100));
          return "\${\"{$veriable}\"}";
      }

      private static function randomString($length) : string {
          $allowedChars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

          $rand = "";
          $charCount = strlen($allowedChars);
          for ($i = 0; $i < $length; $i++) {
              $rand .= $allowedChars[rand(0, $charCount - 1)];
          }

          return $rand;
      }

      /**
       * @param string $str
       * @return string
       */
      private static function chrToHex($str)
      {
        return "\\x".dechex(ord($str));
      }

      /**
       * @param string $str
       * @return string
       */
      private static function chrToOct($str)
      {
        return "\\".decoct(ord($str));
      }

      /**
    	 * @param int $n
    	 * @param bool $noExtended
    	 * @return string
    	 */
    	private static function gen(int $n, bool $noExtendedAscii = false): string
    	{
    		$r = "";
    		$a = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM_";
    		if (! $noExtendedAscii) {
    			for ($i=0; $i < 5; $i++) {
    				for ($i=135; $i < 255; $i++) {
    					$a .= chr($i);
    				}
    			}
    		}
    		$c = strlen($a) - 1;
    		$r.= $a[rand(0, $c)];
    		if ($n === 1) {
    			return $r;
    		}
    		$a.= "1234567890";
    		$c = strlen($a) - 1;
    		$n--;
    		for ($i=0; $i < $n; $i++) {
    			$r.= $a[rand(0, $c)];
    		}
    		return $r;
    	}

    	/**
    	 * @param int $n
    	 * @param bool $noExtended
    	 * @return string
    	 */
    	private static function gen2(int $n): string
    	{
    		$r = "";
    		$a = range(chr(0), chr(31));
    		$c = count($a) - 1;
    		$d = range(chr(32), chr(255));
    		$e = count($d) - 1;
    		for ($i=0; $i < $n; $i++) {
    			rand(0, 1) ?
    				$r.= $a[rand(0, $c)] :
    					$r.= $d[rand(0, $e)];
    		}
    		return $r;
    	}

      /**
    	 * @param bool   $defSave
    	 * @param int    $n
    	 * @param string $e
    	 * @return string
    	 */
    	public static function rstr(bool $defSave = true, int $n = 32, ?string $e = null): string
    	{
    		$n = abs($n);
    		if (!is_string($e)) {
    			$e = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPPASDFGHJKLZXCVBNM1234567890___";
    		}

    		for ($r = "", $c = strlen($e) - 1, $i = 0; $i < $n; $i++) {
    			$r .= $e[rand(0, $c)];
    		}

    		if ($defSave) {
    			is_numeric($r[0]) and $r[0] = "_";
    		}

    		return $r;
    	}

      /**
    	 * @param int     $n
    	 * @param string  $pre
    	 * @param ?string $e
    	 * @return string
    	 */
    	private static function varGen(int $n = 32, string $pre = "", ?string $e = null): string
    	{
    		if (!is_string($e)) {
    			$e = self::UNPRINT_ALL;
    		}
    		return "\${\"".self::escape($pre.self::rstr(true, $n, $e))."\"}";
    	}

      /**
       * @param string $str
       * @return string
       */
      private static function convert($str)
      {
        $r = "";
        foreach (str_split($str) as $char) {
          $r .= rand(2, 3) % 2 ? self::chrToOct($char) : self::chrToHex($char);
        }
        return $r;
      }

      /**
    	 * @param string $str
    	 * @return string
    	 */
    	private static function escape(string $str): string
    	{
    		return str_replace(
    			["\\", "\"", "\$"],
    			["\\\\", "\\\"", "\\\$"],
    			$str
    		);
    	}

      /**
    	 * @param int $n
    	 * @return string
    	 */
    	private static function saltGenerator($n = 5)
    	{
    		$s = range(chr(1), chr(0x7f));
    		$r = ""; $c=count($s)-1;
    		for($i=0;$i<$n;$i++) {
    			$r.= $s[rand(0, $c)];
    		}
    		return $r;
    	}

      /**
         * @const string
         */
        public const UNPRINT_ELI_CHARS = "\x80\x81\x82\x83\x84\x85\x86\x87\x88\x89\x8a\x8b\x8c\x8d\x8e\x8f\x90\x91\x92\x93\x94\x95\x96\x97\x98\x99\x9a\x9b\x9c\x9d\x9e\x9f\xa0\xa1\xa2\xa3\xa4\xa5\xa6\xa7\xa8\xa9\xaa\xab\xac\xad\xae\xaf\xb0\xb1\xb2\xb3\xb4\xb5\xb6\xb7\xb8\xb9\xba\xbb\xbc\xbd\xbe\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf7\xf8\xf9\xfa\xfb\xfc\xfd\xfe\xff";

        /**
         * @const string
         */
        public const UNPRINT_ALL = "\x0\x1\x2\x3\x4\x5\x6\x7\x8\x9\xa\xb\xc\xd\xe\xf\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f\x80\x81\x82\x83\x84\x85\x86\x87\x88\x89\x8a\x8b\x8c\x8d\x8e\x8f\x90\x91\x92\x93\x94\x95\x96\x97\x98\x99\x9a\x9b\x9c\x9d\x9e\x9f\xa0\xa1\xa2\xa3\xa4\xa5\xa6\xa7\xa8\xa9\xaa\xab\xac\xad\xae\xaf\xb0\xb1\xb2\xb3\xb4\xb5\xb6\xb7\xb8\xb9\xba\xbb\xbc\xbd\xbe\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf7\xf8\xf9\xfa\xfb\xfc\xfd\xfe\xff";

        /**
         * @const string
         */
        public const ALL_CHARS = "\x0\x1\x2\x3\x4\x5\x6\x7\x8\x9\xa\xb\xc\xd\xe\xf\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f\x20\x21\x22\x23\x24\x25\x26\x27\x28\x29\x2a\x2b\x2c\x2d\x2e\x2f\x30\x31\x32\x33\x34\x35\x36\x37\x38\x39\x3a\x3b\x3c\x3d\x3e\x3f\x40\x41\x42\x43\x44\x45\x46\x47\x48\x49\x4a\x4b\x4c\x4d\x4e\x4f\x50\x51\x52\x53\x54\x55\x56\x57\x58\x59\x5a\x5b\x5c\x5d\x5e\x5f\x60\x61\x62\x63\x64\x65\x66\x67\x68\x69\x6a\x6b\x6c\x6d\x6e\x6f\x70\x71\x72\x73\x74\x75\x76\x77\x78\x79\x7a\x7b\x7c\x7d\x7e\x7f\x80\x81\x82\x83\x84\x85\x86\x87\x88\x89\x8a\x8b\x8c\x8d\x8e\x8f\x90\x91\x92\x93\x94\x95\x96\x97\x98\x99\x9a\x9b\x9c\x9d\x9e\x9f\xa0\xa1\xa2\xa3\xa4\xa5\xa6\xa7\xa8\xa9\xaa\xab\xac\xad\xae\xaf\xb0\xb1\xb2\xb3\xb4\xb5\xb6\xb7\xb8\xb9\xba\xbb\xbc\xbd\xbe\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf7\xf8\xf9\xfa\xfb\xfc\xfd\xfe\xff";

}
