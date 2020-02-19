<?php
/**
 * Copyright 2020 LABOR.digital
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
 * Last modified: 2019.09.23 at 17:48
 */

namespace Labor\Helferlein\Php\PathsAndLinks;

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
	 * Can be used to convert a Fully\Qualified\Classname to Classname
	 *
	 * @param string $classname The classname to get the basename of.
	 *
	 * @return string
	 */
	public static function classBasename(string $classname): string {
		return basename(str_replace("\\", DIRECTORY_SEPARATOR, $classname));
	}
	
	/**
	 * Can be used to convert a Fully\Qualified\Classname to Fully\Qualified
	 * This works the same way dirname() would with a folder path.
	 *
	 * @param string $classname The classname of to get the namespace of.
	 *
	 * @return string
	 */
	public static function classNamespace(string $classname): string {
		$result = str_replace(DIRECTORY_SEPARATOR, "\\", dirname(str_replace("\\", DIRECTORY_SEPARATOR, $classname)));
		if ($result === ".") return "";
		return $result;
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
	 * Can be used to calculate a path after a given, relative path has been applied.
	 * Example: $base = /my/path/to/somewhere $relative = ../../that/leads/anywhere
	 * Result: /my/path/that/leads/anywhere
	 *
	 * @param string $base     The base path to modify using the given $relative path
	 * @param string $relative The relative path modifier to resolve using the given absolute path
	 *
	 * @return string
	 */
	public static function absolutePath(string $base, string $relative): string {
		$parts = explode("/", rtrim(PathsAndLinks::unifySlashes($base, "/"), "/"));
		$relativeParts = explode("/", ltrim(PathsAndLinks::unifySlashes($relative, "/"), "/"));
		foreach ($relativeParts as $part) {
			if (empty($part)) continue;
			if ($part === ".") continue;
			if ($part === "..") array_pop($parts);
			else array_push($parts, $part);
		}
		return implode("/", $parts);
	}
	
	/**
	 * Returns an instance of Link which is a super simple url builder.
	 *
	 * Possible values for $url are:
	 * TRUE: Returns the representation of the current url
	 * string: A fully qualified url, or a query string beginning with ?
	 * array: The result of parse_url() as an array
	 * Link: Another instance of a link to clone into a new instance
	 *
	 * @param null|boolean|string|\Labor\Helferlein\Php\PathsAndLinks\Link|array $url
	 *
	 * @return \Labor\Helferlein\Php\PathsAndLinks\Link
	 * @throws \Labor\Helferlein\Php\PathsAndLinks\InvalidLinkException
	 */
	public static function getLink($url = NULL): Link {
		
		// Skip if the given url is empty
		if (empty($url)) return new Link();
		
		// A link instance was given
		if ($url instanceof Link) return clone $url;
		
		// True -> create from current url
		if ($url === TRUE) {
			// Protocol
			$url = isset($_SERVER["REQUEST_SCHEME"]) ? $_SERVER["REQUEST_SCHEME"] . "://" :
				(isset($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'off') ? 'https://' : 'http://');
			// Hostname
			$url .= isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] :
				(isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost');
			// URI
			$url .= isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "";
		}
		
		// Convert string url / query to an url array
		if (is_string($url)) {
			$url = trim($url);
			if (!filter_var($url, FILTER_VALIDATE_URL)) {
				if ($url[0] === "?") {
					parse_str($url, $url);
					$url = ["query" => $url];
				}
			}
			if (is_string($url)) $url = parse_url($url);
			if (!is_array($url)) throw new InvalidLinkException("Could not convert the given string \"" . $url . "\" into a link, because it is no valid url");
		}
		
		// Validate that we now got an array
		if (!is_array($url))
			throw new InvalidLinkException("Could not create a link for the given url, because it is neither a string, nor an array");
		
		// Make sure the query is parsed as array
		if (!empty($url["query"]) && is_string($url["query"])) {
			parse_str($url["query"], $tmp);
			$url["query"] = $tmp;
		}
		
		// Return the link instance
		return new Link($url);
	}
}