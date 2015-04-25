<?php defined('SYSPATH') or die('No direct access');
class Kohana_Exception extends Kohana_Kohana_Exception {

	public static $frontend_error_view = 'error';

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
		if (Kohana::DEVELOPMENT === Kohana::$environment OR Kohana::$is_cli)
		{
			return parent::handler($e);
		}

		try
		{
			// Get the exception information
			$type    = get_class($e);
			$code    = $e->getCode();
			$message = strip_tags($e->getMessage());
			$file    = Debug::path($e->getFile());
			$line    = $e->getLine();

			if ($e instanceof ErrorException)
			{
				if (isset(Kohana_Exception::$php_errors[$code]))
				{
					// Use the human-readable error name
					$code = Kohana_Exception::$php_errors[$code];
				}
			}

			// Create a text version of the exception
			$error = "{$type} [{$code}]: {$message} ~ {$file} [{$line}]";

			if (is_object(Kohana::$log))
			{
				// Add this exception to the log
				Kohana::$log->add(Log::ERROR, $error);

				// Make sure the logs are written
				Kohana::$log->write();
			}

			// Clean the output buffer
			ob_get_level() and ob_clean();

			// Make sure the proper http header is sent
			$http_header_status = ($e instanceof HTTP_Exception) ? $e->getCode() : 500;

			// Make correct title
			$title = isset(Response::$messages[$http_header_status]) ? Response::$messages[$http_header_status] : $type;

			if ( ! headers_sent())
			{

				header('Content-Type: '.Kohana_Exception::$error_view_content_type.'; charset='.Kohana::$charset, TRUE, $http_header_status);
			}

			if (Request::$current !== NULL AND Request::current()->is_ajax() === TRUE)
			{
				// Just display the text of the exception
				echo "\n{$type} [{$code}]: {$message}\n";

				// Exit with an error status
				exit(1);
			}
			else
			{
				// Start an output buffer
				ob_start();

				// Include the exception HTML
				if ($view_file = Kohana::find_file('views', Kohana_Exception::$frontend_error_view.'/'.$http_header_status))
				{
					// Include custom exception view
					include $view_file;
				}
				if ($view_file = Kohana::find_file('views', Kohana_Exception::$frontend_error_view))
				{
					// Include common exception view
					include $view_file;
				}
				else
				{
					throw new Kohana_Exception('Error view file does not exist: views/:file', array(
						':file' => Kohana_Exception::$frontend_error_view,
					));
				};

				// Display the contents of the output buffer
				echo ob_get_clean();

				// Exit with an error status
				exit(1);
			}

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
} // End Kohana_Exception
