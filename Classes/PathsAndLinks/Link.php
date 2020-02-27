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

namespace Neunerlei\Helferlein\Php\PathsAndLinks;


use Neunerlei\Helferlein\Php\Inflector\Inflector;
use Neunerlei\Helferlein\Php\Options\Options;

class Link {
	
	/**
	 * @var string|null
	 */
	protected $scheme;
	
	/**
	 * @var string|null
	 */
	protected $host;
	
	/**
	 * @var string|null
	 */
	protected $port;
	
	/**
	 * @var string|null
	 */
	protected $user;
	
	/**
	 * @var string|null
	 */
	protected $pass;
	
	/**
	 * @var string|null
	 */
	protected $path;
	
	/**
	 * @var array
	 */
	protected $query = [];
	
	/**
	 * @var string|null
	 */
	protected $fragment;
	
	/**
	 * Link constructor.
	 *
	 * @param array $config
	 *
	 * @throws \Neunerlei\Helferlein\Php\PathsAndLinks\InvalidLinkException
	 */
	public function __construct(array $config = []) {
		foreach (["scheme", "host", "port", "user", "pass", "path", "fragment"] as $key) {
			if (!empty($config[$key])) {
				if (!is_string($config[$key]))
					throw new InvalidLinkException("The given link option: " . $key . " has to be a string!");
				$setter = Inflector::toSetter($key);
				$this->$setter($config[$key]);
			}
		}
		if (!empty($config["query"])) {
			if (!is_array($config["query"]))
				throw new InvalidLinkException("The given link option: query has to be an array!");
			$this->setQuery($config["query"]);
		}
	}
	
	/**
	 * @return string|null
	 */
	public function getScheme(): ?string {
		return $this->scheme;
	}
	
	/**
	 * @param string|null $scheme
	 *
	 * @return Link
	 */
	public function setScheme(string $scheme): Link {
		$this->scheme = strtolower($scheme);
		return $this;
	}
	
	/**
	 * @return string|null
	 */
	public function getHost(): ?string {
		return $this->host;
	}
	
	/**
	 * @param string|null $host
	 *
	 * @return Link
	 */
	public function setHost(string $host): Link {
		$this->host = $host;
		return $this;
	}
	
	/**
	 * @return string|null
	 */
	public function getPort(): ?string {
		return $this->port;
	}
	
	/**
	 * @param string|null $port
	 *
	 * @return Link
	 */
	public function setPort(string $port): Link {
		$this->port = $port;
		return $this;
	}
	
	/**
	 * @return string|null
	 */
	public function getUser(): ?string {
		return $this->user;
	}
	
	/**
	 * @param string|null $user
	 *
	 * @return Link
	 */
	public function setUser(string $user): Link {
		$this->user = $user;
		return $this;
	}
	
	/**
	 * @return string|null
	 */
	public function getPass(): ?string {
		return $this->pass;
	}
	
	/**
	 * @param string|null $pass
	 *
	 * @return Link
	 */
	public function setPass(string $pass): Link {
		$this->pass = $pass;
		return $this;
	}
	
	/**
	 * Returns the folder path of the link
	 * @return string|null
	 */
	public function getPath(): ?string {
		return (string)$this->path;
	}
	
	/**
	 * Can be used to set the folder path of the link
	 *
	 * @param string|null $path
	 *
	 * @return Link
	 */
	public function setPath(string $path): Link {
		$this->path = "/" . trim(ltrim(trim($path), "/"));
		return $this;
	}
	
	/**
	 * Returns the query data as an array
	 * @return array
	 */
	public function getQuery(): array {
		return $this->query;
	}
	
	/**
	 * Resets the complete query data
	 *
	 * @param array|null $query
	 *
	 * @return Link
	 */
	public function setQuery(?array $query): Link {
		$this->query = $query;
		return $this;
	}
	
	/**
	 * Adds a single key/value pair to the query list
	 *
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return \Neunerlei\Helferlein\Php\PathsAndLinks\Link
	 */
	public function addToQuery(string $key, $value): Link {
		$this->query[$key] = $value;
		return $this;
	}
	
	/**
	 * Removes a single key/value pair from the query list
	 *
	 * @param string $key
	 *
	 * @return \Neunerlei\Helferlein\Php\PathsAndLinks\Link
	 */
	public function removeFromQuery(string $key): Link {
		unset($this->query[$key]);
		return $this;
	}
	
	/**
	 * Returns the fragment/anchor tag of the link
	 *
	 * @return string|null
	 */
	public function getFragment(): ?string {
		return $this->fragment;
	}
	
	/**
	 * Sets the fragment/anchor tag of the link
	 *
	 * @param string|null $fragment
	 *
	 * @return Link
	 */
	public function setFragment(string $fragment): Link {
		$this->fragment = trim(ltrim(trim($fragment), "#"));
		return $this;
	}
	
	/**
	 * Merges two link instances into each other and returns the resulting link instance.
	 * It will only override not existing values with the given link!
	 *
	 * @param \Neunerlei\Helferlein\Php\PathsAndLinks\Link $link
	 *
	 * @return \Neunerlei\Helferlein\Php\PathsAndLinks\Link
	 */
	public function mergeWith(Link $link): Link {
		$l = new self();
		foreach ([$this, $link] as $o)
			foreach (get_object_vars($o) as $k => $v)
				if (empty($l->$k)) $l->$k = $v;
		return $l;
	}
	
	/**
	 * Combines the configured link into a string version
	 *
	 * @param array $options Options determining the output format
	 *                       - hostOnly (bool): If set to true the method will only return the host and potential port
	 *                       - relative (bool): If set to true the method will return a relative url, without the full
	 *                       hostname
	 *
	 * @return string
	 */
	public function build(array $options = []): string {
		// Prepare options
		$options = Options::make($options, [
			"hostOnly" => [
				"type"    => ["boolean"],
				"default" => FALSE,
			],
			"relative" => [
				"type"    => ["boolean"],
				"default" => FALSE,
			],
		]);
		
		// Build url
		$url = "";
		if (!$options["relative"] && !empty($this->host)) {
			if (!empty($this->scheme)) $url .= $this->scheme . "://";
			if (!empty($this->user)) {
				$url .= $this->user;
				if (!empty($this->pass)) $url .= ":" . $this->pass;
				$url .= "@";
			}
			$url .= $this->host;
			if (!empty($this->port)) $url .= ":" . $this->port;
			if ($options["hostOnly"]) return $url;
		}
		$url .= "/";
		if (!empty($this->path)) $url .= trim(ltrim(trim($this->path), "/"));
		if (!empty($this->query)) $url .= "?" . http_build_query($this->query);
		if (!empty($this->fragment)) $url .= "#" . $this->fragment;
		return $url;
	}
	
	/**
	 * To string will result in the link being build
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->build();
	}
}