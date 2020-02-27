<?php
/**
 * Copyright 2020 Martin Neundorfer (Neunerlei)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Last modified: 2020.02.27 at 10:42
 */

namespace Neunerlei\Helferlein\Php\Options;

class Options {
	
	/**
	 * Our internal applier as singleton
	 * @var \Neunerlei\Helferlein\Php\Options\OptionApplier
	 */
	protected static $applier;
	
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
			$result = static::getApplier()->apply(
				is_null($variable) ? [] : ["@dummySingleParam" => $variable], ["@dummySingleParam" => $definition]
			);
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
	 * myFunc("something", ["rumpel" => 234]) => This will cause a Helferlein exception, because the key is not nknown
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
	 * If not set the option key is required! If the default value is a Closure the closure is called
	 * and it's result is used as value.
	 * The callback receives $key, $options, $definition, $path(For child arrays)
	 *
	 * - type (string|array): Allows basic type validation of the input. Can either be a string or an array of strings.
	 * Possible values are: boolean, bool, true, false, integer, int, double, float, number (int and float) or numeric
	 * (both int and float + string numbers), string, resource, null, callable and also class, class-parent and
	 * interface names. If multiple values are supplied they will be seen as chained via OR operator.
	 *
	 * - preFilter (callable): A callback which is called BEFORE the type validation takes place and can be used to
	 * cast the incoming value before validating it's type.
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
		return static::getApplier()->apply($input, $definition, $options);
	}
	
	/**
	 * Internal helper to get the singleton instance of our internal option applier
	 * @return \Neunerlei\Helferlein\Php\Options\OptionApplier
	 */
	protected static function getApplier(): OptionApplier {
		if (!empty(static::$applier)) return static::$applier;
		return static::$applier = new OptionApplier();
	}
}