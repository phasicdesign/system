<?php return [

/*
|--------------------------------------------------------------------------
| Settings - Hard Code WP Options
|--------------------------------------------------------------------------
|
*/
	'blogname'           => 'App Name',
	'blogdescription'    => 'description of app',
	'users_can_register' => 1,

	'theme_my_login' => [
	    'enable_css' => false,
	    'login_type' => 'email',
	    'active_modules' => [
	        'security/security.php',
	        'themed-profiles/themed-profiles.php'
	    ],
	    'version' => '6.4.9'
	],

	'theme_my_login_security' => [
	    'private_site' => false,
	    'private_login' => false,
	    'failed_login' => [
	        'threshold' => 5,
	        'threshold_duration' => 1,
	        'threshold_duration_unit' => 'hour',
	        'lockout_duration' => 24,
	        'lockout_duration_unit' => 'hour'
	    ]
	],

	'theme_my_login_themed_profiles' => [
	    'developer'     => ['theme_profile' => true,'restrict_admin' => false],
	    'administrator' => ['theme_profile' => true,'restrict_admin' => false],
	    'editor'        => ['theme_profile' => true,'restrict_admin' => true],
	    'author'        => ['theme_profile' => true,'restrict_admin' => true],
	    'contributor'   => ['theme_profile' => true,'restrict_admin' => true],
	    'subscriber'    => ['theme_profile' => true,'restrict_admin' => true
	    ]
	],

];
