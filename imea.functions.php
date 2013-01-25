<?php
/**
 * This code provides utility functions to the InforMEA plugin.
 * Put your generic functions here.
 */

if(!function_exists('imea_log')) {
	/**
	 * Log information
	 */
	function imea_log( $message, $warn = FALSE ) {
		$message = 'IMEA: ' . ( $warn ? '[WARN] ' : '' ) . $message;
		if( WP_DEBUG === true ) {
			if( is_array( $message ) || is_object( $message ) ) {
				error_log( print_r( $message, true ) );
			} else {
				error_log( $message );
			}
		}
	}
}

if(!function_exists('format_mysql_date')) {
	function format_mysql_date($date, $fmt_out = 'j M, Y') {
		$d = strtotime($date);
		return date($fmt_out, $d);
	}
}

if(!function_exists('imea_debug')) {
	/**
	 * Output debug information. Works if user enables 'Debug' in plugin configuration page from administration area.
	 */
	function imea_debug( $message, $warn = FALSE ) {
		$options = get_option('informea_options');
		if($options['debug']) {
			$message = 'IMEA: [DEBUG] ' . $message;
			if( WP_DEBUG === true ) {
				if( is_array( $message ) || is_object( $message ) ) {
					error_log( print_r( $message, true ) );
				} else {
					error_log( $message );
				}
			}
		}
	}
}

if(!function_exists('echo_trace')) {
	function echo_trace() {
		echo '<pre>';
		$trace = debug_backtrace();
		foreach($trace as $t) {
			echo $t['file'].'('.$t['line'].')';
			echo "\n";
		}
		echo "
			-- Trace end --
		</pre>";
	}
}

if(!function_exists('html_script')) {
	/**
	 * Write <script ...>
	 * @param $rel_path Path relative to template_directory
	 * @return String so you can echo
	 */
	function html_script($rel_path) {
		return '<script type="text/javascript" src="' . get_bloginfo('template_directory') . '/' . $rel_path . '"></script>' . "\n";
	}
}

if(!function_exists('html_style')) {
	/**
	 * Write <link stylesheet ...>
	 * @param $rel_path Path relative to template_directory
	 * @return String so you can echo
	 */
	function html_style($rel_path) {
		return '<link href="' . get_bloginfo('template_directory') . '/' . $rel_path . '" rel="stylesheet" type="text/css" />' . "\n";
	}
}

if(!function_exists('subwords')) {
	/**
	 * Extract the first $len words from a string
	 * @param $len Length in words
	 * @return Sliced string
	 */
	function subwords($s, $len = 10, $suffix = ' ...') {
		if($s !== NULL && $s != '') {
			$put_suffix = FALSE;
			$arr = explode(' ', $s);
			if(count($arr) > $len) {
				$arr = array_slice($arr, 0, $len);
				$put_suffix = TRUE;
			}

			return implode(' ', $arr) . ($put_suffix ? $suffix : '');
		}
	}
}


if(!function_exists('slugify')) {
	function slugify($phrase, $maxLength = 50) {
		$result = strtolower($phrase);
		$result = preg_replace("/[^a-z0-9\s-]/", "", $result);
		$result = trim(preg_replace("/[\s-]+/", " ", $result));
		$result = preg_replace("/\s/", "-", $result);
		return $result;
	}
}


if(!function_exists('stdclass_copy')) {
	/**
	 * Copy subset of object properties to other object
	 * @param object $source Source object
     * @param array $properties Properties to copy to the new object
	 * @return New stdClass object with copied properties. References are kept intact. NULL if $source is not object, $source is NULL or $properties is empty array
	 */
	function stdclass_copy($source, $properties) {
        if(!is_object($source) || empty($source)) {
            error_log("shallow_copy(): Invalid source object passed");
            return NULL;
        }
        if(empty($properties)) {
            error_log("shallow_copy(): Refusing to return object without properties");
            return NULL;
        }
        $ob = new stdClass();
        foreach($properties as $property) {
            if(isset($source->$property)) {
                $ob->$property = $source->$property;
            } else {
                $ob->$property = NULL;
                error_log("shallow_copy(): Object does not have property $property, ignoring");
            }
        }
        return $ob;
	}
}


if(!function_exists('number_order_human')) {
	/**
	 * Extract the first $len words from a string
	 * //TODO: Fix for various languages?
	 * @param $no Number to transform
	 * @return Transformed number
	 */
	function number_order_human($no) {
		if($no == 1) {
			return '1st';
		}
		if($no == 2) {
			return '2nd';
		}
		if($no == 3) {
			return '3rd';
		}
	}
}


/**
 * Pretty print a date interval
 *
 * @param integer $start
 *     (required) Start date as timestamp or string. If string, format would be 'YYYY-MM-dd'
 * @param integer $end
 *     (optional) End date as timestamp or string. If string, format is 'YYYY-MM-dd'
 * @param string $month
 *     Month format. %B - Full month name, %b - Abbreviated month etc. See strftime() manual for more details
 * @param string $year
 *     Year format. %Y - 4-digit year, %y - 2-digit year. See date() manual for more details
 * @param string $separator
 *     Separator between dates. For instance ' - ' in "16 - 19 October 2012"
 *
 * @return Date interval formatted, depending wether $end is null or not. Examples:
 *     * 19 - 23 January 2012  - same month, different month format
 *     * 28 Oct - 29 Nov 2012 - different months
 *     * 15 Dec 2012 - 3 Jan 2013 - different years
 *     Returns empty string if $start is invalid
 */
if(!function_exists('date_interval')) {
function date_interval($start, $end = null, $month = '%B', $year = '%Y', $separator = ' - ') {
	$ret = '';
	if(empty($start)) {
		return $ret;
	}
	$df = '%e'; // day format - default single digit
	$fmt = '%Y-%m-%d';
	if(is_string($start)) {
		$a = strptime($start, $fmt);
		$start = mktime(0, 0, 0, $a['tm_mon']+1, $a['tm_mday'], $a['tm_year']+1900);
	}
	if(!empty($end)) {
		if(is_string($end)) {
			$a = strptime($end, $fmt);
			$end = mktime(0, 0, 0, $a['tm_mon']+1, $a['tm_mday'], $a['tm_year']+1900);
		}
	} else {
		$end = false;
	}
	$full_start_date = strftime("$df $month $year", $start);
	if($end) {
		$full_end_date = trim(strftime("$df $month $year", $end));
		$ey = strftime('%Y', $end);
		$sy = strftime('%Y', $start);
		if($ey != $sy) {
			$ret = sprintf('%s%s%s', $full_start_date, $separator, $full_end_date);
		} else {
			// Same year
			$em = strftime('%m', $end);
			$sm = strftime('%m', $start);
			if($em != $sm) {
				$ret = sprintf('%s%s%s', strftime("$df $month", $start), $separator, $full_end_date);
			} else {
				// Same month
				$ret = sprintf('%s%s%s', strftime("$df", $start), $separator, $full_end_date);
			}
		}
	} else {
		$ret = $full_start_date;
	}
	return trim($ret);
}
}


/**
 * Mobile Detect
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class Mobile_Detect
{

	protected $accept;
	protected $userAgent;
	protected $isMobile = false;
	protected $isAndroid = null;
	protected $isAndroidtablet = null;
	protected $isIphone = null;
	protected $isIpad = null;
	protected $isBlackberry = null;
	protected $isBlackberrytablet = null;
	protected $isOpera = null;
	protected $isPalm = null;
	protected $isWindows = null;
	protected $isWindowsphone = null;
	protected $isGeneric = null;
	protected $devices = array(
		"android" => "android.*mobile",
		"androidtablet" => "android(?!.*mobile)",
		"blackberry" => "blackberry",
		"blackberrytablet" => "rim tablet os",
		"iphone" => "(iphone|ipod)",
		"ipad" => "(ipad)",
		"palm" => "(avantgo|blazer|elaine|hiptop|palm|plucker|xiino)",
		"windows" => "windows ce; (iemobile|ppc|smartphone)",
		"windowsphone" => "windows phone os",
		"generic" => "(kindle|mobile|mmp|midp|pocket|psp|symbian|smartphone|treo|up.browser|up.link|vodafone|wap|opera mini)"
	);

	public function __construct()
	{
		if(isset($_SERVER['HTTP_USER_AGENT'])) {
			$this->userAgent = $_SERVER['HTTP_USER_AGENT'];
		}
		if(isset($_SERVER['HTTP_ACCEPT'])) {
			$this->accept = $_SERVER['HTTP_ACCEPT'];
		}

		if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
			$this->isMobile = true;
		} elseif (strpos($this->accept, 'text/vnd.wap.wml') > 0 || strpos($this->accept, 'application/vnd.wap.xhtml+xml') > 0) {
			$this->isMobile = true;
		} else {
			foreach ($this->devices as $device => $regexp) {
				if ($this->isDevice($device)) {
					$this->isMobile = true;
				}
			}
		}
	}

	/**
	 * Overloads isAndroid() | isAndroidtablet() | isIphone() | isIpad() | isBlackberry() | isBlackberrytablet() | isPalm() | isWindowsphone() | isWindows() | isGeneric() through isDevice()
	 *
	 * @param string $name
	 * @param array $arguments
	 * @return bool
	 */
	public function __call($name, $arguments)
	{
		$device = substr($name, 2);
		if ($name == "is" . ucfirst($device) && array_key_exists(strtolower($device), $this->devices)) {
			return $this->isDevice($device);
		} else {
			trigger_error("Method $name not defined", E_USER_WARNING);
		}
	}

	/**
	 * Returns true if any type of mobile device detected, including special ones
	 * @return bool
	 */
	public function isMobile()
	{
		return $this->isMobile;
	}

	protected function isDevice($device)
	{
		$var = "is" . ucfirst($device);
		$return = $this->$var === null ? (bool) preg_match("/" . $this->devices[strtolower($device)] . "/i", $this->userAgent) : $this->$var;
		if ($device != 'generic' && $return == true) {
			$this->isGeneric = false;
		}

		return $return;
	}

}


if(!function_exists('get_request_value')) {
	/**
	 * Retrieve request parameter also from GET
	 * @param $name Parameter name
	 * @param $default Default value
	 * @param $trim Trim resulting string
	 * @return parameter value or empty string if not set
	 */
	function get_request_value($name, $default = NULL, $trim = TRUE) {
		$ret = $default;
		if (isset($_POST[$name]) && $_POST[$name] != '') {
			$ret = $_POST[$name];
			if($trim == TRUE) {
				$ret = trim($ret);
			}
		} else if (isset($_GET[$name]) && $_GET[$name] != '') {
			$ret = $_GET[$name];
			if($trim == TRUE) {
				$ret = trim($ret);
			}
		}
		return $ret;
	}
}

if(!function_exists('get_request_int')) {
	/**
	 * Retrieve request parameter also from GET
	 * @param name of the parameter
	 * @return parameter value or empty string if not set
	 */
	function get_request_int($name, $default = NULL) {
		$ret = get_request_value($name, $default, TRUE);
		if(!empty($ret)) {
			$ret = intval($ret);
		}
		return $ret;
	}
}

if(!function_exists('get_request_boolean')) {
	/**
	 * Retrieve boolean (checkbox) from request
	 * @param name of the parameter
	 * @return TRUE if the parameter was set, FALSE otherwise
	 */
	function get_request_boolean($name) {
		return isset($_POST[$name]) || isset($_GET[$name]);
	}
}

if(!function_exists('microtime_float')) {
function microtime_float($start = null, $msg = 'Execution took') {
	list ($msec, $sec) = explode(' ', microtime());
	$microtime = (float)$msec + (float)$sec;
	if($start) {
		imea_debug($msg . ': '. (round($microtime - $start, 3) * 1000) . ' ms');
	}
	return $microtime;
}
}



# PHP Calendar (version 2.3), written by Keith Devens
# http://keithdevens.com/software/php_calendar
#  see example at http://keithdevens.com/weblog
# License: http://keithdevens.com/software/license

if(!function_exists('generate_calendar')) {
	function generate_calendar($year, $month, $days = array(), $day_name_length = 3, $month_href = NULL, $first_day = 0, $pn = array()){
		$first_of_month = gmmktime(0,0,0,$month,1,$year);
		#remember that mktime will automatically correct if invalid dates are entered
		# for instance, mktime(0,0,0,12,32,1997) will be the date for Jan 1, 1998
		# this provides a built in "rounding" feature to generate_calendar()

		$day_names = array(); #generate all the day names according to the current locale
		for($n=0,$t=(3+$first_day)*86400; $n<7; $n++,$t+=86400) #January 4, 1970 was a Sunday
			$day_names[$n] = ucfirst(gmstrftime('%A',$t)); #%A means full textual day name

		list($month, $year, $month_name, $weekday) = explode(',',gmstrftime('%m,%Y,%B,%w',$first_of_month));
		$weekday = ($weekday + 7 - $first_day) % 7; #adjust for $first_day
		$title   = htmlentities(ucfirst($month_name)).'&nbsp;'.$year;  #note that some locales don't capitalize month and day names

		#Begin calendar. Uses a real <caption>. See http://diveintomark.org/archives/2002/07/03
		@list($p, $pl) = each($pn); @list($n, $nl) = each($pn); #previous and next links, if applicable
		if($p) $p = '<span class="calendar-prev">'.($pl ? '<a href="'.htmlspecialchars($pl).'">'.$p.'</a>' : $p).'</span>&nbsp;';
		if($n) $n = '&nbsp;<span class="calendar-next">'.($nl ? '<a href="'.htmlspecialchars($nl).'">'.$n.'</a>' : $n).'</span>';
		$calendar = '<table class="calendar">'."\n".
			'<caption class="calendar-month">'.$p.($month_href ? '<a href="'.htmlspecialchars($month_href).'">'.$title.'</a>' : $title).$n."</caption>\n<tr>";

		if($day_name_length){ #if the day names should be shown ($day_name_length > 0)
			#if day_name_length is >3, the full name of the day will be printed
			foreach($day_names as $d)
				$calendar .= '<th abbr="'.htmlentities($d).'">'.htmlentities($day_name_length < 4 ? substr($d,0,$day_name_length) : $d).'</th>';
			$calendar .= "</tr>\n<tr>";
		}

		if($weekday > 0) $calendar .= '<td colspan="'.$weekday.'">&nbsp;</td>'; #initial 'empty' days
		for($day=1,$days_in_month=gmdate('t',$first_of_month); $day<=$days_in_month; $day++,$weekday++){
			if($weekday == 7){
				$weekday   = 0; #start a new week
				$calendar .= "</tr>\n<tr>";
			}
			if(isset($days[$day]) and is_array($days[$day])){
				@list($link, $link_title, $external, $classes, $content) = $days[$day];
				if(is_null($content))  $content  = $day;
				$calendar .= '<td'.($classes ? ' class="'.htmlspecialchars($classes).'">' : '>').
					($link ? '<a href="'.htmlspecialchars($link).'" title="' . $link_title . '"' . ($external ? ' target="_blank" ' : '') . '>'.$content.'</a>' : $content).'</td>';
			}
			else $calendar .= "<td>$day</td>";
		}
		if($weekday != 7) $calendar .= '<td colspan="'.(7-$weekday).'">&nbsp;</td>'; #remaining "empty" days

		return $calendar."</tr>\n</table>\n";
	}
}


if(!function_exists('generate_calendar_eventspage')) {
	function generate_calendar_eventspage($year, $month, $days = array(), $day_name_length = 3, $month_href = NULL, $first_day = 0, $pn = array()){
		$first_of_month = gmmktime(0,0,0,$month,1,$year);
		#remember that mktime will automatically correct if invalid dates are entered
		# for instance, mktime(0,0,0,12,32,1997) will be the date for Jan 1, 1998
		# this provides a built in "rounding" feature to generate_calendar()

		$day_names = array(); #generate all the day names according to the current locale
		for($n=0,$t=(3+$first_day)*86400; $n<7; $n++,$t+=86400) #January 4, 1970 was a Sunday
			$day_names[$n] = ucfirst(gmstrftime('%A',$t)); #%A means full textual day name

		list($month, $year, $month_name, $weekday) = explode(',',gmstrftime('%m,%Y,%B,%w',$first_of_month));
		$weekday = ($weekday + 7 - $first_day) % 7; #adjust for $first_day
		$title   = htmlentities(ucfirst($month_name)).'&nbsp;'.$year;  #note that some locales don't capitalize month and day names

		#Begin calendar. Uses a real <caption>. See http://diveintomark.org/archives/2002/07/03
		@list($p, $pl) = each($pn); @list($n, $nl) = each($pn); #previous and next links, if applicable
		if($p) $p = '<span class="calendar-prev">'.($pl ? '<a href="'.htmlspecialchars($pl).'">'.$p.'</a>' : $p).'</span>';
		if($n) $n = '<span class="calendar-next">'.($nl ? '<a href="'.htmlspecialchars($nl).'">'.$n.'</a>' : $n).'</span>';
		$calendar = '<table class="calendar">'."\n".
			'<caption class="calendar-month">'.$p.$n.'<span class="month">'.($month_href ? '<a href="'.htmlspecialchars($month_href).'">'.$title.'</a>' : $title).'</span>'."</caption>\n<tr>";

		if($day_name_length){ #if the day names should be shown ($day_name_length > 0)
			#if day_name_length is >3, the full name of the day will be printed
			foreach($day_names as $d)
				$calendar .= '<th abbr="'.htmlentities($d).'">'.htmlentities($day_name_length < 4 ? substr($d,0,$day_name_length) : $d).'</th>';
			$calendar .= "</tr>\n<tr>";
		}

		if($weekday > 0) $calendar .= '<td colspan="'.$weekday.'">&nbsp;</td>'; #initial 'empty' days
		for($day=1,$days_in_month=gmdate('t',$first_of_month); $day<=$days_in_month; $day++,$weekday++){
			if($weekday == 7){
				$weekday   = 0; #start a new week
				$calendar .= "</tr>\n<tr>";
			}
			if(isset($days[$day]) and is_array($days[$day])){
				@list($link, $link_title, $external, $classes, $content) = $days[$day];
				if(is_null($content))  $content  = $day;
				$calendar .= '<td'.($classes ? ' class="'.htmlspecialchars($classes).'">' : '>').
					($link ? '<a href="'.htmlspecialchars($link).'" title="' . $link_title . '"' . ($external ? ' target="_blank" ' : '') . '>'.$content.'</a>' : $content).'</td>';
			}
			else $calendar .= "<td>$day</td>";
		}
		if($weekday != 7) $calendar .= '<td colspan="'.(7-$weekday).'">&nbsp;</td>'; #remaining "empty" days

		return $calendar."</tr>\n</table>\n";
	}
}


if(!function_exists('show_event_interval')) {
	function show_event_interval($ob_event) {
		$interval = '';
		$start = NULL;
		$end = NULL;
		if($ob_event->start !== NULL) {
			$start = strtotime($ob_event->start);
		}
		if($ob_event->end !== NULL) {
			$end = strtotime($ob_event->end);
		}
		if($start !== NULL && $end === NULL) {
			$interval = date('j M, Y', $start);
		}
		if($start !== NULL && $end !== NULL) {
			// Same year
			if( date('Y', $start) == date('Y', $end)) {
				// Same month
				if( date('n', $start) == date('n', $end)) {
					// Same day
					if( date('j', $start) == date('j', $end)) {
						$interval = date('j M, Y', $end);
					} else {
						$interval = date('j', $start) . ' - ' . date('j M, Y', $end);
					}
				} else {
					$interval = date('j M', $start) . ' - ' . date('j M, Y', $end);
				}
			} else {
				$interval = date('j, M Y', $start) . ' - ' . date('j M, Y', $end);
			}
		}
		return $interval;
	}
}


if(!function_exists('get_request_variable')) {
	function get_request_variable($name, $type = 'str', $default_value = NULL) {
		global $wp_query, $wp_rewrite;
		$ret = $default_value;
		if ($wp_rewrite->using_permalinks()) {
			if(!empty($wp_query->query_vars[$name])) {
				$ret = $wp_query->query_vars[$name];
			}
		} else {
			if(isset($_POST[$name])) {
				$ret = $_POST[$name];
			}
			if(isset($_GET[$name])) {
				$ret = $_GET[$name];
			}
		}

		if(!empty($ret) && is_int($type)) {
			$ret = intval($ret);
		}
		return $ret;
	}
}

if(!function_exists('is_request_variable')) {
	function is_request_variable($name) {
		global $wp_query, $wp_rewrite;
		$ret = FALSE;
		if ($wp_rewrite->using_permalinks()) {
			if(isset($wp_query->query_vars[$name])) {
				$ret = TRUE;
			}
		} else {
			if(isset($_POST[$name])) {
				$ret = TRUE;
			}
			if(isset($_GET[$name])) {
				$ret = TRUE;
			}
		}
		return $ret;
	}
}


if(!function_exists('replace_enter_br')) {
	function replace_enter_br($strval) {
		if($strval !== NULL) {
			$r = array("\r\n", "\n", "\r");
			return str_replace($r, '<br />', $strval);
		}
	}
}


if(!function_exists('get_administrators')) {
	function get_administrators() {
		global $wpdb;
		$sql = "SELECT user_email, meta_value FROM wp_users a INNER JOIN wp_usermeta b ON a.id = b.user_id WHERE meta_key = 'wp_capabilities'"; //TODO: replace wp_ with prefix_
		return $wpdb->get_results($sql);
	}
}

if(!function_exists('decode_decision_status_image')) {
	/**
	 * Decode the status field for an ai_decision
	 * @param status MySQL ENUM value
	 * @return Translated value in human language
	 */
	function decode_decision_status_image($status) {
		$url = get_bloginfo('template_directory') . '/images/decision/';
		switch($status) {
			case 'draft':
				return $url . 'draft.png';
			case 'active':
				return $url . 'active.png';
			case 'revised':
				return $url . 'active.png';
			case 'amended':
				return $url . 'amended.png';
			case 'retired':
				return $url . 'retired.png';
			default:
				return null;
		}
	}
}

if(!function_exists('decode_decision_status')) {
	/**
	 * Decode the status field for an ai_decision
	 * @param status MySQL ENUM value
	 * @return Translated value in human language
	 */
	function decode_decision_status($status) {
		switch($status) {
			case 'draft':
				return __('Draft', 'informea');
			case 'active':
				return __('Active', 'informea');
			case 'revised':
				return __('Revised', 'informea');
			case 'amended':
				return __('Amended', 'informea');
			case 'retired':
				return __('Retired', 'informea');
			default:
				return __('Unknown', 'informea');
		}
	}
}

if(!function_exists('decode_decision_type')) {
	/**
	 * Decode the type field for an ai_decision
	 * @param status MySQL ENUM value
	 * @return Translated value in human language
	 */
	function decode_decision_type($status) {
		switch($status) {
			case 'decision':
				return __('Decision', 'informea');
			case 'resolution':
				return __('Resolution', 'informea');
			case 'recommendation':
				return __('Recommendation', 'informea');
			default:
				return __('Unknown', 'informea');
		}
	}
}

if(!function_exists('qsort')) {
function qsort(&$array, $field = 'order') {
	$cur = 1;
	$stack[1]['l'] = 0;
	$stack[1]['r'] = count($array)-1;
	do {
		$l = $stack[$cur]['l'];
		$r = $stack[$cur]['r'];
		$cur--;
		do {
			$i = $l;
			$j = $r;
			$tmp = $array[(int)( ($l+$r)/2 )];
			// partion the array in two parts.
			// left from $tmp are with smaller values,
			// right from $tmp are with bigger ones
			do {
				while($array[$i]->$field < $tmp->$field)
					$i++;
				while($tmp->$field < $array[$j]->$field)
					$j--;
				// swap elements from the two sides
				if( $i <= $j) {
					$w = $array[$i];
					$array[$i] = $array[$j];
					$array[$j] = $w;

					$i++;
					$j--;
				}
			} while( $i <= $j );
			if( $i < $r ) {
				$cur++;
				$stack[$cur]['l'] = $i;
				$stack[$cur]['r'] = $r;
			}
			$r = $j;
		} while( $l < $r );
	} while( $cur != 0 );
}
}

if(!class_exists('paginated_query')) {

/**
 * Pagination class used to retrieve the dataset from database in chunks of data.
 */
class paginated_query {
	private $sql;
	private $sql_count;

	private $page_size = 10;
	private $page = 0;
	/** Total number of results */
	private $count = 0;
	/** Accepted request parameters to build URLs for pagination */
	private $req_parameters = array();

	private $method = 'get';

	private $tpl_post = "
<input type='hidden' name='%s' value='%s' />";

	/**
	 * Where the base_url / action form will point to. Default to current page ''.
	 */
	private $target_url = '';

	/**
	 * Constructor
	 * @param sql SQL statement that retrieves the data
	 * @param req_parameters HTTP request parameters specific to the page in order to correctly reconstruct the URL and add specific parameters (i.e. page)
	 * @param page Page of results to display (ex. 0, 1, 2, 3, 4 ...)
	 * @param page_size Size of the results page (ex. 10)
	 * @param sql_count The SQL statement used for counting.
	 * For simple queries `SELECT * FROM table` there is no need, class will figure it out by replacing `*` with `COUNT(*)`.
	 * For complex selects write here the statement.
	 */
	function __construct($sql, $req_parameters, $page = NULL, $page_size = 10, $sql_count = NULL) {
		$this->sql = $sql;
		$this->sql_count = $sql_count;
		$this->page_size = $page_size;
		$this->req_parameters = $req_parameters;

		$this->page = $page;
		if($this->page === NULL) {
			$this->page = get_request_value('page', 0);
		}
		// Fixe page to avoid SQL errors
		if($this->page < 0) { $this->page = 0; }
	}

	function __destruct() {
		return true;
	}

	function _count_results() {
		global $wpdb;
		$ret = 0;
		$sql = $this->sql_count;
		if ($sql === NULL) {
			// Gues the count sql by replacing `SELECT ... FROM table` with `SELECT COUNT(*) FROM table`.
			// This should work for most simple statements.
			$sql = preg_replace('/^SELECT .* FROM/', 'SELECT COUNT(*) as cnt FROM', $this->sql, 1);
		}
		$count = $wpdb->get_results($sql);
		if (count($count)) {
			$ret = $count[0]->cnt;
		}
		$this->count = $ret;
		return $ret;
	}

	/**
	 * Retrieve the page of results
	 * @return A list of rows from the database. object with properties as column names.
	 */
	function results() {
		global $wpdb;
		$this->_count_results();

		$start = $this->page * $this->page_size;
		// Normalize the start value to last page if erroneous
		if($start > $this->count) {
			$start = $this->count - ($this->count % $this->page_size);
		}
		$this->sql = $this->sql . " LIMIT " . $start . ", " . $this->page_size;
		return $wpdb->get_results($this->sql);
	}

	/**
	 * Get the starting index for this page (10th, 20th row etc.)
	 * @return integer with first row index
	 */
	function start() {
		return $this->page * $this->page_size + 1;
	}

	/**
	 * Get the last index for this page (21th, 31th etc.)
	 * @return integer with last row index
	 */
	function end() {
		if($this->is_last_page()) return $this->count;
		return $this->page * $this->page_size + $this->page_size;
	}

	/**
	 * Check if we are on last page of results.
	 * @return TRUE if this is the last page
	 */
	function is_last_page() {
		return $this->page == ceil($this->count / $this->page_size) - 1;
	}

	/**
	 * Retrieve the total number of results.
	 * @return integer with count of all records from the query.
	 */
	function total() {
		return $this->count;
	}

	/**
	 * Check if we have a next page of results.
	 * @return TRUE if we have next page
	 */
	function has_next() {
		$pages = ceil($this->count / $this->page_size);
		return $this->page < $pages - 1;
	}

	/**
	 * Get the URL for the next page of results. Useful for templating.
	 * @return string with next's page URL
	 */
	function next_url() {
		$url = $this->base_url();
		return $url . '&page=' . ($this->page + 1);
	}

	function next_form() {
		$ret = $this->base_form();
		$ret .= sprintf($this->tpl_post, 'page', $this->page + 1);
		return $ret;
	}

	/**
	 * Check if we have a previous page of results.
	 * @return TRUE if we have previous page
	 */
	function has_previous() {
		$pages = ceil($this->count / $this->page_size);
		return $this->page > 0;
	}

	/**
	 * Get the URL for the previous page of results. Useful for templating.
	 * @return string with previous's page URL
	 */
	function previous_url() {
		$url = $this->base_url();
		return $url . '&page=' . ($this->page - 1);
	}

	function previous_form() {
		$ret = $this->base_form();
		$ret .= sprintf($this->tpl_post, 'page', $this->page - 1);
		return $ret;
	}

	/**
	 * Retrieve the HTTP base URL to the current page and reconstruct the GET parameters.
	 * @return string with URL to the page.
	 */
	function base_url() {
		$ret = $this->target_url . '?';
		foreach ($this->req_parameters as $name => $type) {
			if(isset($_GET[$name])) {
				$value = $_GET[$name];
				if(is_int($type) && !empty($value)) {
					$value = intval($value);
				}
				$ret .= "&$name=$value";
			}
		}
		return $ret;
	}

	/**
	 * Retrieve the HTTP base URL to the current page and reconstruct the GET parameters.
	 * @return string with URL to the page.
	 */
	function base_form() {
		$ret = '';
		foreach ($this->req_parameters as $name => $type) {
			if(isset($_POST[$name])) {
				$value = $_POST[$name];
				if(is_int($type)) {
					$ret .= sprintf($this->tpl_post, $name, intval($value));
				}
				else if(is_array($type)) {
					$value = $_POST[$name];
					foreach($value as $item) {
						// Go on safe side and make them integers for now, as I am transmiting only integers on array of values
						$ret .= sprintf($this->tpl_post, $name . '[]', intval($item));
					}
				} else {
					$ret .= sprintf($this->tpl_post, $name, $value);
				}
			}
		}
		return $ret;
	}

	/**
	 * Set the page size
	 * @param size New page size (Results per page)
	 */
	 function set_page_size($size) {
		 if($size !== NULL) {
			 $this->page_size = $size;
		 }
	 }

	/**
	 * HTTP method 'post' or 'get'
	 */
	function get_method() {
		return $this->method;
	}

	/**
	 * HTTP method 'post' or 'get'
	 */
	function set_method($method) {
		$this->method = $method;
	}

	function get_target_url() {
		return $this->target_url;
	}

	function set_target_url($url) {
		$this->target_url = $url;
	}

	function get_sql() {
		return $this->sql;
	}

}
}


if(!class_exists('paginated_array')) {

/**
 * Pagination class used to retrieve the dataset from an array of results in chunks of data.
 */
class paginated_array {
	private $page_size = 10;
	private $page = 0;
	/** Total number of results */
	private $count = 0;

	/**
	 * Constructor
	 * @param array array that holds the data
	 * @param page Page of results to display (ex. 0, 1, 2, 3, 4 ...)
	 * @param page_size Size of the results page (ex. 10)
	 */
	function __construct($array, $page = 0, $page_size = 10) {
		$this->array = $array;
		$this->count = count($array);
		$this->page = $page;
		$this->page_size = $page_size;
	}

	function __destruct() {
		return true;
	}

	/**
	 * Retrieve the page of results
	 * @return sub-array
	 */
	function results() {
		$start = $this->page * $this->page_size;
		// Normalize the start value to last page if erroneous
		if($start > $this->count) {
			$start = $this->count - ($this->count % $this->page_size);
		}
		return array_slice($this->array, $start, $this->page_size, TRUE);
	}

	/**
	 * Get the starting index for this page (10th, 20th row etc.)
	 * @return integer with first row index
	 */
	function start() {
		return $this->page * $this->page_size + 1;
	}

	/**
	 * Get the last index for this page (21th, 31th etc.)
	 * @return integer with last row index
	 */
	function end() {
		if($this->is_last_page()) return $this->count;
		return $this->page * $this->page_size + $this->page_size;
	}

	/**
	 * Check if we are on last page of results.
	 * @return TRUE if this is the first page
	 */
	function is_first_page() {
		return $this->page == 0;
	}

	/**
	 * Check if we are on last page of results.
	 * @return TRUE if this is the last page
	 */
	function is_last_page() {
		return $this->page == ceil($this->count / $this->page_size) - 1;
	}

	/**
	 * Retrieve the total number of results.
	 * @return integer with count of all records from the query.
	 */
	function total() {
		return $this->count;
	}

	/**
	 * Check if we have a next page of results.
	 * @return TRUE if we have next page
	 */
	function has_next() {
		$pages = ceil($this->count / $this->page_size);
		return $this->page < $pages - 1;
	}

	/**
	 * Check if we have a previous page of results.
	 * @return TRUE if we have previous page
	 */
	function has_previous() {
		$pages = ceil($this->count / $this->page_size);
		return $this->page > 0;
	}

	/**
	 * Set the page size
	 * @param size New page size (Results per page)
	 */
	 function set_page_size($size) {
		 if($size !== NULL) {
			 $this->page_size = $size;
		 }
	 }
}
}
