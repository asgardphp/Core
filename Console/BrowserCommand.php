<?php
namespace Asgard\Core\Console;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class BrowserCommand extends \Asgard\Console\Command {
	protected $name = 'browser';
	protected $description = 'Execute an HTTP request';

	protected function execute() {
		$method = $this->input->getArgument('method');
		$url = $this->input->getArgument('url');
		
		$headers = $this->input->getOption('h') ? json_decode($this->input->getOption('h')):[];
		$post = $this->input->getOption('p') ? json_decode($this->input->getOption('p')):[];
		$session = $this->input->getOption('ss') ? json_decode($this->input->getOption('ss')):[];
		$server = $this->input->getOption('sr') ? json_decode($this->input->getOption('sr')):[];
		$cookies = $this->input->getOption('c') ? json_decode($this->input->getOption('c')):[];
		$body = $this->input->getOption('b');
		$files = $this->input->getOption('f') ? json_decode($this->input->getOption('f')):[];
		if($files) {
			$files = json_decode($files);
			foreach($files as $k=>$v)
				$files[$k] = new \Asgard\Http\HttpFile($v['path'], $v['name'], $v['size'], $v['error']);
		}

		$browser = new \Asgard\Http\Browser\Browser($this->getAsgard());
		$browser->getCookies()->setAll($cookies);
		$browser->getSession()->setAll($session);
		$response = $browser->req($url, $method, $post, $files, $body, $headers, $server);

		if($this->input->getOption('showAll') || $this->input->getOption('showCode'))
			$this->output->writeln('Code: '.($response->isOK() ? '<info>':'<error>').$response->getCode().($response->isOK() ? '</info>':'</error>'));
		if($this->input->getOption('showAll') || $this->input->getOption('showContent'))
			$this->output->writeln($response->getContent());
		if($this->input->getOption('showAll') || $this->input->getOption('showSession')) {
			$this->output->writeln('Session:');
			$this->output->writeln(json_encode($browser->getSession()->all(), JSON_PRETTY_PRINT));
		}
		if($this->input->getOption('showAll') || $this->input->getOption('showCookies')) {
			$this->output->writeln('Cookies');
			$this->output->writeln(json_encode($browser->getCookies()->all(), JSON_PRETTY_PRINT));
		}
		if($this->input->getOption('showAll') || $this->input->getOption('showHeaders')) {
			$this->output->writeln('Headers');
			$this->output->writeln(json_encode($response->getHeaders(), JSON_PRETTY_PRINT));
		}
	}

	protected function getOptions() {
		return [
			['showAll', null, InputOption::VALUE_NONE, 'Show the whole response'],
			['showSession', null, InputOption::VALUE_NONE, 'Show response session'],
			['showCookies', null, InputOption::VALUE_NONE, 'Show response cookies'],
			['showHeaders', null, InputOption::VALUE_NONE, 'Show response headers'],
			['showCode', null, InputOption::VALUE_NONE, 'Show response code'],
			['showContent', null, InputOption::VALUE_NONE, 'Show response content'],
			['h', null, InputOption::VALUE_OPTIONAL, 'Headers'],
			['p', null, InputOption::VALUE_OPTIONAL, 'Post data'],
			['f', null, InputOption::VALUE_OPTIONAL, 'Files'],
			['ss', null, InputOption::VALUE_OPTIONAL, 'Session data'],
			['sr', null, InputOption::VALUE_OPTIONAL, 'Server data'],
			['c', null, InputOption::VALUE_OPTIONAL, 'Cookies'],
			['b', null, InputOption::VALUE_OPTIONAL, 'Body'],
		];
	}

	protected function getArguments() {
		return [
			['method', InputArgument::REQUIRED, 'The HTTP method'],
			['url', InputArgument::REQUIRED, 'The HTTP url'],
		];
	}
}