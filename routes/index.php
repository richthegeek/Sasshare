<?php

Route::get('/', function() {
	return array(
		'GET user' => 'List available actions on users',
		'GET snippets' => 'List available actions on snippets',
		'GET documents' => 'List avaialble actions on documents',
	);
});

?>
