<?php
/**
 * User: Martin Neundorfer
 * Date: 26.01.2019
 * Time: 23:00
 * Vendor: LABOR.digital
 */

namespace Labor\Helferlein\Php\Options;

use Labor\Helferlein\Php\Arrays\Arrays;

class Options {
	/**
	 * In general, this does exactly the same as Options::make() but is designed to validate non-array options.
	 *
	 * An Example:
	 * function myFunc($value, $anOption = null){
	 *    $defaults = [
	 *        "type" => ["string"],
	 *        "default" => "foo",
	 *    ];
	 *    $anOption = Options::makeSingle("anOption", $anOption, $defaults);
	 *    ...
	 * }
	 *
	 * NOTE: There is one gotcha. As you see in our example we define $anOption as = null in the signature.
	 * This will cause the method to use the default value of "foo" if the property is not set. This will not
	 * cause issues when not checking for null tho!
	 *
	 * @param string      $paramName  The name of the parameter for output purposes
	 * @param mixed       $variable   The variable you want to filter
	 * @param array|mixed $definition See Options::make() for a detailed documentation
	 *
	 * @return mixed
	 * @throws InvalidDefinitionException
	 * @throws InvalidOptionException
	 */
	public static function makeSingle(string $paramName, $variable, $definition) {
		try {
			$result = static::make(
				is_null($variable) ? [] : ["@dummySingleParam" => $variable], ["@dummySingleParam" => $definition]);
			return $result["@dummySingleParam"];
		} catch (InvalidOptionException $e) {
			throw new InvalidOptionException(str_replace("@dummySingleParam", $paramName, $e->getMessage()));
		}
	}
	
	/**
	 * This nifty little helper is used to apply a default definition of options
	 * to a given array of options (presumably transferred as a function parameter)
	 *
	 * An Example:
	 * function myFunc($value, array $options = []){
	 *    $defaults = [
	 *        "foo" => 123,
	 *        "bar" => null,
	 *    ];
	 *    $options = Options::make($options, $defaults);
	 *    ...
	 * }
	 *
	 * myFunc("something") => $options will be $defaults
	 * myFunc("something", ["foo" => 234]) => $options will be ["foo" => 234, "bar" => null]
	 * myFunc("something", ["rumpel" => 234]) => This will cause Helferlein exception, because the key is not nknown
	 * myfunc("something", ["foo" => "rumpel"]) $options will be ["foo" => "rumpel", "bar" => null], because the
	 * options were merged
	 *
	 * IMPORTANT NOTE: When you want to set an array as default value make sure to wrap it in an additional array.
	 * Example: $defaults = ["foo" => []] <- This will crash! This will not -> ["foo" => [[]]]
	 *
	 * Advanced definitions
	 * =============================
	 * In addition to the simple default values you can also use an array as value in your definitions array.
	 * In it you can set the following options to validate and filter options as you wish.
	 *
	 * - default (mixed|callable): This is the default value to use when the key in $options is empty.
	 * If not set the default value is NULL. If the default value is a Closure the closure is called
	 * and it's result is used as value.
	 * The callback receives $key, $options, $definition, $path(For child arrays)
	 *
	 * - type (string|array): Allows basic type validation of the input. Can either be a string or an array of strings.
	 * Possible values are: boolean, bool, true, false, integer, int, double, float, number or numeric (both int and float)
	 * string, resource, null, callable and also class, class-parent and interface names.
	 * If multiple values are supplied they will be seen as chained via OR operator.
	 *
	 * - filter (callable): A callback which is called after the type validation took place and can be used to process
	 * a given value before the custom validation begins.
	 * The callback receives $value, $key, $options, $definition, $path(For child arrays)
	 *
	 * - validator (callable): A callback which allows custom validation using closures or other callables. If used the
	 * function should return true if the validation was successful or false if not. It is also possible to return a
	 * string which allows you to set your own error message. In addition you may return an array of values that will
	 * be passed to the "values" validator (see the next point for the functionality)
	 * The callback receives $value, $key, $options, $definition, $path(For child arrays)
	 *
	 * - values (array): A basic validation routine which receives a list of possible values and will check if
	 * the given value will match at least one of them (OR operator). The array can either be set statically
	 * in your definition, or by using a "validator" callback that returns an array of possible values.
	 * The values validation takes place after the "validator" callback ran.
	 *
	 * - children (array): This can be used to apply nested definitions on option trees.
	 * The children definition is done exactly the same way as on root level.
	 * NOTE: The children will only be used if the value in $options is an array
	 * (or has a default value of an empty array)
	 *
	 * Boolean flags
	 * =============================
	 * It is also possible to supply options that have a type of "boolean" as "flags" which means you don't have
	 * to supply any values to it.
	 *
	 * An Example:
	 * function myFunc($value, array $options = []){
	 *    $defaults = [
	 *        "myFlag" => [
	 *                "type" => "boolean",
	 *                "default" => false
	 *          ],
	 *        ...
	 *    ];
	 *    $options = Options::make($options, $defaults);
	 *    ...
	 * }
	 *
	 * In action
	 * myFunc($foo, ["myFlag"])
	 *
	 * In this case your $options["myFlag"] will contain a value of TRUE
	 *
	 * @param array $input
	 * @param array $definition
	 * @param array $options Additional options
	 *                       - allowUnknown|ignoreUnknown (bool) false: If set to true, unknown keys will be ignored
	 *                       and kept in the result, otherwise an exception is thrown
	 *
	 * @return array|mixed
	 * @throws InvalidDefinitionException
	 * @throws InvalidOptionException
	 */
	public static function make(array $input, array $definition, array $options = []): array {
		// Prepare internals
		$path = is_array($options["@childrensPath"]) ? $options["@childrensPath"] : [];
		$definition = static::prepareAndValidateDefinition($definition, $path);
		$out = $input;
		$errors = [];
		
		// Apply defaults
		foreach ($definition as $k => $def) {
			if (array_key_exists($k, $out)) continue;
			if (is_object($def["default"]) && $def["default"] instanceof \Closure)
				$out[$k] = $def["default"]($k, $out, $definition, $path);
			else
				$out[$k] = $def["default"];
		}
		
		// Read the input
		foreach ($out as $k => $v) {
			$path[] = $k;
			
			// Check for unknown keys
			if (!array_key_exists($k, $definition)) {
				// Check vor boolean flags
				if (is_int($k) && array_key_exists($v, $definition) && is_array($definition[$v])
					&& is_array($definition[$v]["type"]) &&
					(in_array("bool", $definition[$v]["type"]) || in_array("boolean", $definition[$v]["type"]) ||
						in_array("true", $definition[$v]["type"]))) {
					// Handle flag
					unset($out[$k]);
					$k = $v;
					$v = $out[$v] = TRUE;
				} else if ($options["allowUnknown"] !== TRUE && $options["ignoreUnknown"] !== TRUE) {
					// Handle not found key
					$alternativeKey = Arrays::getSimilarKey($definition, $k);
					$e = "Invalid option key: \"" . implode(".", $path) . "\" given!";
					if (!empty($alternativeKey)) $e .= " Did you mean: \"$alternativeKey\" instead?";
					$errors[] = $e;
					array_pop($path);
					continue;
				}
			}
			
			// Apply type check
			if (!empty($definition[$k]["type"])) {
				if (!static::validateTypesOf($v, $definition[$k]["type"])) {
					$type = strtolower(gettype($v));
					if ($type === "object") $type = "Instance of: " . get_class($v);
					$errors[] = "Invalid option type at: \"" . implode(".", $path) . "\" given; Allowed types: \"" .
						implode("\", \"", array_keys($definition[$k]["type"])) . "\". Given type: \"" . $type . "\"!";
					array_pop($path);
					continue;
				}
			}
			
			// Apply callback if required
			if (!empty($definition[$k]["filter"]))
				$out[$k] = $v = call_user_func($definition[$k]["filter"], $v, $k, $out, $definition, $path);
			
			// Apply validator
			if (!empty($definition[$k]["validator"])) {
				$validatorResult = call_user_func($definition[$k]["validator"], $v, $k, $out, $definition, $path);
				if (is_array($validatorResult)) {
					$definition[$k]["values"] = $validatorResult;
				} else if ($validatorResult !== TRUE) {
					if (!is_string($validatorResult)) $errors[] = "Invalid option: \"" . implode(".", $path) . "\" given!";
					else $errors[] = "Validation failed at: \"" . implode(".", $path) . "\" - " . $validatorResult;
					array_pop($path);
					continue;
				}
			}
			
			// Apply values validator
			if (!empty($definition[$k]["values"])) {
				if (!in_array($v, $definition[$k]["values"])) {
					// Stringify value list
					$valueStrings = [];
					foreach ($definition[$k]["values"] as $_v) {
						if (is_string($valueStrings) || is_numeric($valueStrings)) $valueStrings[] = $_v;
						else if (is_object($valueStrings)) {
							if (method_exists($valueStrings, "__toString")) {
								$s = substr((string)$valueStrings, 0, 50);
								if (strlen($s) === 50) $s .= "...";
								$valueStrings[] = $s;
							} else
								$valueStrings[] = "Object of type: " . get_class($_v);
						} else $valueStrings[] = "Value of type: " . gettype($_v);
					}
					$valueStrings = array_unique($valueStrings);
					$errors[] = "Validation failed at: \"" . implode(".", $path) . "\" - Only the following values are allowed: \"" . implode("\", \"", $valueStrings);
					array_pop($path);
					continue;
				}
			}
			
			// Apply children definition
			if (is_array($v) && !empty($definition[$k]["children"])) {
				$childOptions = $options;
				$childOptions["@childrensPath"] = $path;
				$childrensOut = Options::make($v, $definition[$k]["children"], $childOptions);
				if (isset($childrensOut["@childrensErrors"])) $errors = array_merge($errors, $childrensOut["@childrensErrors"]);
				else $out[$k] = $childrensOut;
			}
			array_pop($path);
		}
		
		// Check if there were errors
		if (!empty($errors)) {
			if (!empty($path)) return ["@childrensErrors" => $errors];
			throw new InvalidOptionException("Errors while validating options: " . PHP_EOL . " -" . implode(PHP_EOL . " -", $errors));
		}
		
		// Done
		return $out;
	}
	
	/**
	 * Internal helper which is used to prepare the given definition.
	 * It will also check if the definition can be used for our purposes
	 *
	 * @param array $definition
	 * @param array $path
	 *
	 * @return array
	 * @throws InvalidDefinitionException
	 */
	protected static function prepareAndValidateDefinition(array $definition, array $path): array {
		$definitionPrepared = [];
		foreach ($definition as $k => $v) {
			$path[] = $k;
			
			// Convert simple definition -> Fast lane
			if (!is_array($v)) {
				$definitionPrepared[$k] = ["default" => $v];
				continue;
			} else if (is_array($v) && count($v) === 1 && is_numeric(key($v)) && is_array(reset($v))) {
				$definitionPrepared[$k] = ["default" => reset($v)];
				continue;
			}
			
			// Validate the given definition
			$hasConfiguration = FALSE;
			// Default value
			if (isset($v["default"])) $hasConfiguration = TRUE;
			else $v["default"] = NULL;
			// Validator
			if (isset($v["validator"])) {
				$hasConfiguration = TRUE;
				if (!is_callable($v["validator"]))
					throw new InvalidDefinitionException(
						"Definition error at: " . implode(".", $path) . " - The validator is not callable!");
			}
			// Filter
			if (isset($v["filter"])) {
				$hasConfiguration = TRUE;
				if (!is_callable($v["filter"]))
					throw new InvalidDefinitionException(
						"Definition error at: " . implode(".", $path) . " - The filter is not callable!");
			}
			// Value type
			if (isset($v["type"])) {
				$hasConfiguration = TRUE;
				if (!is_array($v["type"])) {
					if (!is_string($v["type"]))
						throw new InvalidDefinitionException(
							"Definition error at: " . implode(".", $path) . " - Type definitions have to be an array or a string!");
					$v["type"] = [$v["type"]];
				}
				
				// Unify types
				$typesUnique = [];
				$defaultTypes = ["boolean", "integer", "double", "float", "string", "array", "object", "resource",
								 "null", "number", "true", "false", "callable",
				];
				foreach (array_values($v["type"]) as $type) {
					$typeLc = trim(strtolower($type));
					if (in_array($typeLc, $defaultTypes)) $typesUnique[$typeLc] = TRUE;
					else if ($typeLc === "numeric") $typesUnique["number"] = TRUE;
					else if ($typeLc === "int") $typesUnique["integer"] = TRUE;
					else if ($typeLc === "bool") $typesUnique["boolean"] = TRUE;
					else $typesUnique[$type] = TRUE;
				}
				$v["type"] = $typesUnique;
			}
			// Child definition
			if (isset($v["children"])) {
				$hasConfiguration = TRUE;
				if ($v["default"] === NULL) $v["default"] = [];
				if (!is_array($v["children"]))
					throw new InvalidDefinitionException(
						"Definition error at: " . implode(".", $path) . " - Children definitions have to be an array!");
			}
			
			// Kill if $v was an array without configuration
			if (!$hasConfiguration)
				throw new InvalidDefinitionException(
					"Definition error at: " . implode(".", $path) . " - Make sure to wrap arrays in definitions in an outer array!");
			$definitionPrepared[$k] = $v;
			array_pop($path);
		}
		return $definitionPrepared;
	}
	
	/**
	 * Internal helper which validates the type of a given value against a list of valid types
	 *
	 * @param mixed $value the value to validate
	 * @param array $types The list of types to validate $value against
	 *
	 * @return bool
	 */
	protected static function validateTypesOf($value, array $types): bool {
		$type = strtolower(gettype($value));
		
		// Simple lookup
		if (isset($types[$type])) return TRUE;
		
		// Object lookup
		if ($type === "object") {
			if (isset($types[get_class($value)])) return TRUE;
			if (count(array_intersect(class_parents($value), array_keys($types))) > 0) return TRUE;
			if (count(array_intersect(class_implements($value), array_keys($types))) > 0) return TRUE;
			return FALSE;
		}
		
		// Boolean lookup
		if ($type === "boolean") return isset($types[$value ? "true" : "false"]);
		
		// Numeric lookup
		if ($type === "integer" || $type === "double" || $type === "float")
			return isset($types["number"]);
		
		// Callable lookup
		if (isset($types["callable"])) return is_callable($value);
		
		// Nope...
		return FALSE;
	}
}