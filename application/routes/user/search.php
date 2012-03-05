<?php

Route::get('user/search/(:any)', function($term) {
	$field = Input::get('field', 'username');
	$key = implode('-', array('user', 'search', $field, $term));

	if (!($results = Cache::get($key))) {
		$results = User::search($field, $term);
		Cache::put($key, $results, 10);
	}

	$count = Input::get('count', 20);
	$page = Input::get('page', 0);

	return array_slice($results, ($page * $count), $count);
});