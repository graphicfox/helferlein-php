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

namespace Neunerlei\Helferlein\Php\FormatAndConvert;


class FormatAndConvert {
	
	/**
	 * Converts a string into a float value, making sure that both , and . are used as correct
	 * decimal dividers.
	 *
	 * @param float|int|string $value The value to convert into a float
	 *
	 * @return float
	 */
	public static function floatFromString($value): float {
		if (is_float($value)) return $value;
		return @floatval(is_string($value) ? str_replace(",", ".", $value) : $value);
	}
	
	/**
	 * Converts a given, numeric value 0.45 and converts it into a percent representation like 45,00
	 * or 45 if dropDecimals is set to true
	 *
	 * @param float|int|string $number       The value to convert into a percent format
	 * @param bool             $dropDecimals True to drop the decimal numbers in the output
	 *
	 * @return string
	 */
	public static function numberAsPercent($number, bool $dropDecimals = FALSE): string {
		$number = static::floatFromString($number);
		$value = round($number * 10000) / 100;
		return self::numberAsMoney($value, $dropDecimals);
	}
	
	/**
	 * This helper can be used to convert a number value into a formatted money string.
	 * The output format will be 1.000.000,00 or 1.000.000 if "dropDecimals" is set to true
	 *
	 * @param float|int|string $number            The value to convert into a money format
	 * @param bool             $dropDecimals      True to drop the decimal numbers in the output
	 * @param bool             $thousandSeparator Set to FALSE to disable the thousand separators
	 *
	 * @return string
	 */
	public static function numberAsMoney($number, bool $dropDecimals = FALSE, bool $thousandSeparator = TRUE): string {
		if (!is_float($number)) $number = static::floatFromString($number);
		return number_format($number, $dropDecimals ? 0 : 2, ",", $thousandSeparator ? "." : "");
	}
	
	/**
	 * This method can be used to replace markers/placeholders in a given string.
	 *
	 * For example a $definition like:
	 * array(
	 *    'value' => '100 + 20 + 3',
	 *    'int' => 123)
	 *
	 * And an $subject like: 'I calculated {{value}} and got {{int}} as a result.'
	 * Will end up like: 'I calculated 100 + 20 + 3 and got 123 as a result.'
	 *
	 * @param array       $definition     The definition of markers to replace by their value as associative array
	 * @param string      $subject        The subject to perform the replacements on
	 * @param bool        $stripRemaining If true all remaining (unused) markers will be replaced with an empty string
	 * @param string|null $wrap           Defines how the markers are wrapped, by default its '{{|}}', but you can do
	 *                                    something like '[|]' to look for '[value]' for example.
	 *                                    The pipe defines the position where your key will be placed.
	 *
	 * @return string
	 */
	public static function replaceMarkers(array $definition, string $subject,
										  bool $stripRemaining = FALSE, ?string $wrap = NULL): string {
		// Prepare the wrapping markers
		$w = ["{{", "}}"];
		if ($wrap !== NULL) {
			$wrap = explode("|", $wrap);
			$w = [array_shift($wrap), array_shift($wrap)];
		}
		
		// Apply the wrapping to the definition keys
		$targets = array_map(function ($v) use ($w) {
			return $w[0] . trim(trim($v, $w[0] . $w[1])) . $w[1];
		}, array_keys($definition));
		
		// Do the replacement
		$result = str_replace($targets, $definition, $subject);
		
		// Clean up if required
		if ($stripRemaining)
			$result = preg_replace("/" . preg_quote($w[0]) . ".*?" . preg_quote($w[1]) . "/si", "", $result);
		
		// Done
		return $result;
	}
}