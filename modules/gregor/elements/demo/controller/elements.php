<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Modules_Elements extends Controller_Front {

	const SESSION_KEY = 'program_like';
	const ORM_NAME = 'element';
	const LIKE_ORM_NAME = 'element_Like';
	const CONFIG_NAME = '_elements';
	
	protected $page_id = 1;
	
	public function action_like()
	{
		if ( ! $this->check_refferer())
			throw new HTTP_Exception_500;

		if ( ! $this->request->is_ajax())
			throw new HTTP_Exception_404;
		
		$element_id = (int) Request::current()->post('id');
		$vote = (int) Request::current()->post('vote');
		$vote = ($vote >= 1) ? 1 : 0;
		
		$session_like = Session::instance()->get(self::SESSION_KEY, array());
		$last_action = (int) Arr::get($session_like, $element_id, 0);

		$return = array();
		if ( $last_action != $vote ) {
			$orm = ORM::factory(self::ORM_NAME)
				->select('id', 'like', 'page_id', 'site_id', 'name' )
				->where('id', '=', $element_id)
				->and_where('page_id', '=', $this->page_id)
				->find();

			if ( ! $orm->loaded())
				throw new HTTP_Exception_404;

			$config = Kohana::$config
				->load(self::CONFIG_NAME);
			$orm_like = ORM::factory(self::LIKE_ORM_NAME)
				->where('element_id', '=', $element_id)
				->and_where('ip', '=', Request::$client_ip)
				->and_where('user_agent', '=', Request::$user_agent)
				->and_where('expires', '>', time())
				->find();

			$process = TRUE;
			if ($orm_like->loaded()) {
				if ( $vote == 0 AND $orm_like->count > 0 ) {
					$orm_like->count = $orm_like->count - 1;
				} elseif ( $vote != 0 AND $last_action != 0 ) {
					// do nothing
				} elseif ( $orm_like->count < $config['max_like_count'] AND $vote != 0 ) {
					$orm_like->count = $orm_like->count + 1;
				} else {
					$process = $return['success'] = FALSE;
					$return['error'] = 'Высокая активность с вашего ip. Попробуйте проголосовать позже.';
				}
			} else {
				$orm_like->values(array(
					'element_id' => $element_id,
					'ip' => Request::$client_ip,
					'user_agent' => Request::$user_agent,
					'expires' => time() + $config['expires'],
				));
			}

			if ($process) {
				$orm->like = (int) $orm->like + $vote - $last_action;

				try {
					$orm_like->save();
					$orm->save();
				} catch (Exception $e) {
					Kohana::$log->add(
						Log::ERROR,
						'File: :file. Exception occurred: :exception',
						array(
							':file'      => __FILE__.':'.__LINE__,
							':exception' => $e->getMessage()
						)
					);
				}
				$return['success'] = TRUE;
				$return['message'] = 'You vote was successed';
				$session_like[ $element_id ] = $vote;
			}
		} else {
			$return['success'] = FALSE;
			$return['code'] = 'already_voted';
			$return['error'] = 'Ты уже голосовал за эту программу!';
		}

		Session::instance()->set(self::SESSION_KEY, $session_like);
		Ku_AJAX::send('json', $return);
	}
} 
