<?php
class Minify
{
	public static function sanitize($buffer) {
		$search = array(
			'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
			'/[^\S ]+\</s',  // strip whitespaces before tags, except space
			'/(\s)+/s',       // shorten multiple whitespace sequences
			'/\t/s'
		);

		$replace = array(
			'>',
			'<',
			'\\1',
			''
		);

		$buffer = preg_replace($search, $replace, $buffer);

		return $buffer;
	}
}
?>