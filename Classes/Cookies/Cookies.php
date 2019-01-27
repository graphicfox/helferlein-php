<?php
/**
 * User: Martin Neundorfer
 * Date: 25.04.2018
 * Time: 10:33
 * Vendor: LABOR.digital
 */

namespace Labor\Helferlein\Php\Cookies;

class Cookies
{
	/**
	 * Returns the instance of a given cookie.
	 *
	 * @param string     $identifier   The name of the cookie to get the instance of
	 *
	 * @param null|mixed $defaultValue If the required cookie does not exist, the method will return this value
	 *
	 * @return mixed|null
	 */
	public static function get(string $identifier, $defaultValue = null)
	{
		// Check if this cookie exists
		if (!isset($_COOKIE[$identifier])) return $defaultValue;
		
		// Load the value
		$value = $_COOKIE[$identifier];
		if (substr($value, 0, 5) === "@enc@") $value = unserialize(base64_decode(substr($value, 5)));
		return $value;
	}
	
	/**
	 * Shortcut to: $this->get($identifier)->setValue()->setLifetime()->setHttp()
	 * Sets a cookie with the given identifier to the given values.
	 *
	 * NOTE: Cookie values will be serialized and base64 encoded if the value is neither a string, nor a number!
	 *
	 * @param string $identifier The name of the cookie
	 * @param mixed  $value      The value to store in the cookie
	 * @param int    $lifetime   -1 means "90 days (legal disclaimer)", 0 means "end of the session"
	 * @param bool   $httpOnly
	 */
	public static function set(string $identifier, $value, int $lifetime = 0, bool $httpOnly = true)
	{
		// Prepare value
		if (!is_string($value) && !is_numeric($value))
			$value = "@enc@" . base64_encode(serialize($value));
		
		// Prepare lifetime
		switch ($lifetime)
		{
			case 0:
				$lifetime = 0;
				break;
			case -1:
				$lifetime = time() + 60 * 60 * 24 * 90;
				break;
			default:
				$lifetime = time() + $lifetime;
		}
		
		// Prepare url
		$url = isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] :
			(isset($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : "localhost");
		
		// Prepare secure tag
		$secure = isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] === "443";
		
		// Update storage
		$_COOKIE[$identifier] = $value;
		setcookie($identifier, $value, $lifetime, "/", $url, $secure, $httpOnly);
	}
	
	/**
	 * Unsets the cookie with the given identifier
	 *
	 * @param string $identifier
	 */
	public static function remove(string $identifier)
	{
		unset($_COOKIE[$identifier]);
		setcookie($identifier, "", time() - 60 * 60 * 24 * 7);
	}
	
	/**
	 * Returns true if the cookie with the given identifier exists, false if not
	 *
	 * @param string $identifier
	 *
	 * @return bool
	 */
	public function has(string $identifier): bool
	{
		return isset($_COOKIE[$identifier]);
	}
}