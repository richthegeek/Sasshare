<?php

class User {

	public static function exists($id) {
		return DB::table('users')->where('id', '=', $id)->first();
	}

	public static function exists_username($username) {
		return DB::table('users')->where('username', '=', $username)->first();
	}

	public static function exists_email($email) {
		return (DB::table('users')->where('email', '=', $email)->first());
	}

	public static function get($uid) {
		$user = DB::table('users')
		  ->where('username', '=', $uid)
		  ->or_where('id', '=', $uid)
		  ->first();

		if ($user) {
			foreach ($user as $k => $v) {
				if (is_numeric($v)) {
					$user->$k = 1 * $v;
				}
				else if ($v = @json_decode($v)) {
					$user->$k = $v;
				}
			}
			unset ($user->password);
			return $user;
		}
	}


	public static function create($username, $email, $password) {
		return DB::table('users')->insert(array(
			'id' => $username,
			'email' => $email,
			'password' => Hash::make($password),
			'points_up' => 0,
			'points_down' => 0,
			'info' => json_encode(new stdClass)
		));
	}

	public static function update($id, $info) {
		$data = array();

		// remove unmodifiable data
		unset ($info['id'], $info['points_up'], $info['points_down']);

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
}
