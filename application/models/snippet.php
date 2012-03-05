<?php

class Snippet {

	public static function get($id) {
		if ($snippet = DB::table('snippets')->where('id', '=', $id)->first()) {
			foreach ($snippet as $k=>$v) {
				if (is_numeric($v)) {
					$snippet->$k = 0 + $v;
				}
			}
			$snippet->documents = Snippet::documents($id);
			$snippet->votes = Snippet::votes($id);
		}

		return $snippet;
	}

	public static function documents($id, $truncate = FALSE) {
		$return = array();
		$documents = DB::table('documents')->where('snippet_id', '=', $id)->get();
		foreach ($documents as $doc) {
			unset ($doc->snippet_id);

			$doc->truncated = false;
			if ($truncate && strlen($doc->data) > $truncate) {
				$doc->truncated = true;
				$doc->data = substr($doc->data, 0, $truncate);
			}

			$return[$doc->title] = $doc;
		}
		return $return;
	}

	public static function votes($id) {
		$votes = DB::table('votes')->where('snippet_id', '=', $id)->get();
		$result = (object) array('up' => 0, 'down' => 0, 'total' => 0);
		foreach ($votes as $vote) {
			if ($vote->direction > 0) {
				$result->up++;
				$result->total++;
			}
			else {
				$result->down++;
				$result->total--;
			}
		}
		return $result;
	}

	public static function create($author, $title, $description = '') {
		return DB::table('snippets')->insert_get_id(array(
			'user_id' => $author,
			'title' => $title,
			'description' => $description,
			'created' => time()
		));
	}
}
