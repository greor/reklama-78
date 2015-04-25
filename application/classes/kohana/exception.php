<?php defined('SYSPATH') or die('No direct access');
class Kohana_Exception extends Kohana_Kohana_Exception {
	/**
	 * Inline exception handler, displays the error message, source of the
	 * exception, and the stack trace of the error.
	 *
	 * @uses    Kohana_Exception::text
	 * @param   object   exception object
	 * @return  boolean
	 */
	public static function handler(Exception $e)
	{
		if ((Kohana::DEVELOPMENT === Kohana::$environment OR Kohana::$is_cli))
		{
			return parent::handler($e);
		}
		else
		{
			try
			{
				Kohana::$log->add(Log::ERROR, parent::text($e));

				$attributes = array
				(
					'action'  => 500,
					'message' => rawurlencode($e->getMessage())
				);
				// чтобы для админки вызывался свой контроллер ошибок
				$route_name = 'error';
				if (Request::initial()->route() !== NULL)
				{
					$admin_config = Kohana::$config->load('admin/admin')->as_array();
					if (in_array(Route::name(Request::initial()->route()), $admin_config['admin_part_route_names']))
					{
						$route_name = 'admin_error';
					}
				}

				if ($e instanceof HTTP_Exception)
				{
					$attributes['action'] = $e->getCode();
				}
				// Clean the output buffer
				ob_get_level() and ob_clean();

				// Start an output buffer
				ob_start();

				// Error sub-request.
				echo Request::factory(Route::get($route_name)->uri($attributes))
					->execute()
					->send_headers()
					->body();
				// Display the contents of the output buffer
				echo ob_get_clean();

				exit();
			}
			catch (Exception $e)
			{
				// Clean the output buffer if one exists
				ob_get_level() and ob_clean();

				// Display the exception text
				echo parent::text($e);

				// Exit with an error status
				exit(1);
			}
		}
	}
} // End Kohana_Exception
