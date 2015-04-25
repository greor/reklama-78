<?php defined('SYSPATH') or die('No direct script access.');

interface ORM_Position {

	const MOVE_PREV   = 1;
	const MOVE_NEXT   = 2;
	const MOVE_FIRST  = 3;
	const MOVE_LAST   = 4;

	/**
	 * Returns array of position fields with params
	 *
	 * @return  array
	 */
	public function position_fields();

	/**
	 * Returns position step for field
	 *
	 * @param   string   $value  Position field name
	 * @return  integer
	 */
	public function position_step($field);

	/**
	 * Change position of item
	 * Move item up, down, first or last
	 *
	 * @param    string   $field      Position field name
	 * @param    integer  $direction  Direction of moving
	 * @return   boolean
	 */
	public function position_move($field, $direction);

	/**
	 * Move item to previous position
	 *
	 * @param    string   $field      Position field name
	 * @return   boolean
	 */
	public function position_prev($field);

	/**
	 * Move item to next position
	 *
	 * @param    string   $field      Position field name
	 * @return   boolean
	 */
	public function position_next($field);

	/**
	 * Move item to first position
	 *
	 * @param    string   $field      Position field name
	 * @return   boolean
	 */
	public function position_first($field);

	/**
	 * Move item to last position
	 *
	 * @param    string   $field      Position field name
	 * @return   boolean
	 */
	public function position_last($field);

	/**
	 * Renumerate positions of items
	 * Returns count of fixed positions
	 *
	 * @param    string   $field      Position field name
	 * @param    boolean  $reset      Reset all positions
	 * @return   integer
	 */
	public function position_fix($field, $reset = FALSE);

	/**
	 * Returns min existing position
	 *
	 * @param    string   $field      Position field name
	 * @return   integer
	 */
	public function position_min($field);

	/**
	 * Returns max existing position
	 *
	 * @param    string   $field      Position field name
	 * @return   integer
	 */
	public function position_max($field);

} // End ORM_Position

