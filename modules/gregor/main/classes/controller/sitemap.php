<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Sitemap extends Controller {

	protected $site_id;
	protected $main_site_id;
	private $domain = 'http://radio7.ru';
	private $site_code;
	private $parsed_modules = array( 
		'news', 'actions', 'programs', 'staff',
		'playlist', 'schedule', 'podcasts' 
	);
	private $sitemap_directory_base = 'upload/sitemaps';
	private $sitemap_directory;
	private $pages_uris = array();
	
	public function before()
	{
		parent::before();
		
		$this->main_site_id = $this->request->main_site_id;
		$this->sitemap_directory_base = str_replace('/', DIRECTORY_SEPARATOR, $this->sitemap_directory_base);
		
		if ($this->request->action() == 'generate') {
			try {
				$_dir = DOCROOT.$this->sitemap_directory_base.DIRECTORY_SEPARATOR;
				Ku_Dir::make_writable($_dir);
				Ku_Dir::remove($_dir);
				unset($_dir);
			} catch (Exception $e) {}
		}
	}

	public function action_index()
	{
		if ( ! file_exists(DOCROOT.$this->sitemap_directory))
			throw new HTTP_Exception_404();
		
		$this->site_code = ORM::factory('site', $this->request->site_id)->code;
		$this->sitemap_directory = $this->sitemap_directory_base.DIRECTORY_SEPARATOR.$this->site_code;
		
		$this->response
			->headers('Content-Type', 'text/xml')
			->headers('cache-control', 'max-age=0, must-revalidate, public')
			->headers('expires', gmdate('D, d M Y H:i:s', time()).' GMT');
		try {
			$dir = new DirectoryIterator(DOCROOT.$this->sitemap_directory);
		
			$xml = new DOMDocument('1.0', Kohana::$charset);
			$xml->formatOutput = TRUE;
			$root = $xml->createElement('sitemapindex');
			$root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
			$xml->appendChild($root);

			foreach ($dir as $fileinfo) {
				if ($fileinfo->isDot() OR $fileinfo->isDir())
					continue;
				
				$_file_path = str_replace(DOCROOT, '', $fileinfo->getPathName());
				$_file_url = $this->domain.'/'.str_replace(DIRECTORY_SEPARATOR, '/', $_file_path);
				$sitemap = $xml->createElement('sitemap');
				$root->appendChild($sitemap);

				$sitemap->appendChild(new DOMElement('loc', $_file_url));
				
				$_last_mod = Sitemap::date_format($fileinfo->getCTime());
				$sitemap->appendChild(new DOMElement('lastmod', $_last_mod));
			}
			
		} catch (Exception $e) {
			echo Debug::vars(
				$e->getMessage()
			); die;
		};
		
		echo $xml->saveXML();
	}
	
	public function action_generate()
	{
		$sites = ORM::factory('site')
					->find_all();
		
		foreach ($sites as $item) {
			ORM_Base::$site_id = $item->id;
			$this->site_id = $item->id;
			$this->site_code = $item->code;
			$this->sitemap_directory = $this->sitemap_directory_base.DIRECTORY_SEPARATOR.$this->site_code;
			$this->_generate();
		}
	}
	
	protected function _generate()
	{
		$_common_set = array();
		$this->pages_uris = Helper_Page::parse_to_base_uri( ORM::factory('page')->find_all() );
		$pages = $this->get_pages();

		foreach ($pages as $item) {
			$_set = array();
			if ($item['type'] == 'static') {
				$_set[] = $this->_page_item($item);
			} elseif ($item['type'] == 'module') {
				switch($item['data']) {
					case 'podcasts':
					case 'schedule':
					case 'playlist':
						$_set[] = $this->_page_item($item);
						break;
					case 'news':
						$_set = $this->_news_items($item);
						break;
					case 'actions':
						$_set = $this->_actions_items($item);
						break;
					case 'programs':
						$_set = $this->_programs_items($item);
						break;	
					case 'staff':
						$_set = $this->_staff_items($item);
						break;	
				}
			}
			if ($item['sm_separate_file'] == 1 AND ! empty($_set)) {
				$sitemap = new Sitemap;
				foreach ($_set as $_item) {
					$sitemap_url = new Sitemap_URL;
					$sitemap_url->set_loc($_item['loc'])
						->set_change_frequency($_item['changefreq'])
						->set_priority(str_replace(',', '.', $_item['priority']));
					if ( ! empty($_item['lastmod'])) {
						$_unix_time = strtotime($_item['lastmod']);
						$sitemap_url->set_last_mod($_unix_time);
					}
					$sitemap->add($sitemap_url);
					unset($sitemap_url);
				}

				$this->write_to_file($this->sitemap_directory.DIRECTORY_SEPARATOR.'part_'.uniqid().'.xml', urldecode($sitemap->render()));
				unset($sitemap);
			} else {
				$_common_set = array_merge($_common_set, $_set);
			}
		}
		
		if ( ! empty($_common_set)) {
			$sitemap = new Sitemap;
			foreach ($_common_set as $_item) {
				$sitemap_url = new Sitemap_URL;
				$sitemap_url->set_loc($_item['loc'])
					->set_change_frequency($_item['changefreq'])
					->set_priority(str_replace(',', '.', $_item['priority']));
				if ( ! empty($_item['lastmod'])) {
					$_unix_time = strtotime($_item['lastmod']);
					$sitemap_url->set_last_mod($_unix_time);
				}
				$sitemap->add($sitemap_url);
				unset($sitemap_url);
			}
			$this->write_to_file($this->sitemap_directory.DIRECTORY_SEPARATOR.uniqid('common_').'.xml', urldecode($sitemap->render()));
			unset($sitemap);
		}
	}
	
	private function get_pages()
	{
		$_builder = DB::select(
				'id', 'site_id', 'type', 'data', 'created', 'updated', 
				'sm_changefreq', 'sm_priority', 'sm_separate_file'
			)
			->from('pages')
			->where('delete_bit', '=', 0)
			->and_where('status', '>', 0)
			->and_where_open()
				->and_where_open()
					->where('type', '=', 'static')
					->and_where('site_id', '=', $this->site_id)
				->and_where_close()
				->or_where_open()
					->where('type', '=', 'module')
					->and_where('data', 'IN', $this->parsed_modules)
					->and_where_open()
						->where('site_id', '=', $this->site_id)
						->or_where_open()
							->where('site_id', '=', $this->main_site_id)
							->and_where('for_all', '=', 1)
						->or_where_close()
					->and_where_close()
				->or_where_close()
			->and_where_close();
		$hiden_pages = $this->hidden_elements('page');
		if ( ! empty($hiden_pages)) {
			$_builder->where('id', 'NOT IN', $hiden_pages);
		}
		$_builder
			->order_by('sm_priority', 'desc')
			->order_by('site_id', 'asc');
		return $_builder->execute();
	}
	
	private function hidden_elements($model_name)
	{
		return ORM::factory('hided_List')
			->where('object_name', '=', $model_name)
			->find_all()
			->as_array(NULL, 'element_id');
	}
	
	private function write_to_file($file_name, $str)
	{
		$file_name = str_replace('/', DIRECTORY_SEPARATOR, $file_name);
		if (strpos($file_name, DOCROOT) !== 0) {
			$file_name = DOCROOT.$file_name;
		}
		$dirname = dirname($file_name);
		if ( ! file_exists($dirname)) {
			Ku_Dir::make($dirname);
		}
		Ku_Dir::make_writable($dirname);
		$handle = fopen($file_name, 'w');
		fwrite($handle, $str);
		fclose($handle);
	}
	
	private function _page_item($page)
	{
		$_uri = Arr::get($this->pages_uris, $page['id']);
		$_page_loc = ($_uri !== NULL) ? ($this->domain.URL::base().$_uri) : FALSE;
		$_page_last_mod = ($page['updated'] == '0000-00-00 00:00:00') ? $page['created'] : $page['updated'];
		return array(
			'loc'        => $_page_loc,
			'lastmod'    => $_page_last_mod,
			'changefreq' => $page['sm_changefreq'],
			'priority'   => $page['sm_priority'],
		);
	}
	
	private function _news_items($page)
	{
		$return = array();
		$url_base = $this->domain.URL::base();
		$return[] = $this->_page_item($page);
		$categories_uri = array();
		$category_link_tpl = $url_base.Page_Route::uri($page['id'], 'news', array( 
			'category_name' => '{category_name}'
		));
		$item_link_tpl = $url_base.Page_Route::uri($page['id'], 'news', array( 
			'category_name' => '{category_name}', 
			'id'           => '{id}', 
		));
		$db_categories = ORM::factory('news_Category')
			->exclude_hidden_elements( ($this->site_id == $this->main_site_id) )
			->find_all();
		
		foreach ($db_categories as $_item) {
			$categories_names[ $_item->id ] = $_item->name;
			if ($_item->page_id == $page['id'] AND $_item->site_id == $page['site_id']) {
				$return[] = array(
					'loc'        => str_replace('{category_name}', $_item->name, $category_link_tpl),
					'changefreq' => $page['sm_changefreq'],
					'priority'   => $page['sm_priority'],
				);
			}
		}
		
		$db_news = ORM::factory('news')
			->where('site_id', '=', $this->site_id)
			->and_where('page_id', '=', $page['id'])	
			->limit(1000)	
			->find_all();
		
		$_date = date('Y-m-d H:i:s', strtotime('-1 month'));
		$stop = ($db_news->count() <= 0);
		while ( ! $stop) {
			$_item = $db_news->current();
			$stop = ($_item == FALSE) ? TRUE : FALSE;
			if ($_item != FALSE) {
				$_category_name = Arr::get($categories_names, $_item->category_id);
				if ($_category_name == NULL) {
					$db_news->next();
					continue;
				}
					
				$_last_mod = ($_item->updated == '0000-00-00 00:00:00') ? $_item->created : $_item->updated;
				
				if ( $_last_mod < $_date ) {
					$_changefreq = 'monthly';
				} else {
					$_changefreq = $page['sm_changefreq'];
				}
				
				$return[] = array(
					'loc'        => str_replace(
						array( '{category_name}', '{id}' ), 
						array( $_category_name, $_item->id ), 
						$item_link_tpl
					),
					'lastmod'    => $_last_mod,
					'changefreq' => $page['sm_changefreq'],
					'priority'   => $page['sm_priority'],
				);
			}
			$db_news->next();
		}

		return $return;
	}
	
	private function _actions_items($page)
	{
		$return = array();
		$url_base = $this->domain.URL::base();
		$return[] = $this->_page_item($page);
		$item_link_tpl = $url_base.Page_Route::uri($page['id'], 'actions', array( 
			'id'           => '{id}', 
		));
		$db_actions = ORM::factory('action')
			->where('site_id', '=', $this->site_id)
			->and_where('page_id', '=', $page['id'])		
			->find_all();
		
		$_date = date('Y-m-d H:i:s', strtotime('-1 month'));
		$stop = ($db_actions->count() <= 0);
		while ( ! $stop) {
			$_item = $db_actions->current();
			$stop = ($_item == FALSE) ? TRUE : FALSE;
			if ($_item != FALSE) {
				$_last_mod = ($_item->updated == '0000-00-00 00:00:00') ? $_item->created : $_item->updated;
				
				if ( $_last_mod < $_date ) {
					$_changefreq = 'monthly';
				} else {
					$_changefreq = $page['sm_changefreq'];
				}
				
				$return[] = array(
					'loc'        => str_replace( '{id}', $_item->id, $item_link_tpl ),
					'lastmod'    => $_last_mod,
					'changefreq' => $page['sm_changefreq'],
					'priority'   => $page['sm_priority'],
				);
			}
			$db_actions->next();
		}

		return $return;
	}
	
	private function _programs_items($page)
	{
		$return = array();
		$url_base = $this->domain.URL::base();
		$return[] = $this->_page_item($page);
		$item_link_tpl = $url_base.Page_Route::uri($page['id'], 'programs', array(
			'uri' => '{uri}',
		));
		
		$db_programs = ORM::factory('program')
			->where('site_id', '=', $this->site_id)
			->and_where('page_id', '=', $page['id'])
			->find_all();
		
		$stop = ($db_programs->count() <= 0);
		while ( ! $stop) {
			$_item = $db_programs->current();
			$stop = ($_item == FALSE) ? TRUE : FALSE;
			if ($_item != FALSE) {
				$_last_mod = ($_item->updated == '0000-00-00 00:00:00') ? $_item->created : $_item->updated;
				$return[] = array(
					'loc'        => str_replace( '{uri}', $_item->uri, $item_link_tpl ),
					'lastmod'    => $_last_mod,
					'changefreq' => $page['sm_changefreq'],
					'priority'   => $page['sm_priority'],
				);
			}
			$db_programs->next();
		}
		
		return $return;
	}
	
	private function _staff_items($page)
	{
		$return = array();
		$url_base = $this->domain.URL::base();
		$return[] = $this->_page_item($page);
		$categories_uri = array();
		$category_link_tpl = $url_base.Page_Route::uri($page['id'], 'staff', array( 
			'category_uri' => '{category_uri}'
		));
		$item_link_tpl = $url_base.Page_Route::uri($page['id'], 'staff', array( 
			'category_uri' => '{category_uri}', 
			'name'         => '{name}', 
		));
		$db_categories = ORM::factory('staff_Category')
			->exclude_hidden_elements( ($this->site_id == $this->main_site_id) )
			->find_all();
		foreach ($db_categories as $_item) {
			$categories_uri[ $_item->id ] = $_item->uri;
			if ($_item->page_id == $page['id'] AND $_item->site_id == $page['site_id']) {
				$return[] = array(
					'loc'        => str_replace('{category_uri}', $_item->uri, $category_link_tpl),
					'changefreq' => $page['sm_changefreq'],
					'priority'   => $page['sm_priority'],
				);
			}
		}
		
		$db_staff = ORM::factory('staff')
			->where('site_id', '=', $this->site_id)
			->where('hided', '=', 0)
			->and_where('page_id', '=', $page['id'])		
			->find_all();
		
		$stop = ($db_staff->count() <= 0);
		while ( ! $stop) {
			$_item = $db_staff->current();
			$stop = ($_item == FALSE) ? TRUE : FALSE;
			if ($_item != FALSE) {
				$_category_uri = Arr::get($categories_uri, $_item->category_id);
				if ($_category_uri == NULL) {
					$db_staff->next();
					continue;
				}
					
				$_last_mod = ($_item->updated == '0000-00-00 00:00:00') ? $_item->created : $_item->updated;
				$return[] = array(
					'loc'        => str_replace(
						array( '{category_uri}', '{name}' ), 
						array( $_category_uri, $_item->uri ), 
						$item_link_tpl
					),
					'lastmod'    => $_last_mod,
					'changefreq' => $page['sm_changefreq'],
					'priority'   => $page['sm_priority'],
				);
			}
			$db_staff->next();
		}
		
		return $return;
	}
}