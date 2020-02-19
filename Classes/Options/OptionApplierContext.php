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
 * Last modified: 2019.09.30 at 20:25
 */

namespace Labor\Helferlein\Php\Options;


class OptionApplierContext {
	/**
	 * The list of errors that occurred while running the applier
	 * @var array
	 */
	public $errors = [];
	
	/**
	 * The given list of options from the outside world
	 * @var array
	 */
	public $options;
	
	/**
	 * Local cache to avoid duplicate definition generation
	 * @var array
	 */
	public $preparedDefinitions = [];
}