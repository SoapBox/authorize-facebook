<?php namespace SoapBox\AuthorizeFacebook;

use Facebook\FacebookRedirectLoginHelper;

class RedirectLoginHelper extends FacebookRedirectLoginHelper {

	protected function storeState($state) {
		if (FacebookStrategy::$store == null) {
			parent::storeState($state);
		} else {
			FacebookStrategy::$store('facebook.state', $state);
		}
	}

	protected function loadState() {
		if (FacebookStrategy::$load == null) {
			return parent::loadState();
		}
		return $this->state = FacebookStrategy::$load('facebook.state');
	}

}
