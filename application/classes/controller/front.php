<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Front extends Controller_Template {

	/* Common config */
	protected $config = 'site';

	/* Page templates */
	protected $layout = 'layout/template';
	protected $layout_ajax = 'layout/template-ajax';

	/* Template vars */
	protected $body_class = '';
	protected $title;
	protected $breadcrumbs = array();
	protected $breadcrumbs_title = '';
	protected $page_meta;
	protected $page_header = array();
	protected $open_graph = array();
	protected $open_fileds = array( 'title', 'type', 'image', 'description', 'url', 'site_name' );
	protected $title_delimiter = ' | ';
	
	protected $_css = array();
	protected $_js = array();
	protected $plugins = array();
	protected $without_layout = FALSE;

	protected $media;
	protected $no_img = 'images/no_img.png';
	protected $_0_gif = 'images/0.gif';

	/* Internal vars */
	protected $site;
	
	protected $page_id;
	protected $_menu = NULL;

	/* Cache settings */
	protected $ttl = 60;

	public function before()
	{
		if ($this->auto_render === TRUE) {
			$template = $this->template;
			$this->template = NULL;
			parent::before();
			$this->template = View_Theme::factory($template);
		} else {
			parent::before();
		}

		if ( ! Helper_Module::check_module('gregor-main')) {
			$message = "'gregor-main' module is missing.(".__FILE__.':'.__LINE__.')';
			Log::instance()->add(Log::ERROR, $message);
			echo 'Internal error (start before).';
			die;
		}

		$this->site = ORM::factory('site')
			->find()
			->as_array();
		
		$this->config = Kohana::$config
			->load($this->config)->as_array();

		$this->media = '/media/'.$this->config['theme'].'/';
		$this->no_img = trim($this->media.$this->no_img, '/');
		$this->_0_gif = $this->media.$this->_0_gif;

		if ( $this->request->page !== NULL ) {
			$this->page_id = $this->request->page['id'];
		}
	}

	public function after()
	{
		$this->_generate_plugins();
		
		View::set_global('BODY_CLASS', $this->body_class);
		View::bind_global('TITLE', $this->title);
		View::bind_global('PAGE_META', $this->page_meta);
		View::set_global('MEDIA', $this->media);
		View::set_global('CSS', $this->_get_css());
		View::set_global('JS', $this->_get_js());
		View::set_global('IS_AJAX', (bool) $this->request->is_ajax());
		View::set_global('CONFIG', $this->config);
		View::set_global('NO_IMG', $this->no_img );
		View::set_global('_0_GIF', $this->_0_gif );
		View::set_global('PAGE_ID', $this->page_id );
		View::set_global('SITE', $this->site);
		View::set_global('BREADCRUMBS', $this->breadcrumbs);
		View::set_global('BREADCRUMBS_TITLE', $this->breadcrumbs_title);

		if (Request::current()->is_initial()) {
			if ($this->auto_render === TRUE AND ! $this->without_layout) {
				$this->render_layout();
			}
// 			$this->set_cache_headers();
		}

		parent::after();

	}
	
	protected function render_layout()
	{
		$this->generate_og_tags();
		if ($this->request->is_initial()) {
			$this->page_meta = Arr::extract($this->site, array( 
				'title_tag', 
				'keywords_tag', 
				'description_tag'
			 ));
			$this->set_page_meta($this->get_page_meta());
			if ( ! empty($this->page_meta['title_tag'])) {
				if ( empty($this->title) ) {
					$this->title = $this->page_meta['title_tag'];
				} elseif ( strpos($this->title, $this->page_meta['title_tag']) === FALSE ) {
					$this->title = $this->title.$this->title_delimiter.$this->page_meta['title_tag'];
				}
			}
		}

		$content = $this->template
			->render();
		$data = array(
			'content' => $content,
			'menu' => $this->get_top_menu()
		);
		if ( ! $this->request->is_ajax()) 	{
			$data['page_header'] = $this->page_header;
			$data['og_tags'] = $this->open_graph;
			$this->template = View_Theme::factory($this->layout, $data);
		} else {
			$this->template = View_Theme::factory($this->layout_ajax, $data);
		}
	}
	
	protected function set_cache_headers()
	{
		if ($this->ttl) {
			$this->response
			->headers('cache-control', 'public, max-age='.$this->ttl)
			->headers('expires', gmdate('D, d M Y H:i:s', time()+$this->ttl).' GMT');
		} else {
			$this->response
			->headers('cache-control', 'max-age=0, must-revalidate, public')
			->headers('expires', gmdate('D, d M Y H:i:s', time()).' GMT');
		}
	}
	
	protected function remove_escape_sequenced($string)
	{
		return str_replace( array("\r\n","\r","\n","\t") , '', $string);
	}
	
	private function generate_og_tags()
	{
		$url_base = URL::base(TRUE);
		foreach ($this->open_fileds as $name) {
			if ( ! empty($this->open_graph[ $name ])) {
				$this->open_graph[$name] = $this->open_graph[ $name ];
				continue;
			}
			switch ($name) {
				case 'title':
					$this->open_graph['title'] = $this->title;
					break;
				case 'type':
					$this->open_graph['type'] = 'website';
					break;
				case 'image':
					$this->open_graph['image'] = $url_base.$this->media.'images/logo_social.jpg';
					break;
				case 'description':
					if (empty($this->site['description_tag']))
						continue 2;
					$this->open_graph['description'] = $this->site['description_tag'];
					break;
				case 'url':
					$_query = $this->request->query();
					$_query = empty($_query) ? '' : '?'.http_build_query($_query);
					$this->open_graph['url'] = $url_base.$this->request->uri().$_query;
					break;
				case 'site_name':
					if (empty($this->site['name']))
						continue 2;
					$this->open_graph['site_name'] = $this->site['name'];
					break;
			}
		}
	}
	
	protected function og_extract_site() {
		if (empty($this->site))
			throw new HTTP_Exception_404();
		foreach ($this->open_fileds as $name) {
			switch ($name) {
				case 'description':
					if (empty($this->site['description_tag']))
						continue 2;
					$this->open_graph['description'] = $this->site['description_tag'];
					break;
				case 'site_name':
					if (empty($this->site['name']))
						continue 2;
					$this->open_graph['site_name'] = $this->site['name'];
					break;
			}
		}
	}

	protected function set_page_meta(array $meta = NULL)
	{
		if ( ! empty($meta)) {
			if ( ! empty($meta['title_tag'])) {
				$this->page_meta['title_tag'] = $meta['title_tag'];
			}
			if ( ! empty($meta['keywords_tag'])) {
				$this->page_meta['keywords_tag'] = $meta['keywords_tag'];
			}
			if ( ! empty($meta['description_tag'])) {
				$this->page_meta['description_tag'] = $meta['description_tag'];
			}
		}
	}

	protected function get_page_meta()
	{
		return $this->load_meta('page', $this->page_id);
	}

	protected function load_meta($model_name, $id)
	{
		if ($id) {
			$meta = ORM::factory($model_name)
				->select('title_tag', 'keywords_tag', 'description_tag')
				->where('id', '=', $id)
				->find()
				->as_array();

			return array_filter($meta, 'strlen');
		}
		return array();
	}

	protected function extract_meta(array $values)
	{
		$meta = Arr::extract($values, array('title_tag', 'keywords_tag', 'description_tag'), '');
		return array_filter($meta, 'strlen');
	}

	protected function merge_meta(array $meta_1, array $meta_2)
	{
		if (empty($meta_2['title_tag'])) {
			$meta_2['title_tag'] = '';
		}
		return array_merge($meta_1, $meta_2);
	}

	protected function check_refferer()
	{
		$referrer = str_replace(array( 'http://', 'https://', 'www.'), '', Request::current()->referrer());
		$site_name = str_replace(array( 'http://', 'https://', 'www.'), '', URL::base(TRUE));
		return strpos($referrer, $site_name) === 0;
	}

	protected function get_top_menu()
	{
		if ($this->_menu !== NULL)
			return $this->_menu;

		if ( ! DONT_USE_CACHE) {
			try {
				$this->_menu = Cache::instance('file-struct')
					->get('top-menu');
			}
			catch (ErrorException $e) {};
		}

		if( $this->_menu === NULL ) {
			$page_statuses = Kohana::$config
				->load('_pages.status_codes');

			$pages = ORM::factory('page')
				->select('id', 'parent_id', 'uri', 'title', 'level', 'position', 'name', 'type', 'data')
				->where('status', '=', $page_statuses['active'])
				->and_where('level', '<', 3)
				->order_by('level', 'asc')
				->order_by('position', 'asc')
				->limit(8)
				->find_all();

			$this->_menu = $this->_parse_menu_item($pages);

			if ( ! DONT_USE_CACHE) {
				try {
					Cache::instance('file-struct')
						->set('top-menu', $this->_menu, Date::DAY);
				}
				catch (ErrorException $e) {};
			}
		}

		return $this->_menu;
	}

	private function _parse_menu_item($pages)
	{
		$return = array();

		$current_url = Request::current()->uri();
		foreach ($pages as $item) {
			$return_item = array(
				'name' => $item->name,
				'title' => $item->title,
				'target' => '_self',
				'is_active' => '',
				'is_dynamic' => FALSE,
			);
			if ( $item->type == 'url' AND strpos($item->data, '//') !== FALSE) {
				$return_item['target'] = '_blank';
			}
			$return_item['sub'] = array();
			
			// тут добавляем элементы "подменю"
			
			if ( $item->type == 'url' ) {
				$return_item['uri'] = $item->data;
				$return[ $item->id ] = $return_item;
			} elseif (isset( $return[ $item->parent_id ] )) {
				$return_item['uri'] = $return[ $item->parent_id ]['uri'].'/'.$item->uri;
				$return_item['is_page'] = TRUE;
				$return[ $item->parent_id ]['sub'][ $item->id ] = $return_item;
			} elseif ( $item->parent_id == 0 ) {
				$return_item['uri'] = $item->uri;
				$return[ $item->id ] = $return_item;
			}
			
			
// 			var_dump($current_url, $return_item); die;
		}

		return $return;
	}
	
	private function _get_css()
	{
		$css = array();
		foreach ($this->_css as $_css) {
			if (strpos($_css, '/media/') !== 0) {
				$_css = $this->media.$_css;
			}
			$css[] = $_css;
		}
		return $css;
	}
	
	private function _get_js()
	{
		$js = array();
		foreach ($this->_js as $_js) {
			if (strpos($_js, '/media/') !== 0) {
				$_js = $this->media.$_js;
			}
			$js[] = $_js;
		}
		return $js;
	}
	
	private function _generate_plugins()
	{
		$plugins = $this->_tpl_plugins();
		foreach ($plugins as $_name => $_conf) {
			if ( ! in_array($_name, $this->plugins))
				continue;
			
			if (array_key_exists('css', $_conf)) {
				$this->_css = array_merge($this->_css, $_conf['css']);
			}
			if (array_key_exists('js', $_conf)) {
				$this->_js = array_merge($this->_js, $_conf['js']);
			}
		}
	}
	
	protected function switch_on_plugin($name)
	{
		if ( ! in_array($name, $this->plugins)) {
			$this->plugins[] = $name;
		}
	}
	
	private function _tpl_plugins()
	{
		return array(
			'fancybox' => array(
				'css' => array('assets/js/fancybox/jquery.fancybox.css'),
				'js' => array('assets/js/fancybox/jquery.fancybox.pack.js'),
			),
			'revolutionslider' => array(
				'css' => array('assets/js/revolutionslider/css/settings.css'),
				'js' => array('assets/js/revolutionslider/js/jquery.themepunch.tools.min.js', 'assets/js/revolutionslider/js/jquery.themepunch.revolution.min.js'),
			),
			'bxslider' => array(
				'css' => array('assets/js/bxslider/jquery.bxslider.css'),
				'js' => array('assets/js/bxslider/jquery.bxslider.min.js'),
			),
			'parallax' => array(
				'js' => array('assets/js/parallax/jquery.parallax-scroll.min.js'),
			),
			'isotope' => array(
				'js' => array('assets/js/isotope/imagesloaded.pkgd.min.js', 'assets/js/isotope/isotope.pkgd.min.js'),
			),
			'forms_validate' => array(
				'js' => array('assets/js/validate/jquery.validate.min.js'),
			),
			'forms_submit' => array(
				'js' => array('assets/js/submit/jquery.form.min.js'),
			),
			'googlemaps' => array(
				'js' => array('http://maps.google.com/maps/api/js?sensor=false', 'assets/js/googlemaps/jquery.gmap.min.js'),
			),
			'charts' => array(
				'js' => array('assets/js/charts/chart.min.js', 'assets/js/charts/jquery.easypiechart.min.js'),
			),
			'counter' => array(
				'js' => array('assets/js/counter/jQuerySimpleCounter.js'),
			),
			'ytplayer' => array(
				'css' => array('assets/js/ytplayer/css/YTPlayer.css'),
				'js' => array('assets/js/ytplayer/jquery.mb.YTPlayer.js'),
			),
			'twitter' => array(
				'js' => array('assets/js/twitter/twitterfetcher.js'),
			),
		);
	}

} 