<?php defined('SYSPATH') OR die('No direct access allowed.');

class Kohana_Ku_HTML_Head {

	// Singleton instance
	protected static $_instance;

	public $meta = array();

	public $title = array();

	public $title_separator = ' - ';

	public $base;

	public $styles = array();

	public $scripts = array();

	public $links = array();

	public $keywords = array();

	public $description = array();


	/**
	 * Singleton instance of Ku_HTML_Head.
	 *
	 * @return Ku_HTML_Head $this instance.
	 */
	public static function instance()
	{
		if (Ku_HTML_Head::$_instance == NULL)
		{
			// Create a new instance
			Ku_HTML_Head::$_instance = new Ku_HTML_Head;
		}

		return Ku_HTML_Head::$_instance;
	}

	/**
	 * Returns the HTML code for inclusion.
	 *
	 * @return  string
	 */
	public function render()
	{
		$return = array();

		// Render meta keywords tag
		if ($this->keywords)
		{
			$return[] = Ku_HTML::meta(implode(', ', $this->keywords), array('name' => 'keywords'));
		}

		// Render meta description tag
		if ($this->description)
		{
			$return[] = Ku_HTML::meta(implode(', ', $this->description), array('name' => 'description'));
		}

		// Render other meta tags
		foreach ($this->meta as $item)
		{
			$return[] = (string) $item;
		}

		// Render base tag
		if ($this->base)
		{
			$return[] = is_string($this->base) ? Ku_HTML::base($this->base) : (string) $this->base;
		}

		// Render title tag
		$title = is_array($this->title) ? implode($this->title_separator, $this->title) : $this->title;
		$return[] = is_string($title) ? '<title>'.HTML::chars($title, FALSE).'</title>' : (string) $title;

		// Render styles
		foreach ($this->styles as $item)
		{
			$return[] = (string) $item;
		}

		// Render scripts
		foreach ($this->scripts as $item)
		{
			$return[] = (string) $item;
		}

		// Render links
		foreach ($this->links as $item)
		{
			$return[] = (string) $item;
		}

		// Remove empty lines
		$return = array_filter($return, 'strlen');

		return implode("\n", $return);
	}

	public function __toString()
	{
		// Default render all
		return (string) $this->render();
	}

	public function add_style($style)
	{
		$this->styles[] = $style;
		return $this;
	}

	public function add_script($script)
	{
		$this->scripts[] = $script;
		return $this;
	}

	public function add_meta($meta)
	{
		$this->meta[] = $meta;
		return $this;
	}

	public function add_link($link)
	{
		$this->links[] = $link;
		return $this;
	}

	public function add_title($title)
	{
		$this->title[] = $title;
		return $this;
	}

	public function add(array $values = NULL)
	{
		if (is_array($values))
		{
			$allowed = array('styles', 'scripts', 'links', 'meta');

			foreach ($values as $key => $set)
			{
				if ( ! in_array($key, $allowed))
					continue;

				if (is_array($set))
				{
					$this->$key = array_merge($this->$key, $set);
				}
				else
				{
					$this->{$key}[] = $set;
				}
			}
		}
		return $this;
	}
/**/
} // End Ku_HTML_Head