<?php
class ORM_Helper_Feedback extends ORM_Helper {

	/**
	 * Name of field what marks record as "deleted"
	 * @var string
	 */
	protected $_safe_delete_field = 'delete_bit';

	public function save(array $values, Validation $validation = NULL)
	{
		parent::save($values, $validation);

		$config = ORM::factory('feedback_Config')
			->select('id', 'email', 'send_email')
			->where('page_id', '=', $this->_orm->page_id)
			->and_where('send_email', '=', 1)
			->and_where('email', '!=', '')
			->find();

		if ($config->loaded())
		{
			$site_name = 'No named site';
			$site = ORM::factory('site')->find();
			if ($site->loaded()) {
				$site_name = $site->name;
			}
			
			$message =	__('This message was send from feedback form')." {$site_name}\r\n".
						__('Date').' :'.date('Y-m-d H:i:s')."\r\n\r\n".
						$this->_orm->text."\r\n";
			Email::send(
				$config->email, 
				Kohana::$config->load('site.feedback_from'), 
				"[{$site_name}]: ".__('feedback message'), 
				$message
			);
		}
	}

}
