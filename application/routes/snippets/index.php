<?php

Route::get('snippets', function() {
	$path = URL::current();

	return array(
		'GET ' . $path . '/list' => 'List the latest snippets',
		'GET ' . $path . '/:id' => 'Get information about a specific snippet',
		'POST ' . $path . '/vote/:id/up,down' => 'Vote for a snippet',
		'POST ' . $path . '/create' => 'Create a new snippet',
		'POST ' . $path . '/:id' => 'Update a snippet',
		'DELETE ' . $path . '/:id' => 'Delete a snippet'
	);
});

Route::get('snippets/(:any)',  array('before' => 'cache', 'after' => 'cache', function($id) {
	if ($snip = Snippet::get($id)) {
		return $snip;
	}
	return Redirect::error('404');
}));
