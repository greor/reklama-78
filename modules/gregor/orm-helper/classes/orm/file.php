<?php defined('SYSPATH') or die('No direct script access.');

interface ORM_File {

	const ON_DELETE_UNLINK = 1;
	const ON_DELETE_RENAME = 2;
	const ON_UPDATE_UNLINK = 4;
	const ON_UPDATE_RENAME = 8;

	const MAX_FILENAME_LENGTH = 127;

	/**
	 * Returns array of file fields whith params
	 *
	 * @return  array
	 */
	public function file_fields();

	/**
	 * Returns validation rules for file fields
	 *
	 * @return  array
	 */
	public function file_rules();

	/**
	 * Saves file and returns file name
	 *
	 * @param   string   $field  File field name
	 * @param   mixed    $value  File field value
	 * @return  string
	 */
	public function file_save($field, $value);

	/**
	 * Returns full file path
	 *
	 * @param   string   $field  File field name
	 * @param   string   $value  File field value
	 * @return  string
	 */
	public function file_path($field, $value = NULL);

	/**
	 * Returns "inner" file path
	 *
	 * @param   string   $field  File field name
	 * @param   string   $value  File field value
	 * @return  string
	 */
	public function file_sub_dir($field, $value = NULL);

	/**
	 * Returns file web path
	 *
	 * @param   string   $field  File field name
	 * @param   string   $value  File field value
	 * @return  string
	 */
	public function file_uri($field, $value = NULL);

} // End ORM_File