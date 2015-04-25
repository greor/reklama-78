<?php defined('SYSPATH') or die('No direct script access.');

return array(
// not_empty
':field must not be empty'=>
'«:field» должно содержать значение',

// matches
':field must be the same as :param2'=>
'«:field» должно быть равным полю :param2',

// regex
':field does not match the required format'=>
'«:field» не соответствует требуемому формату',

// exact_length
':field must be exactly :param2 characters long'=>
'«:field» должно быть длиной в точности :param2 символов',

// min_length
':field must be at least :param2 characters long'=>
'«:field» должно быть длиной не менее :param2 символов',

// max_length
':field must be less than :param2 characters long'=>
'«:field» должно быть длиной не более :param2 символов',

// in_array
':field must be one of the available options'=>
'«:field» должно быть одним из допустимых значений',

// digit
':field must be a digit'=>
'«:field» должно содержать цифры',

// decimal
':field must be a decimal with :param2 places'=>
'«:field» должно быть десятичным числом с количеством разрядов :param2',

// range
':field must be within the range of :param2 to :param3'=>
'«:field» должно быть в диапазоне от :param2 до :param3',

// url
':field must be valid URL'=>
'«:field» должно быть корректным URL',

// email
':field must be valid email'=>
'«:field» должно быть корректным email-адресом',

// email_domain
':field must be valid email and domain must have a valid MX record'=>
'«:field» должно быть корректным email-адресом с реальным почтовым доменом',

//ip
':field must be valid IP address'=>
'«:field» должно быть корректным IP-адресом',

//color
':field must be a color'=>
'«:field» должно быть цветом в HEX-формате',

//credit_card
':field must be a credit card number'=>
'«:field» должно корректным номером кредитной карты',

//numeric
':field must be numeric'=>
'«:field» должно быть числом',

//phone
':field must be valid phone number'=>
'«:field» должно быть корректным телефонным номером',

//date
':field must be valid date'=>
'«:field» должно быть корректной датой',

// unique
':field must unique'=>
'«:field» должно быть уникальным',

// image_width
':field must have image width :param2 px'=>
'«:field» должно иметь ширину :param2 px',

// image_max_width
':field must have maximum image width :param2 px'=>
'«:field» должно иметь ширину не более :param2 px',

// image_min_width
':field must have minimum image width :param2 px'=>
'«:field» должно иметь ширину не менее :param2 px',

// image_height
':field must have image height :param2 px'=>
'«:field» должно иметь высоту :param2 px',

// image_max_height
':field must have maximum image height :param2 px'=>
'«:field» должно иметь высоту не более :param2 px',

// image_min_height
':field must have minimum image height :param2 px'=>
'«:field» должно иметь высоту не менее :param2 px',

// file_valid
':field must be valid file'=>
'«:field» должно быть файлом',

// file_type
':field must be file of allowed type: :param2'=>
'«:field» должно быть файлом разрешенного типа: :param2',

// file_size
':field must have maximum file size :param2'=>
'«:field» должно иметь размер файла не более :param2',

// not file delete
'Error of deleting file :param2'=>
'Невозможно удалить файл «:param2»',

// alpha
':field must contain only letters' =>
'«:field» должно содержать только буквы',

// alpha_dash
':field must contain only alphabetic characters, numbers, underscores and dashes' =>
'«:field» должно состоять только из алфавитных символов, цифр, знаков подчеркивания и тире',

// alpha_numeric
':field must contain only letters and numbers' =>
'«:field» должно содержать только буквы и цифры',

// Pagination
'First' => 'Первая',
'Last' => 'Последняя',
'Previous' => 'Предыдущая',
'Next' => 'Следующая',

/*
 * Weekdays
 */
// Long day names
'monday'    => 'понедельник',
'tuesday'   => 'вторник',
'wednesday' => 'среда',
'thursday'  => 'четверг',
'friday'    => 'пятница',
'saturday'  => 'суббота',
'sunday'    => 'воскресенье',

'Monday'    => 'Понедельник',
'Tuesday'   => 'Вторник',
'Wednesday' => 'Среда',
'Thursday'  => 'Четверг',
'Friday'    => 'Пятница',
'Saturday'  => 'Суббота',
'Sunday'    => 'Воскресенье',

// Two letter days
'mo'        => 'пн',
'tu'        => 'вт',
'we'        => 'ср',
'th'        => 'чт',
'fr'        => 'пт',
'sa'        => 'сб',
'su'        => 'вс',

'Mo'        => 'Пн',
'Tu'        => 'Вт',
'We'        => 'Ср',
'Th'        => 'Чт',
'Fr'        => 'Пт',
'Sa'        => 'Сб',
'Su'        => 'Вс',

// Short day names
'mon'       => 'пнд',
'tue'       => 'втр',
'wed'       => 'срд',
'thu'       => 'чтв',
'fri'       => 'птн',
'sat'       => 'сбт',
'sun'       => 'вск',

'Mon'       => 'Пнд',
'Tue'       => 'Втр',
'Wed'       => 'Срд',
'Thu'       => 'Чтв',
'Fri'       => 'Птн',
'Sat'       => 'Сбт',
'Sun'       => 'Вск',

/*
 * Months
 */
// Short month names
'jan'       => 'янв',
'feb'       => 'фев',
'mar'       => 'мар',
'apr'       => 'апр',
'may'       => 'май',
'jun'       => 'июн',
'jul'       => 'июл',
'aug'       => 'авг',
'sep'       => 'сен',
'oct'       => 'окт',
'nov'       => 'ноя',
'dec'       => 'дек',

'Jan'       => 'Янв',
'Feb'       => 'Фев',
'Mar'       => 'Мар',
'Apr'       => 'Апр',
'May'       => 'Май',
'Jun'       => 'Июн',
'Jul'       => 'Июл',
'Aug'       => 'Авг',
'Sep'       => 'Сен',
'Oct'       => 'Окт',
'Nov'       => 'Ноя',
'Dec'       => 'Дек',

// Long month names
'january'   => 'январь',
'february'  => 'февраль',
'march'     => 'март',
'april'     => 'апрель',
'may'       => 'май',
'june'      => 'июнь',
'july'      => 'июль',
'august'    => 'август',
'september' => 'сентябрь',
'october'   => 'октябрь',
'november'  => 'ноябрь',
'december'  => 'декабрь',

'January'   => 'Январь',
'February'  => 'Февраль',
'March'     => 'Март',
'April'     => 'Апрель',
'May'       => 'Май',
'June'      => 'Июнь',
'July'      => 'Июль',
'August'    => 'Август',
'September' => 'Сентябрь',
'October'   => 'Октябрь',
'November'  => 'Ноябрь',
'December'  => 'Декабрь',

'of january'   => 'января',
'of february'  => 'февраля',
'of march'     => 'марта',
'of april'     => 'апреля',
'of may'       => 'мая',
'of june'      => 'июня',
'of july'      => 'июля',
'of august'    => 'августа',
'of september' => 'сентября',
'of october'   => 'октября',
'of november'  => 'ноября',
'of december'  => 'декабря',

'of January'   => 'января',
'of February'  => 'февраля',
'of March'     => 'марта',
'of April'     => 'апреля',
'of May'       => 'мая',
'of June'      => 'июня',
'of July'      => 'июля',
'of August'    => 'августа',
'of September' => 'сентября',
'of October'   => 'октября',
'of November'  => 'ноября',
'of December'  => 'декабря',
);