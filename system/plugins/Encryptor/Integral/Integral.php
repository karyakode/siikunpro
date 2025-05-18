<?php namespace Encryptor\Integral;

use Error as PhpError;
use Exception;
use KaryaKode\PhpObfuscator\Obfuscator;
use KaryaKode\Plugins\PluginInterface;

class Integral implements PluginInterface {
  private static $isActive = true;
  private static $name = "Integral Encoder";
  private static $author = "Karya Kode";
  private static $version = "1.0.0";
  private static $id = "integral";

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


  public function javascript(){

    return '<script></script>';
  }

  public static function encrypt($sourceFile, $targetFile){

    $integral = new Encoder($sourceFile, $targetFile);
    $integral->execute();
  }

}



class Encoder
{
	/**
	 * @var string
	 */
	private $inputFile;

	/**
	 * @var string
	 */
	private $outputFile;

	/**
	 * @var string
	 */
	private $sheBang;

	/**
	 * @var string
	 */
	private $compiled;

	/**
	 * @var string
	 */
	private $key;

	/**
	 * @var string
	 */
	public $stringDecryptor;

	/**
	 * @var string
	 */
	public $stringEncryptor;

	/**
	 * @var string
	 */
	public $hashInputFile;

	/**
	 * @var array
	 */
	private $handledSignals = [];

	/**
	 * @var int
	 */
	private $writtenBytes = 0;

	/**
	 * @var string
	 */
	private $footerSign;

	/**
	 * @param ?string $inputFile
	 * @param ?string $outputFile
	 * @param ?string $sheBang
	 * @throws \Exception
	 *
	 * Constructor.
	 */

	public $hashType = 'sha1';

	public function __construct(?string $inputFile = null, ?string $outputFile = null, ?string $key = null, ?string $sheBang = null)
	{
		$this->inputFile = $inputFile;
		$this->outputFile = $outputFile;
		$key = self::rstr(5);
		$this->key = $key;

		$this->sheBang = $sheBang;

		if (is_string($inputFile) && (!file_exists($this->inputFile))) {
			throw new Exception("Input file does not exist: {$inputFile}");
		}

		/**
		 * Linux only.
		 */
/*		$this->handledSignals[] = "SIGHUP";
		$this->handledSignals[] = "SIGINT";
		$this->handledSignals[] = "SIGCHLD";
		$this->handledSignals[] = "SIGTERM";
*/
		$this->fx = [
			"hash" => self::varGen(64, "a"),
			"rand" => self::varGen(64, "b"),
			"fopen" => self::varGen(64, "d"),
			"fread" => self::varGen(64, "e"),
			"fseek" => self::varGen(64, "f"),
			"explode" => self::varGen(64, "g"),
			"time" => self::varGen(64, "0"),
			"extension_loaded" => self::varGen(64, "h"),
		];

	}

	/**
	 * @param ?string $inputFile
	 * @return void
	 */
	public function setInputFile(?string $inputFile): void
	{
		$this->inputFile = $inputFile;
	}

	/**
	 * @param ?string $outputFile
	 * @return void
	 */
	public function setOutputFile(?string $outputFile): void
	{
		$this->outputFile = $outputFile;
	}

	/**
	 * @param ?string $key
	 * @return void
	 */
	public function setKey(?string $key): void
	{
		$this->key = $key;
	}

	/**
	 * @param ?string $sheBang
	 * @return void
	 */
	public function setSheBang(?string $sheBang): void
	{
		$this->sheBang = $sheBang;
	}

	/**
	 * @throws \Exception
	 * @return void
	 */
	public function execute(): void
	{
		$this->stringEncryptor = function ($str) {
			return $this->encrypt($str, $this->key);
		};
		$this->integralCompile();
		$this->buildTargetFile();
	}

	/**
	 * @throws \Exception
	 * @return void
	 */
	private function integralCompile(): void
	{
		$this->hashInputFile = sha1_file($this->inputFile);
		$inputFile = Obfuscator::obfuscateContent($this->inputFile);
		$this->key = hash($this->hashType,$this->key, true);
		$function_name = 'SIIKUNPRO'.$this->getFileName().strtoupper(substr(MD5(microtime()), 0, 17));
		$function_name = self::rstr(true, 5, self::UNPRINT_ELI_CHARS);
		$this->stringDecryptor = "_".$function_name;
		$this->clonerName = $function_name;
		$this->compiled = $inputFile;

	}

	private function getFileName() {

		return strtoupper(strrev(str_replace(['.php','-',' ','_'],['','','',''], basename($this->inputFile))));
	}

	/**
	 * @return void
	 */
	private function buildTargetFile(): void
	{
		$handle = fopen($this->outputFile, "wb+");
		$this->buildHeader($handle);
		$this->buildBody($handle);
		$this->buildFooter($handle);
		fclose($handle);
	}

	/**
	 * @param resource $handle
	 */
	private function buildHeader($handle): void
	{

		$this->kfk = $this->encrypt($this->key, "siikun_integral_obfuscator");
		if (!is_string($this->key)) {
			$this->key = self::rstr(true, 32);
		}

// \0\x7f\1\1\1

$decryptor = $this->escape(gzdeflate($this->generateDecryptor($this->stringDecryptor), 9));

		$siikunpro = "\$SIIKUNPRO".strtoupper(self::rstr2(32));
    $TextArtGenerator = \KaryaKode\TextArtGenerator\TextArtGenerator::generateTextArt();
		$r  = !empty($this->sheBang) ? "#!{$this->sheBang}\n" : "";
		$r .= <<<PHP_CODE
<?php
{$TextArtGenerator}
eval(("{$this->convert("gzinflate")}")("$decryptor"))xor
PHP_CODE;
		$this->writtenBytes += fwrite($handle, $r);
		foreach ($this->fx as $k => $v) {
			$this->writtenBytes += fwrite(
				$handle,
				"{$v}={$this->stringDecryptor}(\"{$this->escape($this->encrypt($k, $this->key))}\")xor/*\ec\0*/"
			);
		}
	}

	/**
	 * @param resource $handle
	 */
	private function buildBody($handle): void
	{
		$mkey = self::rstr(true, 10, self::ALL_CHARS);
		$kfk2 = $this->key."_";
		$enMkey = $this->encrypt($mkey, $kfk2);
		$hfVar = self::rstr(true, 8, self::UNPRINT_ELI_CHARS);
		$mkeyVar = self::rstr(true, 8, self::UNPRINT_ELI_CHARS);
		$vars = [
			"time" => self::varGen(8, "a", self::UNPRINT_ALL),
			"handle" => self::varGen(8, "a", self::UNPRINT_ALL),
			"myHash" => self::varGen(8, "b", self::UNPRINT_ALL),
			"correctHash" => self::varGen(8, "c", self::UNPRINT_ALL),
			"footerHash" => self::varGen(8, "d", self::UNPRINT_ALL),
		];
		$this->footerSign = self::rstr(true, 2000, self::ALL_CHARS);
		$footerHash = hash($this->hashType,$this->footerSign, true);//sha1($this->footerSign, true);
		$footerChecksum = $this->encrypt("siikun_integral", $footerHash);
		$cloner = '';//$this->writeCloner();
		$orCloner = ""; #" or clone new _{$this->clonerName}";
		$keyGenerator = <<<KEY_GENERATOR
			/*\0\ec*/
			{$vars["time"]}={$this->fx["time"]}()xor
			{$vars["handle"]}={$this->fx["fopen"]}({$this->fx["explode"]}("(", __FILE__, 0x2)[0x0], "{$this->convert("rb")}")xor
			{$vars["myHash"]}={$this->fx["hash"]}("{$this->hashType}",{$this->fx["fread"]}({$vars["handle"]}, \${$hfVar}), true)xor
			{$this->fx["fseek"]}({$vars["handle"]}, \${$hfVar} + 0x12c)xor
			{$vars["correctHash"]}={$this->fx["fread"]}({$vars["handle"]}, 20)xor
			{$vars["footerHash"]}={$this->fx["hash"]}("{$this->hashType}",substr({$this->fx["fread"]}({$vars["handle"]}, 0x148d), 0, -3), true)xor
			(
				(
					(!{$this->fx["extension_loaded"]}("{$this->convert("evalhook")}"))
						/*\0*/&&/*\0*/
					({$vars["correctHash"]} === {$vars["myHash"]})
						/*\0*/&&/*\0*/
					{$this->stringDecryptor}("{$this->escape($footerChecksum)}", "{$vars["footerHash"]}") === "{$this->convert("siikun_integral")}"
				)
				and \${$mkeyVar} = {$this->stringDecryptor}("{$this->escape($enMkey)}", "{$this->escape($kfk2)}")
				{$orCloner}
			);{$cloner}//\0\ec
KEY_GENERATOR;

		$this->compiled = "?>".$this->compiled;
		$lastEvaluation = <<<LAST_EVALUATION
			/*\0\ec*/{$vars["time"]}==={$this->fx["time"]}(){$orCloner};/*\0\ec*/
			/*\0\ec*/eval({$this->stringDecryptor}("{$this->escape($this->encrypt($this->compiled, $mkey.$footerHash))}", \${$mkeyVar}.{$vars["footerHash"]}));//\0\ec
LAST_EVALUATION;

		unset($this->compiled);

		$keyGenerator = $this->encrypt(trim($keyGenerator), $this->key);
		$lastEvaluation = $this->encrypt(trim($lastEvaluation), $this->key);
		$this->writtenBytes += fwrite($handle, "/*\0\ec*/\${$hfVar}=__COMPILER_HALT_OFFSET__ xor");
		$this->writtenBytes += fwrite($handle, "/*\0\ec*/eval({$this->stringDecryptor}(\"{$this->escape($keyGenerator)}\"))xor");
		$this->writtenBytes += fwrite($handle, "/*\0\ec*/eval({$this->stringDecryptor}(\"{$this->escape($lastEvaluation)}\"));__halt_compiler();");
		fflush($handle);
	}

	/**
	 * @param resource $handle
	 */
	private function buildFooter($handle): void
	{
		rewind($handle);
		$hash = hash($this->hashType,fread($handle, $this->writtenBytes), true);//sha1(fread($handle, $this->writtenBytes), true);
		fseek($handle, $this->writtenBytes);
		$this->writtenBytes += fwrite(
			$handle,
			self::rstr(false, 300, self::UNPRINT_ALL)."{$hash}{$this->footerSign}\ec\0"
		);
		fflush($handle);
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


	/**
	 * @return string
	 */
	private function writeHandledSignals(): string
	{
		if (count($this->handledSignals)) {
			return " handled_signals = [".implode(", ", $this->handledSignals)."]";
		}
		return "";
	}

	/**
	 * @return string
	 */
	public function writeSignalHandlers(): string
	{
		if (count($this->handledSignals)) {
			$r = "if (function_exists(\"pcntl_signal\")) {\n";
			foreach ($this->handledSignals as $signal) {
				$r .= "\tpcntl_signal($signal, SIG_IGN);\n";
			}
			$r .= "}";

			return $r;
		}
		return "";
	}

	/**
	 * @return string
	 */
	private function writeCloner(): string
	{
		$writeCloner ="if(!class_exists(_{$this->clonerName})){";
		$writeCloner .= "class _{$this->clonerName}{function __clone(){clone\$this;}}";
		$writeCloner .= "}";
		return $writeCloner;
	}

	/**
	 * @param string $decryptorName
	 * @return string
	 */
	private function generateDecryptor($decryptorName): string
	{
		$rc = range(chr(128), chr(255));
		$var = [
			"string" => "\$".self::rstr(true, 8, self::UNPRINT_ELI_CHARS),
			"key" => "\$".self::rstr(true, 8, self::UNPRINT_ELI_CHARS),
			"binary" => "\$".self::rstr(true, 8, self::UNPRINT_ELI_CHARS),
			"slen" => "\$".self::rstr(true, 8, self::UNPRINT_ELI_CHARS),
			"salt" => "\$".self::rstr(true, 8, self::UNPRINT_ELI_CHARS),
			"klen" => "\$".self::rstr(true, 8, self::UNPRINT_ELI_CHARS),
			"new" => "\$".self::rstr(true, 8, self::UNPRINT_ELI_CHARS),
			"r" => "\$".self::rstr(true, 8, self::UNPRINT_ELI_CHARS),
			"cost" => "\$".self::rstr(true, 8, self::UNPRINT_ELI_CHARS),
			"i" => "\$".self::rstr(true, 8, self::UNPRINT_ELI_CHARS),
			"j" => "\$".self::rstr(true, 8, self::UNPRINT_ELI_CHARS),
			"k" => "\$".self::rstr(true, 8, self::UNPRINT_ELI_CHARS)
		];

		$decryptorFunc ="//\ec\0\n"."if(!function_exists('{$decryptorName}')){";
		$decryptorFunc .= 'function '.$decryptorName.'('.$var["string"].', '.$var["key"].' = "'.$this->escape($this->key).'", '.$var["binary"].' = false) { if ('.$var["binary"].') { '.$var["string"].' = base64_decode(strrev('.$var["string"].')); } '.$var["slen"].' = strlen('.$var["string"].'); '.$var["salt"].' = substr('.$var["string"].', '.$var["slen"].' - 5); '.$var["string"].' = substr('.$var["string"].', 0, ('.$var["slen"].' = '.$var["slen"].' - 5)); '.$var["klen"].' = strlen('.$var["key"].'); '.$var["new"].' = '.$var["r"].' = ""; '.$var["cost"].' = 1; for('.$var["i"].'='.$var["j"].'=0;'.$var["i"].'<'.$var["klen"].';'.$var["i"].'++) { '.$var["new"].' .= chr(ord('.$var["key"].'['.$var["i"].']) ^ ord('.$var["salt"].'['.$var["j"].'++])); if ('.$var["j"].' === 5) { '.$var["j"].' = 0; } } '.$var["new"].' = hash("'.$this->hashType.'",'.$var["new"].'); for('.$var["i"].'='.$var["j"].'='.$var["k"].'=0;'.$var["i"].'<'.$var["slen"].';'.$var["i"].'++) { '.$var["r"].' .= chr( ord('.$var["string"].'['.$var["i"].']) ^ ord('.$var["new"].'['.$var["j"].'++]) ^ ord('.$var["salt"].'['.$var["k"].'++]) ^ ('.$var["i"].' << '.$var["j"].') ^ ('.$var["k"].' >> '.$var["j"].') ^ ('.$var["slen"].' % '.$var["cost"].') ^ ('.$var["cost"].' >> '.$var["j"].') ^ ('.$var["cost"].' >> '.$var["i"].') ^ ('.$var["cost"].' >> '.$var["k"].') ^ ('.$var["cost"].' ^ ('.$var["slen"].' % ('.$var["i"].' + '.$var["j"].' + '.$var["k"].' + 1))) ^ (('.$var["cost"].' << '.$var["i"].') % 2) ^ (('.$var["cost"].' << '.$var["j"].') % 2) ^ (('.$var["cost"].' << '.$var["k"].') % 2) ^ (('.$var["cost"].' * ('.$var["i"].'+'.$var["j"].'+'.$var["k"].')) % 3) ); '.$var["cost"].'++; if ('.$var["j"].' === '.$var["klen"].') { '.$var["j"].' = 0; } if ('.$var["k"].' === 5) { '.$var["k"].' = 0; } } return gzinflate('.$var["r"].'); }';
		$decryptorFunc .= "}"."//\ec\0";

		return $decryptorFunc;
	}

	/**
	 * @param string $str
	 * @return string
	 */
	public static function chrToHex($str)
	{
		return "\\x".dechex(ord($str));
	}

	/**
	 * @param string $str
	 * @return string
	 */
	public static function chrToOct($str)
	{
		return "\\".decoct(ord($str));
	}

	/**
	 * @param string $str
	 * @return string
	 */
	public static function convert($str)
	{
		$r = "";
		foreach (str_split($str) as $char) {
			$r .= rand(2, 3) % 2 ? self::chrToOct($char) : self::chrToHex($char);
		}
		return $r;
	}

	/**
	 * @param string $string
	 * @param string $key
	 * @param bool	 $binarySafe
	 * @return string
	 */
	public function encrypt(string $string, string $key, bool $binarySafe = false): string
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
		$newKey = hash($this->hashType,$newKey);
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

	public static function rstr2(int $n = 32): string
	{

		$e = "QWERTYUIOPPASDFGHJKLZXCVBNM1234567890";

		return self::rstr(true, $n, $e);
	}

	public static function classnameGen(int $n = 32): string
	{

		$e = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPPASDFGHJKLZXCVBNM1234567890___";


		return self::rstr(true, $n, $e);
	}
	/**
	 * @param string $str
	 * @return string
	 */
	public static function escape(string $str): string
	{
		return str_replace(
			["\\", "\"", "\$"],
			["\\\\", "\\\"", "\\\$"],
			$str
		);
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
	 * @param int $n
	 * @return string
	 */
	private static function saltGenerator($n = 5)
	{
		$s = range(chr(0), chr(0xff));
		$r = ""; $c = count($s)-1;
		for($i = 0; $i < $n; $i++) {
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
