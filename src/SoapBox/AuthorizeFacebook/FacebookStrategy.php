<?php namespace SoapBox\AuthorizeFacebook;

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use SoapBox\Authorize\Helpers;
use SoapBox\Authorize\Exceptions\AuthenticationException;
use SoapBox\Authorize\Strategies\SingleSignOnStrategy;

class FacebookStrategy extends SingleSignOnStrategy {

	/**
	 * Initializes the FacebookSession with our id and secret
	 *
	 * @param array $settings array('id' => string, 'secret' => string)
	 */
	public function __construct($settings = array()) {
		if (!isset($settings['id']) || !isset($settings['secret'])) {
			throw new Exception(
				'Both id and secret are required to use the facebook login. (http://developers.facebook.com/apps)'
			);
		}
		FacebookSession::setDefaultApplication($settings['id'], $settings['secret']);
	}

	/**
	 * Used to authenticate our user through one of the various methods.
	 *
	 * @param array parameters array('access_token' => string,
	 *	'redirect_url' => string)
	 *
	 * @throws AuthenticationException If the provided parameters do not
	 *	successfully authenticate.
	 *
	 * @return User A mixed array repreesnting the authenticated user.
	 */
	public function login($parameters = array()) {
		if (!isset($parameters['access_token']) && !isset($parameters['redirect_url'])) {
			throw new Exception('You must provide either an access_token or redirect_url');
		}
		if (isset($parameters['redirect_url'])) {
			$helper = new FacebookRedirectLoginHelper($parameters['redirect_url']);
			$loginUrl = $helper->getLoginUrl();
			Helpers::redirect($loginUrl);
		}

		$session = new FacebookSession($parameters['access_token']);
		$request = (new FacebookRequest($session, 'GET', '/me'))->execute();
		return $request->getGraphObject();
	}

	/**
	 * Used to handle tasks after login. This could include retrieving our users
	 * token after a successful authentication.
	 *
	 * @return array Mixed array of the tokens and other components that
	 *	validate our user.
	 */
	public function endpoint() {
		$helper = new FacebookRedirectLoginHelper();

		try {
			return $helper->getSessionFromRedirect();
		} catch (FacebookRequestException $ex) {
			throw new AuthenticationException();
		} catch (\Exception $ex) {
			throw new AuthenticationException();
		}

		throw new AuthenicationException();
	}

}
