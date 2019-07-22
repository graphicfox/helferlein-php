<?php
/**
 * User: Martin Neundorfer
 * Date: 21.01.2019
 * Time: 14:59
 * Vendor: LABOR.digital
 */

namespace Labor\Helferlein\Php\Arrays;

use Labor\Helferlein\Php\Exceptions\HelferleinInvalidArgumentException;

/**
 * Class ArrayPaths
 *
 * This is functionality which was extracted from Arrays in order to keep the class somewhat short...
 * @package Labor\Helferlein\Php\Arrays
 * @internal
 */
class ArrayPaths {
	
	/**
	 * A list of possible escaped control objects in path"s
	 */
	const CONTROL_OBJECT_ESCAPING = [
		"\\*" => "*",
		"\*"  => "*",
	];
	
	/**
	 * The different key types
	 */
	const KEY_TYPE_DEFAULT  = 0;
	const KEY_TYPE_WILDCARD = 1;
	const KEY_TYPE_KEYS     = 2;
	
	/**
	 * A list of string path"s and their parsed equivalent for faster lookups
	 * @var array
	 */
	protected static $cache = [];
	
	/**
	 * A list of sub-keys (e.g. my.path[key,in,path]) as an array for faster lookups
	 * @var array
	 */
	protected static $subKeyCache = [];
	
	/**
	 * @param        $path
	 * @param string $separator
	 *
	 * @return array
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinInvalidArgumentException
	 */
	public static function _parsePath($path, string $separator = "."): array {
		if (empty($path)) return [];
		if (!is_string($path) && !is_numeric($path) && !is_array($path))
			throw new HelferleinInvalidArgumentException("The given path: " . json_encode($path) . " is not valid! Only strings, numbers and arrays are supported!");
		
		// Check if the given path array is valid
		if (is_array($path)) {
			$path = array_values($path);
			$validPathParts = array_filter($path, function ($v) {
				// Filter out all non strings and non numerical
				return is_string($v) || is_numeric($v);
			});
			if (count($path) !== count($validPathParts))
				throw new HelferleinInvalidArgumentException("Not all parts of the given path are formatted correctly. " .
					"There are problems with: " . implode(", ", array_diff($validPathParts, $path)));
		} else {
			// Check if we know this path already
			$cacheKey = md5(is_string($path) || is_numeric($path) ? $path . $separator : json_encode($path) . $separator);
			if (!empty(static::$cache[$cacheKey])) return static::$cache[$cacheKey];
			
			// Parse the path from a string
			$hasEscapedSeparator = stripos($path, "\\" . $separator) !== FALSE;
			$path = array_map("trim",
				preg_split("~(?<!\\\\)" . preg_quote($separator, "~") . "~", $path, -1, PREG_SPLIT_NO_EMPTY)
			);
			
			// Remove escaped separators in created path keys
			if ($hasEscapedSeparator)
				$path = array_map(function ($v) use ($separator) {
					return str_replace(["\\" . $separator], $separator, $v);
				}, $path);
			
			static::$cache[$cacheKey] = $path;
		}
		
		return $path;
	}
	
	/**
	 * @param        $pathA
	 * @param        $pathB
	 * @param string $separatorA
	 * @param string $separatorB
	 *
	 * @return array
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinInvalidArgumentException
	 */
	public static function _mergePaths($pathA, $pathB, $separatorA = ".", $separatorB = "."): array {
		$pathA = static::_parsePath($pathA, $separatorA);
		$pathB = static::_parsePath($pathB, $separatorB);
		foreach ($pathB as $p) $pathA[] = $p;
		return $pathA;
	}
	
	/**
	 * @param array|mixed  $input     The array to check
	 * @param array|string $path      The path to check for in $input
	 * @param string       $separator Default: "." Can be set to any string you want to use as separator of path parts.
	 *
	 * @return bool
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinInvalidArgumentException
	 */
	public static function _has($input, $path, string $separator = "."): bool {
		// Ignore empty inputs
		if (!is_array($input)) return FALSE;
		
		// Fastlane for simple paths
		if (is_string($path) && stripos($path, $separator) === FALSE)
			return array_key_exists($path, $input);
		
		// Convert the path into an array
		$path = static::_parsePath($path, $separator);
		if (empty($path))
			throw new HelferleinInvalidArgumentException("The given path was empty!");
		
		// Check recursive if the path exists
		try {
			$walker = function (array $input, array $path, callable $walker) {
				list($keys, $isLastKey) = static::initWalkerStep($input, $path);
				if (empty($input) || empty($keys)) throw new \Exception();
				foreach ($keys as $key) {
					if (!array_key_exists($key, $input)) throw new \Exception();
					if (!$isLastKey) {
						if (is_array($input[$key])) {
							$walker($input[$key], $path, $walker);
							continue;
						}
						throw new \Exception();
					}
				};
			};
			$walker($input, $path, $walker);
			unset($walker);
			return TRUE;
		} catch (\Exception $e) {
			return FALSE;
		}
	}
	
	/**
	 * @param array|mixed  $input     The array to retrieve the path"s values from
	 * @param array|string $path      The path to receive from the $input array
	 * @param null|mixed   $default   The value which will be returned if the $path did not match anything.
	 * @param string       $separator Default: "." Can be set to any string you want to use as separator of path parts.
	 *
	 * @return array|mixed|null
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinInvalidArgumentException
	 */
	public static function &_get(&$input, $path, $default = NULL, string $separator = ".") {
		// Ignore empty inputs
		if (!is_array($input)) return $default;
		
		// Fastlane for simple paths
		if (empty($path)) return $input;
		if (is_string($path) && stripos($path, $separator) === FALSE && array_key_exists($path, $input))
			return $input[$path];
		
		// Convert the path into an array
		$path = static::_parsePath($path, $separator);
		if (empty($path))
			throw new HelferleinInvalidArgumentException("The given path was empty!");
		
		// Extract the result object by walking the array recursively
		$walker = function &(array &$input, array $path, callable $walker) use ($default) {
			list($keys, $isLastKey, $keyType) = static::initWalkerStep($input, $path);
			if (empty($input) || empty($keys)) return $default;
			$result = [];
			foreach ($keys as $key) {
				if (!array_key_exists($key, $input)) {
					$result[$key] = $default;
					continue;
				}
				if (!$isLastKey) {
					if (is_array($input[$key])) {
						$result[$key] = &$walker($input[$key], $path, $walker);
						continue;
					}
					$result[$key] = $default;
					continue;
				}
				$result[$key] = &$input[$key];
			}
			if ($keyType === static::KEY_TYPE_DEFAULT)
				$result = &$result[key($result)];
			return $result;
		};
		$result = &$walker($input, $path, $walker);
		unset($walker);
		return $result;
	}
	
	/**
	 * @param array        $input     The array to set the values in
	 * @param array|string $path      The path to set $value at
	 * @param mixed        $value     The value to set at $path in $input
	 * @param string       $separator Default: "." Can be set to any string you want to use as separator of path parts.
	 *
	 * @return void
	 *
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinInvalidArgumentException
	 */
	public static function _set(array &$input, $path, $value, string $separator = ".") {
		// Fastlane for simple paths
		if (is_string($path) && stripos($path, $separator) === FALSE) {
			$input[$path] = $value;
			return;
		}
		
		// Convert the path into an array
		$path = static::_parsePath($path, $separator);
		if (empty($path)) throw new HelferleinInvalidArgumentException("The given path was empty!");
		
		// Set the values using the recursion walker
		$walker = function (array &$input, array $path, callable $walker) use ($value) {
			list($keys, $isLastKey) = static::initWalkerStep($input, $path);
			foreach ($keys as $key) {
				if ($isLastKey) {
					$input[$key] = $value;
					continue;
				}
				if (!array_key_exists($key, $input) || !is_array($input[$key]))
					$input[$key] = [];
				$walker($input[$key], $path, $walker);
			}
		};
		$walker($input, $path, $walker);
		unset($walker);
	}
	
	
	/**
	 * @param array        $input              The array to remove the values from
	 * @param array|string $path               The path which defines which values have to be removed
	 * @param string       $separator          Default: "." Can be set to any string you want to use as separator of
	 *                                         path parts.
	 * @param bool         $removeEmptyRemains Set this to false to disable the automatic cleanup of empty remains when
	 *                                         the lowest child was removed from a tree.
	 *
	 * @return void
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinInvalidArgumentException
	 */
	public static function _remove(array &$input, $path, string $separator = ".", bool $removeEmptyRemains = TRUE) {
		// Ignore empty inputs
		if (is_string($path) && stripos($path, $separator) === FALSE) {
			unset($input[$path]);
			return;
		}
		
		// Convert the path into an array
		$path = static::_parsePath($path, $separator);
		if (empty($path)) throw new HelferleinInvalidArgumentException("The given path was empty!");
		
		// Start walker
		$walker = function (array &$input, array $path, callable $walker) use ($removeEmptyRemains) {
			list($keys, $isLastKey) = static::initWalkerStep($input, $path);
			foreach ($keys as $key) {
				if ($isLastKey) {
					unset($input[$key]);
					break;
				}
				if (is_array($input[$key])) $walker($input[$key], $path, $walker);
				if ($removeEmptyRemains && empty($input[$key])) unset($input[$key]);
			}
		};
		$walker($input, $path, $walker);
		unset($walker);
		
	}
	
	/**
	 * @param array        $input     The array to filter
	 * @param array|string $path      The path which defines the values to filter
	 * @param callable     $callback  The callback to trigger on every value found by $path
	 * @param string       $separator Default: "." Can be set to any string you want to use as separator of path parts.
	 *
	 * @return void
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinInvalidArgumentException
	 */
	public static function _filter(array &$input, $path, callable $callback, string $separator = ".") {
		// Convert the path into an array
		$path = static::_parsePath($path, $separator);
		if (empty($path)) throw new HelferleinInvalidArgumentException("The given path was empty!");
		
		// Start the walker
		$walker = function (array &$input, array $path, string $pathCallback, callable $walker) use ($separator, $callback) {
			list($keys, $isLastKey) = static::initWalkerStep($input, $path);
			foreach ($keys as $key) {
				$pathCallbackLocal = ltrim($pathCallback . $separator . $key, $separator);
				if ($isLastKey) call_user_func_array($callback, [$pathCallbackLocal, &$input[$key]]);
				else if (is_array($input[$key])) $walker($input[$key], $path, $pathCallbackLocal, $callback);
			}
		};
		$walker($input, $path, "", $walker);
		unset($walker);
	}
	
	/**
	 * @param array      $input       The input array to gather the list from. Should be a list of arrays.
	 * @param array      $valueKeys   The list of value keys to extract from the list, can contain sub-paths
	 *                                like seen in example 4
	 * @param string     $keyKey      Optional key or sub-path which will be used as key in the result array
	 * @param string     $path        Optional path to filter / normalize the $input array. Default: "*" => array list
	 * @param null|mixed $default     The default value if a key was not found in $input.
	 * @param string     $separator   A separator which is used when splitting string paths
	 * @param bool       $gatherLists True to gather lists by keys instead of overwriting already set keys.
	 *
	 * @return array|null
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinInvalidArgumentException
	 */
	public static function &_getList(array &$input, array $valueKeys, string $keyKey = "", $path = "*",
									 $default = NULL, string $separator = ".", bool $gatherLists = FALSE) {
		// Convert the path into an array
		$path = static::_parsePath($path, $separator);
		if (empty($path)) throw new HelferleinInvalidArgumentException("The given path was empty!");
		
		// Check if we have to dig deeper -> $path !== "*"
		if ($path !== ["*"])
			$input = static::_get($input, $path, $default, $separator);
		
		// Validate if the input is still an array list
		if (empty($input) || count(array_filter($input, function ($v) { return is_array($v); })) !== count($input))
			return $default;
		
		// Handle wildcard value keys => $valueKeys = "*";
		if (isset($valueKeys[0]) && $valueKeys[0] === "*") $valueKeys = [];
		
		// Holds the output
		$result = [];
		// True if only a single valueKey was requested => unwrap the output so a key => value array is returned
		$isSingleValueKey = count($valueKeys) === 1;
		// True if a $keyKey was defined
		$hasKeyKey = !empty($keyKey);
		// True if a $keyKey was defined, but not requested in the list of $valueKeys => we will have to remove it at the end
		$keyKeyWasInjected = FALSE;
		// Marks an empty result when resolving path value fields
		$emptyMarker = "__EMPTY__" . rand(0, 999) . "__";
		
		// Fastlane for empty value fields
		if (!$isSingleValueKey && empty($valueKeys)) {
			// Special handling if everything stays, but the parent key should be set
			if (!$hasKeyKey) return $input;
			// Check if we can use the fast lookup
			$isSimpleKey = stripos($keyKey, $separator) === FALSE;
			foreach ($input as $row) {
				// Get the key value
				$keyKeyValue = $isSimpleKey ?
					(isset($row[$keyKey]) ? $row[$keyKey] : NULL) :
					static::_get($row, $keyKey, NULL, $separator);
				// Build the output
				if ($keyKeyValue === NULL) $result[] = $row;
				else if ($gatherLists) $result[$keyKeyValue][] = $row;
				else $result[$keyKeyValue] = $row;
			}
			return $result;
		}
		
		// Add key key to the list of required keys
		if ($hasKeyKey && !in_array($keyKey, $valueKeys)) {
			$valueKeys[] = $keyKey;
			$keyKeyWasInjected = TRUE;
		}
		
		// This block checks if we have to resolve keys which are sub-paths in the current array list.
		// It is possible to define a valueKey like sub.array.id to extract that deeper level"s
		// information and put it into the current context. The key will be the same as the
		// path, in our case: "sub.array.id", if we want something more speaking we can
		// define an alias like sub.array.id as myId. Now the value will show up with myId as key.
		// This block prepares the parsing so we don"t have to do it in every loop
		$pathValueKeys = $simpleValueKeys = $keyAliasMap = [];
		array_map(function ($v) use ($separator, &$pathValueKeys, &$simpleValueKeys, &$keyAliasMap, $isSingleValueKey) {
			// Store the alias
			$vOrg = $alias = $v;
			$aliasSeparator = " as ";
			if (stripos($v, $separator) !== FALSE) {
				// Check for an alias || Ignore when only one value will be returned -> save performance (a bit at least)
				if (!$isSingleValueKey && stripos($v, $aliasSeparator) !== FALSE) {
					$v = explode($aliasSeparator, $v);
					$alias = array_pop($v);
					$v = implode($aliasSeparator, $v);
				}
				$pathValueKeys[$alias] = $v;
				$simpleValueKeys[] = $alias;
			} else {
				$simpleValueKeys[] = $v;
			}
			$keyAliasMap[$vOrg] = $alias;
		}, $valueKeys);
		$hasPathValueKeys = !empty($pathValueKeys);
		$simpleValueKeys = array_fill_keys($simpleValueKeys, $default);
		
		// Loop over the list of rows
		foreach ($input as $row) {
			// Only simple value keys -> use the fast lane
			$rowValues = array_intersect_key($row, $simpleValueKeys);
			
			// Determine if the value keys contain paths themselves
			if ($hasPathValueKeys) {
				// Contains path value keys -> also gather their values
				foreach ($pathValueKeys as $alias => $pathValueKey) {
					// Read the path value from the current context
					$value = static::_get($row, $pathValueKey, $emptyMarker, $separator);
					if ($value !== $emptyMarker) $rowValues[$alias] = $value;
				}
			}
			
			// Check if we are completely empty
			if (empty($rowValues)) continue;
			
			// Get key key
			$keyKeyValue = $hasKeyKey ? $rowValues[$keyAliasMap[$keyKey]] : NULL;
			
			// Check if we have a single value key -> strip the surrounding array
			if ($isSingleValueKey) {
				// Remove if the key key was injected and not part of the requested columns
				if ($keyKeyWasInjected) unset($rowValues[$keyAliasMap[$keyKey]]);
				
				// Extract first value
				$rowValues = reset($rowValues);
			} else {
				// Fill up with default values (if we are missing some)
				$rowValues = array_merge($simpleValueKeys, $rowValues);
				
				// Remove if the key key was injected and not part of the requested columns
				if ($keyKeyWasInjected) unset($rowValues[$keyAliasMap[$keyKey]]);
			}
			
			// Append to result
			if ($hasKeyKey && !empty($keyKeyValue)) {
				if ($gatherLists) $result[$keyKeyValue][] = $rowValues;
				else $result[$keyKeyValue] = $rowValues;
			} else {
				// Sequential
				$result[] = $rowValues;
			}
		}
		
		// Done
		return $result;
	}
	
	/**
	 * Internal helper which is used to parse the current path into a list of control variables
	 *
	 * @param array  $input
	 * @param array  $path
	 * @param string $keySeparator
	 *
	 * @return array
	 */
	protected static function initWalkerStep(array $input, array &$path, string $keySeparator = ","): array {
		// Get the current key we have to work with
		$key = $keyEscaped = (string)array_shift($path);
		$isLastKey = empty($path);
		
		// Handle control object escaping
		if (isset(static::CONTROL_OBJECT_ESCAPING[$keyEscaped]))
			$key = static::CONTROL_OBJECT_ESCAPING[$keyEscaped];
		
		// Get the type of the current key
		if ($keyEscaped === "*") {
			// WILDCARD
			$keyType = static::KEY_TYPE_WILDCARD;
			$keys = array_keys($input);
		} else if (isset($keyEscaped[0]) && $keyEscaped[0] === "[" && substr($keyEscaped, -1) === "]") {
			// SUBKEYLIST
			$keyType = static::KEY_TYPE_KEYS;
			if (isset(static::$subKeyCache[$keyEscaped]))
				$keys = static::$subKeyCache[$keyEscaped];
			else {
				// Parse keys to array
				$tmp = substr(trim($keyEscaped), 1, -1);
				$keys = array_map("trim",
					preg_split("~(?<!\\\)" . preg_quote($keySeparator, "~") . "~", $tmp, -1, PREG_SPLIT_NO_EMPTY)
				);
				static::$subKeyCache[$keyEscaped] = $keys;
			}
		} else {
			// DEFAULT
			$keyType = static::KEY_TYPE_DEFAULT;
			$keys = [$key];
		}
		return [$keys, $isLastKey, $keyType];
	}
}