<?php

class User extends Model {

	static $table = 'users';
	static $kill_keys = array('password');
	static $strip_keys = array('email', 'address', 'settings');

	public static function exists_username($username) {
		return self::process(DB::table(self::$table)->where_username($username)->first());
	}

	public static function exists_email($email) {
		return self::process(DB::table(self::$table)->where_email($email)->first());
	}

	public static function get($uid, $strip = TRUE) {
		$user = DB::table(self::$table)
		  ->where_username_or_id($uid, $uid)
		  ->first();
		return self::process($user, $strip);
	}

	public static function create($username, $email, $password) {
		return DB::table(self::$table)->insert_get_id(array(
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
		DB::table(self::$table)->where('id', '=', $id)->update($data);
	}
}
