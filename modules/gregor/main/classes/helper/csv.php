<?php defined('SYSPATH') or die('No direct script access.');

class Helper_CSV {
	
	public static $instance;

	public static function instance()
	{
		if ( ! self::$instance) {
			self::$instance = new Helper_CSV();
		}
		return self::$instance;
	}
	
	public function send($data, $charset = 'windows-1251')
	{
		$this->_send_headers("data_export_".date("Y-m-d_H-i-s").".csv", $charset);
		echo $this->array2csv($data);
		exit();
	}
	
	protected function array2csv(array &$array)
	{
		if (count($array) == 0) 
			return NULL;
		
		ob_start();
		$df = fopen("php://output", 'w');
		fputcsv($df, array_keys(reset($array)), ';');
		foreach ($array as $row) {
			fputcsv($df, $row, ';');
		}
		fclose($df);
		return ob_get_clean();
	}
	
	protected function _send_headers($filename, $charset) 
	{
		// disable caching
		$now = gmdate("D, d M Y H:i:s");
		header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
		header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
		header("Last-Modified: {$now} GMT");
	
		// force download
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
	
		// disposition / encoding on response body
		header("Content-type: text/csv; charset={$charset}");
		header("Content-Disposition: attachment;filename={$filename}");
		header("Content-Transfer-Encoding: binary");
	}
	
	public function save($data, $filename) {}
	
	public function read($filename) {}
}