<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_ORM_Base extends ORM {

	const FILTER_NONE     = 0;
	const FILTER_FRONTEND = 1;
	const FILTER_BACKEND  = 2;

	/**
	 * Default filter mode
	 * @var integer
	 */
	public static $filter_mode = self::FILTER_NONE;

	/**
	 * Default site id
	 * @var integer
	 */
	public static $site_id = NULL;
	
	/**
	 * Master site id
	 * @var integer
	 */
	public static $site_id_master = NULL;

	/**
	 * Broadcast flag
	 * @var string
	 */
	public $broadcast = FALSE;
	
	/**
	 * Filter mode
	 * @var integer
	 */
	protected $_filter_mode = NULL;

	/**
	 * Site id
	 * @var integer
	 */
	protected $_site_id = NULL;

	/**
	 * Master site id
	 * @var integer
	 */
	protected $_site_id_master = NULL;

	/**
	 * Deleted column name
	 * @var string
	 */
	protected $_deleted_column = 'deleted';
	/**
	 * Deleted column name
	 * @var string
	 */
	protected $_active_column = 'is_active';

	/**
	 * Site id column name
	 * @var string
	 */
	protected $_site_id_column = 'site_id';

	/**
	 * For all column name
	 * @var string
	 */
	protected $_for_all_column = 'for_all';
	
	/**
	 * Empty rules for Validation object
	 * @var array
	 */
	protected $_empty_rules = array();

	public function for_all_column()
	{
		return $this->_for_all_column;
	}
	
	public function site_id_column()
	{
		return $this->_site_id_column;
	}
	
	/*
	 * Override initialization action to set filter_mode and site_id fields.
	 *
	 * @return void
	 */
	protected function _initialize()
	{
		parent::_initialize();

		if ($this->_filter_mode === NULL)
		{
			$this->_filter_mode = self::$filter_mode;
		}
		if ($this->_site_id === NULL)
		{
			$this->_site_id = self::$site_id;
		}
		if ($this->_site_id_master === NULL)
		{
			$this->_site_id_master = self::$site_id_master;
		}
	}

	/**
	 * Gets or sets filter mode
	 *
	 * @param   integer   $filter_mode New filter mode
	 * @return  ORM_Base
	 */
	public function filter_mode($filter_mode = NULL)
	{
		if ($filter_mode === NULL)
		{
			return $this->_filter_mode;
		}
		else
		{
			$this->_filter_mode = (int) $filter_mode;
		}
		return $this;
	}

	/**
	 * Gets or sets site ID
	 *
	 * @param   mixed   $site_id New site ID (NULL, int or array)
	 * @return  ORM_Base
	 */
	public function site_id($site_id = FALSE)
	{
		if ($site_id === FALSE)
		{
			return $this->_site_id;
		}
		else
		{
			$this->_site_id = $site_id;
		}
		return $this;
	}

	/**
	 * Gets or sets master site ID
	 *
	 * @param   mixed   $site_id New site ID (NULL, int or array)
	 * @return  ORM_Base
	 */
	public function site_id_master($site_id = FALSE)
	{
		if ($site_id === FALSE)
		{
			return $this->_site_id_master;
		}
		else
		{
			$this->_site_id_master = $site_id;
		}
		return $this;
	}

	/*
	 * Overrides to add filters by site and front
	 *
	 * @param  integer $type Type of Database query
	 * @return ORM
	 */
	protected function _build($type)
	{
		switch ($type)
		{
			case Database::SELECT:
			case Database::UPDATE:
			case Database::DELETE:
				if ($this->_filter_mode)
				{
					$this->apply_mode_filter();
				}
				if ($this->_site_id !== NULL)
				{
					$this->apply_site_id_filter();
				}
		}
		return parent::_build($type);
	}

	/**
	 * Apply mode filter
	 *
	 * @return void
	 */
	public function apply_mode_filter()
	{
		switch ($this->_filter_mode)
		{
			case self::FILTER_FRONTEND:
				$this->apply_not_deleted_filter();
				$this->apply_active_filter();
			break;
			case self::FILTER_BACKEND:
				$this->apply_not_deleted_filter();
			break;
		}
	}

	/**
	 * Apply filter by site ID
	 *
	 * @return void
	 */
	public function apply_site_id_filter()
	{
		if ( ! $this->broadcast AND $this->_site_id !== NULL AND isset($this->_table_columns[$this->_site_id_column]))
		{
			if (isset($this->_table_columns[$this->_for_all_column]))
			{
				$this->where_open();
			}

			if (is_scalar($this->_site_id))
			{
				// Filter by single site id
				$this->where($this->_object_name.'.'.$this->_site_id_column, '=', (int) $this->_site_id);
			}
			elseif (is_array($this->_site_id) AND ! empty($this->_site_id))
			{
				// Filter by several site ids
				$this->where($this->_object_name.'.'.$this->_site_id_column, 'IN', $this->_site_id);
			}

			if (isset($this->_table_columns[$this->_for_all_column]))
			{
				$this
					->or_where($this->_object_name.'.'.$this->_for_all_column, '=', 1)
					->where_close();
			}
		}
	}

	/**
	 * Apply filter by deleted status
	 *
	 * @return void
	 */
	public function apply_not_deleted_filter()
	{
		if (isset($this->_table_columns[$this->_deleted_column]))
		{
			// Filter only not deleted records
			$this->where($this->_object_name.'.'.$this->_deleted_column, '=', 0);
		}
	}

	/**
	 * Apply filter by active status
	 *
	 * @return void
	 */
	public function apply_active_filter()
	{
		if (isset($this->_table_columns[$this->_active_column]))
		{
			// Filter only active records
			$this->where($this->_object_name.'.'.$this->_active_column, '>', 0);
		}
	}
	
	/**
	 * Check exist 'site_id' column or not
	 *
	 * @return boolean
	 */
	public function has_site_id_column()
	{
		return isset($this->_table_columns[$this->_site_id_column]);
	}
	
	/**
	 * Check exist 'for_all' column or not
	 *
	 * @return boolean
	 */
	public function has_for_all_column()
	{
		return isset($this->_table_columns[$this->_for_all_column]);
	}
	
	/**
	 * Check exist 'for_all' column or not
	 *
	 * @return boolean
	 */
	public function is_master()
	{
		$column = $this->_site_id_column;
		return $this->$column == $this->_site_id_master;
	}
	
	/**
	 * Initializes validation rules, and labels (reloaded method)
	 *
	 * @return void
	 */
	protected function _validation()
	{
		// Build the validation object with its rules
		$this->_validation = Extended_Validation::factory($this->_object)
								->bind(':model', $this)
								->bind(':original_values', $this->_original_values)
								->bind(':changed', $this->_changed);
	
		foreach ($this->rules() as $field => $rules)
		{
			$this->_validation->rules($field, $rules);
		}
	
		// Use column names by default for labels
		$columns = array_keys($this->_table_columns);
	
		// Merge user-defined labels
		$labels = array_merge(array_combine($columns, $columns), $this->labels());
	
		foreach ($labels as $field => $label)
		{
			$this->_validation->label($field, $label);
		}
		
		foreach ($this->_empty_rules as $name)
		{
			$this->_validation->add_empty_rule($name);
		}
	}

} // End ORM_Base
