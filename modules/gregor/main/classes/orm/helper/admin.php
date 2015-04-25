<?php
class ORM_Helper_Admin extends ORM_Helper {

	/**
	 * Name of field what marks record as "deleted"
	 * @var string
	 */
	protected $_safe_delete_field = 'delete_bit';

}