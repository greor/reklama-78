<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Modules_Home extends Controller_Front {

	public $template = 'modules/home/content';

	public function action_index() {
		
		$this->template
			->set('promo', $this->_get_promo());
		
	}
	
	private function _get_promo()
	{
		$_date = date('Y-m-d H:i:s');
		$_elements = ORM::factory('promo')
			->where('public_date', '<=', $_date)
			->and_where_open()
				->where('hidden_date', '>', $_date)
				->or_where('hidden_date', '=', '0000-00-00 00:00:00')
			->and_where_close()
			->find_all();
		
		
		$elements = array();
		$helper = ORM_Helper::factory('promo');
		$url_base = URL::base();
		foreach ($_elements as $_row) {
			$_item = $_row->as_array();
			if ( ! empty($_item['background'])) {
				$_item['background'] = $url_base.Thumb::uri('promo_1920x635', $helper->file_uri('background', $_item['background']));
			}
			if ( ! empty($_item['image'])) {
				$_item['image'] = $url_base.Thumb::uri('promo_786x449', $helper->file_uri('image', $_item['image']));
			} 
			$_item['settings'] = @unserialize($_item['settings']);
			$_item['settings'] = empty($_item['settings']) ? array() : $_item['settings'];
			
			$elements[] = $_item;
		}
		
		if ( ! empty($elements)) {
			$this->switch_on_plugin('revolutionslider');
		}
		
		return $elements;
	}

}