<?php

Route::get('user/info', function() {
	if (!($user = Auth::user())) {
		return FALSE;
	}

	return User::get($user->id);
});

Route::get('user/info/(:any)', function($username) {
	// load based on username OR id
	if ($username == 'me') {
		return Redirect::to('user/info');
	}
	if ($user = User::get($username)) {
		unset ($user->email, $user->settings);
		return $user;
	}

	return Response::error('404');
});

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
