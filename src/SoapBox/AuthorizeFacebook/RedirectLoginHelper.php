<?php namespace SoapBox\AuthorizeFacebook;

use SoapBox\Authorize\Session;
use SoapBox\Authorize\Router;
use Facebook\FacebookRedirectLoginHelper;

class RedirectLoginHelper extends FacebookRedirectLoginHelper {

	/**
	 * The session that can be used to store session data between
	 * requests / redirects
	 *
	 * @var Session
	 */
	private $session;

	/**
	 * The router that can be used to redirect the user between views
	 *
	 * @var Router
	 */
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
