<?php defined('SYSPATH') or die('No direct access allowed.');

class Auth_Admin_Checker {

	/**
	 * @var string Table name for failed login attempts
	 */
	protected $table_name = 'admin_fail_logins';

	/**
	 * @var mixed Database group or instance
	 */
	protected $db = 'default';

	/**
	 * @var integer Limit of failed login attempts per fail_interval
	 */
	protected $fail_limit = 3;

	/**
	 * @var integer Fail time interval in the seconds
	 */
	protected $fail_interval = 600;

	/**
	 * @var string Login
	 */
	protected $login;

	/**
	 * @var string IP address
	 */
	protected $ip;

	/**
	 * Construct checker for specified IP address
	 *
	 * @param string $login
	 * @param string $ip
	 * @param array $options
	 */
	public function __construct($login, $ip, array $options = NULL)
	{
		$this->login = $login;
		$this->ip = $ip;
		if ($options)
		{
			foreach ($options as $key => $value)
			{
				if (property_exists($this, $key))
				{
					$this->$key = $value;
				}
			}
		}
	}

	/**
	 * Returns fail limit
	 *
	 * @return number
	 */
	public function fail_limit()
	{
		return $this->fail_limit;
	}

	/**
	 * Returns fail interval in the seconds
	 *
	 * @return number
	 */
	public function fail_interval()
	{
		return $this->fail_interval;
	}

	/**
	 * Add failed login attempt to database
	 *
	 * @param string $password
	 * @param string $user_agent
	 * @return void
	 */
	public function add($password = NULL, $user_agent = NULL)
	{
		$insert = array(
			'login'      => $this->login,
			'password'   => $password,
			'ip'         => $this->ip,
			'user_agent' => $user_agent,
			'time'       => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'])
		);

		//Add failed login to database
		DB::insert($this->table_name)
			->columns(array_keys($insert))
			->values(array_values($insert))
			->execute($this->db);
	}

	/**
	 * Clear failed login attempts from database
	 *
	 * @return void
	 */
	public function clear()
	{
		//Add failed login to database
		DB::delete($this->table_name)
			->where('login', '=', $this->login)
			->or_where('ip', '=', $this->ip)
			->execute($this->db);
	}

	/**
	 * Checks if limit of failed login attempts not exceeded.
	 *
	 * @return  boolean
	 */
	public function check()
	{
		$count = DB::select(DB::expr('COUNT(*)'))
			->from($this->table_name)
			->where('time', '>', date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] - $this->fail_interval))
			->and_where_open()
				->where('login', '=', $this->login)
				->or_where('ip', '=', $this->ip)
			->and_where_close()
			->execute($this->db)
			->get('COUNT(*)');

		return ($count < $this->fail_limit);
	}

} // End Auth_Admin_Checker