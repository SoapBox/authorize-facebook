# Authorize-Facebook
[Authorize](http://github.com/soapbox/authorize) strategy for Facebook authentication.

## Getting Started
- Install [Authorize](http://github.com/soapbox/authorize) into your application
to use this Strategy.
- Configure a new Facebook application on https://developers.facebook.com/apps/
and keep a record of your `id` and `secret`

## Installation
Add the following to your `composer.json`
```
"require": {
	...
	"soapbox/authorize-facebook": "1.*",
	...
}
```

### app/config/app.php
Add the following to your `app.php`, note this will be removed in future
versions since it couples us with Laravel, and it isn't required for the library
to function
```
'providers' => array(
	...
	"SoapBox\AuthorizeFacebook\AuthorizeFacebookServiceProvider",
	...
)
```

## Usage

### Login
```php

use SoapBox\Authroize\Authenticator;
use SoapBox\Authorize\Exceptions\InvalidStrategyException;
...
$settings = [
	'id' => 'APPID',
	'secret' => 'APPSECRET',
	'redirect_url' => 'http://example.com/social/facebook/callback'
];

//If you already have an accessToken from a previous authentication attempt
$parameters = ['accessToken' => 'sometoken'];

$strategy = new Authenticator('facebook', $settings);

$bool = $strategy->authenticate($parameters);

```

### Endpoint
```php

use SoapBox\Authroize\Authenticator;
use SoapBox\Authorize\Exceptions\InvalidStrategyException;
...
$settings = [
	'id' => 'APPID',
	'secret' => 'APPSECRET',
	'redirect_url' => 'http://example.com/social/facebook/callback'
];

$strategy = new Authenticator('facebook', $settings);
$user = $strategy->getUser();

```
