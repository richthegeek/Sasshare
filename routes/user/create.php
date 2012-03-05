<?php

Route::get('user/create', function() {
	if (Auth::check()) {
		return FALSE;
	}

	$input = Input::all();
	$rules = array(
		'username' => 'required|unique:users',
		'email' => 'required|email|unique:users',
		'password' => 'required|min:5',
	);
	$validate = Validator::make($input, $rules);

	if ($validate->fails()) {
		return array(
			'error' => $validate->errors
		);
	}

	if ($user_id = User::create($input['username'], $input['email'], $input['password'])) {
		Auth::login($user_id);
		return Redirect::to('user/info');
	}

	return array(
		'error' => 'An unknown error has occurred'
	);
});
