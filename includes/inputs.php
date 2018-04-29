<?php

class Inputs {

	public static function existes($type = 'post') {
		/*
		 * this function is to check is data as bean post or get
		 */
		switch ($type) {
			case 'post' :
				return (!empty($_POST)) ? TRUE : FALSE;
				break;
			case 'get' :
				return (!empty($_GET)) ? TRUE : FALSE;
				break;
			default :
				return false;
				break;
		}

	}

	public static function get($item) {

		if (isset($_POST[$item])) {

			return $_POST[$item];
		} else if (isset($_GET[$item])) {

			return $_GET[$item];

		}
		return '';
	}


}
?>