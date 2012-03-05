<?php

Route::get('documents', function() {
	$path = URL::current();

	return array(
		'GET ' . $path . '/:id' => 'Retrieve data about a specific document',
		'POST ' . $path => 'Create a new document',
		'POST ' . $path . '/:id' => 'Update a document',
		'DELETE ' . $path . '/:id' => 'Delete a document',
	);
});

Route::get('documents/(:num)', function($id) {
	if ($doc = Document::get($id)) {
		return $doc;
	}
	return Redirect::error('404');
});
