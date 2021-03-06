<?php namespace SoapBox\AuthorizeFacebook;

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use SoapBox\AuthorizeFacebook\RedirectLoginHelper;
use SoapBox\Authorize\Helpers;
use SoapBox\Authorize\User;
use SoapBox\Authorize\Contact;
use SoapBox\Authorize\Exceptions\AuthenticationException;
use SoapBox\Authorize\Strategies\SingleSignOnStrategy;

class FacebookStrategy extends SingleSignOnStrategy {

	/**
	 * The url to redirect the user to after they have granted permissions on
	 * facebook.
	 */
	private $redirectUrl = '';

	/**
	 * An array of the permissions we require for the application.
	 */
	private $scope = array('email', 'user_friends');

	/**
	 * Callable store method
	 */
	public static $store = null;

	/**
	 * Callable load method
	 */
	public static $load = null;

	/**
	 * Initializes the FacebookSession with our id and secret
	 *
	 * @param array $settings array('id' => string, 'secret' => string)
	 * @param callable $store A callback that will store a KVP (Key Value Pair).
	 * @param callable $load A callback that will return a value stored with the
	 *	provided key.
	 */
	public function __construct($settings = array(), $store = null, $load = null) {
		if (!isset($settings['id']) || !isset($settings['secret']) || !isset($settings['redirect_url'])) {
			throw new \Exception(
				'redirect_url, id, and secret are required to use the facebook login. (http://developers.facebook.com/apps)'
			);
		}
		if (isset($settings['scope'])) {
			$this->scope = array_merge($this->scope, $settings['scope']);
		}
		$this->redirectUrl = $settings['redirect_url'];

		if ($store != null && $load != null) {
			FacebookStrategy::$store = $store;
			FacebookStrategy::$load = $load;
		} else {
			session_start();
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
		$helper = new RedirectLoginHelper($this->redirectUrl);

		if(isset($parameters['access_token'])) {
			$session = new FacebookSession($parameters['access_token']);
		} else {
			$session = $helper->getSessionFromRedirect();
		}

		if (!isset($session)) {
			Helpers::redirect($helper->getLoginUrl($this->scope));
		}

		return $this->getUser(['accessToken' => $session->getToken()]);
	}

	/**
	 * Used to retrieve the user from the strategy.
	 *
	 * @param array parameters The parameters required to authenticate against
	 *	this strategy. (i.e. accessToken)
	 *
	 * @throws AuthenticationException If the provided parameters do not
	 *	successfully authenticate.
	 *
	 * @return User A mixed array representing the authenticated user.
	 */
	public function getUser($parameters = array()) {
		if (!isset($parameters['accessToken'])) {
			throw new AuthenticationException();
		}

		$session = new FacebookSession($parameters['accessToken']);

		$request = (new FacebookRequest($session, 'GET', '/me?fields=id,email,first_name,last_name'))->execute();
		$response = $request->getGraphObject();

		$user = new User;
		$user->id = $response->getProperty('id');
		$user->email = $response->getProperty('email');
		$user->accessToken = $parameters['accessToken'];
		$user->firstname = $response->getProperty('first_name');
		$user->lastname = $response->getProperty('last_name');

		return $user;
	}

	/**
	 * Used to retrieve the friends of this user that are also using this app
	 *
	 * @param array parameters The parameters required to authenticate against
	 *	this strategy. (i.e. accessToken)
	 *
	 * @throws AuthenticationException If the provided parameters do not
	 *	successfully authenticate.
	 *
	 * @return array A list of userId's that are friends of this user.
	 */
	public function getFriends($parameters = array()) {
		if (!isset($parameters['accessToken'])) {
			throw new AuthenticationException();
		}

		$session = new FacebookSession($parameters['accessToken']);
		//change this to me/taggable_friends if you want all friends (will require facebook to verify your app)
		$request = (new FacebookRequest($session, 'GET', '/me/friends'))->execute();

		$friends = [];

		foreach ($request->getGraphObject()->getProperty('data')->asArray() as $data) {
			$friend = new Contact;
			$friend->id = $data->id;
			$friend->displayName = $data->name;
			$friend->displayPicture = $data->picture->data->url;
			$friends[] = $friend;
		}

		return $friends;
	}

	/**
	 * Used to handle tasks after login. This could include retrieving our users
	 * token after a successful authentication.
	 *
	 * @return array Mixed array of the tokens and other components that
	 *	validate our user.
	 */
	public function endpoint($parameters = array()) {
		return $this->login($parameters);
	}

}
