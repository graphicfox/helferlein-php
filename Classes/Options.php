<?php
/**
 * User: Martin Neundorfer
 * Date: 14.01.2019
 * Time: 10:29
 * Vendor: LABOR.digital
 */

namespace Labor\Helferlein\Php;


use Labor\Helferlein\Php\Exceptions\HelferleinException;

class Options {
	
	/**
	 * This little helper is used to apply a default definition of options
	 * to a given array of options (presumably transferred as a function arg)
	 *
	 * An Example:
	 * function myFunc($value, array $options = []){
	 *    $defaults = [
	 *        'foo' => 123,
	 *        'bar' => null,
	 *    ];
	 *    $options = x::castOptions($options, $defaults);
	 *    ...
	 * }
	 *
	 * myFunc('something') => $options will be $defaults
	 * myFunc('something', ['foo' => 234]) => $options will be ['foo' => 234, 'bar' => null]
	 * myFunc('something', ['rumpel' => 234]) => This will cause Helferlein exception, because the key is not nknown
	 * myfunc('something', ['foo' => 'rumpel']) $options will be ['foo' => 'rumpel', 'bar' => null], because the
	 * options were merged
	 *
	 * It is also possible to filter inputs using a callback.
	 * function myFunc($value, array $options = []){
	 *    $defaults = [
	 *        'foo' => [
	 *            "optionFilter" => function($key, $value, $inputArray, $definitionArray){},
	 *            "optionDefault" => "defaultValue"
	 *           ],
	 *        'bar' => null,
	 *    ];
	 *    $options = x::castOptions($options, $defaults);
	 *    ...
	 * }
	 *
	 * @param array $input
	 * @param array $definition
	 * @param array $options
	 *
	 * @return array|mixed
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinException
	 */
	public static function make(array $input, array $definition, array $options = []) {
		$out = $input;
		$errors = [];
		
		// Apply defaults
		foreach ($definition as $k => $v) {
			if (array_key_exists($k, $out)) continue;
			if (is_array($definition[$k]) && !empty($definition[$k]["optionFilter"]) &&
				!empty($definition[$k]["optionDefault"])) {
				$out[$k] = $definition[$k]["optionDefault"];
				continue;
			}
			$out[$k] = $v;
		}
		
		// Read input
		foreach ($out as $k => $v) {
			// Check for unknown keys
			if (!array_key_exists($k, $definition) && $options["allowUnknownKeys"] !== true) {
				// @todo Add similar key as soon as we have it reimplemented
				$errors[] = "Invalid option key: \"" . $k . "\" given!";
				continue;
			}
			
			// Apply callback if required
			if (is_array($definition[$k]) && !empty($definition[$k]["optionFilter"])) {
				if (!is_callable($definition[$k]["optionFilter"]))
					$errors[] = "Invalid \"optionFilter\" given for: \"" . $k . "\" given!";
				$out[$k] = call_user_func($definition[$k]["optionFilter"], $k, $v, $input, $definition);
			}
		}
		
		// Check if there were errors
		if (!empty($errors))
			throw new HelferleinException("Errors while validating options: " . PHP_EOL . implode(PHP_EOL . " -", $errors));
		
		// Done
		return $out;
		
	}
}