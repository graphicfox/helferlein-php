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

namespace Neunerlei\Helferlein\Php\Arrays;


use DOMDocument;
use SimpleXMLElement;

class ArrayDumper {
	
	/**
	 * Receives the result of Arrays::makeFromXml() and converts the array back into an xml format
	 *
	 * @param array $array
	 * @param bool  $asString
	 *
	 * @return \SimpleXMLElement|string
	 * @throws \Neunerlei\Helferlein\Php\Arrays\ArrayDumperException
	 */
	public static function _toXml(array $array, bool $asString = FALSE) {
		
		// Die if we got an invalid node
		if (count($array) !== 1)
			throw new ArrayDumperException("Only arrays with a single root node can be converted into xml");
		
		// Helper to traverse the given array recursively
		$walker = function (array $entry, array $path, ?SimpleXMLElement $xml, callable $walker) {
			if (!is_array($entry))
				throw new ArrayDumperException("All entries have to be arrays, but " . implode(".", $path) . " isn't");
			
			if (!isset($entry["tag"]))
				throw new ArrayDumperException("All entries in an XML array have to specify a \"tag\" property, but " .
					implode(".", $path) . " doesn't have one");
			
			if ($xml === NULL)
				$child = $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" standalone=\"yes\"?><" . $entry["tag"] . "/>");
			else
				$child = $xml->addChild($entry["tag"], isset($entry["content"]) ? htmlspecialchars($entry["content"]) : NULL);
			
			foreach ($entry as $prop => $value) {
				if ($prop === "tag" || $prop === "content") continue;
				if (!is_string($prop)) {
					$pathLocal = $path;
					$pathLocal[] = $entry["tag"];
					if (!isset($value["tag"])) $pathLocal[] = $prop;
					$walker($value, $pathLocal, $child, $walker);
				} else if ($prop[0] === "@") $child->addAttribute(substr($prop, 1), $value);
				else throw new ArrayDumperException("Invalid entry prop: " . $prop . " at " . implode(".", $path));
			}
			return $xml;
		};
		
		// Start the recursive array traversing
		$xml = $walker($array[0], [], NULL, $walker);
		
		// Return the xml if we don't want a string
		if (!$asString) return $xml;
		
		// Format the output
		$xmlDocument = new DOMDocument('1.0', "utf-8");
		$xmlDocument->formatOutput = TRUE;
		$xmlDocument->preserveWhiteSpace = FALSE;
		$xmlDocument->loadXML($xml->asXML());
		return $xmlDocument->saveXML();
	}
}