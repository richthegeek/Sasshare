<?php

class Snippet extends Model {

	static $table = 'snippets';

	public static function get($id) {
		if ($snippet = DB::table(static::$table)->where('id', '=', $id)->first()) {
			foreach ($snippet as $k=>$v) {
				if (is_numeric($v)) {
					$snippet->$k = 0 + $v;
				}
			}
			$snippet->documents = Snippet::documents($id);
			$snippet->votes = Snippet::votes($id);
			$snippet->syntax = Snippet::get_primary_syntax($snippet);
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

	public static function process($object, $strip = TRUE) {
		$object->documents = Snippet::documents($object->id);
		$object->votes = Snippet::votes($object->id);
		$object->syntax = Snippet::get_primary_syntax($object);
		return parent::process($object, $strip);
	}

	public static function get_primary_syntax($snippet) {
		if (!isset($snippet->documents)) {
			$snippet->documents = self::documents($snippet->id, 1);
		}
		$syntax = array();
		foreach ($snippet->documents as $doc) {
			if ($doc->syntax) {
				$syntax += array($doc->syntax => 0);
				$syntax[$doc->syntax]++;
			}
		}
		asort($syntax);
		return current(array_keys($syntax));
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
		return DB::table(static::$table)->insert_get_id(array(
			'user_id' => $author,
			'title' => $title,
			'description' => $description,
			'created' => time(),
			'updated' => time(),
		));
	}
}
