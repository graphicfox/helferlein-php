<?php
/**
 * User: Martin Neundorfer
 * Date: 20.01.2019
 * Time: 22:50
 * Vendor: LABOR.digital
 */

namespace Labor\Helferlein\Php\Arrays;


use Labor\Helferlein\Php\Exceptions\HelferleinException;
use Labor\Helferlein\Php\Exceptions\HelferleinInvalidArgumentException;
use Labor\Helferlein\Php\Inflector\Inflector;
use Labor\Helferlein\Php\Options\Options;

class Arrays {
	
	/**
	 * Returns true if the given array is an associative array
	 * Associative arrays have string keys instead of numbers!
	 *
	 * @param array $list The array to check for
	 *
	 * @return bool
	 */
	public static function isAssociative(array $list) {
		return count(array_filter(array_keys($list), "is_string")) > 0;
	}
	
	/**
	 * Returns true if the given array is sequential.
	 * Sequential arrays are numeric and in order like 0 => 1, 1 => 2, 2 => 3.
	 *
	 * @param array $list The array to check for
	 *
	 * @return bool
	 */
	public static function isSequential(array $list) {
		return array_keys($list) === range(0, count($list) - 1);
	}
	
	/**
	 * Returns true if the given array is a numeric list of arrays.
	 * Meaning:
	 *    $list = ["asdf" => 1] => FALSE
	 *    $list = ["asdf" => ["asdf"]] => FALSE
	 *    $list = [["asdf"], [123]] => TRUE
	 *
	 * @param array $list The list to check
	 *
	 * @return bool
	 */
	public static function isArrayList(array $list): bool {
		return count(array_filter($list, function ($v) { return is_array($v); })) === count($list);
	}
	
	/**
	 * This method merges multiple arrays into eachother. It will traverse elements recursively. While
	 * traversing b all its values will be merged into a overruling the value in a. If both values
	 * are arrays the merge will go deeper and merge the child arrays into eachother.
	 *
	 * NOTE: By default numeric keys will be merged into eachother so: [["foo"]] + [["bar"]] becomes [["bar"]]
	 * You can disable this behaviour by setting FALSE as last argument of this method. The result would then be
	 * [["foo"], ["bar"]]
	 *
	 * @param array[] ...$args
	 * param bool $mergeNumeric Optional last flag to switch numeric key merge behaviour
	 *
	 * @return array
	 * @throws HelferleinInvalidArgumentException
	 */
	public static function merge(...$args): array {
		// Handle last option -> boolean for "mergeNumerics"
		$lastArg = end($args);
		$mergeNumeric = TRUE;
		if (is_bool($lastArg)) {
			$mergeNumeric = $lastArg;
			array_pop($args);
		}
		
		// Validate input
		if (count($args) < 2)
			throw new HelferleinInvalidArgumentException("At least 2 elements are required to be merged into eachother!");
		if (in_array(false, array_map("is_array", $args)))
			throw new HelferleinInvalidArgumentException("All elements have to be arrays!");
		
		// Recursion walker
		$walker = function ($a, $b, $walker) use ($mergeNumeric) {
			if (empty($a)) return $b;
			foreach ($b as $k => $v) {
				if (!$mergeNumeric && is_numeric($k)) {
					$a[] = $v;
					continue;
				}
				if (isset($a[$k]) && is_array($a[$k]) && is_array($v))
					$v = $walker($a[$k], $v, $walker);
				$a[$k] = $v;
			}
			return $a;
		};
		
		// Loop over all given arguments
		$a = array_shift($args);
		while (count($args) > 0)
			$a = $walker($a, array_shift($args), $walker);
		return $a;
	}
	
	/**
	 * This helper can be used to attach one array to the end of another.
	 * This is basically [...] + [...] but without overriding numeric keys
	 *
	 * @param array $args
	 *
	 * @return array
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinInvalidArgumentException
	 */
	public static function attach(...$args): array {
		$_args = $args;
		if (count($args) < 2)
			throw new HelferleinInvalidArgumentException("At least 2 elements are required to be attached to eachother!");
		if (in_array(false, array_map("is_array", $_args)))
			throw new HelferleinInvalidArgumentException("All elements have to be arrays!");
		
		$a = array_shift($args);
		while (count($args) > 0) {
			foreach (array_shift($args) as $k => $v) {
				if (!is_numeric($k)) $a[$k] = $v;
				else $a[] = $v;
			}
		}
		return $a;
	}
	
	/**
	 * This method can rename keys of a given array according to a given map
	 * of ["keyToRename" => "RenamedKey"] as second parameter. Keys not present in $list will be ignored
	 *
	 * @param array $list         The list to rename the keys in
	 * @param array $keysToRename The map to define which keys should be renamed with another key
	 *
	 * @return array The renamed array
	 */
	public static function renameKeys(array $list, array $keysToRename) {
		$result = [];
		foreach ($list as $k => $v)
			$result[isset($keysToRename[$k]) ? $keysToRename[$k] : $k] = $v;
		return $result;
	}
	
	/**
	 * Tiny helper which will shorten a multidimensional array until it"s smallest element.
	 * This is especially useful for database results.
	 *
	 * Example:
	 * $a = array(
	 *        "b" => array(
	 *            "test" => 123
	 *        )
	 * )
	 *
	 * Result: 123
	 *
	 * @param array $array
	 *
	 * @return array|mixed
	 */
	public static function shorten(array $array) {
		while (is_array($array) && count($array) === 1)
			$array = reset($array);
		return $array;
	}
	
	/**
	 * Searches the most similar key to the given needle from the haystack
	 *
	 * @param array  $haystack The array to search similar keys in
	 * @param string $needle   The needle to search similar keys for
	 *
	 * @return string|null The best matching key or null if the given haystack was empty
	 */
	public static function getSimilarKey(array $haystack, $needle) {
		// Check if the needle exists
		if (isset($haystack[$needle])) return $needle;
		
		// Generate alternative keys
		$alternativeKeys = array_keys($haystack);
		$alternativeKeys = array_map(function ($v) {
			return Inflector::toComparable((string)$v);
		}, array_combine($alternativeKeys, $alternativeKeys));
		
		// Search for a similar key
		$needlePrepared = Inflector::toComparable((string)$needle);
		$similarKeys = [];
		foreach ($alternativeKeys as $alternativeKey => $alternativeKeyPrepared) {
			similar_text($needlePrepared, $alternativeKeyPrepared, $percent);
			$similarKeys[(int)ceil($percent)] = $alternativeKey;
		}
		ksort($similarKeys);
		
		// Check for empty keys
		if (empty($similarKeys)) return NULL;
		return array_pop($similarKeys);
	}
	
	/**
	 * Sorts a given multidimensional array by either a key or a path to a key, by keeping
	 * the associative relations like asort would
	 *
	 * Example:
	 * $a = array(
	 *        'asdf' => array(
	 *            'key' => 2,
	 *            'sub' => array(
	 *                'key' => 2
	 *            )
	 *        ),
	 *        'cde' => array(
	 *            'key' => 1,
	 *            'sub' => array(
	 *                'key' => 3
	 *            )
	 *        )
	 * )
	 *
	 * // Keys in order
	 * Arrays::sortBy($a, 'key') => cde, asdf
	 * Arrays::sortBy($a, 'sub.key') => asdf, cde
	 *
	 * @param array  $array   The array to sort
	 * @param string $key     Either the key or the path to sort by
	 * @param array  $options Additional config options:
	 *                        - separator: (Default ".") The separator between the parts if path's are used in $key
	 *                        - desc: (Default FALSE) By default the method sorts ascending. To change to descending,
	 *                        set this to true
	 *
	 * @return array
	 * @throws HelferleinInvalidArgumentException
	 * @throws \Labor\Helferlein\Php\Options\InvalidOptionException
	 */
	public static function sortBy(array $array, string $key, array $options = []): array {
		$options = Options::make($options, [
			"separator" => [
				"type"    => "string",
				"default" => ".",
			],
			"desc"      => [
				"type"    => "bool",
				"default" => FALSE,
			],
		]);
		
		// Check if it is a simple sort => Use fastlane
		if (stripos($key, $options["separator"]) === FALSE) {
			uasort($array, function ($a, $b) use ($key) {
				// Validate input
				if (!isset($a[$key]) || !isset($b[$key])) {
					throw new HelferleinInvalidArgumentException('The sort array is maleformed!');
				}
				return $a[$key] <=> $b[$key];
			});
			return $options["desc"] ? array_reverse($array) : $array;
		}
		
		// Use the workaround for paths as key
		// This is exorbitantly faster than using arrayGetPath in the approach above.
		// So this will combine the best of two worlds together
		$sorter = [];
		foreach ($array as $k => $v)
			$sorter[$k] = Arrays::getPath($array, $key, $options["separator"]);
		asort($sorter);
		
		// Sort output
		$output = [];
		foreach ($sorter as $k => $foo)
			$output[$k] = $array[$k];
		unset($sorter);
		
		// Done
		return $options["desc"] ? array_reverse($output) : $output;
	}
	
	/**
	 * This method is used to convert a string into a path array.
	 * It will also validate already existing path arrays.
	 *
	 * By default a dot (.) is used to separate path parts like: "my.array.path" => ["my","array","path"].
	 * If you require another separator you can set another one by using the $separator parameter.
	 * In most circumstances it will make more sense just to escape a separator, tho. Do that by using a backslash like:
	 * "my\.array.path" => ["my.array", "path"].
	 *
	 * If an array is given it will be validated for invalid parts before returning it again.
	 *
	 * @param array|string $path      The path to parse as described above.
	 * @param string       $separator Default: "." Can be set to any string you want to use as separator of path parts.
	 *
	 * @return array
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinInvalidArgumentException
	 */
	public static function parsePath($path, string $separator = "."): array {
		return ArrayPaths::_parsePath($path, $separator);
	}
	
	/**
	 * This method can be used to merge two path"s together.
	 * This becomes useful if you want to work with a dynamic part in form of an array
	 * and a static string part. The result will always be a path array.
	 * You can specify a separator type for each part of the given path if you merge
	 * differently formatted paths.
	 *
	 * It merges stuff like:
	 *        - "a.path.to." and ["parts","inTheTree"] => ["a", "path", "to", "parts", "inTheTree"]
	 *        - "a.b.*" and "c.d.[asdf|id]" => ["a", "b", "*", "c", "d", "[asdf|id]"
	 *        - "a.b" and "c,d" => ["a","b","c","d"] (If $separatorB is set to ",")
	 * and so on...
	 *
	 * @param array|string $pathA      The path to add $pathB to
	 * @param array|string $pathB      The path to be added to $pathA
	 * @param string       $separatorA The separator for string paths in $pathA
	 * @param string       $separatorB The separator for string paths in $pathB
	 *
	 * @return array
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinInvalidArgumentException
	 */
	public static function mergePaths($pathA, $pathB, $separatorA = ".", $separatorB = "."): array {
		return ArrayPaths::_mergePaths($pathA, $pathB, $separatorA, $separatorB);
	}
	
	/**
	 * This method checks if a given path exists in a given $input array
	 *
	 * @param array|mixed  $input     The array to check
	 * @param array|string $path      The path to check for in $input
	 * @param string       $separator Default: "." Can be set to any string you want to use as separator of path parts.
	 *
	 * @return bool
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinInvalidArgumentException
	 */
	public static function hasPath($input, $path, string $separator = "."): bool {
		return ArrayPaths::_has($input, $path, $separator);
	}
	
	/**
	 * This method reads a single value or multiple values (depending on the given $path) from
	 * the given $input array.
	 *
	 * All results will be returned as references to the original input, so you can
	 * use them as pointers to edit the values in a huge array tree.
	 *
	 * @param array|mixed  $input     The array to read the path"s values from
	 * @param array|string $path      The path to read in the $input array
	 * @param null|mixed   $default   The value which will be returned if the $path did not match anything.
	 * @param string       $separator Default: "." Can be set to any string you want to use as separator of path parts.
	 *
	 * @return array|mixed|null
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinInvalidArgumentException
	 */
	public static function &getPath(&$input, $path, $default = NULL, string $separator = ".") {
		return ArrayPaths::_get($input, $path, $default, $separator);
	}
	
	/**
	 * This method lets you set a given value at a path of your array.
	 * You can also set multiple keys to the same value at once if you use wildcards.
	 *
	 * @param array        $input     The array to set the values in
	 * @param array|string $path      The path to set $value at
	 * @param mixed        $value     The value to set at $path in $input
	 * @param string       $separator Default: "." Can be set to any string you want to use as separator of path parts.
	 *
	 * @return void
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinInvalidArgumentException
	 */
	public static function setPath(array &$input, $path, $value, string $separator = ".") {
		ArrayPaths::_set($input, $path, $value, $separator);
	}
	
	/**
	 * Removes the values at the given $path"s from the $input array.
	 * It can also remove multiple values at once if you use wildcards.
	 *
	 * NOTE: The method tries to remove empty remains recursively when the last
	 * child was removed from the branch. If you don"t want to use this behaviour
	 * set $removeEmptyRemains to false.
	 *
	 * @param array        $input              The array to remove the values from
	 * @param array|string $path               The path which defines which values have to be removed
	 * @param array        $options            Additional config options
	 *                                         - separator (Default: ".") Can be set to any string
	 *                                         you want to use as separator of path parts.
	 *                                         - removeEmpty (Default: TRUE) Set this to false to disable
	 *                                         the automatic cleanup of empty remains when the lowest
	 *                                         child was removed from a tree.
	 *
	 * @return void
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinInvalidArgumentException
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinException
	 */
	public static function removePath(array &$input, $path, array $options = []) {
		$options = Options::make($options, [
			"separator"   => ".",
			"removeEmpty" => TRUE,
		]);
		ArrayPaths::_remove($input, $path, $options["separator"], $options["removeEmpty"]);
	}
	
	
	/**
	 * This method can be used to apply a filter to all values the given $path matches.
	 * The given $callback will receive the following parameters:
	 * $path: "the.path.trough.your.array" to let you decide how to handle the current value
	 * $value: The reference of the current $input"s value. Change this value to change $input correspondingly.
	 * The callback should always return void.
	 *
	 * @param array        $input     The array to filter
	 * @param array|string $path      The path which defines the values to filter
	 * @param callable     $callback  The callback to trigger on every value found by $path
	 * @param string       $separator Default: "." Can be set to any string you want to use as separator of path parts.
	 *
	 * @return void
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinInvalidArgumentException
	 */
	public static function filter(array &$input, $path, callable $callback, string $separator = ".") {
		ArrayPaths::_filter($input, $path, $callback, $separator);
	}
	
	/**
	 * This is a multi purpose tool to handle different scenarios when dealing with array lists.
	 * The best option to describe it, is to show some examples in this case.
	 * We assume an input array like:
	 * $array = [
	 *        [
	 *            "id" => "234",
	 *            "title" => "medium",
	 *            "asdf" => "asdf",
	 *            "array" => [
	 *                    "id" => "12",
	 *                    "rumpel" => "di",
	 *                    "bar" => "baz",
	 *                ]
	 *        ],
	 *        [
	 *            "id" => "123",
	 *            "title" => "apfel",
	 *            "asdf" => "asdf",
	 *            "array" => [
	 *                    "id" => "23",
	 *                    "rumpel" => "pumpel",
	 *                    "foo" => "bar"
	 *                ]
	 *        ]
	 * ];
	 *
	 * // Example 1: Return a list of all "id" values
	 * getList($array, ["id"]);
	 * Result: ["234","123"];
	 *
	 * // Example 2: Return a list of all "id" and "title" values
	 * getList($array, ["id", "title"]);
	 * Result: [
	 *           ["id" => "234", "title" => "medium"],
	 *           [ "id" => "123", "title" => "apfel"]
	 *        ];
	 *
	 * // Example 3: Return a list of all "title" values by their matching "id" value
	 * getList($array, ["title"], "id");
	 * Result: ["234" => "medium", "123" => "apfel"];
	 *
	 * // Example 4: Subarrays, aliases and default values for missing values
	 * getList($array, ["array.id", "array.bar as foo"], "id");
	 * Result: [
	 *            "234" => ["array.id" => "12", "foo" => "baz"],
	 *            "123" => ["array.id" => "23", "foo" => null]
	 *        ];
	 *
	 * // Example 5: Extracting columns as keys
	 * getList($array, [], "id");
	 * Result: ["234" => [VALUE UNCHANGED], "123" => [VALUE UNCHAGED]];
	 *
	 * // Example 6: Sorting entries by a key
	 * getList($array, [], "asdf", "*", null, ".", TRUE);
	 * Result: ["asdf" => [ [VALUE UNCHANGED], [VALUE UNCHANGED] ];
	 *
	 * // Example 7: Dealing with strange sorting and nested lists
	 * // We assume for this example: $array = [ "foo" => $array ];
	 * getList($array, ["id"], "", "foo.*");
	 * Result: ["234","123"];
	 *
	 * // Example 8: Dealing with path based key keys
	 * getList($array, ["id"], "array.id");
	 * Result: ["12" => "234", "23" => "123"];
	 *
	 * @param array  $input           The input array to gather the list from. Should be a list of arrays.
	 * @param array  $valueKeys       The list of value keys to extract from the list, can contain sub-paths
	 *                                like seen in example 4
	 * @param string $keyKey          Optional key or sub-path which will be used as key in the result array
	 * @param array  $options         Additional configuration options:
	 *                                - path: Optional path to filter / normalize the $input array. Default: "*" =>
	 *                                array list
	 *                                - default: The default value if a key was not found in $input.
	 *                                - separator: A separator which is used when splitting string paths
	 *                                - gatherLists: True to gather lists by keys instead of overwriting already set
	 *                                keys.
	 *
	 * @return array|null
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinException
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinInvalidArgumentException
	 */
	public static function &getList(array &$input, array $valueKeys, string $keyKey = "", array $options = []) {
		$options = Options::make($options, [
			"path"        => "*",
			"default"     => NULL,
			"separator"   => ".",
			"gatherLists" => FALSE,
		]);
		return ArrayPaths::_getList($input, $valueKeys, $keyKey, $options["path"],
			$options["default"], $options["separator"], $options["gatherLists"]);
	}
	
	/**
	 * Receives a xml-input and converts it into a multidimensional array
	 *
	 * @param string|array|null|\DOMNode|\SimpleXMLElement $input
	 *
	 * @return array
	 * @throws ArrayGeneratorException
	 */
	public static function makeFromXml($input): array {
		return ArrayGenerator::_fromXml($input);
	}
	
	/**
	 * The method receives an object of sorts and converts it into a multidimensional array
	 *
	 * @param $input
	 *
	 * @return array
	 * @throws ArrayGeneratorException
	 */
	public static function makeFromObject($input): array {
		return ArrayGenerator::_fromObject($input);
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
	public static function makeFromStringList($input, string $separator = ","): array {
		return ArrayGenerator::_fromStringList($input, $separator);
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
	public static function makeFromCsv($input, bool $firstLineKeys = FALSE,
									   string $delimiter = ",", string $quote = "\""): array {
		return ArrayGenerator::_fromCsv($input, $firstLineKeys, $delimiter, $quote);
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
	public static function makefromJson($input): array {
		return ArrayGenerator::_fromJson($input);
	}
}