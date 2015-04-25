<?php defined('SYSPATH') OR die('No direct access allowed.');

class Kohana_Ku_AJAX {

	/**
	 * @var boolean Use or not iframe transport for response
	 */
	public static $iframe = FALSE;

	/**
	 * @var string Callback or string template to wrap response
	 *             when iframe transport used
	 */
	public static $iframe_wrapper;

	/**
	 * Set body and headers for "json" type of response
	 *
	 * @param mixed  $data
	 * @param string $namespace
	 */
	public static function json($data = NULL, $namespace = NULL)
	{
		$request = Request::current();

		if ($data === NULL)
		{
			$data = $request->response()->body();
		}

		$request->response()->body(json_encode(
			$namespace === NULL
			? $data
			: array($namespace => $data)
		));

		Ku_AJAX::headers('json');
	}

	/**
	 * Set body and headers for "xml" type of response
	 *
	 * @param mixed $data
	 */
	public static function xml($data = NULL)
	{
		if ($data !== NULL)
		{
			Request::current()->response()->body((string) $data);
		}

		self::headers('xml');
	}

	/**
	 * Set body and headers for "html" type of response
	 *
	 * @param mixed $data
	 */
	public static function html($data = NULL)
	{
		if ($data === NULL)
		{
			$data = Request::current()->response()->body();
		}

		$data = (string) $data;

		Request::current()->response()->body($data);

		Ku_AJAX::headers('html');
	}

	/**
	 * Set body and headers for "text" type of response
	 *
	 * @param mixed $data
	 */
	public static function text($data = NULL)
	{
		if ($data === NULL)
		{
			$data = Request::current()->response()->body();
		}

		$data = (string) $data;

		Request::current()->response()->body($data);

		Ku_AJAX::headers('text');
	}

	/**
	 * Set AJAX response headers for specified mime type
	 *
	 * @param string  $mime Mime type of response
	 * @param boolean $cleanup Remove or not existing response headers
	 */
	public static function headers($mime, $cleanup = TRUE)
	{
		if ( ! headers_sent())
		{
			$response = Request::current()->response();

			if ($cleanup === TRUE)
			{
				// Remove any existing headers from response
				$response->headers(array());
			}

			$response->headers('Last-Modified', gmdate('D, d M Y H:i:s').' GMT');

			if (Ku_AJAX::$iframe === TRUE)
			{
				self::_wrap_response();
				// Force iframe mode
				$mime = 'html';
			}

			if ($mime == 'json')
			{
				$response->headers('Content-Transfer-Encoding', '8bit');
			}

			$mime = Kohana::$config->load('mimes.'.$mime);

			$response->headers('Content-Type', $mime[0].'; charset='.Kohana::$charset);
			$response->headers('Content-Length', strlen($response->body()));
			$response->headers('Cache-Control', 'no-store, no-cache, must-revalidate');
			$response->headers('Pragma', 'no-cache');
		}
	}

	/**
	 * Send AJAX response and exit
	 *
	 * @param string $type Type of sended response (json|xml|html|text)
	 * @param mixed  $data Data to send as response
	 */
	public static function send($type, $data = NULL)
	{
		$args = func_get_args();
		$args = array_slice($args, 1);
		call_user_func_array(array('Ku_AJAX', $type), $args);
		exit(Request::current()->response()->send_headers()->body());
	}

	/**
	 * Get/set current iframe flag
	 *
	 * @param boolean $value
	 * @return boolean Ku_AJAX::$iframe
	 */
	public static function iframe($value = NULL)
	{
		if ($value === NULL)
		{
			return Ku_AJAX::$iframe;
		}
		else
		{
			Ku_AJAX::$iframe = $value;
		}
	}

	/**
	 * Wrap response to iframe wrapper, e.g.
	 *   if
	 *       Ku_AJAX::$iframe_wrapper = '<textarea>%s</textarea>'
	 *   when
	 *    Request::current()->response()->body(json_encode(array('foo'=>'bar')))
	 *        will sending "<textarea>{foo:'bar'}</textarea>"
	 *    Request::current()->response()->body("bla-bla-bla")
	 *        will sending  "<textarea>bla-bla-bla</textarea>"
	 *
	 */
	protected static function _wrap_response()
	{
		if (Ku_AJAX::$iframe_wrapper)
		{
			if (is_callable(Ku_AJAX::$iframe_wrapper))
			{
				Request::current()->response()->body(call_user_func(Ku_AJAX::$iframe_wrapper, Request::current()->response()->body()));
			}
			else
			{
				Request::current()->response()->body(sprintf(Ku_AJAX::$iframe_wrapper, Request::current()->response()->body()));
			}
		}
	}
} // End Ku_AJAX