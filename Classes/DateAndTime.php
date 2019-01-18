<?php
/**
 * User: Martin Neundorfer
 * Date: 14.01.2019
 * Time: 10:09
 * Vendor: LABOR.digital
 */

namespace Labor\Helferlein\Php;


use Labor\Helferlein\Php\Exceptions\HelferleinException;
use phpDocumentor\Reflection\Types\Boolean;

class DateAndTime {
	protected static $timezone   = "UTC";
	protected static $dateFormat = "Y.m.d";
	protected static $timeFormat = "h:i";
	
	/**
	 * Sets the default timezone
	 *
	 * @param \DateTimeZone|string $timezone
	 *
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinException
	 */
	public static function setTimeZone($timezone) {
		if (!$timezone instanceof \DateTimeZone && !is_string($timezone))
			throw new HelferleinException("Only strings and objects of type: " . \DateTimeZone::class . " are supported!");
		static::$timezone = $timezone;
	}
	
	/**
	 * Sets the default date format, like Y.m.d
	 *
	 * @param string $format
	 */
	public static function setDateFormat(string $format) {
		static::$dateFormat = $format;
	}
	
	/**
	 * Sets the default date format, like h:i
	 *
	 * @param string $format
	 */
	public static function setTimeFormat(string $format) {
		static::$timeFormat = $format;
	}
	
	/**
	 * Helper to create a DateTime object based on different inputs
	 *
	 * @param string $time   Either a DateTime string, a unix timestamp or a formatted string
	 * @param array  $options
	 *                       - timezone (DEFAULT: default timezone) A timezone to use when creating the dateTime object
	 *                       - format (Default: empty) A format accepted by date() to decode the $time input with
	 *
	 * @return \DateTime
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinException
	 */
	public static function make($time = "now", array $options = []): \DateTime {
		
		// Ignore date time objects
		if (is_object($time) && $time instanceof \DateTime) return $time;
		
		// Prepare options
		$options = Options::make($options, [
			"timezone" => static::$timezone,
			"format"   => NULL,
		]);
		
		// Convert by string format
		if (!empty($format) && is_string($time)) {
			$time = \DateTime::createFromFormat($format, $time, static::makeTimezone($options["timezone"]));
			if (!$time) throw new HelferleinException('The given time can not be parsed with the given format!');
			return $time;
		}
		
		// Convert unix timestamps
		if (is_numeric($time)) $time = '@' . $time;
		
		// Try to create using the default
		try {
			return new \DateTime($time, static::makeTimezone($options["timezone"]));
		} catch (\Exception $e) {
			$fallbackFormat = "Y-m-d H:i:s";
			if ($options["format"] !== $fallbackFormat) {
				$options["format"] = $fallbackFormat;
				return static::make($time, $options);
			}
			throw $e;
		}
	}
	
	/**
	 * Creates a new timezone object
	 *
	 * DateAndTime::makeTimezone() -> Default Timezone
	 * DateAndTime::makeTimezone(new DateTimeZone()) -> The given timezone
	 * DateAndTime::makeTimezone("utc") -> The utc timezone
	 *
	 * @param $timezone
	 *
	 * @return \DateTimeZone
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinException
	 */
	public static function makeTimezone($timezone = NULL): \DateTimeZone {
		if (is_object($timezone) && $timezone instanceof \DateTimeZone) return $timezone;
		if ($timezone === NULL) return static::makeTimezone($timezone);
		if (is_string($timezone)) return new \DateTimeZone(trim($timezone));
		throw new HelferleinException("Invalid timezone given. Only objects and strings are allowed!");
	}
	
	/**
	 * Converts a DateTime object from any given timezone into any other timezone.
	 *
	 * @param null|int|string|\DateTimeZone $time           The time to convert from utc to something else
	 * @param null|int|string|\DateTimeZone $targetTimezone The timezone to convert the date to
	 *                                                      null/(nothing): Convert from the appLocation's primary
	 *                                                      timezone string: A string identifier for the timezone
	 *                                                      anything else: Any possible timezone using DateTimeZone
	 * @param null|int|string|\DateTimeZone $sourceTimezone Timezone which is used to parse the given $time.
	 *                                                      The same rules apply than with $targetTimezone
	 *
	 * @return \DateTime
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinException
	 */
	public static function convertTimezone($time, $targetTimezone = NULL, $sourceTimezone = NULL): \DateTime {
		return static::make($time, ["timezone" => $sourceTimezone])->setTimezone(static::makeTimezone($targetTimezone));
	}
	
	/**
	 * Checks if a given $time is in a range from $rangeStart to $rangeEnd and returns true if so.
	 *
	 * @param null|int|string|\DateTimeZone $time       The time to check
	 * @param null|int|string|\DateTimeZone $rangeStart The beginning of the range
	 * @param null|int|string|\DateTimeZone $rangeEnd   The end of the range
	 * @param array                         $options    The same options as static::make()
	 *
	 * @return bool
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinException
	 */
	public static function inRange($time, $rangeStart, $rangeEnd, $options = []): Boolean {
		$time = static::make($time, $options);
		return $time > static::make($rangeStart, $options) && $time < static::make($rangeEnd, $options);
	}
	
	/**
	 * Applys a given format to the given time object.
	 * Use the format from: http://php.net/manual/en/function.date.php
	 *
	 * Modifiers like F, l, M or D will be translated into your local language as they would with strftime()
	 *
	 * @param null|int|string|\DateTimeZone $time    The time to format
	 * @param string                        $format  The format to apply
	 * @param array                         $options The same options as static::make()
	 *
	 * @return string
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinException
	 */
	public static function format($time, string $format, array $options = []): string {
		$time = static::make($time, $options);
		$timestamp = $time->getTimestamp();
		$format = preg_replace_callback('/(?<!\\\\)[F|l|M|D]/s', function ($v) use ($timestamp) {
			return preg_replace('/(.)/', '\\\\$1',
				strftime(
					str_replace(
						['F', 'l', 'M', 'D'],
						['%B', '%A', '%b', '%a'],
						reset($v)
					),
					$timestamp
				)
			);
		}, $format);
		return $time->format($format);
	}
	
	/**
	 * Returns either the current timestamp or a specific timestamp as mysql datetime string
	 *
	 * @param null|int|string|\DateTimeZone $time    The timestamp to format for mysql
	 * @param array                         $options The same options as static::make()
	 *
	 * @return string
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinException
	 */
	public static function formatMysql($time = "now", array $options = []): string {
		return static::make($time, $options)->format('Y-m-d H:i:s');
	}
	
	/**
	 * Returns the given time as javascript formatted date string
	 *
	 * @param null|int|string|\DateTimeZone $time    The timestamp to prepare for javascript
	 * @param array                         $options The same options as static::make()
	 *
	 * @return string
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinException
	 */
	public static function formatJavascript($time = "now", array $options = []): string {
		return static::make($time, $options)->format("D M d Y H:i:s O");
	}
	
	/**
	 * Formats the given time based on the configured static::$timeFormat
	 *
	 * @param null|int|string|\DateTimeZone $time    The datetime to format as time
	 * @param array                         $options The same options as static::make()
	 *
	 * @return string
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinException
	 */
	public static function formatTime($time = "now", array $options = []): string {
		return static::format($time, static::$timeFormat, $options);
	}
	
	/**
	 * Formats the given date based on the configured static::$dateFormat
	 *
	 * @param null|int|string|\DateTimeZone $time    The datetime to format as date
	 * @param array                         $options The same options as static::make()
	 *
	 * @return string
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinException
	 */
	public static function formatDate($time = "now", array $options = []): string {
		return static::format($time, static::$dateFormat, $options);
	}
	
	/**
	 * Formats the given date and time based on the configured static::$dateFormat and static::$timeFormat
	 *
	 * @param null|int|string|\DateTimeZone $time    The datetime to format as date and time
	 * @param array                         $options The same options as static::make()
	 *
	 * @return string
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinException
	 */
	public static function formatDateAndtime($time = "now", array $options = []): string {
		return static::formatDate($time, $options) . " " . static::formatTime($time, $options);
	}
}