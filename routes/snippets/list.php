<?php

Route::get('snippets/list',  array('before' => 'cache', 'after' => 'cache', function() {
	$count = (int) Input::get('count', 10);
	$page = (int) Input::get('page', 0);
	
	$order = Input::get('order', 'created');
	$order_fields = array('votes_up', 'votes_down', 'votes_total', 'created', 'updated');
	if (!in_array($order, $order_fields)) {
		return array(
			'error' => 'You can only order snippets by the following fields: ' . implode(', ', $order_fields)
		);
	}
	if (substr($order, 0, 5) != 'votes') {
		$order = 'snippets.' . $order;
	}

	$snippets = DB::query("SELECT snippets.*,
			(SELECT COUNT(*) FROM votes WHERE snippet_id = snippets.id AND direction > 0) as votes_up,
			(SELECT COUNT(*) FROM votes WHERE snippet_id = snippets.id AND direction < 0) as votes_down,
			(SELECT SUM(direction) FROM votes WHERE snippet_id = snippets.id) as votes_total
		FROM snippets
		ORDER BY $order DESC
		LIMIT " . ($page * $count) . ", " . $count);

	foreach ($snippets as $i => $snippet) {
		foreach ($snippet as $k => $v) {
			if (is_numeric($v)) {
				$snippets[$i]->$k = 1 * $v;
			}
		}
		$snippets[$i]->votes_total = (int) $snippet->votes_total;
		$snippets[$i]->documents = Snippet::documents($snippet->id, 100);
	}

	return $snippets;
}));
