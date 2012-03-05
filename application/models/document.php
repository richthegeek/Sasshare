<?php

class Document {

	public static function get($id) {
		if ($document = DB::table('documents')->where('id', '=', $id)->first()) {
			foreach ($document as $k=>$v) {
				if (is_numeric($v)) {
					$document->$k = 0 + $v;
				}
			}
		}

		return $document;
	}


	public static function create($parent, $info) {
		unset ($info['id']);
		$info['snippet_id'] = $parent;
		$info['created'] = $info['modified'] = time();

		return DB::table('documents')->insert_get_id($info);
	}

}
