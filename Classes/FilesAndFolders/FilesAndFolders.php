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


class FilesAndFolders {
	
	/**
	 * Removes the given directory or file recursively
	 *
	 * @param string $filename
	 */
	public static function remove(string $filename) {
		if (is_dir($filename)) {
			static::flushDirectory($filename);
			rmdir($filename);
		} else if (file_exists($filename))
			unlink($filename);
	}
	
	/**
	 * Removes all contents from a given directory without removing the element itself
	 * @param string $directory
	 */
	public static function flushDirectory(string $directory) {
		if (is_dir($directory)) {
			foreach (scandir($directory) as $child) {
				if ($child !== "." && $child !== "..")
					static::remove($directory . DIRECTORY_SEPARATOR . $child);
			}
		}
	}
	
	/**
	 * Wrapper to create directories recursively
	 *
	 * @param string $directory
	 * @param int    $mode
	 */
	public static function mkdir(string $directory, $mode = 0777){
		if(is_dir($directory)) return;
		mkdir($directory, $mode, true);
	}
}