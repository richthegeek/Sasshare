<?php

@include 'snippets.php';

Route::get('user/info, user/info/me', function() {
	if (!($user = Auth::user())) {
		return FALSE;
	}
	return User::get($user->id, false);
});

Route::get('user/info/(:any)',  array('before' => 'cache', 'after' => 'cache', function($username) {
	// load based on username OR id
	if ($user = Auth::user() && Auth::user()->username == trim($username)) {
		return Redirect::to('user/info/me');
	}
	if ($user = User::get($username, true)) {
		return $user;
	}

	return Response::error('404');
}));

Route::post('user/info', function() {
	if (!$user = Auth::user()) {
		return FALSE;
	}
	$input = Input::all();

	// if we aren't changing the email or password, stop them from being validated
	if (@$input['email'] == $user->email) {
		unset ($input['email']);
	}
	if (@$input['password'] == $user->password) {
		unset ($input['password']);
	}

	foreach ($input as $k => $v) {
		if (trim($v) == '') {
			unset ($input[$k]);
		}
	}

	$rules = array(
		'email' => 'email|unique:users',
		'password' => 'confirmed',
		'name' => 'alpha_space',
		'homepage' => 'url',
		'twitter' => 'match:a-z_\,i,i',
	);

	$validated = Validator::make($input, $rules);

	if ($validated->fails()) {
		return array(
			'error' => $validated->errors
		);
	}

	return User::update($user->id, $input);
});
