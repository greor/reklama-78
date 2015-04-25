<?php defined('SYSPATH') OR die('No direct access allowed.');

class Paginator {

	const QUERY_PARAM = 'p';

	protected $_per_page = 20;

	protected $_radius = 3;

	protected $_count;

	protected $_current;

	protected $_template;

	public function __construct($_template)
	{
		$this->_template = $_template;
		$this->_current = Request::current()->query( Paginator::QUERY_PARAM );

		if ($this->_current == 0)
		{
			$this->_current++;
		}
	}

	public function current($value = NULL)
	{
		if ($value === NULL)
		{
			return $this->_current;
		}

		$value = (int) $value;
		if ($value > 0)
		{
			$this->_current = $value;
		}

		return $this;
	}

	public function per_page($value = NULL)
	{
		if ($value === NULL)
		{
			return $this->_per_page;
		}

		$value = (int) $value;
		if ($value >= 0)
		{
			$this->_per_page = (int) $value;
		}

		return $this;
	}

	public function count($value = NULL)
	{
		if ($value === NULL)
		{
			return $this->_count;
		}

		$value = (int) $value;
		if ($value >= 0)
		{
			$this->_count = (int) $value;
		}

		return $this;
	}

	public function limit()
	{
		return $this->_per_page;
	}

	public function offset()
	{
		return $this->_per_page * ($this->_current - 1);
	}

	public function get($link, $javascript = TRUE)
	{
		$page_count = (int) floor( $this->_count / $this->_per_page );
		if ( ($this->_count % $this->_per_page) > 0)
		{
			$page_count++;
		}

		if ($page_count < 2)
			return FALSE;

		if ($this->_current > $this->_count )
		{
			$this->_current = 1;
		}

		if (strpos($link, '?') === FALSE)
		{
			$link .= '?'.Paginator::QUERY_PARAM.'=';
		}
		else
		{
			$link .= '&'.Paginator::QUERY_PARAM.'=';
		}

		$return = array();

		for ($i = 1; $i <= $page_count; $i++)
		{
			$page = array(
				'title'		=>	$i,
				'link'		=>	$link.$i,
			);

			if ($this->_current == $i)
			{
				$page['current'] = TRUE;

				if (($i - 1) > 0)
				{
					$return['previous'] = $link.($i - 1);
				}

				if (($i + 1) <= $page_count)
				{

					$return['next'] = $link.($i + 1);
				}
			}

			$return['items'][] = $page;
		}

		$return['first_page'] = $link.'1';
		$return['last_page'] = $link.$page_count;

		$return['page_count'] = $page_count;
		$return['current'] = $this->_current;
		$return['link'] = $link;
		$return['per_page'] = $this->_per_page;
		$return['param_name'] = Paginator::QUERY_PARAM;

		return $return;
	}

	public function render($link, $options = array())
	{
		$paginator = $this->get($link);

		$tpl = View_Theme::factory($this->_template)
					->set('paginator', $paginator)
					->set('options', $options);

		if ( ! empty( $options['get_array'] ) )
		{
			$tpl->set( 'pagination_array', $this->_get_array( $paginator, $options ) );
		}

		return $tpl->render();
	}

	private function _get_array($paginator, $options)
	{
		$array = array();

		$padding = empty($options['padding']) ? 2 : $options['padding'];
		$padding_left = empty($options['padding_left']) ? 2 : $options['padding_left'];
		$padding_right = empty($options['padding_right']) ? 2 : $options['padding_right'];

		$start = $paginator['current'] - $padding;
		$start = ( $start < 1 ) ? 1 : $start;

		$end = $paginator['current'] + $padding;
		$end = ( $end > $paginator['page_count'] ) ? $paginator['page_count'] : $end;

		$tmp = $start - 1;
		if ( $tmp > 1 + $padding_left )
		{
			for( $i = 1; $i <= 1 + $padding_left; $i++ )
			{
				$array[] = $i - 1;
			}

			$array[] = FALSE;
		}
		elseif ( $tmp > 0 )
		{
			for( $i = 1; $i < $start; $i++ )
			{
				$array[] = $i - 1;
			}
		}

		for ( $i = $start; $i <= $end; $i++ )
		{
			$array[] = $i - 1;
		}

		$tmp = $end + 1;
		if ( $tmp < $paginator['page_count'] - $padding_right )
		{
			$array[] = FALSE;

			for( $i = $paginator['page_count'] - $padding_right; $i <= $paginator['page_count']; $i++ )
			{
				$array[] = $i - 1;
			}
		}
		elseif ( $tmp > 0 )
		{
			for( $i = $end + 1; $i <= $paginator['page_count']; $i++ )
			{
				$array[] = $i - 1;
			}
		}

		return $array;
	}

} // End Kohana_Ku_Paginator