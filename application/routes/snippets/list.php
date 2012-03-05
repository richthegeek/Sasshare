<?php

Route::get('snippets/list', function() {
	// TODO : pagination
	$snippets = DB::query("SELECT snippets.*,
			(SELECT COUNT(*) FROM votes WHERE snippet_id = snippets.id AND direction > 0) as votes_up,
			(SELECT COUNT(*) FROM votes WHERE snippet_id = snippets.id AND direction < 0) as votes_down,
			(SELECT SUM(direction) FROM votes WHERE snippet_id = snippets.id) as votes_total
		FROM snippets
			JOIN documents ON snippets.id = documents.snippet_id
		ORDER BY snippets.created DESC");

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
});
