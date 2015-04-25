<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_ORM_Helper implements ORM_File, ORM_Position, ORM_Restore {

	/**
	 * Default configuration of file field
	 * @var array
	 */
	public static $default_file_config = array(
		'path' => NULL,
		'uri'  => NULL,
		'force_unique_prefix' => TRUE,
		'max_filename_length' => 127,
		'on_delete' => ORM_File::ON_DELETE_RENAME,
		'on_update' => ORM_File::ON_UPDATE_RENAME,
		'deleted_prefix' => '_DELETED_',
		'replaced_prefix' => '_REPLACED_',
		'tmp_prefix' => '_TMP_',
		'allowed_src_dirs' => array(),
		'dir_chmod' => NULL,
		'file_chmod' => NULL,

	);

	/**
	 * Default configuration of position field
	 * @var array
	 */
	public static $default_position_config = array(
		'step' => 1,
		'start'  => 1,
		'group_by' => array(),
		'order_by' => array('id' => 'ASC'),
	);

	/**
	 * Default value of delete records from pivot tables in "many-to-many" relationships
	 * @var boolean
	 */
	public static $on_delete_clear_pivot_tables = FALSE;

	/**
	 * ORM instance
	 * @var ORM
	 */
	protected $_orm = NULL;

	/**
	 * name of ORM model
	 * @var string
	 */
	protected $_model_name = NULL;

	/**
	 * Class name of ORM model
	 * @var string
	 */
	protected $_model_class = NULL;

	/**
	 * Database Object
	 * @var Database
	 */
	protected $_db = NULL;

	/**
	 * Database config group
	 * @var String
	 */
	protected $_db_group = NULL;


	/**
	 * Stores file fields information
	 * @var array
	 */
	protected $_file_fields = array();

	/**
	 * Stores file fields information
	 * @var array
	 */
	protected $_position_fields = array();

	/**
	 * Array of cascade deleted objects
	 * array('blog', 'comment', 'custom_foreign_key' => 'blog')
	 * @var array
	 */
	protected $_on_delete_cascade = array();

	/**
	 * Name of field what marks record as "deleted"
	 * @var string
	 */
	protected $_safe_delete_field = NULL;

	/**
	 * Manual delete records from pivot tables in "many-to-many" relationships
	 * @var boolean
	 */
	protected $_on_delete_clear_pivot_tables = NULL;


	/**
	 * Creates and returns a new model wrapper.
	 *
	 * @chainable
	 * @param   mixed   $model  Model name or ORM instance
	 * @param   mixed   $id     Parameter for find()
	 * @return  ORM_Helper
	 */
	public static function factory($model, $id = NULL)
	{
		if ($model instanceof ORM)
		{
			$model_name = $model->object_name();
		}
		else
		{
			$model_name = $model;
		}
		// Set class name
		$class = 'ORM_Helper_'.ucfirst($model_name);

		return new $class($model, $id);
	}

	/**
	 * Constructs a new wrapper and loads a record into model if given
	 *
	 * @param   mixed   $model  Model name or ORM instance
	 * @param   mixed   $id     Parameter for find or object to load
	 * @return  void
	 */
	public function __construct($model, $id = NULL)
	{
		if (empty($this->_model_name))
		{
			// Get model name from current class name
			$this->_model_name = substr(get_class($this), 11);
		}

		// Set model class name
		$this->_model_class = 'Model_'.ucfirst($this->_model_name);

		// Assign instance of model
		if ($model instanceof ORM)
		{
			// Assign existing model
			$this->_orm = $model;
			if ($id !== NULL)
			{
				if (is_array($id))
				{
					foreach ($id as $column => $value)
					{
						// Passing an array of column => values
						$model->where($column, '=', $value);
					}

					$model->find();
				}
				else
				{
					// Passing the primary key
					$model->where($model->primary_key(), '=', $id)->find();
				}
			}
		}
		else
		{
			// Create new model
			$this->_orm = ORM::factory($model, $id);
		}

		$this->_initialize();
	}

	/**
	 * Execute any initialization actions.
	 *
	 * @return void
	 */
	protected function _initialize()
	{
		// Check class of model
		$this->_check_orm();

		if ( ! is_object($this->_db))
		{
			// Get database instance
			$this->_db = Database::instance($this->_db_group);
		}

		if ($this->_on_delete_clear_pivot_tables === NULL)
		{
			// Set default value
			$this->_on_delete_clear_pivot_tables = self::$on_delete_clear_pivot_tables;
		}

		$this->_initialize_file_fields();
		$this->_initialize_position_fields();

	}

	/**
	 * Execute any initialization actions.
	 *
	 * @return void
	 */
	protected function _check_orm(ORM $orm = NULL)
	{
		if ($orm === NULL)
		{
			$orm = $this->_orm;
		}

		// Check class of model
		if ( ! is_a($orm, $this->_model_class))
		{
			throw new Kohana_Exception('Model :model is not a instance of :class',
					array(':model' => get_class($id), ':class' => $this->_model_class));
		}
	}

	/**
	 * Returns ORM instance or assigns new instance
	 *
	 * @param   ORM   New ORM instance
	 * @return  ORM
	 */
	public function orm(ORM $orm = NULL)
	{
		if ($orm === NULL)
		{
			return $this->_orm;
		}
		else
		{
			$this->_check_orm($orm);
			$this->_orm = $orm;
		}
	}

	/**
	 * Returns array of file fields with params
	 *
	 * @return  array
	 */
	public function file_fields()
	{
		return $this->_file_fields;
	}

	/**
	 * Returns validation rules for file fields
	 *
	 * @return  array
	 */
	public function file_rules()
	{
		return array();
	}

	/**
	 * Saves file and returns file name
	 *
	 * @param   string   $field  File field name
	 * @param   mixed    $value  File field value
	 * @return  string
	 */
	public function file_save($field, $value)
	{
		$this->_check_file_field($field);

		$config = $this->_file_fields[$field];

		$base_path = $this->file_path($field, '');

		// Upload a file?
		if (is_array($value) AND Ku_Upload::valid($value) AND Ku_Upload::not_empty($value))
		{
			// Get path to save file
			$sub_dir = $this->file_sub_dir($field, $value['name']);
			$save_path = $base_path.$sub_dir;

			// Create and make directory writable
			Ku_Dir::make_writable($base_path.$sub_dir, $config['dir_chmod']);

			// Generate safe filename
			$filename = Ku_File::safe_name($value['name'], TRUE, $config['max_filename_length']);

			$prefix = '';
			if ($config['force_unique_prefix'])
			{
				// Make unique filename
				$prefix = uniqid().'_';
			}

			while (file_exists($save_path.$prefix.$filename))
			{
				// Make unique filename to prevent override existing file
				$prefix = uniqid().'_';
			}

			$filename = $prefix.$filename;

			$filename = Ku_Upload::save($value, $filename, $save_path, $config['file_chmod']);

			if ( ! $filename)
			{
				throw new Kohana_Exception('File :filename not saved to a field :field of model :model',
						array(':filename' => $value['name'], ':field' => $field,':model' => $this->_orm->object_name()));
			}

		}
		elseif (is_string($value) AND is_file($value))
		{
			// Test allowed source directories
			if ( ! is_array($config['allowed_src_dirs']) OR empty($config['allowed_src_dirs']))
			{
				throw new Kohana_Exception('Field :field of model :model has no allowed source directories',
						array(':field' => $field,':model' => $this->_orm->object_name()));
			}

			foreach ($config['allowed_src_dirs'] as $dir)
			{
				if (strpos(realpath($value), realpath($dir)) === 0)
				{
					// Allowed directory found
					$allowed_dir = $dir;
					break;
				}
			}

			if ( ! isset($allowed_dir))
			{
				// Allowed directory not found
				throw new Kohana_Exception('File :filename is not in the allowed source directory of field :field of model :model',
						array(':filename' => Debug::path($value), ':field' => $field,':model' => $this->_orm->object_name()));
			}

			// Get path to save file
			$sub_dir = $this->file_sub_dir($field, basename($value));
			$save_path = $base_path.$sub_dir;

			// Create and make directory writable
			Ku_Dir::make_writable($base_path.$sub_dir, Arr::get($config, 'dir_chmod'));

			// Generate safe filename
			$filename = Ku_File::safe_name(basename($value), TRUE, $config['max_filename_length']);

			if ($value !== $save_path.$filename)
			{
				$prefix = '';
				if ($config['force_unique_prefix'])
				{
					// Make unique filename
					$prefix = uniqid().'_';
				}

				while (file_exists($save_path.$prefix.$filename))
				{
					// Make unique filename to prevent override existing file
					$prefix = uniqid().'_';
				}

				$filename = $prefix.$filename;

				if (rename($value, $save_path.$filename))
				{
					$filename = $save_path.$filename;
				}
				else
				{
					// File not saved
					throw new Kohana_Exception('File :filename not saved to a field :field of model :model',
						array(':filename' => Debug::path($value), ':field' => $field,':model' => $this->_orm->object_name()));
				}
			}
			else
			{
				$filename = $value;
			}
		}
		else
		{
			throw new Kohana_Exception('Invalid file parameter :value for field :field of model :model',
					array(':value' => (string) $value, ':field' => $field,':model' => $this->_orm->object_name()));
		}

		if ( ! empty($filename))
		{
			// Set chmod
			chmod($filename, $config['file_chmod']);

			// Save only path relative base path
			$save_value = $sub_dir.basename($filename);
			$save_value = ltrim(str_replace('\\', '/',  $save_value), '/');
			// Assign ORM field
			$this->_orm->$field = $save_value;
		}

		return $filename;
	}

	/**
	 * Returns full file path
	 *
	 * @param   string   $field  File field name
	 * @param   string   $value  File field value
	 * @return  string
	 */
	public function file_path($field, $value = NULL)
	{
		$this->_check_file_field($field);

		if ($value === NULL)
		{
			$value = $this->_orm->$field;
		}

		$path = $this->_file_fields[$field]['path'].DIRECTORY_SEPARATOR.$value;

		return str_replace('/', DIRECTORY_SEPARATOR, $path);
	}

	/**
	 * Returns "inner" file path relative base path
	 * Useful to prevent overflowing of directory
	 *
	 * @param   string   $field  File field name
	 * @param   string   $value  File field value
	 * @return  string
	 */
	public function file_sub_dir($field, $value = NULL)
	{
		$this->_check_file_field($field);

		if ($value === NULL)
		{
			$value = $this->_orm->$field;
		}

		$value = (string) $value;

		if (empty($value))
			return '';

		$value = basename($value);

		$md5 = md5($value);

		return substr($md5, 0, 2).DIRECTORY_SEPARATOR.substr($md5, 2, 2).DIRECTORY_SEPARATOR;
	}

	/**
	 * Returns file web path or FALSE if this field
	 * is not available from web
	 *
	 * @param   string   $field  File field name
	 * @param   string   $value  File field value
	 * @return  string|boolean
	 */
	public function file_uri($field, $value = NULL)
	{
		$this->_check_file_field($field);

		$uri = Arr::get($this->_file_fields[$field], 'uri');

		if ($uri === FALSE)
			// This field is not available from web
			return FALSE;

		if ($value === NULL)
		{
			$value = $this->_orm->$field;
		}

		return $uri.($uri ? '/' : '').$value;
	}

	/**
	 * Returns array of position fields with params
	 *
	 * @return  array
	 */
	public function position_fields()
	{
		return $this->_position_fields;
	}

	/**
	 * Returns position step for field
	 *
	 * @param   string   $value  Position field name
	 * @return  integer
	 */
	public function position_step($field)
	{
		$this->_check_position_field($field);

		return $this->_position_fields[$field]['step'];
	}

	/**
	 * Change position of item
	 * Move item prev, next, first or last
	 *
	 * @param    string   $field      Position field name
	 * @param    integer  $direction  Direction of moving
	 * @return   boolean
	 */
	public function position_move($field, $direction)
	{
		$this->_check_position_field($field);

		switch ($direction) {
			case ORM_Position::MOVE_FIRST:
				$this->position_first($field);
			break;
			case ORM_Position::MOVE_LAST:
				$this->position_last($field);
			break;
			case ORM_Position::MOVE_PREV:
				$this->position_prev($field);
			break;
			case ORM_Position::MOVE_NEXT:
				$this->position_next($field);
			break;

			default:
				throw new Kohana_Exception(
					'Cannot change position of :model model because direction :direction is undefined.',
					array(':model' => $model->object_name(), ':direction' => $direction));
			break;
		}
	}

	/**
	 * Move item to previous position
	 *
	 * @param    string   $field      Position field name
	 * @return   boolean
	 */
	public function position_prev($field)
	{
		// Check
		$this->_check_position_field($field);

		// Prepare
		$model = $this->_orm;
		$current_position = $model->$field;
		$table = $model->table_name();
		$config = $this->_position_fields[$field];

		if ( ! $model->loaded())
			throw new Kohana_Exception('Cannot set previous position of :model model because it is not loaded.', array(':model' => $model->object_name()));

		// Find previos position
		$builder = DB::select($field)
			->from($table);
		foreach ($config['group_by'] as $gb_field)
		{
			$builder->where($gb_field, '=', $model->$gb_field);
		}
		$builder->where($field, '<', $current_position);
		$builder->order_by($field, 'DESC');
		$builder->limit(1);
		$previos_position = $builder
			->execute($this->_db)
			->get($field);

		if ($previos_position < $config['start'])
			// Do nothing
			return FALSE;

		// Shift down top elements
		$builder = DB::update($table)
			->value($field, $current_position);
		foreach ($config['group_by'] as $gb_field)
		{
			$builder->where($gb_field, '=', $model->$gb_field);
		}
		$builder->where($field, '=', $previos_position);
		$builder->execute($this->_db);

		// Set new position
		$model->$field = $previos_position;
		$model->save();

		return $model->saved();
	}

	/**
	 * Move item to next position
	 *
	 * @param    string   $field      Position field name
	 * @return   boolean
	 */
	public function position_next($field)
	{
		// Check
		$this->_check_position_field($field);

		// Prepare
		$model = $this->_orm;
		$current_position = $model->$field;
		$table = $model->table_name();
		$config = $this->_position_fields[$field];

		if ( ! $model->loaded())
			throw new Kohana_Exception('Cannot set next position of :model model because it is not loaded.', array(':model' => $model->object_name()));

		// Find next position
		$builder = DB::select($field)
			->from($table);
		foreach ($config['group_by'] as $gb_field)
		{
			$builder->where($gb_field, '=', $model->$gb_field);
		}
		$builder->where($field, '>', $current_position);
		$builder->order_by($field, 'ASC');
		$builder->limit(1);
		$next_position = $builder
			->execute($this->_db)
			->get($field);

		if ($next_position < $current_position)
			// Do nothing
			return FALSE;

		// Shift up bottom elements
		$builder = DB::update($table)
			->value($field, $current_position);
		foreach ($config['group_by'] as $gb_field)
		{
			$builder->where($gb_field, '=', $model->$gb_field);
		}
		$builder->where($field, '=', $next_position);
		$builder->execute($this->_db);

		// Set new position
		$model->$field = $next_position;
		$model->save();

		return $model->saved();
	}

	/**
	 * Move item to first position
	 *
	 * @param    string   $field      Position field name
	 * @return   boolean
	 */
	public function position_first($field)
	{
		// Check
		$this->_check_position_field($field);

		// Prepare
		$model = $this->_orm;
		$current_position = $model->$field;
		$table = $model->table_name();
		$config = $this->_position_fields[$field];
		$step= $config['step'];

		if ( ! $model->loaded())
			throw new Kohana_Exception('Cannot set first position of :model model because it is not loaded.', array(':model' => $model->object_name()));

		// Find first position
		$builder = DB::select($field)
			->from($table);
		foreach ($config['group_by'] as $gb_field)
		{
			$builder->where($gb_field, '=', $model->$gb_field);
		}
		$builder->where($field, '<', $current_position);
		$builder->order_by($field, 'ASC');
		$builder->limit(1);
		$first_position = $builder
			->execute($this->_db)
			->get($field);

		if ( ! $first_position OR $first_position == $current_position)
			// Do nothing
			return FALSE;

		// Shift down top elements
		$builder = DB::update($table)
			->value($field, DB::expr("`$field`+$step"));
		foreach ($config['group_by'] as $gb_field)
		{
			$builder->where($gb_field, '=', $model->$gb_field);
		}
		$builder->where($field, '<', $current_position);
		$builder->execute($this->_db);

		// Set new position
		$model->$field = $first_position;
		$model->save();

		return $model->saved();
	}

	/**
	 * Move item to last position
	 *
	 * @param    string   $field      Position field name
	 * @return   boolean
	 */
	public function position_last($field)
	{
		// Check
		$this->_check_position_field($field);

		// Prepare
		$model = $this->_orm;
		$current_position = $model->$field;
		$table = $model->table_name();
		$config = $this->_position_fields[$field];
		$step= $config['step'];

		if ( ! $model->loaded())
			throw new Kohana_Exception('Cannot set last position of :model model because it is not loaded.', array(':model' => $model->object_name()));

		// Find last position
		$builder = DB::select($field)
			->from($table);
		foreach ($config['group_by'] as $gb_field)
		{
			$builder->where($gb_field, '=', $model->$gb_field);
		}
		$builder->where($field, '>', $current_position);
		$builder->order_by($field, 'DESC');
		$builder->limit(1);
		$last_position = $builder
			->execute($this->_db)
			->get($field);

		if ( ! $last_position OR $last_position == $current_position)
			// Do nothing
			return FALSE;

		// Shift down top elements
		$builder = DB::update($table)
			->value($field, DB::expr("`$field`-$step"));
		foreach ($config['group_by'] as $gb_field)
		{
			$builder->where($gb_field, '=', $model->$gb_field);
		}
		$builder->where($field, '>', $current_position);
		$builder->execute($this->_db);

		// Set new position
		$model->$field = $last_position;
		$model->save();

		return $model->saved();
	}

	/**
	 * Move item to specified position
	 *
	 * @param    string   $field      Position field name
	 * @param    integer  $field      New position value
	 * @return   boolean
	 */
	public function position_set($field, $position = NULL)
	{
		$this->_check_position_field($field);

		$model = $this->_orm;
		$table = $model->table_name();
		$config = $this->_position_fields[$field];
		$step = $config['step'];

		if ( ! $model->loaded())
			throw new Kohana_Exception('Cannot set position :model model because it is not loaded.', array(':model' => $model->object_name()));

		if ($position === NULL)
		{
			$position = $model->$field;
		}

		$old_position = $model->changed($field)
			? Arr::get($model->original_values(), $field)
			: $model->$field;

		if ($position == $old_position)
			// Do nothing
			return TRUE;

		$builder = DB::update($table);

		if ($old_position > $position)
		{
			// Move current item up
			$builder->value($field, DB::expr("`$field`+$step"));
			$builder->where($field, '>=', $position);
			$builder->where($field, '<', $old_position);
		}
		else
		{
			// Move current item down
			$builder->value($field, DB::expr("`$field`-$step"));
			$builder->where($field, '<=', $position);
			$builder->where($field, '>', $old_position);
		}

		foreach ($config['group_by'] as $gb_field)
		{
			$builder->where($gb_field, '=', $model->$gb_field);
		}
		$builder->execute($this->_db);

		$model->set($field, $position);
		$model->save();

		return $model->saved();
	}

	/**
	 * Renumerate positions of items
	 * Returns count of fixed positions
	 *
	 * @param    string   $field      Position field name
	 * @param    boolean  $reset      Reset all positions
	 * @return   integer
	 */
	public function position_fix($field, $reset = FALSE)
	{
		$this->_check_position_field($field);

		$model = $this->_orm;
		$table = $model->table_name();
		$primary_key = $model->primary_key();

		$config = $this->_position_fields[$field];

		$select_array = $config['group_by'];
		$select_array[] = $primary_key;
		$select_array[] = $field;

		$builder = DB::select_array($select_array)
			->from($table);
		foreach ($config['group_by'] as $gb_field)
		{
			$builder->order_by($gb_field, 'ASC');
		}
		if ( ! $reset)
		{
			// Use current positions
			$builder->order_by($field, 'ASC');
		}
		foreach ($config['order_by'] as $ob_field => $ob_direction)
		{
			$builder->order_by($ob_field, $ob_direction);
		}
		$result = $builder
			->execute($this->_db)
			->as_array();

		$counter = 0;
		$step = $config['step'];
		$start = $config['start'];
		$pos = $start;
		$current_gb = NULL;
		foreach ($result as $row)
		{
			if ($config['group_by'])
			{
				// Get "GROUP BY" columns
				$gb_value = array_slice($row, 0, count($config['group_by']));
				if ($gb_value != $current_gb)
				{
					// Start new group
					$current_gb = $gb_value;
					$pos = $start;
				}
			}

			if ($row[$field] != $pos)
			{
				// Execute update
				DB::update($table)
					->value($field, $pos)
					->where($primary_key, '=', $row[$primary_key])
					->execute($this->_db);

				++$counter;
			}

			$pos += $step;
		}

		return $counter;
	}


	/**
	 * Returns min existing position
	 *
	 * @param    string   $field      Position field name
	 * @return   integer
	 */
	public function position_min($field)
	{
		$this->_check_position_field($field);

		$model = $this->_orm;

		$config = $this->_position_fields[$field];

		$builder = DB::select(array(DB::expr("MIN(`$field`)"), 'min_position'))
			->from($model->table_name());
		foreach ($config['group_by'] as $gb_field)
		{
			$builder->where($gb_field, '=', $model->$gb_field);
		}

		return $builder->execute($this->_db)->get('min_position');
	}

	/**
	 * Returns max existing position
	 *
	 * @param    string   $field      Position field name
	 * @return   integer
	 */
	public function position_max($field)
	{
		$this->_check_position_field($field);

		$model = $this->_orm;

		$config = $this->_position_fields[$field];

		$builder = DB::select(array(DB::expr("MAX(`$field`)"), 'max_position'))
		->from($model->table_name());
		foreach ($config['group_by'] as $gb_field)
		{
			$builder->where($gb_field, '=', $model->$gb_field);
		}

		return $builder->execute($this->_db)->get('max_position');
	}

	/**
	 * Returns name of "deleted" field
	 *
	 * @return  string
	 */
	public function deleted_field()
	{
		return $this->_safe_delete_field;
	}

	/**
	 * Updates or Creates the record depending on orm->loaded()
	 * Validate and save any file fields.
	 *
	 * @chainable
	 * @param   array    $values  Array of values to save
	 * @param  Validation $validation Validation object
	 * @return  ORM_Helper
	 */
	public function save(array $values, Validation $validation = NULL)
	{
		$orm = $this->_orm;

		$file_errors = FALSE;
		$tmp_files = array();
		$old_files = array();

		try
		{
			// Process files
			if ( ! empty($this->_file_fields))
			{
				$original_values = $orm->original_values();

				$file_values = array_intersect_key($values, $this->_file_fields);

				foreach ($this->_file_fields as $field => $config)
				{
					if ( ! isset($values[$field]))
						continue;

					if (empty($values[$field]))
					{
						// Store old value to delete after saving
						$old_files[$field] = Arr::get($original_values, $field);
						// Dont validate empty value
						unset($file_values[$field]);
					}

					if ($orm->$field === $values[$field])
					{
						// File not changed - skip this value
						unset($file_values[$field]);
					}
				}

				// Get user-defined labels
				$labels = $orm->labels();

				// Create file validation object
				$file_validation = Validation::factory($file_values);
				foreach ($this->file_rules() as $field =>$rules)
				{
					$file_validation->rules($field, $rules);
					$file_validation->label($field, Arr::get($labels, $field, $field));
				}

				// Determine if any file validation failed
				$file_errors = ! $file_validation->check();

				if ( ! $file_errors)
				{
					foreach ($file_values as $field => $value)
					{
						// Store old value
						$old_files[$field] = Arr::get($original_values, $field);
						// Save files
						try
						{
							$tmp_files[$field] = $this->file_save($field, $value);
						}
						catch (Kohana_Exception $e)
						{
							$filename = is_array($value)
								? Arr::get($value, 'name')
								: Debug::path($value);

							$file_validation->error(
								$field,
								'Error of saving file ":file": :message',
								array(
									':file' => $filename,
									':message' => $e->getMessage()
								)
							);

							// File not saved
							Kohana::$log->add(
								Log::ERROR,
								'File :file in the :field field of :model model not saved. Exception occurred: :exception',
								array(
									':file'      => $filename,
									':field'     => $field,
									':model'     => $orm->object_name(),
									':exception' => $e->getMessage()
								)
							);

							break; // Stop saving of files
						}
					}
				}

				// Remove file fields from array of values
				$values = array_diff_key($values, $file_values);
			}

			// Assign values
			$orm->values($values);

			// Process position fields for create action
			if ( ! $orm->loaded() AND $this->_position_fields)
			{
				foreach ($this->_position_fields as $field => $config)
				{
					if (isset($values[$field]))
						// Position already assigned
						continue;
					$orm->$field = $this->position_max($field) + $config['step'];
				}
			}

			try
			{
				if ($file_errors)
				{
					$orm->check($validation);
					// Force exception
					throw new ORM_Validation_Exception($orm->errors_filename(), $orm->validation());
				}
				else
				{
					$orm->save($validation);
				}
			}
			catch (ORM_Validation_Exception $e)
			{
				if ($file_errors)
				{
					// Merge any possible errors from the external object
					$e->add_object('_files', $file_validation);
				}
				throw $e;
			}
		}
		catch (Exception $e)
		{
			// Delete unsaved files
			foreach ($tmp_files as $field => $filename)
			{
				if ( ! empty($filename) AND is_file($filename))
				{
					try
					{
						chmod($filename, $this->_file_fields[$field]['file_chmod']);
						unlink($filename);
					}
					catch (Exception $e)
					{
						// File not deleted
						Kohana::$log->add(
								Log::ERROR,
								'File :file in the :field field of :model model not deleted after save error. Exception occurred: :exception',
								array(
									':file'      => Debug::path($filename),
									':field'     => $field,
									':model'     => $orm->object_name(),
									':exception' => $e->getMessage()
								)
						);
					}
				}
			}
			throw $e;
		}

		if ($orm->saved() AND count($old_files))
		{
			foreach ($old_files as $field => $value)
			{

				if (empty($value) OR $value === $orm->$field)
					continue;

				$filename = $this->file_path($field, $value);

				if ( ! is_file($filename))
					continue;

				$config = $this->_file_fields[$field];

				switch ($config['on_update'])
				{
					case ORM_File::ON_UPDATE_RENAME:
						try
						{
							if ( ! isset($uniqid))
							{
								$uniqid = uniqid();
							}
							$prefix = $config['replaced_prefix'].$uniqid.'_';
							$new_filename = dirname($filename).DIRECTORY_SEPARATOR.$prefix.basename($filename);
							chmod($filename, $config['file_chmod']);
							rename($filename, $new_filename);
						}
						catch (Exception $e)
						{
							// File not renamed
							Kohana::$log->add(
									Log::ERROR,
									'File :file in the :field field of :model model not renamed after replacing. Exception occurred: :exception',
									array(
										':file'      => Debug::path($filename),
										':field'     => $field,
										':model'     => $orm->object_name(),
										':exception' => $e->getMessage()
									)
							);
						}

					break;
					case ORM_File::ON_UPDATE_UNLINK:
						try
						{
							chmod($filename, $config['file_chmod']);
							unlink($filename);
						}
						catch (Exception $e)
						{
							// File not deleted
							Kohana::$log->add(
									Log::ERROR,
									'File :file in the :field field of :model model not deleted after replacing. Exception occurred: :exception',
									array(
										':file'      => Debug::path($filename),
										':field'     => $field,
										':model'     => $orm->object_name(),
										':exception' => $e->getMessage()
									)
							);
						}
					break;
				}
			}
		}

		return $this;
	}

	/**
	 * Provide safe or real cascade delete with file fields processing
	 *
	 * @chainable
	 * @param   boolean  $real Execute real delete or mark record as "deleted"
	 * @param   array    $where Array of "WHERE" conditions
	 * @param   boolean  $cascade_delete Execute cascade delete
	 * @param   boolean  $is_slave_delete Execute delete of slave record
	 * @return  ORM_Helper
	 */
	public function delete($real_delete, array $where = NULL, $cascade_delete = TRUE, $is_slave_delete = FALSE)
	{
		$safe_delete = ! $real_delete;

		$model = $this->_orm;
		$table = $model->table_name();
		$primary_key = $model->primary_key();
		$deleted_field = $this->deleted_field();

		if ($safe_delete AND empty($deleted_field))
			throw new Kohana_Exception('Cannot safe delete :model model because it is not suport this action.',
					array(':model' => $model->object_name()));

		if (empty($where) AND ! $model->loaded())
			throw new Kohana_Exception('Cannot delete :model model because it is not loaded.',
					array(':model' => $model->object_name()));

		if (empty($where))
		{
			// Delete current record only
			$where = array(array($primary_key, '=', $model->pk()));
		}

		// Process file fields
		if (count($this->_file_fields))
		{
			// Get all affected files
			$selected_fields = array_keys($this->_file_fields);
			$selected_fields[] = $primary_key;

			// Use one uniqid
			$uniqid = uniqid();

			$builder = DB::select_array($selected_fields)
				->from($table);
			if ($safe_delete)
			{
				// process only currently "active" records
				$builder->where($deleted_field, '=', 0);
			}
			foreach ($where as $condition)
			{
				$builder->where($condition[0], $condition[1], $condition[2]);
			}
			$rows = $builder
				->execute($this->_db)
				->as_array();

			foreach ($rows as $row)
			{
				$update = array();

				foreach ($this->_file_fields as $field => $field_config)
				{
					$value = $row[$field];

					if (empty($value))
						continue;

					$file_path = $this->file_path($field, $value);

					if ( ! is_file($file_path))
					{
						// File not exists
						Kohana::$log->add(
								Log::ERROR,
								'File :file in the :field field of :model model with primary key :pk not exists',
								array(
									':file'  => Debug::path($file_path),
									':field' => $field,
									':model' => $model->object_name(),
									':pk'    => $row[$primary_key]
								)
						);
						continue;
					}

					// Process file
					switch ($field_config['on_delete'])
					{
						case ORM_File::ON_DELETE_UNLINK:

							if ($safe_delete)
								continue;

							try
							{
								unlink($file_path);
							}
							catch (Exception $e)
							{
								Kohana::$log->add(
										Log::ERROR,
										'File :file in the :field field of :model model with primary key :pk cannot be deleted',
										array(
											':file'  => Debug::path($file_path),
											':field' => $field,
											':model' => $model->object_name(),
											':pk'    => $row[$primary_key]
										)
								);
							}
							break;

						case ORM_File::ON_DELETE_RENAME:
							try
							{
								$del_prefix = $field_config['deleted_prefix'];
								if ( ! empty($del_prefix) AND strpos(basename($value),$del_prefix) !== 0)
								{
									$del_prefix .= $uniqid.'_';

									$new_path = dirname($file_path).DIRECTORY_SEPARATOR.$del_prefix.basename($file_path);

									rename($file_path,$new_path);

									$new_value = dirname($value).DIRECTORY_SEPARATOR.$del_prefix.basename($value);
									$update[$field] = ltrim(str_replace('\\', '/', $new_value), '/');
								}
							}
							catch (Exception $e)
							{
								Kohana::$log->add(
										Log::ERROR,
										'File :file in the :field field of :model model with primary key :pk cannot be renamed',
										array(
											':file'  => Debug::path($file_path),
											':field' => $field,
											':model' => $model->object_name(),
											':pk'    => $row[$primary_key]
										)
								);
							}
							break;
					}
				}

				if ($safe_delete AND count($update))
				{
					// Save new file names in the database
					DB::update($table)
						->set($update)
						->where($primary_key, '=', $row[$primary_key])
						->execute();
				}
			}
		}
		// end process file fields

		if (($cascade_delete AND ! empty($this->_on_delete_cascade)) OR
		    ($real_delete AND $this->_on_delete_clear_pivot_tables))
		{
			// Get affected ids
			$builder = DB::select($primary_key)
				->from($table);

			foreach ($where as $condition)
			{
				$builder->where($condition[0], $condition[1], $condition[2]);
			}

			if ( ! $is_slave_delete)
			{
				// This master delete action
				// Select only "active" records
				$builder->where($deleted_field, '=', 0);
			}

			$affected_ids = $builder
				->execute($this->_db)
				->as_array(NULL, $primary_key);

			if ( ! empty($affected_ids))
			{
				$has_many   = $model->has_many();
				$belongs_to = $model->belongs_to();
				$has_one    = $model->has_one();

				if ($cascade_delete)
				{
					// Execute cascade delete
					foreach ($this->_on_delete_cascade as $foreign_key => $column)
					{
						$related_model_name = $column;

						if (is_int($foreign_key))
						{
							$foreign_key = $model->object_name().'_id';
						}

						if (isset($has_many[$column]))
						{
							$related_model_name = $has_many[$column]['model'];

							if (isset($has_many[$column]['through']))
							{
								if  ($real_delete)
								{
									// Clear through table
									DB::delete($has_many[$column]['through'])
										->where($has_many[$column]['far_key'], 'IN', $affected_ids)
										->execute();

									// Prevent double delete
									unset($has_many[$column]);
								}

								// Dont delete many to many
								continue;
							}
							else
							{
								$related_model = ORM::factory($has_many[$column]['model']);
								$orm_helper_class = 'ORM_Helper_'.ucfirst($related_model->object_name());

								if (class_exists($orm_helper_class))
								{
									// Use wrapper for cascade processing
									ORM_Helper::factory($related_model)
										->delete($real_delete, array(array($has_many[$column]['foreign_key'], 'IN', $affected_ids)), TRUE, TRUE);
								}
								else if ($real_delete)
								{
									// Remove related items manually
									DB::delete($related_model->table_name())
										->where($has_many[$column]['foreign_key'], 'IN', $affected_ids)
										->execute();
								}
							}
						}
						else if (isset($has_one[$column]))
						{
							$related_model = ORM::factory($has_one[$column]['model']);
							$orm_helper_class = 'ORM_Helper_'.ucfirst($related_model->object_name());

							if (class_exists($orm_helper_class))
							{
								// Use wrapper for cascade processing
								ORM_Helper::factory($related_model)
									->delete($real_delete, array(array($has_one[$column]['foreign_key'], 'IN', $affected_ids)), TRUE, TRUE);
							}
							else if ($real_delete)
							{
								// Remove related items manually
								DB::delete($related_model->table_name())
									->where($has_one[$column]['foreign_key'], 'IN', $affected_ids)
									->execute();
							}
						}
						else if (isset($belongs_to[$column]))
						{
							// It's unexpected
							continue;
						}
						else
						{
							// Column not found in the relations
							if ( ! $safe_delete)
							{
								// Remove related items manually
								// Use $column as a table name
								DB::delete($column)
									->where($foreign_key, 'IN', $affected_ids)
									->execute();
							}
						}

					}
				}

				if ($real_delete AND $this->_on_delete_clear_pivot_tables AND ! empty($has_many))
				{
					foreach ($has_many as $hm_config)
					{
						if (isset($hm_config['through']))
						{
							// Clear through table
							DB::delete($hm_config['through'])
								->where($hm_config['far_key'], 'IN', $affected_ids)
								->execute();
						}
					}
				}
			}
		}

		if ($safe_delete)
		{
			// Safe delete
			if ($is_slave_delete)
			{
				$del_value = DB::expr("`$deleted_field`+2");
			}
			else
			{
				$del_value = DB::expr("IF(MOD(`$deleted_field`,2)=0,`$deleted_field`+1,`$deleted_field`)");
			}

			$builder = DB::update($table)
				->value($deleted_field, $del_value);
			foreach ($where as $condition)
			{
				$builder->where($condition[0], $condition[1], $condition[2]);
			}
			$builder->execute($this->_db);
		}
		else
		{
			// Real delete
			$builder = DB::delete($table);
			foreach ($where as $condition)
			{
				$builder->where($condition[0], $condition[1], $condition[2]);
			}
			$builder->execute($this->_db);
		}

		$this->_orm->clear();

		return $this;
	}

	/**
	 * Restore safe deleted records with file fields processing
	 *
	 * @param   array    $where Array of "WHERE" conditions
	 * @param   boolean  $cascade_restore Execute cascade restore
	 * @param   boolean  $is_slave_restore Execute restore of slave record
	 * @return  void
	 */
	public function restore(array $where = NULL, $cascade_restore = TRUE, $is_slave_restore = FALSE)
	{
		$model = $this->_orm;
		$table = $model->table_name();
		$primary_key = $model->primary_key();
		$deleted_field = $this->deleted_field();

		if (empty($deleted_field))
			throw new Kohana_Exception('Cannot restore :model model because it is not suport this action.',
					array(':model' => $model->object_name()));

		if (empty($where) AND ! $model->loaded())
			throw new Kohana_Exception('Cannot restore :model model because it is not loaded.',
					array(':model' => $model->object_name()));

		if (empty($where))
		{
			// Restore current record only
			$where = array(array($primary_key, '=', $model->pk()));
		}

		// Safe restore
		if ($is_slave_restore)
		{
			$del_value = DB::expr("IF(`$deleted_field`>1, `$deleted_field`-2, `$deleted_field`)");
		}
		else
		{
			$del_value = DB::expr("IF(MOD(`$deleted_field`,2)>0, `$deleted_field`-1, `$deleted_field`)");
		}

		$builder = DB::update($table)->value($deleted_field, $del_value);
		foreach ($where as $condition)
		{
			$builder->where($condition[0], $condition[1], $condition[2]);
		}
		$builder->execute($this->_db);

		// Process file fields
		if (count($this->_file_fields))
		{
			// Get all affected files
			$selected_fields = array_keys($this->_file_fields);
			$selected_fields[] = $primary_key;

			// Process only "active" records
			$builder = DB::select_array($selected_fields)
				->from($table)
				->where($deleted_field, '=', 0);
			foreach ($where as $condition)
			{
				$builder->where($condition[0], $condition[1], $condition[2]);
			}
			$builder;
			$rows = $builder
				->execute($this->_db)
				->as_array();

			foreach ($rows as $row)
			{
				$update = array();

				foreach ($this->_file_fields as $field => $field_config)
				{
					$value = $row[$field];

					if (empty($value))
						continue;

					$file_path = $this->file_path($field, $value);

					if ( ! is_file($file_path))
					{
						// File not exists
						Kohana::$log->add(
								Log::ERROR,
								'File :file in the :field field of :model model with primary key :pk not exists',
								array(
									':file'  => Debug::path($file_path),
									':field' => $field,
									':model' => $model->object_name(),
									':pk'    => $row[$primary_key]
								)
						);
						continue;
					}

					// Process file
					switch ($field_config['on_delete'])
					{
						case ORM_File::ON_DELETE_RENAME:
							try
							{
								$del_prefix = $field_config['deleted_prefix'];
								$base_name = basename($value);

								if ( ! empty($del_prefix) AND strpos($base_name, $del_prefix) === 0)
								{
									$del_prefix_length = strlen($del_prefix) + 14; // strlen(uniqid().'_') === 14

									// Remove prefix
									$new_base_name = substr($base_name, $del_prefix_length);

									$new_path = dirname($file_path).DIRECTORY_SEPARATOR.$new_base_name;

									rename($file_path, $new_path);

									$new_value = dirname($value).DIRECTORY_SEPARATOR.$new_base_name;
									$update[$field] = ltrim(str_replace('\\', '/', $new_value), '/');
								}
							}
							catch (Exception $e)
							{
								Kohana::$log->add(
										Log::ERROR,
										'File :file in the :field field of :model model with primary key :pk cannot be renamed',
										array(
											':file'  => Debug::path($file_path),
											':field' => $field,
											':model' => $model->object_name(),
											':pk'    => $row[$primary_key]
										)
								);
							}
							break;
					}
				}

				if (count($update))
				{
					// Save restored file names in the database
					DB::update($table)
						->set($update)
						->where($primary_key, '=', $row[$primary_key])
						->execute();
				}
			}
		}
		// end process files

		if ($cascade_restore AND ! empty($this->_on_delete_cascade))
		{
			$builder = DB::select($primary_key)
				->from($table);

			foreach ($where as $condition)
			{
				$builder->where($condition[0], $condition[1], $condition[2]);
			}

			$affected_ids = $builder
				->execute($this->_db)
				->as_array(NULL, $primary_key);

			if ( ! empty($affected_ids))
			{
				$has_many   = $model->has_many();
				$belongs_to = $model->belongs_to();
				$has_one    = $model->has_one();

				// Execute cascade restore
				foreach ($this->_on_delete_cascade as $foreign_key => $column)
				{
					$related_model_name = $column;

					if (is_int($foreign_key))
					{
						$foreign_key = $model->object_name().'_id';
					}

					if (isset($has_many[$column]))
					{
						$related_model_name = $has_many[$column]['model'];

						if (isset($has_many[$column]['through']))
						{
							// Dont process many to many
							continue;
						}
						else
						{
							$related_model = ORM::factory($has_many[$column]['model']);
							$orm_helper_class = 'ORM_Helper_'.ucfirst($related_model->object_name());

							if (class_exists($orm_helper_class))
							{
								// Use wrapper for cascade processing
								ORM_Helper::factory($related_model)
									->restore(array(array($has_many[$column]['foreign_key'], 'IN', $affected_ids)), TRUE);
							}
						}
					}
					else if (isset($has_one[$column]))
					{
						$related_model = ORM::factory($has_one[$column]['model']);
						$orm_helper_class = 'ORM_Helper_'.ucfirst($related_model->object_name());

						if (class_exists($orm_helper_class))
						{
							$has_one_ids = DB::select($has_one[$column]['foreign_key'])
								->from($table)
								->where($primary_key, 'IN', $affected_ids)
								->execute()
								->as_array(NULL, $has_one[$column]['foreign_key'])
								;

							// Use wrapper for cascade processing
							ORM_Helper::factory($related_model)
								->restore(array(array($related_model->primary_key(), 'IN', $has_one_ids)), TRUE);
						}
					}
				}
			}
		}

		$this->_orm->reload();
	}

	/**
	 * Provide safe cascade delete with file fields processing
	 *
	 * @param   array    $where Array of "WHERE" conditions
	 * @param   boolean  $cascade_delete Execute cascade delete
	 * @return  void
	 */
	public function safe_delete(array $where = NULL, $cascade_delete = TRUE)
	{
		$this->delete(FALSE, $where, $cascade_delete);
	}

	/**
	 * Initialize file fields
	 *
	 * @return  void
	 */
	protected function _initialize_file_fields()
	{
		// Init file fields
		$def_config = self::$default_file_config;

		if (empty($def_config['dir_chmod']))
		{
			$def_config['dir_chmod'] = Ku_Upload::$default_dir_chmod;
		}
		if (empty($def_config['file_chmod']))
		{
			$def_config['file_chmod'] = Ku_Upload::$default_file_chmod;
		}
		if (empty($def_config['path']))
		{
			$def_config['path'] = Ku_Upload::$default_directory;
		}

		foreach ($this->_file_fields as $field => $config)
		{
			if (empty($config['path']))
			{
				$config['path'] = $def_config['path']
					.DIRECTORY_SEPARATOR.$this->_orm->object_name()
					.DIRECTORY_SEPARATOR.$field;
			}

			$config += $def_config;

			if ($config['uri'] === NULL)
			{
				$config['uri'] = str_replace('\\', '/', $config['path']);
				if (strlen($config['uri']) > strlen(DOCROOT))
				{
					$config['uri'] = str_replace(str_replace('\\', '/', DOCROOT), '', $config['uri']);
				}
			}
			$config['uri'] = trim($config['uri'], '/');

			$this->_file_fields[$field] = $config;

		}
	}

	/**
	 * Initialize position fields
	 *
	 * @return  void
	 */
	protected function _initialize_position_fields()
	{
		// Init file fields
		foreach ($this->_position_fields as $field => $config)
		{
			$this->_position_fields[$field] = $config + self::$default_position_config;
		}
	}

	/**
	 * Check if this field is a file field
	 *
	 * @param   string   $field  Field name
	 * @return  string
	 */
	protected function _check_file_field($field)
	{
		if ( ! isset($this->_file_fields[$field]))
			throw new Kohana_Exception('Field :field not is a file field of model :model',
					array(':field' => $field,':model' => get_class($this->_orm)));
	}

	/**
	 * Check if this field is a position field
	 *
	 * @param   string   $field  Field name
	 * @return  string
	 */
	protected function _check_position_field($field)
	{
		if ( ! isset($this->_position_fields[$field]))
			throw new Kohana_Exception('Field :field not is a position field of model :model',
					array(':field' => $field,':model' => get_class($this->_orm)));
	}
} // End ORM_Helper
