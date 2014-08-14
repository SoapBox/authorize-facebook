<?php namespace SoapBox\AuthorizeFacebook;

use Facebook\FacebookRedirectLoginHelper;

class RedirectLoginHelper extends FacebookRedirectLoginHelper {

	protected function storeState($state) {
		$store = FacebookStrategy::$store;

		if ($store == null) {
			parent::storeState($state);
		} else {
			$store('facebook.state', $state);
		}
	}

	protected function loadState() {
		$load = FacebookStrategy::$load;

		if ($load == null) {
			return parent::loadState();
		}
		return $this->state = $load('facebook.state');
	}

}
