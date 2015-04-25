<?php defined('SYSPATH') or die('No direct script access.');

interface ORM_Restore {

	const SLAVE_DELETE_STEP = 2;

	/**
	 * Returns name of "deleted" field
	 *
	 * @return  string
	 */
	public function deleted_field();

	/**
	 * Mark record as deleted
	 *
	 * @param   array    $where Array of "WHERE" conditions
	 * @param   boolean  $cascade_delete Execute cascade delete
	 * @return  boolean
	 */
	public function safe_delete(array $where = NULL, $cascade_delete = TRUE);

	/**
	 * Restore deleted record
	 *
	 * @param   array    $where Array of "WHERE" conditions
	 * @param   boolean  $cascade_restore Execute cascade restore
	 * @return  boolean
	 */
	public function restore(array $where = NULL, $cascade_restore = TRUE);

} // End ORM_Restore

