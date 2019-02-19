<?php
/**
 * User: Martin Neundorfer
 * Date: 20.01.2019
 * Time: 23:12
 * Vendor: LABOR.digital
 */

namespace Labor\Helferlein\Php\Inflector;


use Labor\Helferlein\Php\PathsAndLinks\PathsAndLinks;

class Inflector {
	/**
	 * Default map of accented and special characters to ASCII characters
	 *
	 * @var array
	 */
	const TRANSLITERATIONS = array(
		"/À|Á|Â|Ã|Å|Ǻ|Ā|Ă|Ą|Ǎ/"           => "A",
		"/Æ|Ǽ/"                           => "AE",
		"/Ä/"                             => "Ae",
		"/Ç|Ć|Ĉ|Ċ|Č/"                     => "C",
		"/Ð|Ď|Đ/"                         => "D",
		"/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě/"             => "E",
		"/Ĝ|Ğ|Ġ|Ģ|Ґ/"                     => "G",
		"/Ĥ|Ħ/"                           => "H",
		"/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|І/"         => "I",
		"/Ĳ/"                             => "IJ",
		"/Ĵ/"                             => "J",
		"/Ķ/"                             => "K",
		"/Ĺ|Ļ|Ľ|Ŀ|Ł/"                     => "L",
		"/Ñ|Ń|Ņ|Ň/"                       => "N",
		"/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ/"         => "O",
		"/Œ/"                             => "OE",
		"/Ö/"                             => "Oe",
		"/Ŕ|Ŗ|Ř/"                         => "R",
		"/Ś|Ŝ|Ş|Ș|Š/"                     => "S",
		"/ẞ/"                             => "SS",
		"/Ţ|Ț|Ť|Ŧ/"                       => "T",
		"/Þ/"                             => "TH",
		"/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ/" => "U",
		"/Ü/"                             => "Ue",
		"/Ŵ/"                             => "W",
		"/Ý|Ÿ|Ŷ/"                         => "Y",
		"/Є/"                             => "Ye",
		"/Ї/"                             => "Yi",
		"/Ź|Ż|Ž/"                         => "Z",
		"/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª/"         => "a",
		"/ä|æ|ǽ/"                         => "ae",
		"/ç|ć|ĉ|ċ|č/"                     => "c",
		"/ð|ď|đ/"                         => "d",
		"/è|é|ê|ë|ē|ĕ|ė|ę|ě/"             => "e",
		"/ƒ/"                             => "f",
		"/ĝ|ğ|ġ|ģ|ґ/"                     => "g",
		"/ĥ|ħ/"                           => "h",
		"/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|і/"         => "i",
		"/ĳ/"                             => "ij",
		"/ĵ/"                             => "j",
		"/ķ/"                             => "k",
		"/ĺ|ļ|ľ|ŀ|ł/"                     => "l",
		"/ñ|ń|ņ|ň|ŉ/"                     => "n",
		"/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º/"       => "o",
		"/ö|œ/"                           => "oe",
		"/ŕ|ŗ|ř/"                         => "r",
		"/ś|ŝ|ş|ș|š|ſ/"                   => "s",
		"/ß/"                             => "ss",
		"/ţ|ț|ť|ŧ/"                       => "t",
		"/þ/"                             => "th",
		"/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ/" => "u",
		"/ü/"                             => "ue",
		"/ŵ/"                             => "w",
		"/ý|ÿ|ŷ/"                         => "y",
		"/є/"                             => "ye",
		"/ї/"                             => "yi",
		"/ź|ż|ž/"                         => "z",
	);
	
	/**
	 * A list of filenames we know and recognize as such
	 */
	const FILE_EXTENSIONS = "|3dm|3ds|3g2|3gp|7z|accdb|ai|aif|apk|app|asf|asp|aspx|avi|bak|bat|bin|bmp|c|cab|cbr|cer|cfg|cfm|cgi|class|com|cpl|cpp|crdownload|crx|cs|csr|css|csv|cue|cur|dat|db|dbf|dds|deb|dem|deskthemepack|dll|dmg|dmp|doc|docx|drv|dtd|dwg|dxf|eps|exe|fla|flv|fnt|fon|gadget|gam|ged|gif|gpx|gz|h|hqx|htm|html|icns|ico|ics|iff|indd|ini|iso|jar|java|jpg|jpeg|js|jsp|key|keychain|kml|kmz|lnk|log|lua|m|m3u|m4a|m4v|max|mdb|mdf|mid|mim|mov|mp3|mp4|mpa|mpg|msg|msi|nes|obj|odt|otf|pages|part|pct|pdb|pdf|php|pkg|pl|plugin|png|pps|ppt|pptx|prf|ps|psd|pspimage|py|rar|rm|rom|rpm|rss|rtf|sav|sdf|sh|sitx|sln|sql|srt|svg|swf|swift|sys|tar|tar.gz|tax2016|tex|tga|thm|tif|tiff|tmp|toast|torrent|ttf|txt|uue|vb|vcd|vcf|vcxproj|vob|wav|wma|wmv|wpd|wps|wsf|xcodeproj|xhtml|xlr|xls|xlsx|xml|yuv|zip|zipx|";
	
	/**
	 * Converts a "Given string" to "Given-string" or
	 * "another.String-you wouldWant" to "another-string-you-would-want".
	 * But in addition to that, it will convert "Annahäuser_Römertopf.jpg" into "annahaeuser-roemertopf-jpg"
	 *
	 * NOTE: Yes, this is a shameless copy of
	 * http://book.cakephp.org/2.0/en/core-utility-libraries/inflector.html#Inflector::slug
	 * But it works remarkably well!
	 *
	 * @param string $string The string to inflect
	 *
	 * @return string
	 */
	public static function toSlug(string $string): string {
		$map = self::TRANSLITERATIONS + array(
				"/[^\s\p{Zs}\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]/mu" => " ",
				"/[\s\p{Zs}]+/mu"                                     => "-",
				sprintf("/^[%s]+|[%s]+$/", "\\-", "\\-")              => "",
			);
		return strtolower((string)preg_replace(array_keys($map), array_values($map), $string));
	}
	
	/**
	 * Similar to toSlug() but is able to detect file extensions, and if required a path
	 * sement which will be ignored while converting the file into a sluggified version.
	 *
	 * @param string $string     The string to inflect
	 * @param bool   $expectPath True to expect an filepath with only the basename to inflect.
	 *
	 * @return string
	 */
	public static function toFile(string $string, bool $expectPath = false): string {
		// Handle file extension
		$ext = pathinfo($string, PATHINFO_EXTENSION);
		if (!empty($ext) && stripos(self::FILE_EXTENSIONS, $ext) !== false) {
			$ext = '.' . $ext;
			$string = substr($string, 0, -strlen($ext));
		} else $ext = '';
		
		// Handle filepath if required
		$path = '';
		if ($expectPath) {
			$string = basename($string);
			$path = PathsAndLinks::unifyPath(dirname($string));
		}
		return $path . strtolower(static::toSlug($string)) . $ext;
	}
	
	/**
	 * Converts a "Given string" to ["given", "string"] or
	 * "another.String-you wouldWant" to ["another", "string", "you", "would", "want"].
	 *
	 * @param string $string    The string to inflect
	 * @param array  $options   A configuration array to deactivate specific split settings.
	 *                          All split settings are active by default. To disable a splitter
	 *                          set its key to FALSE. Split settings are:
	 *                          - splitAtUpperCase: Splits "myKeyValue" into ["my", "key", "value"]
	 *                          - splitAtDash: Splits "my-key-value" into ["my", "key", "value"]
	 *                          - splitAtUnderscore: Splits "my_key_value" into ["my", "key", "value"]
	 *                          - splitAtPeriod: Splits "my.key.value" into ["my", "key", "value"]
	 *                          - splitAtSpace: Splits "my key value" into ["my", "key", "value"]
	 *
	 *                          - splitAtUpperCaseSensible: The splitter "splitAtUpperCase" is rather dumb
	 *                          if you give it words like IP, URL, and so on, because it will split them
	 *                          like I, P and U, R, L (because of their upper chars) but stuff like HandMeAMango
	 *                          on the other hand will be correctly splitted like: hand, me, a, mango.
	 *                          If you"d like a bit more "sensible" splitting set this to true,
	 *                          so upper case characters following each other will not be splitted
	 *
	 * @return array
	 */
	public static function toArray(string $string, array $options = []): array {
		// Build pattern
		$pattern = "/(?=[A-Z])|[\-]+|[_]+|[\.]+|[\s]+/";
		if (!empty($options)) {
			$pattern = "/";
			if ($options["splitAtUpperCase"] !== false) {
				// Don"t split if the whole string is in upper case
				if ($string === strtoupper($string)) ;
				else if ($options["splitAtUpperCaseSensible"] === true) {
					// Precompile
					// This replaces everything thats in upper case with itself but with a space in front.
					// If there is more than a single char, like in ThisIsAGreatWord it will strip the
					// G (in Great) off and push it into the next word. The result will be: this is a great word.
					// Words like FAQ will be kept together if given alone.
					// Problems might occure when stuff like "ThisIsFAQandMore" is given,
					// because the camelCase is broken the result will be: this is fa qand more.
					$string = preg_replace_callback("/[A-Z]+/s", function ($v) {
						$nextWord = "";
						if (strlen($v = (string)reset($v)) > 1) {
							$nextWord = " " . substr($v, -1);
							$v = substr($v, 0, -1);
						}
						return strtolower(" " . $v . $nextWord);
					}, $string);
				} else {
					// Rather dumb splitting
					$pattern .= "(?=[A-Z])|";
				}
			}
			if ($options["splitAtDash"] !== false) $pattern .= "[\-]+|";
			if ($options["splitAtUnderscore"] !== false) $pattern .= "[_]+|";
			if ($options["splitAtPeriod"] !== false) $pattern .= "[.]+|";
			if ($options["splitAtSpace"] !== false) $pattern .= "[\s]+|";
			$pattern = rtrim($pattern, "|") . "/";
		}
		
		// Do the split
		$parts = preg_split($pattern, $string, -1, PREG_SPLIT_NO_EMPTY);
		$parts = array_map("strtolower", $parts);
		return $parts;
	}
	
	/**
	 * Converts a "Given string" to "Given String" or
	 * "another.String-you wouldWant" to "Another String You Would Want".
	 *
	 * @param string $string  The string to inflect
	 * @param array  $options A configuration array to deactivate specific split settings. @see toArray() for details.
	 *
	 * @return string
	 */
	public static function toSpacedUpper(string $string, array $options = []): string {
		return implode(" ", array_map("ucfirst", static::toArray($string, $options)));
	}
	
	/**
	 * Alias of toSpacedUpper
	 *
	 * @param string $string  The string to inflect
	 * @param array  $options A configuration array to deactivate specific split settings. @see toArray() for details.
	 *
	 * @return string
	 */
	public static function toHuman(string $string, array $options = []): string {
		return static::toSpacedUpper($string, $options);
	}
	
	/**
	 * Converts a "Given string" to "GivenString" or
	 * "another.String-you wouldWant" to "AnotherStringYouWouldWant".
	 *
	 * @param string $string  The string to inflect
	 * @param array  $options A configuration array to deactivate specific split settings. @see toArray() for details.
	 *
	 * @return string
	 */
	public static function toCamelCase(string $string, array $options = []): string {
		return implode(array_map('ucfirst', static::toArray($string, $options)));
	}
	
	/**
	 * Converts a "Given string" to "givenString" or
	 * "another.String-you wouldWant" to "anotherStringYouWouldWant".
	 *
	 * @param string $string  The string to inflect
	 * @param array  $options A configuration array to deactivate specific split settings. @see toArray() for details.
	 *
	 * @return string
	 */
	public static function toCamelBack(string $string, array $options = []): string {
		return lcfirst(static::toCamelCase($string, $options));
	}
	
	/**
	 * Converts a "Given string" to "given-string" or
	 * "another.String-you wouldWant" to "another-string-you-would-want".
	 *
	 * @param string $string  The string to inflect
	 * @param array  $options A configuration array to deactivate specific split settings. @see toArray() for details.
	 *
	 * @return string
	 */
	public static function toDashed(string $string, array $options = []): string {
		return implode('-', static::toArray($string, $options));
	}
	
	/**
	 * Converts a "Given string" to "given_string" or
	 * "another.String-you wouldWant" to "another_string_you_would_want".
	 *
	 * @param string $string  The string to inflect
	 * @param array  $options A configuration array to deactivate specific split settings. @see toArray() for details.
	 *
	 * @return string
	 */
	public static function toUnderscore(string $string, array $options = []): string {
		return implode('_', static::toArray($string, $options));
	}
	
	/**
	 * Alias of toUnderscore();
	 *
	 * @param string $string  The string to inflect
	 * @param array  $options A configuration array to deactivate specific split settings. @see toArray() for details.
	 *
	 * @return string
	 */
	public static function toDatabase(string $string, array $options = []): string {
		return static::toUnderscore($string, $options);
	}
	
	/**
	 * Converts a "Given string" to "getGivenString" or
	 * "another.String-you wouldWant" to "getAnotherStringYouWouldWant".
	 *
	 * @param string $string  The string to inflect
	 * @param string $prefix  By default "get", could be "is" or "has" if it is required
	 * @param array  $options A configuration array to deactivate specific split settings. @see toArray() for details.
	 *                        NOTE: In addition to that $options can contain the following:
	 *                        - sanitize: If false the property sanitazion will be disabled
	 *
	 * @return string
	 */
	public static function toGetter(string $string, $prefix = 'get', array $options = []): string {
		// Only apply if the variable does not contain the prefix already
		$cc = static::toCamelCase(static::sanitizeGetterAndSetterPrefix($string, $options), $options);
		return stripos($cc, $prefix) !== false ? $cc : $prefix . $cc;
	}
	
	/**
	 * Converts a "Given string" to "setGivenString" or
	 * "another.String-you wouldWant" to "setAnotherStringYouWouldWant".
	 *
	 * @param string $string  The string to inflect
	 * @param array  $options A configuration array to deactivate specific split settings. @see toArray() for details.
	 *                        NOTE: In addition to that $options can contain the following:
	 *                        - sanitize: If false the property sanitazion will be disabled
	 *
	 * @return string
	 */
	public static function toSetter(string $string, array $options = []): string {
		return 'set' . static::toCamelCase(static::sanitizeGetterAndSetterPrefix($string, $options), $options);
	}
	
	/**
	 * This is in general an alias of toCamelBack(); But in addition to that it will also strip away has/get/is..
	 * prefixes from the given value
	 *
	 * @param string $string  The string to inflect
	 * @param array  $options A configuration array to deactivate specific split settings. @see toArray() for details.
	 *                        NOTE: In addition to that $options can contain the following:
	 *                        - sanitize: If false the property sanitazion will be disabled
	 *
	 * @return string
	 */
	public static function toProperty(string $string, array $options = []): string {
		return static::toCamelBack(static::sanitizeGetterAndSetterPrefix($string, $options), $options);
	}
	
	/**
	 * This method will convert the given string by unifying it. Unify means, it makes it compareable with other
	 * strings, by removing all special characters, convering everything to lowercase, counting all words and the
	 * number of their occurence (optional) and sorting them alphabetically. This also means, that the text will no
	 * longer make sense for humans, but is easy to use for search and comparatison actions.
	 *
	 * @param string $string                  The string to unify
	 * @param bool   $appendNumberOfOccureces By default words have the number of their occurence added to the result.
	 *                                        For example "the white fox and the hen" => "and1 fox1 hen1 the2 white1"
	 *                                        If you don't want that quanitification you can disable it using this
	 *                                        option. The result would then be: "and fox hen the white"
	 *
	 *
	 * @return string
	 */
	public static function toComparable(string $string, bool $appendNumberOfOccureces = true): string {
		$parts = static::toArray(static::toFile($string));
		$parts = array_count_values($parts);
		if ($appendNumberOfOccureces) {
			array_walk($parts, function (&$v, $k) use ($appendNumberOfOccureces) {
				$v = $k . $v;
			});
		} else $parts = array_keys($parts);
		sort($parts);
		$string = implode(" ", $parts);
		return $string;
	}
	
	/**
	 * Converts any given string into a UUID like: 123e4567-e89b-12d3-a456-426655440000.
	 * Note that this is NOT A REAL UUID(!), but a representation that will unify
	 * all strings like " ASDF ASDF" and "asdf_ASDF" or " ASDF ASDF " into the
	 * same, unified id. This is useful if you want to create, unique id's but
	 * want to merge multiple datasets with different word order.
	 *
	 * Note that "ASDF QWER" will result in the same ID as "QWER ASDF", because
	 * the values will be sorted alphabetically before the id is created.
	 * This makes sorting via firstname and lastname a lot easier.
	 *
	 * @param string $string The string to convert to uuid
	 *
	 * @return string
	 */
	public function toUuid(string $string): string {
		$string = md5(static::toComparable($string));
		return substr($string, 0, 8) . '-' .
			substr($string, 8, 4) . '-' .
			substr($string, 12, 4) . '-' .
			substr($string, 16, 4) . '-' .
			substr($string, 20);
	}
	
	/**
	 * Helper to remove words like set / get / is or has from the beginning of the string
	 * to sanitize optional "setters", "getters" and "properties"
	 *
	 * @param string $string  The string to inflect
	 * @param array  $options A configuration array to deactivate specific split settings. @see toArray() for details.
	 *                        NOTE: In addition to that $options can contain the following:
	 *                        - sanitize: If false the property sanitazion will be disabled
	 *
	 * @return null|string|string[]
	 */
	protected static function sanitizeGetterAndSetterPrefix(string $string, array $options) {
		// Check if we should sanitize the input or not
		if ($options['sanitize'] !== false)
			$string = preg_replace('/^(set|get|is|has)/', '', $string);
		unset($options['sanitize']);
		return $string;
	}
}