<?php

Route::get('documents/create', function() {
	if (!$user = Auth::user()) {
		return array(
			'error' => 'You must be logged in to create a document.'
		);
	}

	$input = Input::all();
	$rules = array(
		'snippet_id' => 'required|exists:snippets,id',
		'title' => 'required|max:255',
		'syntax' => 'alpha',
		'data' => 'required'
	);
	$validated = Validator::make($input, $rules);

	if ($validated->fails()) {
		return array(
			'error' => $validated->errors
		);
	}

	$snippet = Snippet::get($input['snippet_id']);
	if (isset($snippet->documents[$input['title']])) {
		return array(
			'error' => array('title' => 'This snippet already has a document of that name')
		);
	}

	if (!isset($input['syntax'])) {
		$input['syntax'] = substr($input['title'], strpos($input['title'], '.') + 1);
	}

	if ($id = Document::create($input['snippet_id'], $input)) {
		return Redirect::to('documents/' . $id);
	}
});

