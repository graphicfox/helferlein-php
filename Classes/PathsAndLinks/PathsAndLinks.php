<?php
/**
 * User: Martin Neundorfer
 * Date: 27.01.2019
 * Time: 23:08
 * Vendor: LABOR.digital
 */

namespace Labor\Helferlein\Php\PathsAndLinks;

use Labor\Helferlein\Php\Exceptions\HelferleinNotImplementedException;
use Labor\Helferlein\Php\Options\Options;

class PathsAndLinks {
	
	/**
	 * Receives a string (mostly a file path and unifies the slashes for the current filesystem
	 *
	 * @param string $input The string that"s the target to this method
	 * @param string $slash By default the value of DIRECTORY_SEPARATOR, but you can define a slash you"d like here
	 *
	 * @return string The $string with unified slashes
	 */
	public static function unifySlashes(string $input, string $slash = DIRECTORY_SEPARATOR): string {
		return str_replace(
			["\\", "/", $slash . "." . $slash,
			 $slash . $slash . $slash,
			 $slash . $slash,
			],
			$slash, $input);
	}
	
	/**
	 * Works similar to unifySlashes() but also makes sure the given path ends with a tailing slash
	 *
	 * @param string $path  The string that"s the target to this method
	 * @param string $slash By default the value of DIRECTORY_SEPARATOR, but you can define a slash you"d like here
	 *
	 * @return string
	 */
	public static function unifyPath(string $path, string $slash = DIRECTORY_SEPARATOR): string {
		return rtrim(static::unifySlashes(trim($path), $slash), $slash) . $slash;
	}
	
	/**
	 * Automatically adds http:// in front of a given url if it is not yet present.
	 *
	 * @param string $url The url to append the schema to
	 *
	 * @return string
	 */
	public static function ensureUrlSchema(string $url): string {
		$url = trim($url);
		if (empty($url)) return $url;
		if (!preg_match("~^(?:f|ht)tps?://~i", $url)) $url = "http://" . $url;
		return $url;
	}
	
	/**
	 * Can be used to convert a Fully\Quallified\Classname to Classname
	 *
	 * @param string $classname The classname to get the basename of.
	 *
	 * @return string
	 */
	public static function classBasename(string $classname): string {
		return basename(str_replace("\\", DIRECTORY_SEPARATOR, $classname));
	}
	
	/**
	 * Can be used to convert a Fully\Quallified\Classname to Fully\Quallified
	 * This works the same way dirname() would with a folder path.
	 *
	 * @param string $classname The classname of to get the namespace of.
	 *
	 * @return string
	 */
	public static function classNamespace(string $classname): string {
		return str_replace(DIRECTORY_SEPARATOR, "\\", dirname(str_replace("\\", DIRECTORY_SEPARATOR, $classname)));
	}
	
	/**
	 * Computes the relative path between two path's.
	 * Not my code but form here: https://stackoverflow.com/a/2638272
	 *
	 * @param $from
	 * @param $to
	 *
	 * @return string
	 */
	public static function relativePath($from, $to) {
		// Some compatibility fixes for Windows paths
		$from = is_dir($from) ? rtrim($from, "\/") . "/" : $from;
		$to = is_dir($to) ? rtrim($to, "\/") . "/" : $to;
		$from = str_replace("\\", "/", $from);
		$to = str_replace("\\", "/", $to);
		
		$from = explode("/", $from);
		$to = explode("/", $to);
		$relPath = $to;
		
		foreach ($from as $depth => $dir) {
			// find first non-matching dir
			if ($dir === $to[$depth]) {
				// ignore this directory
				array_shift($relPath);
			} else {
				// get number of remaining dirs to $from
				$remaining = count($from) - $depth;
				if ($remaining > 1) {
					// add traversals up to first matching dir
					$padLength = (count($relPath) + $remaining - 1) * -1;
					$relPath = array_pad($relPath, $padLength, "..");
					break;
				} else {
					$relPath[0] = "./" . $relPath[0];
				}
			}
		}
		return implode("/", $relPath);
	}
	
	/**
	 * This method tries to split up a given url into its different parts using a combination of parse_url() and
	 * parse_str() to do so. It is possible to pass a full url or the query segment only.
	 *
	 * NOTE: Only elements which are present in the url will be returned.
	 * NOTE 2: take a look at makeLink() as well.
	 *
	 * @param string $url A url to parse into an array
	 *
	 * @return array
	 */
	public static function parseLink(string $url): array {
		if (filter_var($url, FILTER_VALIDATE_URL)) {
			// Parse full url
			$url = parse_url($url);
			if (!empty($url["query"])) {
				parse_str($url["query"], $tmp);
				$url["query"] = $tmp;
			}
			return $url;
		} else {
			// Try to handle query
			parse_str($url, $url);
			return ["query" => $url];
		}
	}
	
	public static function makeLink(array $options = [], $baseUrl = NULL): string {
		throw new HelferleinNotImplementedException("This feature is currently not completely implemented!");
		$hasOptions = !empty($options);
		$options = Options::make($options, [
			"hostOnly" => [
				"type"    => "bool",
				"default" => FALSE,
			],
			"*"        => [
				"type"    => ["false", "null"],
				"default" => FALSE,
			],
			"user"     => $partDef = [
				"type"    => ["false", "null", "string"],
				"default" => FALSE,
			],
			"pass"     => $partDef,
			"port"     => $partDef,
			"path"     => $partDef,
			"scheme"   => $partDef,
			"host"     => $partDef,
			"fragment" => $partDef,
			"query"    => [
				"type"    => ["false", "null", "string", "array"],
				"default" => FALSE,
			],
		]);
		$baseUrl = Options::makeSingle("baseUrl", $baseUrl, [
			"type" => ["null", "string", "array", "false"],
		]);
		
		\x::dbge($options);
	}
}