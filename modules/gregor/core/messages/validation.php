<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'Upload::not_empty'     => ':field must not be empty',
	'Ku_Upload::not_empty'  => ':field must not be empty',
	'Upload::valid'         => ':field must be valid uploaded file',
	'Ku_Upload::valid'      => ':field must be valid uploaded file',
	'Upload::type'          => ':field must be file of allowed type: :param2',
	'Ku_Upload::type'       => ':field must be file of allowed type: :param2',
	'Upload::size'          => ':field must have maximum file size :param2',
	'Ku_Upload::size'       => ':field must have maximum file size :param2',

	'Ku_File::not_empty'    => ':field must not be empty',
	'Ku_File::valid'        => ':field must be valid file',
	'Ku_File::type'         => ':field must be file of allowed type: :param2',
	'Ku_File::size'         => ':field must have maximum file size :param2',

	'file_not_empty'        => ':field must not be empty',
	'file_valid'            => ':field must be valid file',
	'file_type'             => ':field must be file of allowed type: :param2',
	'file_size'             => ':field must have maximum file size :param2',

	'image_width'           => ':field must have image width :param2 px',
	'image_max_width'       => ':field must have maximum image width :param2 px',
	'image_min_width'       => ':field must have minimum image width :param2 px',
	'image_height'          => ':field must have image height :param2 px',
	'image_max_height'      => ':field must have maximum image height :param2 px',
	'image_min_height'      => ':field must have minimum image height :param2 px',

	'Ku_Image::width'       => ':field must have image width :param2 px',
	'Ku_Image::max_width'   => ':field must have maximum image width :param2 px',
	'Ku_Image::min_width'   => ':field must have minimum image width :param2 px',
	'Ku_Image::height'      => ':field must have image height :param2 px',
	'Ku_Image::max_height'  => ':field must have maximum image height :param2 px',
	'Ku_Image::min_height'  => ':field must have minimum image height :param2 px',

	'url'                   => ':field must be valid URL',
	'email'                 => ':field must be valid email',
	'email_domain'          => ':field must be valid email and domain must have a valid MX record',
	'ip'                    => ':field must be valid IP address',
	'numeric'               => ':field must be numeric',
	'phone'                 => ':field must be valid phone number',
	'date'                  => ':field must be valid date',

	'unique'                => ':field must unique',
);