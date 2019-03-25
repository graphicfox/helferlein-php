<?php
/**
 * User: Martin Neundorfer
 * Date: 27.01.2019
 * Time: 19:15
 * Vendor: LABOR.digital
 */

namespace Labor\Helferlein\Php\Arrays;


class ArrayGenerator {
	/**
	 * Receives a xml-input and converts it into a multidimensional array
	 *
	 * @param string|array|null|\DOMNode|\SimpleXMLElement $input
	 *
	 * @return array
	 * @throws ArrayGeneratorException
	 */
	public static function _fromXml($input): array {
		if (is_array($input)) return $input;
		if (empty($input)) return [];
		// Convert xml string to an object
		if (is_string($input) && stripos(trim($input), "<?xml") === 0)
			$input = simplexml_load_string($input);
		// Convert xml objects into arrays
		if ($input instanceof \DOMNode) $input = simplexml_import_dom($input);
		if ($input instanceof \SimpleXMLElement) return static::xmlObjectToArray($input);
		throw new ArrayGeneratorException("The given input is not supported as XML array source!");
	}
	
	/**
	 * The method receives an object of sorts and converts it into a multidimensional array
	 *
	 * @param $input
	 *
	 * @return array
	 * @throws ArrayGeneratorException
	 */
	public static function _fromObject($input): array {
		if (is_array($input)) return $input;
		if (empty($input)) return [];
		if ($input instanceof \DOMNode || $input instanceof \SimpleXMLElement) return static::_fromXml($input);
		// Convert iterator and standard class
		if ($input instanceof \Iterator || $input instanceof \stdClass) {
			$out = array();
			foreach ($input as $k => $v) $out[$k] = $v;
			return $out;
		}
		if (is_object($input)) return get_object_vars($input);
		throw new ArrayGeneratorException("The given input is not supported as OBJECT array source!");
	}
	
	/**
	 * Receives a string list like: "1,asdf,foo, bar" which will be converted into [1, "asdf", "foo", "bar"]
	 * Note the automatic trimming and value conversion of numbers, TRUE, FALSE an null.
	 * By default the separator is ","
	 *
	 * @param string $input     The value to convert into an array
	 * @param string $separator The separator to split the string at
	 *
	 * @return array
	 * @throws ArrayGeneratorException
	 */
	public static function _fromStringList($input, string $separator = ","): array {
		if (is_array($input)) return $input;
		if (empty($input)) return [];
		if (!is_string($input) && !is_numeric($input) && !(is_object($input) && method_exists($input, "__toString")))
			throw new ArrayGeneratorException("The given input is not supported as STRING array source!");
		$parts = preg_split("~(?<!\\\)" . preg_quote($separator, "~") . "~", trim($input), -1, PREG_SPLIT_NO_EMPTY);
		foreach ($parts as $k => $v) {
			$v = trim($v);
			$vLower = strtolower($v);
			if ($vLower === "null") $parts[$k] = null;
			else if ($vLower === "false") $parts[$k] = false;
			else if ($vLower === "true") $parts[$k] = true;
			else if (is_numeric($vLower)) {
				if (strpos($vLower, ".") !== false) $parts[$k] = (float)$v;
				else $parts[$k] = (int)$v;
			}
		}
		return $parts;
	}
	
	/**
	 * Receives a string value and parses it as a csv into an array
	 *
	 * @param string $input         The csv string to parse
	 * @param bool   $firstLineKeys Set to true if the first line of the csv are keys for all other rows
	 * @param string $delimiter     The delimiter between multiple fields
	 * @param string $quote         The enclosure or quoting tag
	 *
	 * @return array[]
	 * @throws ArrayGeneratorException
	 */
	public static function _fromCsv($input, bool $firstLineKeys = false,
									string $delimiter = ",", string $quote = "\""): array {
		if (is_array($input)) return $input;
		if (empty($input)) return [];
		if (!is_string($input))
			throw new ArrayGeneratorException("The given input is not supported as CSV array source!");
		$lines = preg_split("/$\R?^/m", trim($input));
		if (!is_array($lines))
			throw new ArrayGeneratorException("Error while parsing CSV array source!");
		$keyLength = 0;
		if ($firstLineKeys) {
			$keys = array_shift($lines);
			$keys = str_getcsv($keys, $delimiter, $quote);
			$keys = array_map("trim", $keys);
			$keyLength = count($keys);
		}
		foreach ($lines as $ln => $line) {
			$line = str_getcsv($line, $delimiter, $quote);
			$line = array_map("trim", $line);
			// No keys
			if (!isset($keys)) {
				$lines[$ln] = $line;
				continue;
			}
			// Keys match
			if (count($line) === $keyLength) {
				$lines[$ln] = array_combine($keys, $line);
				continue;
			}
			// Apply key length to line
			$lines[$ln] = array_pad(array_slice($line, 0, $keyLength), $keyLength, null);
		}
		return $lines;
	}
	
	/**
	 * Creates an array out of a json data string.
	 * Only works with json objects or arrays. Other values will throw an exception
	 *
	 * @param $input
	 *
	 * @return array
	 * @throws ArrayGeneratorException
	 */
	public static function _fromJson($input): array{
		if (is_array($input)) return $input;
		if (empty($input)) return [];
		if (!is_string($input))
			throw new ArrayGeneratorException("The given input is not supported as JSON array source!");
		$input = trim($input);
		if($input[0] !== "{" && $input[0] !== "[")
			throw new ArrayGeneratorException("The given input is a string, but has no array as JSON data, so its no supported array source!");
		return @json_decode($input, true);
	}
	
	/**
	 * This method is basically a carbon copy of cakephp"s xml::_toArray method
	 * It recursively converts a given xml tree into an associative php array
	 *
	 * @see https://github.com/cakephp/utility/blob/master/Xml.php
	 *
	 * @param \SimpleXMLElement $xml The xml element to traverse
	 * @param array             $parentData
	 * @param string|NULL       $ns
	 * @param array|NULL        $namespaces
	 *
	 * @return array
	 */
	protected static function xmlObjectToArray(\SimpleXMLElement $xml, array &$parentData = [], string $ns = null, array $namespaces = null) {
		if ($ns === null) $ns = "";
		if ($namespaces === null) $namespaces = array_keys(array_merge(["" => ""], $xml->getNamespaces(true)));
		$data = [];
		
		foreach ($namespaces as $namespace) {
			foreach ($xml->attributes($namespace, true) as $key => $value) {
				if (!empty($namespace)) $key = $namespace . ":" . $key;
				$data["@" . $key] = (string)$value;
			}
			foreach ($xml->children($namespace, true) as $child)
				static::xmlObjectToArray($child, $data, $namespace, $namespaces);
		}
		$asString = trim((string)$xml);
		if (empty($data)) $data = $asString;
		elseif (strlen($asString) > 0) $data["@"] = $asString;
		if (!empty($ns)) $ns .= ":";
		$name = $ns . $xml->getName();
		if (isset($parentData[$name])) {
			if (!is_array($parentData[$name]) || !isset($parentData[$name][0]))
				$parentData[$name] = [$parentData[$name]];
			$parentData[$name][] = $data;
		} else $parentData[$name] = $data;
		return $parentData;
	}
}