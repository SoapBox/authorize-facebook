<?php namespace SoapBox\AuthorizeFacebook;

use Facebook\FacebookRedirectLoginHelper;

class RedirectLoginHelper extends FacebookRedirectLoginHelper {

	private $session;
	private $router;

	public function __construct($url, Session $session, Router $router) {
		$this->session = $session;
		$this->router = $router;
		parent::construct($url);
	}

	protected function storeState($state) {
		$this->session->put('facebook.state', $state);
	}

	protected function loadState() {
		return $this->state = $this->session->get('facebook.state');
	}

	public function redirect($scope) {
		$this->router->redirect($this->getLoginUrl($scope));
	}

}
