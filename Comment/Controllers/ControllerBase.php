<?php

namespace Bcore\Comment\Controllers;

use Phalcon\Mvc\Controller;
use Bcore\Helper\Translate as TranslateHelper;

class ControllerBase extends Controller
{
	public function initialize()
	{
		$siteName = $this->config->application->siteName;
		$webTitle = $this->config->application->webTitle;
		$this->view->setVars([
			'siteName' => $siteName,
			'title'    => $webTitle,
		]);

	}

}
