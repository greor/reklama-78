<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_ORM_Helper implements ORM_File, ORM_Position, ORM_Restore {

	const POSITION_SIMPLE   = 0;
	const POSITION_COMPLEX  = 1;

	const POSITION_NEXT = 0;
	const POSITION_PREV = 1;

	const POSITION_INCREASE = 0;
	const POSITION_DECREASE = 1;

	const POSITION_ALL    = 0;
	const POSITION_MASTER = 1;
	const POSITION_SLAVE  = 2;

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
		'step_master' => 1000,
		'start_master'  => 1000,
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
	 * Position type
	 * @var integer
	 */
	protected $_position_type = self::POSITION_SIMPLE;

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
			try
			{
				chmod($filename, $config['file_chmod']);
			}
			catch (Exception $e)
			{
				Kohana::$log->add(
						Log::ERROR,
						'Exception occurred: :exception. [:file][:line] ',
						array(
							':file'      => Debug::path(__FILE__),
							':line'      => __LINE__,
							':exception' => $e->getMessage()
						)
				);
			}

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

		if ($value === NULL) {
			$value = $this->_orm->$field;
		}
		$value = (string) $value;
		if (empty($value))
			return '';

		$_prefix = '';
		$_orm = $this->_orm;
		if ($_orm->has_site_id_column()) {
			$_site_id_column = $_orm->site_id_column();
			$_prefix = str_pad($_orm->$_site_id_column, 2, '0', STR_PAD_LEFT).DIRECTORY_SEPARATOR;
		}

		$value = basename($value);
		$md5 = md5($value);

		return $_prefix.date('Y').DIRECTORY_SEPARATOR.substr($md5, 0, 2).DIRECTORY_SEPARATOR.substr($md5, 2, 2).DIRECTORY_SEPARATOR;
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
	 * Returns min existing position
	 *
	 * @param    string   $field      Position field name
	 * @return   integer
	 */
	public function position_min($field, $master = FALSE)
	{
		$this->_check_position_field($field);

		$model = $this->_orm;
		$config = $this->_position_fields[$field];

		$builder = DB::select(array(DB::expr("MIN(`{$field}`)"), 'min_position'))
			->from($model->table_name());

		if ($master)
		{
			$builder
				->where(DB::expr("`{$field}`%{$config['step_master']}"), '=', 0);
		}

		$this->_position_apply_builder_conditions($field, $builder);

		return $builder->execute($this->_db)->get('min_position');
	}

	/**
	 * Returns max existing position
	 *
	 * @param    string   $field      Position field name
	 * @return   integer
	 */
	public function position_max($field, $master = FALSE)
	{
		$this->_check_position_field($field);

		$model = $this->_orm;
		$config = $this->_position_fields[$field];
		$builder = DB::select(array(DB::expr("MAX(`$field`)"), 'max_position'))
			->from($model->table_name());

		if ($master)
		{
			$builder
				->where(DB::expr("`{$field}`%{$config['step_master']}"), '=', 0);
		}

		$this->_position_apply_builder_conditions($field, $builder);

		return $builder->execute($this->_db)->get('max_position');
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
		$primary_key = $model->primary_key();
		$site_id_key = $model->site_id_column();
		$config = $this->_position_fields[$field];

		if ( ! $model->loaded())
			throw new Kohana_Exception('Cannot set next position of :model model because it is not loaded.', array(':model' => $model->object_name()));

		if ($this->_position_type === self::POSITION_SIMPLE)
		{
			$prev_orm = $this->_position_sibling( $field, $model->$field, self::POSITION_PREV, self::POSITION_SLAVE);
			if ( $prev_orm->count() > 0 )
			{
				return $this->_position_swap($field, array(
					'key' => $model->$primary_key, 'pos' => $model->$field
				), array(
					'key' => $prev_orm->get($primary_key), 'pos' => $prev_orm->get($field)
				));
			}
			else
			{
				// Do nothing - it is first element
				return FALSE;
			}
		}
		else if ($this->_position_type === self::POSITION_COMPLEX)
		{
			// Find prev position
			$type = $model->is_master()
				? self::POSITION_MASTER
				: self::POSITION_ALL;
			$prev_orm = $this->_position_sibling( $field, $model->$field, self::POSITION_PREV, $type);

			if ( $prev_orm->count() > 0 )
			{
				$sid = $prev_orm->get($site_id_key);
				$pos = $prev_orm->get($field);

				// $model and $prev_orm master/slave both (same type)
				if ($model->$site_id_key == $sid)
				{
					return $this->_position_swap($field, array(
						'key' => $model->$primary_key, 'pos' => $model->$field
					), array(
						'key' => $prev_orm->get($primary_key), 'pos' => $pos
					));
				}
				// $model - slave, $prev_orm - master
				else if ( ! $model->is_master() AND $sid == $model->site_id_master())
				{
					$_prev_orm = $this->_position_sibling( $field, $pos, self::POSITION_PREV, self::POSITION_ALL);

					if ($_prev_orm->count() > 0)
					{
						$model->$field = (int) $_prev_orm->get($field) + (int) $config['step'];
					}
					else
					{
						$model->$field = $config['start'];
					}

					$model->save();
					return $model->saved();
				}
			}
		}
		else
		{
			throw new Kohana_Exception('Cannot change position of :model model because position type is undefined.', array(':model' => $model->object_name()));
		}
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
		$primary_key = $model->primary_key();
		$site_id_key = $model->site_id_column();
		$config = $this->_position_fields[$field];

		if ( ! $model->loaded())
			throw new Kohana_Exception('Cannot set next position of :model model because it is not loaded.', array(':model' => $model->object_name()));

		if ($this->_position_type === self::POSITION_SIMPLE)
		{
			$next_orm = $this->_position_sibling( $field, $model->$field, self::POSITION_NEXT, self::POSITION_SLAVE);
			if ( $next_orm->count() > 0 )
			{
				return $this->_position_swap($field, array(
					'key' => $model->$primary_key, 'pos' => $model->$field
				), array(
					'key' => $next_orm->get($primary_key), 'pos' => $next_orm->get($field)
				));
			}
			else
			{
				// Do nothing - it is last element
				return FALSE;
			}
		}
		else if ($this->_position_type == self::POSITION_COMPLEX)
		{
			// Find next position
			$type = $model->is_master()
				? self::POSITION_MASTER
				: self::POSITION_ALL;
			$next_orm = $this->_position_sibling( $field, $model->$field, self::POSITION_NEXT, $type);

			if ( $next_orm->count() > 0 )
			{
				$sid = $next_orm->get($site_id_key);
				$pos = $next_orm->get($field);

				// $model and $next_orm master/slave both (same type)
				if ($model->$site_id_key == $sid)
				{
					return $this->_position_swap($field, array(
						'key' => $model->$primary_key, 'pos' => $model->$field
					), array(
						'key' => $next_orm->get($primary_key), 'pos' => $pos
					));
				}
				// $model - slave, $next_orm - master
				else if ( ! $model->is_master() AND $sid == $model->site_id_master())
				{
					$_next_orm = $this->_position_sibling( $field, $pos, self::POSITION_NEXT, self::POSITION_ALL);

					if ($_next_orm->count() > 0)
					{
						$_sid = $_next_orm->get($site_id_key);

						// $_next_orm - slave
						if ($_sid != $model->site_id_master())
						{
							$this->_position_shift($field, array(
								'start' => $_next_orm->get($field),
								'end' => (floor($pos / $config['step_master']) + 1) * $config['step_master'],
								'site_id' => $_sid,
								'value' => $config['step'],
								'direction' => self::POSITION_INCREASE,
							));
						}
					}

					$model->$field = (int) $pos + (int) $config['step'];
					$model->save();
					return $model->saved();
				}
			}
		}
		else
		{
			throw new Kohana_Exception('Cannot change position of :model model because position type is undefined.', array(':model' => $model->object_name()));
		}
	}

	/**
	 * Move item to first position
	 *
	 * @param    string   $field      Position field name
	 * @return   boolean
	 */
	public function position_first($field) {

		$this->_check_position_field($field);

		$model = $this->_orm;
		$config = $this->_position_fields[$field];
		$site_id_key = $model->site_id_column();
		$site_id = array_key_exists($site_id_key, $model->table_columns())
			? $model->site_id()
			: NULL;

		if ( ! $model->loaded())
			throw new Kohana_Exception('Cannot set first position of :model model because it is not loaded.', array(':model' => $model->object_name()));

		if ($this->_position_type == self::POSITION_SIMPLE)
		{
			if ($model->$field == $config['step'])
				return FALSE;

			$min = $this->position_min($field);
			if ($min == $config['step'])
			{
				$this->_position_shift($field, array(
					'start' => $min,
					'end' => $model->$field,
					'site_id' => $site_id,
					'direction' => self::POSITION_INCREASE,
				));
			}

			$model->$field = (int) $config['step'];
		}
		elseif ($model->is_master())
		{
			if ($model->$field == $config['step_master'])
				return FALSE;

			$min = $this->position_min($field, TRUE);
			if ($min == $config['step_master'])
			{
				$this->_position_shift($field, array(
					'start' => $min,
					'end' => $model->$field,
					'site_id' => $site_id,
					'direction' => self::POSITION_INCREASE,
					'type' => self::POSITION_MASTER,
				));
			}

			$model->$field = (int) $config['step_master'];
		}
		else
		{
			if ($model->$field == $config['step'])
				return FALSE;

			$null_span = $this->_get_master_span($field, $config['step']);
			if ($null_span['min'] == $config['step'])
			{
				$end = ($model->$field < $config['step_master'])
					? $model->$field
					: ($null_span['max'] + (int) $config['step']);

				$this->_position_shift($field, array(
					'start' => $null_span['min'],
					'end' => $end,
					'site_id' => $site_id,
					'direction' => self::POSITION_INCREASE,
				));
			}

			if ($model->$field > $config['step_master'])
			{
				$master_span = $this->_get_master_span($field, $model->$field);
				$this->_position_shift($field, array(
					'start' => $model->$field,
					'end' => $master_span['max'],
					'site_id' => $site_id,
					'direction' => self::POSITION_DECREASE,
				));

			}

			$model->$field = (int) $config['step'];
		}

		$model->save();
		return $model->saved();
	}

	/**
	 * Move item to last position
	 *
	 * @param    string   $field      Position field name
	 * @return   boolean
	 */
	public function position_last($field) {

		$this->_check_position_field($field);

		$model = $this->_orm;
		$config = $this->_position_fields[$field];
		$site_id_key = $model->site_id_column();
		$site_id = array_key_exists($site_id_key, $model->table_columns())
			? $model->site_id()
			: NULL;

		if ( ! $model->loaded())
			throw new Kohana_Exception('Cannot set last position of :model model because it is not loaded.', array(':model' => $model->object_name()));

		if ($this->_position_type == self::POSITION_SIMPLE)
		{
			$max = $this->position_max($field);
			if ($model->$field == $max)
				return FALSE;

			$this->_position_shift($field, array(
				'start' => $model->$field,
				'end' => $max,
				'site_id' => $site_id,
				'direction' => self::POSITION_DECREASE,
			));

			$model->$field = $max;
		}
		elseif ($model->is_master())
		{
			$max = $this->position_max($field, TRUE);
			if ($model->$field == $max)
				return FALSE;

			$this->_position_shift($field, array(
				'start' => $model->$field,
				'end' => $max,
				'site_id' => $site_id,
				'direction' => self::POSITION_DECREASE,
				'type' => self::POSITION_MASTER,
			));

			$model->$field = $max;
		}
		else
		{
			$max = $this->position_max($field);
			if ($model->$field == $max)
				return FALSE;

			$master_span = $this->_get_master_span($field, $model->$field);
			$this->_position_shift($field, array(
				'start' => $model->$field,
				'end' => $master_span['max'],
				'site_id' => $site_id,
				'direction' => self::POSITION_DECREASE,
			));

			if ($max == $master_span['max'])
			{
				// если изменения в пределах одного интервала
				$model->$field = $max;
			}
			else
			{
				$model->$field = $max + (int) $config['step'];
			}
		}

		$model->save();
		return $model->saved();
	}

	/**
	 * Move item to specified position
	 * TODO: Доработать метод, чтобы после вставки не было "дырок"
	 *
	 * @param    string   $field      Position field name
	 * @param    integer  $field      New position value
	 * @return   boolean
	 *
	 */
	public function position_set($field, $position = NULL) {
		$this->_check_position_field($field);

		$model = $this->_orm;
		$config = $this->_position_fields[$field];
		$primary_key = $model->primary_key();
		$site_id_key = $model->site_id_column();
		$site_id = array_key_exists($site_id_key, $model->table_columns())
			? $model->site_id()
			: NULL;

		if ( ! $model->loaded())
			throw new Kohana_Exception('Cannot set position of :model model because it is not loaded.', array(':model' => $model->object_name()));

		if ($position === NULL)
		{
			$position = $model->$field;
		}

		if ($model->$field == $position)
			return FALSE;

		$builder = DB::select($primary_key, $field)
			->from($model->table_name())
			->where($field, '=', $position);
		if ($site_id)
		{
			$builder
				->where($site_id_key, '=', $site_id);
		}
		$this->_position_apply_builder_conditions($field, $builder);
		$db_result = $builder->execute($this->_db);

		if ($db_result !== NULL)
		{
			$target_position = $db_result->get($field);
			if ($this->_position_type == self::POSITION_SIMPLE)
			{
				$end = ($target_position < $model->$field)
					? $model->$field
					: NULL;

				$this->_position_shift($field, array(
					'start' => $target_position,
					'end' => $end,
					'site_id' => $site_id,
					'direction' => self::POSITION_INCREASE,
				));
			}
			elseif ($model->is_master())
			{
				$end = ($target_position < $model->$field)
					? $model->$field
					: NULL;

				$this->_position_shift($field, array(
					'start' => $target_position,
					'end' => $end,
					'site_id' => $site_id,
					'direction' => self::POSITION_INCREASE,
					'type' => self::POSITION_MASTER,
				));
			}
			else
			{
				$target_span = $this->_get_master_span($field, $target_position);
				$this->_position_shift($field, array(
					'start' => $target_position,
					'end' => $target_span['max'] + (int) $config['step'],
					'site_id' => $site_id,
					'direction' => self::POSITION_INCREASE,
				));
			}
		}

		$model->$field = $position;
		$model->save();
		return $model->saved();
	}

	/**
	 * Renumerate positions of items
	 * Returns count of fixed positions
	 *
	 * @param    string   $field      Position field name
	 * @param    integer  $type       Record type
	 * @param    boolean  $reset      Reset all positions
	 * @return   integer
	 */
	public function position_fix($field, $type = self::POSITION_ALL, $reset = FALSE)
	{
		$this->_check_position_field($field);


		$model = $this->_orm;
		$table = $model->table_name();
		$primary_key = $model->primary_key();
		$deleted_field = $this->deleted_field();
		$config = $this->_position_fields[$field];

		$select_array = $config['group_by'];
		$select_array[] = $primary_key;
		$select_array[] = $field;
		if ($model->has_site_id_column())
		{
			$select_array[] = $model->site_id_column();
		}

		$builder = DB::select_array($select_array)
			->from($table)
			->where($deleted_field, '=', 0);
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

		if ($this->_position_type == self::POSITION_SIMPLE)
		{
			$result = $builder
				->execute($this->_db)
				->as_array();

			$res = $this->_fix_simple($result, $field);
		}
		else
		{
			switch ($type)
			{
				case self::POSITION_ALL:
					$result = $builder
						->execute($this->_db)
						->as_array();

					$res = $this->_fix_all($result, $field);
					break;

				case self::POSITION_MASTER:
					$result = $builder
						->where($model->site_id_column(), '=', $model->site_id_master())
						->execute($this->_db)
						->as_array();

					$res = $this->_fix_master($result, $field);
					break;
				case self::POSITION_SLAVE:
					$result = $builder
						->where($model->site_id_column(), '=', $model->site_id())
						->execute($this->_db)
						->as_array();

					$res = $this->_fix_slave($result, $field);
					break;
			}
		}

		return $res;
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

					switch ($this->_position_type)
					{
						case self::POSITION_COMPLEX:
							if ($orm->is_master())
							{
								$_step_master = $config['step_master'];
								$orm->$field = floor( $this->position_max($field) / $_step_master ) * $_step_master + $_step_master;
							}
							else
							{
								$_step_master = $config['step_master'];
								$_max_position = $this->position_max($field);
								if ($_max_position > $_step_master) {
									$orm->$field = $this->position_max($field) + $config['step'];
								} else {
									$orm->$field = $_step_master + $config['step'];
								}
							}
							break;
						case self::POSITION_SIMPLE:
							$orm->$field = $this->position_max($field) + $config['step'];
							break;
						default:
							continue;
					}
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
					}
					catch (Exception $e2)
					{
						Kohana::$log->add(
								Log::ERROR,
								'Exception occurred: :exception. [:file][:line]',
								array(
									':file'      => Debug::path(__FILE__),
									':line'      => __LINE__,
									':exception' => $e2->getMessage(),
								)
						);
					}

					try
					{
						unlink($filename);
					}
					catch (Exception $e2)
					{
						// File not deleted
						Kohana::$log->add(
								Log::ERROR,
								'File :file in the :field field of :model model not deleted after save error. Exception occurred: :exception',
								array(
									':file'      => Debug::path($filename),
									':field'     => $field,
									':model'     => $orm->object_name(),
									':exception' => $e2->getMessage()
								)
						);
					}
				}
			}

			Kohana::$log->add(
					Log::ERROR,
					'ORM_Helper save exception occurred: :exception. [:file][:line]',
					array(
						':file'      => Debug::path(__FILE__),
						':line'      => __LINE__,
						':exception' => $e->getMessage(),
					)
			);

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
		if ( ! method_exists($this->_orm, 'has_site_id_column') OR ! $this->_orm->has_site_id_column())
		{
			$this->_position_type = self::POSITION_SIMPLE;
		}

		// Init file fields
		foreach ($this->_position_fields as $field => $config)
		{
			// For POSITION_COMPLEX groub by site id column is unacceptable
			// because POSITION_COMPLEX depended by one and not compatible with such grouping
			if ($this->_position_type == self::POSITION_COMPLEX)
			{
				$config['group_by'] = array_diff($config['group_by'], array( $this->_orm->site_id_column() ));
			}
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

	/**
	 * Swap two elements
	 *
	 * @param    string  $field       Field name
	 * @param    array   $element_1   Array ( key => primary_key_value, pos => position_value  )
	 * @param    array   $element_2   Array ( key => primary_key_value, pos => position_value  )
	 * @return   boolean
	 */
	private function _position_swap($field, $element_1, $element_2)
	{
		$model = $this->_orm;
		$table = $model->table_name();
		$primary_key = $model->primary_key();

		$flag = (bool) DB::update($table)
			->value($field, $element_1['pos'])
			->where($primary_key, '=', $element_2['key'])
			->execute($this->_db);

		if ( ! $flag)
			return FALSE;

		$flag = (bool) DB::update($table)
			->value($field, $element_2['pos'])
			->where($primary_key, '=', $element_1['key'])
			->execute($this->_db);
		if ( ! $flag)
		{
			DB::update($table)
				->value($field, $element_1['pos'])
				->where($primary_key, '=', $element_1['key'])
				->execute($this->_db);
			DB::update($table)
				->value($field, $element_2['pos'])
				->where($primary_key, '=', $element_2['key'])
				->execute($this->_db);
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Shift elements position (simple).
	 * For POSITION_INCREASE $end not included
	 * For POSITION_DECREASE $start not included
	 *
	 * @param    string   $field       Field name
	 * @param    array    $config      Config array
	 * @return   boolean
	 */
	private function _position_shift($field, $config)
	{
		$model = $this->_orm;
		$field_config = $this->_position_fields[$field];

		$config = $config + array(
			'start' => NULL,
			'end' => NULL,
			'site_id' => NULL,
			'value' => NULL,
			'direction' => self::POSITION_INCREASE,
			'type' => self::POSITION_SLAVE,
		);

		if ($config['start'] == $config['end'])
			return FALSE;

		switch ($config['type'])
		{
			case self::POSITION_MASTER:
				$config['value'] = ($config['value'] === NULL) ? $field_config['step_master'] : $config['value'];
				break;
			case self::POSITION_SLAVE:
				$config['value'] = ($config['value'] === NULL) ? $field_config['step'] : $config['value'];
				break;
		}

		switch ($config['direction'])
		{
			case self::POSITION_INCREASE:
				$builder = DB::update($model->table_name())
					->value($field, DB::expr("`$field`+{$config['value']}"))
					->where($field, '>=', $config['start']);
				if ( $config['end'] !== NULL )
				{
					$builder->where($field, '<', $config['end']);
				}
				break;
			case self::POSITION_DECREASE:
				$builder = DB::update($model->table_name())
					->value($field, DB::expr("`$field`-{$config['value']}"))
					->where($field, '<=', $config['end']);
				if ( $config['start'] !== NULL )
				{
					$builder->where($field, '>', $config['start']);
				}
				break;
			default:
				return FALSE;
		}

		if ( $config['site_id'] )
		{
			$builder->where($model->site_id_column(), '=', $config['site_id']);
		}

		$this->_position_apply_builder_conditions($field, $builder);

		return (bool) $builder->execute($this->_db);
	}

	/**
	 * Return sibling for element
	 *
	 * @param    Database_Result   $db_result   Record set
	 * @param    string            $field       Position field name
	 * @return   array
	 */
	private function _position_sibling($field, $position, $direction = self::POSITION_NEXT, $type = self::POSITION_ALL)
	{
		$model = $this->_orm;

		switch ($direction)
		{
			case self::POSITION_NEXT:
				$builder = $this->_position_select_builder($field)
					->where($field, '>', $position)
					->order_by($field, 'ASC')
					->limit(1);
				break;
			case self::POSITION_PREV:
				$builder = $this->_position_select_builder($field)
					->where($field, '<', $position)
					->order_by($field, 'DESC')
					->limit(1);
				break;
			default:
				return;
		}

		if ($model->has_site_id_column())
		{
			switch ($type)
			{
				case self::POSITION_ALL:
					if ($model->site_id() == $model->site_id_master())
					{
						$builder->where($model->site_id_column(), '=', $model->site_id_master());
					}
					else
					{
						if ($model->has_for_all_column())
						{
							$builder
								->and_where_open()
									->where($model->site_id_column(), '=', $model->site_id())
									->or_where_open()
										->where($model->site_id_column(), '=', $model->site_id_master())
										->and_where($model->for_all_column(), '=', 1)
									->or_where_close()
								->and_where_close();
						}
						else
						{
							$_v = array( $model->site_id_master(), $model->site_id() );
							$builder->where($model->site_id_column(), 'IN', '('.implode(',', $_v).')');
						}
					}
					break;
				case self::POSITION_MASTER:
					$builder->where($model->site_id_column(), '=', $model->site_id_master());
					if ($model->has_for_all_column() AND $model->site_id() != $model->site_id_master())
					{
						$builder->where($model->for_all_column(), '=', 1);
					}
					break;
				case self::POSITION_SLAVE:
					$builder->where($model->site_id_column(), '=', $model->site_id());
					break;
				default:
					return;
			}
		}

		return $builder->execute($this->_db);
	}

	/**
	 * Return select builder
	 *
	 * @param    string            $field       Position field name
	 * @return   Database_Query_Builder_Select
	 */
	private function _position_select_builder($field)
	{
		$config = $this->_position_fields[$field];
		$model = $this->_orm;
		$select_array = array(
			$model->primary_key(),
			$field,
			array(DB::expr("MOD(`$field`,{$config['step_master']})"), 'is_slave')
		);
		if ($model->has_site_id_column())
		{
			$select_array[] = $model->site_id_column();
		}
		$builder = DB::select_array($select_array)
			->from($model->table_name());
		$this->_position_apply_builder_conditions($field, $builder);

		return $builder;
	}

	/**
	 * Apply std builder conditions
	 *
	 * @param    Database_Query_Builder  $builder   Instance of builder
	 */
	private function _position_apply_builder_conditions($field, $builder)
	{
		$config = $this->_position_fields[$field];
		$model = $this->_orm;

		foreach ($config['group_by'] as $gb_field)
		{
			$builder->where($gb_field, '=', $model->$gb_field);
		}

		$deleted_field = $this->deleted_field();
		if (array_key_exists($deleted_field, $model->table_columns()))
		{
			$builder
				->where($deleted_field, '=', 0);
		}
	}

	private function _fix_all($db_result, $field)
	{
		$model = $this->_orm;
		$table = $model->table_name();
		$primary_key = $model->primary_key();
		$deleted_field = $this->deleted_field();
		$config = $this->_position_fields[$field];

		$site_id_master = $model->site_id_master();
		$site_id_key = $model->site_id_column();

		$counter = 0;
		$current_gb = NULL;
		$_is_master = FALSE;

		$start_slave = $config['start'];
		$step_slave = $config['step'];

		$start_master = $config['start_master'];
		$step_master = $config['step_master'];

		$pos_master = 0;
		$pos_slave = array();

		foreach ($db_result as $row)
		{
			if ($config['group_by'])
			{
				// Get "GROUP BY" columns
				$gb_value = array_slice($row, 0, count($config['group_by']));
				if ($gb_value != $current_gb)
				{
					// Start new group
					$current_gb = $gb_value;
					$pos_master = 0;
					foreach ($pos_slave as & $val)
					{
						$val = 0;
					}
				}
			}

			$_site_id = $row['site_id'];
			if (empty($pos_slave[ $_site_id ]))
			{
				$pos_slave[ $_site_id ] = 0;
			}

			// if master
			if ($row[$site_id_key] == $site_id_master)
			{
				$pos_master += $step_master;
				foreach ($pos_slave as & $val)
				{
					$val = 0;
				}
			}
			// if slave
			else
			{
				$pos_slave[ $_site_id ] += $step_slave;
			}

			$pos_cursor = $pos_master + $pos_slave[ $_site_id ];
			if ($row[$field] != $pos_cursor)
			{
				// Execute update
				DB::update($table)
					->value($field, $pos_cursor)
					->where($deleted_field, '=', 0)
					->where($primary_key, '=', $row[$primary_key])
					->execute($this->_db);

				++$counter;
			}
		}

		return $counter;
	}

	private function _fix_master($db_result, $field)
	{
		$model = $this->_orm;
		$table = $model->table_name();
		$primary_key = $model->primary_key();
		$deleted_field = $this->deleted_field();
		$config = $this->_position_fields[$field];

		$counter = 0;
		$current_gb = NULL;

		$step_master = $config['step_master'];
		$start_master = $config['start_master'];
		$pos_cursor = $start_master;

		foreach ($db_result as $row)
		{
			if ($config['group_by'])
			{
				// Get "GROUP BY" columns
				$gb_value = array_slice($row, 0, count($config['group_by']));
				if ($gb_value != $current_gb)
				{
					// Start new group
					$current_gb = $gb_value;
					$pos_cursor = $start_master;
				}
			}

			if ($row[$field] != $pos_cursor)
			{
				// Execute update
				DB::update($table)
					->value($field, $pos_cursor)
					->where($deleted_field, '=', 0)
					->where($primary_key, '=', $row[$primary_key])
					->execute($this->_db);

				++$counter;
			}

			$pos_cursor += $step_master;
		}

		return $counter;
	}

	private function _fix_slave($db_result, $field)
	{
		$model = $this->_orm;
		$table = $model->table_name();
		$primary_key = $model->primary_key();
		$deleted_field = $this->deleted_field();
		$config = $this->_position_fields[$field];

		$site_id_master = $model->site_id_master();
		$site_id_key = $model->site_id_column();

		$counter = 0;
		$current_gb = NULL;

		$step_slave = $config['step'];
		$step_master = $config['step_master'];

		$start_slave = $config['start'];
		$start_master = $config['start_master'];

		$pos_cursor = NULL;
		$pos_master = NULL;
		$pos_slave = $start_slave;

		foreach ($db_result as $row)
		{
			$_pos_master = floor( $row[$field] / $step_master ) * $step_master;
			if ($row['site_id'] == $site_id_master AND empty($_pos_master)) {
				$_pos_master = $step_master;
			}

			if ($config['group_by'])
			{
				// Get "GROUP BY" columns
				$gb_value = array_slice($row, 0, count($config['group_by']));

				if ($gb_value != $current_gb)
				{
					// Start new group
					$current_gb = $gb_value;
					$pos_slave = $start_slave;
				}
			}
			if ($pos_master != $_pos_master) {
				$pos_slave = $start_slave;
			}
			$pos_master = $_pos_master;
			$pos_cursor = $pos_master + $pos_slave;

			if ($row[$field] != $pos_cursor)
			{
				// Execute update
				DB::update($table)
					->value($field, $pos_cursor)
					->where($deleted_field, '=', 0)
					->where($primary_key, '=', $row[$primary_key])
					->execute($this->_db);

				++$counter;
			}

			$pos_slave += $step_slave;
		}

		return $counter;
	}

	private function _fix_simple($db_result, $field)
	{
		$model = $this->_orm;
		$table = $model->table_name();
		$primary_key = $model->primary_key();
		$deleted_field = $this->deleted_field();
		$config = $this->_position_fields[$field];

		$counter = 0;
		$current_gb = NULL;

		$step = $config['step'];
		$start = $config['start'];
		$pos_cursor = $start;

		foreach ($db_result as $row)
		{
			if ($config['group_by'])
			{
				// Get "GROUP BY" columns
				$gb_value = array_slice($row, 0, count($config['group_by']));
				if ($gb_value != $current_gb)
				{
					// Start new group
					$current_gb = $gb_value;
					$pos_cursor = $start;
				}
			}

			if ($row[$field] != $pos_cursor)
			{
				// Execute update
				DB::update($table)
					->value($field, $pos_cursor)
					->where($deleted_field, '=', 0)
					->where($primary_key, '=', $row[$primary_key])
					->execute($this->_db);

				++$counter;
			}

			$pos_cursor += $step;
		}

		return $counter;
	}

	// текущий мастер интервал (на каком мастер-уровне находится элемент, кратно step_master)
	private function _get_master_span($field, $value)
	{
		// Check
		$this->_check_position_field($field);

		// Prepare
		$model = $this->_orm;
		$primary_key = $model->primary_key();
		$config = $this->_position_fields[$field];
		$site_id_key = $model->site_id_column();
		$site_id = array_key_exists($site_id_key, $model->table_columns())
			? $model->site_id()
			: NULL;

		if ($this->_position_type == self::POSITION_SIMPLE)
			throw new Kohana_Exception('Method is not allowable for :model model because position type is POSITION_SIMPLE', array(':model' => $model->object_name()));

		$return = array(
			'level' => (string) floor($value / (int) $config['step_master']),
			'min' => NULL,
			'max' => NULL
		);

		$builder = DB::select(
				array(DB::expr("MIN(`$field`)"), 'min_position'),
				array(DB::expr("MAX(`$field`)"), 'max_position')
			)
			->from($model->table_name())
			->where($field, '>', ($return['level'] * (int) $config['step_master']))
			->where($field, '<', (($return['level'] + 1) * (int) $config['step_master']));
		if ($site_id)
		{
			$builder->where($site_id_key, '=', $site_id);
		}
		$this->_position_apply_builder_conditions($field, $builder);
		$_db_result = $builder->execute($this->_db);

		$return['min'] = $_db_result->get('min_position');
		$return['max'] = $_db_result->get('max_position');

		return $return;
	}

} // End ORM_Helper
