<?php namespace SoapBox\AuthorizeFacebook;

use Facebook\FacebookRedirectLoginHelper;
use SoapBox\AuthroizeFacebook\FacebookStrategy;

class RedirectLoginHelper extends FacebookRedirectLoginHelper {

	protected function storeState($state) {
		if (FacebookStrategy::$store == null) {
			parent::storeState($state);
		}
		FacebookStrategy::$store('facebook.state', $state);
	}

	protected function loadState() {
		if (FacebookStrategy::$load == null) {
			return parent::loadState();
		}
		return $this->state = FacebookStrategy::$load('facebook.state');
	}

}
