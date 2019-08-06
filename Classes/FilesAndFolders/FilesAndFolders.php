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


use EmptyIterator;
use FilesystemIterator;
use Iterator;
use Labor\Helferlein\Php\Options\Options;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

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
		clearstatcache();
	}
	
	/**
	 * Removes all contents from a given directory without removing the element itself
	 *
	 * @param string $directory
	 */
	public static function flushDirectory(string $directory) {
		if (is_dir($directory))
			foreach (static::directoryIterator($directory, TRUE) as $child) {
				if (!file_exists($child->getRealPath())) continue;
				if ($child->isDir()) @rmdir($child->getRealPath());
				else @unlink($child->getRealPath());
			}
		clearstatcache();
	}
	
	/**
	 * Wrapper to create directories recursively
	 *
	 * @param string $directory
	 * @param int    $mode
	 */
	public static function mkdir(string $directory, $mode = 0777) {
		if (is_dir($directory) || file_exists($directory)) return;
		@mkdir($directory, $mode, TRUE);
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
	public static function directoryIterator(string $directory, bool $recursive = FALSE, array $options = []): Iterator {
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
		if (!is_dir($directory)) return new EmptyIterator();
		
		// Create the iterator
		$it = $recursive ?
			new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
				$options["folderFirst"] ? RecursiveIteratorIterator::SELF_FIRST : RecursiveIteratorIterator::CHILD_FIRST) :
			new FilesystemIterator($directory);
		
		// Apply a file regex if required
		if (!empty($options["fileRegex"])) return new RegexIterator($it, $options["fileRegex"]);
		
		// Done
		return $it;
	}
	
	/**
	 * Returns the unix file permissions for a given file like 0777 as a string.
	 *
	 * @param string $filename The name of the file to get the permissions for
	 *
	 * @return string
	 * @throws \Labor\Helferlein\Php\FilesAndFolders\FilesAndFoldersException
	 */
	public static function getPermissions(string $filename): string {
		if (!file_exists($filename))
			throw new FilesAndFoldersException("Could not get the permissions of file: " . $filename .
				" because the file does not exist!");
		
		// Convert the permissions to a readable string
		$perms = fileperms($filename);
		if ($perms === FALSE)
			throw new FilesAndFoldersException("Could not get the permissions of file: " . $filename .
				" because: " . error_get_last());
		
		// Format and be done
		return substr(sprintf("%o", $perms), -4);
	}
	
	/**
	 * Can be used to set the unix permissions for a file or folder
	 *
	 * @param string $filename  The name of the file to set the permissions for
	 * @param string $mode      The unix permissions to set like 0777
	 * @param bool   $recursive default TRUE: FALSE if directories should NOT be traversed recursively
	 *
	 * @throws \Labor\Helferlein\Php\FilesAndFolders\FilesAndFoldersException
	 */
	public static function setPermissions(string $filename, string $mode, bool $recursive = TRUE) {
		if (stripos(PHP_OS, "win") === 0) return;
		
		if (!file_exists($filename))
			throw new FilesAndFoldersException("Could not set the permissions of file: " . $filename .
				" because the file does not exist! Permissions are: " . static::getPermissions($filename));
		
		// Set the permissions
		$result = @chmod($filename, octdec(str_pad($mode, 4, 0, STR_PAD_LEFT)));
		if ($result === FALSE)
			throw new FilesAndFoldersException("Could not set the permissions for file: " . $filename .
				" because: " . error_get_last()["message"] . "! Permissions are: " . static::getPermissions($filename));
		
		// Handle recursion
		if ($recursive && is_dir($filename))
			foreach (static::directoryIterator($filename, FALSE, ["folderFirst"]) as $file)
				static::setPermissions($file->getPathname(), $mode, $recursive);
	}
	
	/**
	 * Returns the numeric unix user group for the given filename
	 *
	 * @param string $filename
	 *
	 * @return int
	 * @throws \Labor\Helferlein\Php\FilesAndFolders\FilesAndFoldersException
	 */
	public static function getGroup(string $filename): int {
		if (!file_exists($filename))
			throw new FilesAndFoldersException("Could not get the group of file: " . $filename .
				" because the file does not exist! Permissions are: " . static::getPermissions($filename));
		
		$group = @filegroup($filename);
		if ($group === FALSE)
			throw new FilesAndFoldersException("Could not get the group of file: " . $filename .
				" because: " . error_get_last()["message"] . " Permissions are: " . static::getPermissions($filename));
		return $group;
	}
	
	/**
	 * Can be used to update the group of a given file or folder
	 *
	 * @param string     $filename  The file to set the group for
	 * @param string|int $group     The unix group to set for the file
	 * @param bool       $recursive default TRUE: FALSE if directories should NOT be traversed recursively
	 *
	 * @throws \Labor\Helferlein\Php\FilesAndFolders\FilesAndFoldersException
	 */
	public static function setGroup(string $filename, $group, bool $recursive = TRUE) {
		if (stripos(PHP_OS, "win") === 0) return;
		
		if (!file_exists($filename))
			throw new FilesAndFoldersException("Could not set the group of file: " . $filename .
				" because the file does not exist!");
		if (!is_string($group) && !is_int($group))
			throw new FilesAndFoldersException("The group has to be either a string, or an integer! " .
				gettype($group) . " given");
		
		// Update the group
		$result = @chgrp($filename, is_numeric($group) ? (int)$group : $group);
		if ($result === FALSE)
			throw new FilesAndFoldersException("Could not set the group for file: " . $filename .
				" because: " . error_get_last()["message"]);
		
		// Handle recursion
		if ($recursive && is_dir($filename))
			foreach (static::directoryIterator($filename, FALSE, ["folderFirst"]) as $file)
				static::setGroup($file->getPathname(), $group, $recursive);
	}
	
	/**
	 * A wrapper around file_get_contents which reads the contents, but handles unreadable or non existing
	 * files with speaking exceptions.
	 *
	 * @param string $filename
	 *
	 * @return string
	 * @throws \Labor\Helferlein\Php\FilesAndFolders\FilesAndFoldersException
	 */
	public static function readFile(string $filename): string {
		
		// Make sure we can read the file
		if (!is_readable($filename)) {
			if (!file_exists($filename))
				throw new FilesAndFoldersException("Could not read file: " . $filename . " because it does not exist!");
			throw new FilesAndFoldersException("Could not read file: " . $filename .
				" - Permission denied! Permissions: " . static::getPermissions($filename));
		}
		
		// Try to read the file
		$result = @file_get_contents($filename);
		if ($result === FALSE)
			throw new FilesAndFoldersException("Could not read file: " . $filename . " because: " . error_get_last()["message"]);
		return $result;
	}
	
	/**
	 * A wrapper around file() which handles non existing, or unreadable files with speacing exceptions.
	 *
	 * @param string $filename
	 * @param null   $flags
	 * @param null   $context
	 *
	 * @return array
	 * @throws \Labor\Helferlein\Php\FilesAndFolders\FilesAndFoldersException
	 * @see \file()
	 */
	public static function readFileAsLines(string $filename, $flags = NULL, $context = NULL): array {
		// Make sure we can read the file
		if (!is_readable($filename)) {
			if (!file_exists($filename))
				throw new FilesAndFoldersException("Could not read file: " . $filename . " because it does not exist!");
			throw new FilesAndFoldersException("Could not read file: " . $filename .
				" - Permission denied! Permissions: " . static::getPermissions($filename));
		}
		
		// Read lines
		$lines = file($filename, $flags, $context);
		if ($lines === FALSE)
			throw new FilesAndFoldersException("Could not read file: " . $filename . " because: " . error_get_last()["message"]);
		return $lines;
	}
	
	/**
	 * A simple wrapper around file_put_contents, but handles non-writable or broken
	 * files with speaking exceptions.
	 *
	 * @param string $filename
	 * @param string $content
	 * @param int    $flags
	 *
	 * @throws \Labor\Helferlein\Php\FilesAndFolders\FilesAndFoldersException
	 * @see \file_put_contents()
	 */
	public static function writeFile(string $filename, string $content, int $flags = 0) {
		// Make sure we can write the file
		if (file_exists($filename) && !is_writable($filename))
			throw new FilesAndFoldersException("Could not write file: " . $filename .
				" - Permission denied! Permissions: " . static::getPermissions($filename));
		
		// Try to write file with save guard
		$tmpFileName = $filename . ".writing." . md5(microtime(TRUE) . rand(0, 99999999)) . ".txt";
		$result = @file_put_contents($tmpFileName, $content, $flags);
		if ($result) $result = @rename($tmpFileName, $filename);
		if ($result) return;
		else @unlink($tmpFileName);
		
		// Dump the content using the normal way
		$result = @file_put_contents($filename, $content, $flags);
		if (!$result)
			throw new FilesAndFoldersException("Could not write file: " . $filename . " because: " . error_get_last()["message"]);
		
	}
}