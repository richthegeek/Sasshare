<?php

Route::get('snippets/create', function() {
	if (!$user = Auth::user()) {
		return array(
			'error' => 'You must be logged in to create a snippet.'
		);
	}

	$input = Input::all();
	$rules = array(
		'title' => 'required|max:255',
	);
	$validated = Validator::make($input, $rules);

	if ($validated->fails()) {
		return array(
			'error' => $validated->errors
		);
	}

	if ($id = Snippet::create($user->id, $input['title'], @$input['description'])) {
		return Redirect::to('snippets/' . $id);
	}
});

