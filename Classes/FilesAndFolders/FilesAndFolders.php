<?php
/**
 * Copyright 2019 LABOR.digital
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
 * Last modified: 2019.02.22 at 18:51
 */

namespace Labor\Helferlein\Php\FilesAndFolders;


use Labor\Helferlein\Php\Options\Options;

class FilesAndFolders {
	
	/**
	 * Removes the given directory or file recursively
	 *
	 * @param string $filename
	 */
	public static function remove(string $filename) {
		if (is_dir($filename)) {
			static::flushDirectory($filename);
			@rmdir($filename);
		} else if (file_exists($filename))
			@unlink($filename);
	}
	
	/**
	 * Removes all contents from a given directory without removing the element itself
	 *
	 * @param string $directory
	 */
	public static function flushDirectory(string $directory) {
		if (is_dir($directory))
			foreach (static::directoryIterator($directory, TRUE) as $child) {
				if ($child->isDir()) rmdir($child->getPathname());
				else unlink($child->getPathname());
			}
	}
	
	/**
	 * Wrapper to create directories recursively
	 *
	 * @param string $directory
	 * @param int    $mode
	 */
	public static function mkdir(string $directory, $mode = 0777) {
		if (is_dir($directory)) return;
		mkdir($directory, $mode, TRUE);
	}
	
	/**
	 * Helper to create a directory iterator (I always forget the syntax -.-)
	 * Dots will automatically be skipped
	 *
	 * @param string $directory The directory to iterate
	 * @param bool   $recursive default: false | If set to true the directory will be iterated recursively
	 * @param array  $options   Additional configuration options:
	 *                          - fileRegex (string) default: "" | Optional Regex pattern the returned files have to
	 *                          match
	 *                          - folderFirst (bool) default: FALSE | By default the folder is returned after it's
	 *                          contents. If you set this to true, the folder will be returned first
	 *
	 * @return \Iterator|\RecursiveIteratorIterator|\FilesystemIterator|\SplFileInfo[]
	 */
	public static function directoryIterator(string $directory, bool $recursive = FALSE, array $options = []): \Iterator {
		$options = Options::make($options, [
			"fileRegex"   => [
				"type"    => "string",
				"default" => "",
			],
			"folderFirst" => [
				"type"    => "bool",
				"default" => FALSE,
			],
		]);
		
		// Check if we got a directory
		if (!is_dir($directory)) return new \EmptyIterator();
		
		// Create the iterator
		$it = $recursive ?
			new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
				$options["folderFirst"] ? \RecursiveIteratorIterator::SELF_FIRST : \RecursiveIteratorIterator::CHILD_FIRST) :
			new \FilesystemIterator($directory);
		
		// Apply a file regex if required
		if (!empty($options["fileRegex"])) return new \RegexIterator($it, $options["fileRegex"]);
		
		// Done
		return $it;
	}
}