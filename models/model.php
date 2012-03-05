<?php

class Model {

	public static $table = '';
	public static $kill_keys = array('password');
	public static $strip_keys = array();

	public static function exists($id) {
		return self::process(DB::table(static::$table)->where_id($id)->first());
	}

	public static function search($field, $value) {
		self::search_cache();
		self::search_function($value);
		self::search_function(null, $field);

		$data = DB::table(static::$table)->where($field, 'LIKE', $value . '%')->take(1000)->get();
		foreach ($data as $k => $v) {
			$data[$k] = static::process($v);
		}

		usort($data, array('self', 'search_function'));
		return $data;
	}

	private static function search_function($a, $b = null) {
		static $value = FALSE;
		static $key = FALSE;
		if (!$b) {
			return ($value = $a);
		}
		if (!$a) {
			return ($key = $b);
		}
		$a = $a->$key;
		$b = $b->$key;
		$a = self::search_cache($a) ? self::search_cache($a) : self::search_cache($a, levenshtein($value, $a));
		$b = self::search_cache($b) ? self::search_cache($b) : self::search_cache($b, levenshtein($value, $b));
		return $a < $b ? -1 : 1;
	}

	private static function search_cache($key = null, $value = null) {
		static $cache = array();
		if (!$key) {
			$cache = array();
		} else {
			return ($value ? $cache[$key] = $value : @$cache[$key]);
		}
	}

	public static function process($object, $strip = TRUE) {
		if (!$object) {
			return $object;
		}
		foreach (static::$kill_keys as $key) {
			unset($object->$key);
		}
		if ($strip) {
			foreach (static::$strip_keys as $key) {
				unset($object->$key);
			}
		}

		foreach ($object as $k => $v) {
			if (is_numeric($v)) {
				$object->$k = 1 * $v;
			}
			else if ($v = @json_decode($v)) {
				$object->$k = self::process($v);
			}
		}
		return $object;
	}
}
