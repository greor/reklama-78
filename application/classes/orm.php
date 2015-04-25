<?php defined('SYSPATH') or die('No direct script access.');

class ORM extends Kohana_ORM implements Acl_Resource_Interface {

	protected $_required_fields;

	protected $_paginator;

	/**
	 * @see Acl_Resource_Interface::get_resource_id()
	 */
	public function get_resource_id()
	{
		return $this->_object_name;
	}

	/**
	 * Returns an array of columns to include in the select query. This method
	 * can be overridden to change the default select behavior.
	 *
	 * @return array Columns to select
	 */
	protected function _build_select()
	{
		if (isset($this->_db_applied['select']))
		{
			return array();
		}

		return parent::_build_select();
	}

	public function check_meta_fields($values, $switcher_key)
	{
		$meta_columns = array(
			'title_tag',
			'keywords_tag',
			'description_tag',
		);

		if ( ! empty($values[ $switcher_key ]))
		{
			foreach ($meta_columns as $column)
			{
				if ( isset($values[ $column ]) )
					continue;

				$values[ $column ] = '';
			}
		}

		return $values;
	}

	public function unique_ext($field, $value)
	{
		$model = ORM::factory($this->object_name())
			->where($field, '=', $value)
			->where('delete_bit', '=', '0')
			->find();

		if ($this->loaded())
		{
			return ( ! ($model->loaded() AND $model->pk() != $this->pk()));
		}

		return ( ! $model->loaded());
	}

	public function checkbox($value)
	{
		if (empty($value))
			return FALSE;
		else
			return TRUE;

	}

	public function required_fields()
	{
		if (isset($this->_required_fields))
			return $this->_required_fields;

		$this->_required_fields = array();

		foreach ($this->rules() as $field => $rules)
		{
			foreach ($rules as $rule)
			{
				$func = reset($rule);

				if (is_string($func) AND ($func == 'not_empty'))
				{
					$this->_required_fields[] = $field;
					continue 2;
				}
			}
		}

		return $this->_required_fields;
	}

	public function paginator($paginator = NULL)
	{
		if ($paginator === NULL)
			return $this->_paginator;

		$this->_paginator = $paginator;

		$this->limit( $this->_paginator->limit() )
			->offset( $this->_paginator->offset() );

		return $this;
	}

	public function save_has_many($name, $exist_ids)
	{
		$affected_ids = array();

		$post_ids = Request::current()->post($name);

		if ($post_ids === NULL)
		{
			$post_ids = array();
		}

		$post_ids = array_filter($post_ids);

		if ( is_array($post_ids))
		{
			if ( ! empty($post_ids))
			{
				$post_ids = array_unique($post_ids);
			}
			else
			{
				$post_ids = array();
			}

			$del_ids = array_diff($exist_ids, $post_ids);
			$add_ids = array_diff($post_ids, $exist_ids);

			if ( ! empty($del_ids))
			{
				$this->remove($name, $del_ids);
			}
			if ( ! empty($add_ids))
			{
				$this->add($name, $add_ids);
			}

			$affected_ids = array_diff($exist_ids, $del_ids);
			$affected_ids = $affected_ids + $add_ids;
		}

		if ( ! empty($affected_ids))
		{
			$affected_ids = array_combine( $affected_ids, $affected_ids );
		}

		return $affected_ids;
	}

}