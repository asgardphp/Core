<?php
namespace Asgard\Core;

class Config {
	protected $config = array();
	
	public function __construct($config=null) {
		if(is_string($config))
			$this->loadConfigDir($config);
		elseif(is_array($config))
			$this->load($config);
	}

	public function loadConfigDir($dir) {
		foreach(glob(_DIR_.$dir.'/*.php') as $filename)
			$this->loadConfigFile($filename);
		return $this;
	}
	
	public function loadConfigFile($filename) {
		$config = require $filename;
		if(isset($config['all']))
			$this->load($config['all']);
		if(defined('_ENV_') && isset($config[_ENV_]))
			$this->load($config[_ENV_]);
		return $this;
	}
	
	public function load(array $config) {
		foreach($config as $key=>$value)
			$this->set($key, $value);
		return $this;
	}
	
	public function set($str_path, $value) {
		\Asgard\Utils\Tools::string_array_set($this->config, $str_path, $value);
		return $this;
	}
	
	public function get($str_path) {
		return \Asgard\Utils\Tools::string_array_get($this->config, $str_path);
	}
}