<?php

Route::get('user', function() {
	$path = URL::current();

	return array(
		'POST ' . $path . '/login' => 'Login existing user account',
		'POST ' . $path . '/logout' => 'Logout current user',
		'POST ' . $path . '/create' => 'Create a new user',
		'GET  ' . $path . '/info' => 'Information about current user',
		'GET  ' . $path . '/info/:username' => 'Information about a specific user',
		'POST ' . $path . '/info' => 'Edit current user information'
	);
});

Route::get('user/login', function() {
	if (Auth::check() || Auth::attempt(Input::get('username', Input::get('email')), Input::get('password'))) {
		return Redirect::to('user/info');
	}

	return FALSE;
});

Route::get('user/logout', function() {
	Auth::logout();
	return TRUE;
});
