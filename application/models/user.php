<?php

class User {

	public static function exists($id) {
		return self::process(DB::table('users')->where_id($id)->first());
	}

	public static function exists_username($username) {
		return self::process(DB::table('users')->where_username($username)->first());
	}

	public static function exists_email($email) {
		return self::process(DB::table('users')->where_email($email)->first());
	}

	public static function get($uid, $strip = TRUE) {
		$user = DB::table('users')
		  ->where_username_or_id($uid, $uid)
		  ->first();
		return self::process($user, $strip);
	}


	private static function process($object, $strip = TRUE) {
		if (!$object) {
			return $object;
		}

		unset ($object->password);
		if ($strip) {
			unset($object->email, $object->address, $object->settings);
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


	public static function create($username, $email, $password) {
		return DB::table('users')->insert_get_id(array(
			'username' => $username,
			'email' => $email,
			'password' => Hash::make($password),
			'info' => json_encode(new stdClass)
		));
	}

	public static function update($id, $info) {
		$data = array();

		// remove unmodifiable data
		unset ($info['id']);

		// move the primary data to the top level
		foreach (array('username', 'email', 'password') as $key) {
			if (isset($info[$key])) {
				$data[$key] = $info[$key];
				unset($info[$key]);
			}
		}

		// hash the password, if set
		if (isset($data['password'])) {
			$data['password'] = Hash::make($data['password']);
		}

		// move all secondary data into the json blob
		$data['info'] = json_encode($info);

		// write
		DB::table('users')->where('id', '=', $id)->update($data);
	}

	public static function search($field, $value) {
		self::search_cache();
		self::search_function($value);
		self::search_function(null, $field);

		$data = DB::table('users')->where($field, 'LIKE', $value . '%')->get();
		foreach ($data as $k => $v) {
			$data[$k] = self::process($v);
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
}
