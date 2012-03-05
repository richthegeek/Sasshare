<?php

Route::get('snippets/vote/(:num)/(:any)', function($snippet_id, $direction) {
	if (!$user = Auth::user()) {
		return array(
			'error' => 'You must be logged in to vote',
		);
	}

	if ($direction != 'up' && $direction != 'down') {
		return array(
			'error' => 'Direction must be either "up" or "down"'
		);
	}

	$direction = ($direction == 'up' ? 1 : -1);

	# check for an existing vote.
	if ($vote = DB::table('votes')->where('user_id', '=', $user->id)->where('snippet_id', '=', $snippet_id)->first()) {
		if ($vote->direction == $direction) {
			return array(
				'warning' => 'You have already voted for this snippet',
			);
		}
		DB::table('votes')->where('user_id', '=', $user->id)->where('snippet_id', '=', $snippet_id)->update(array('direction' => $direction));
	}
	else {
		DB::table('votes')->insert(array(
			'user_id' => $user->id,
			'snippet_id' => $snippet_id,
			'direction' => $direction,
			'created' => time(),
		));
	}

	return Snippet::get($snippet_id);
});
