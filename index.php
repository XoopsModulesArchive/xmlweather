<?php
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//                         XOOPS XmlWeather Module 1.5	    	 	 //
//                    Just enjoy! Internet for everyone!!			 //
//                      wanikoo <http://www.wanisys.net/>                    //
//  ------------------------------------------------------------------------ //
//                         Based on Weather on your site(weather_xml.php)                       //
//                     	 ( http://notonebit.com/ )		                          //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //


include('../../mainfile.php');

include_once "config.php";
include_once "include/functions.php";

/* meaningless
$php_ver=explode(".", phpversion());
if($php_ver[0]<4 || $php_ver[0]==4 && $php_ver[1]<1) {
$_GET=&$HTTP_GET_VARS;
$_POST=&$HTTP_POST_VARS;
}
*/
//refer to config.php
$partner_ID = XMLWEATHER_PID;
$license_key = XMLWEATHER_KEY;
$image_size = XMLWEATHER_IMGSIZE;
$default_location = XMLWEATHER_LOC;
$default_length = XMLWEATHER_LENGTH;
$interval = XMLWEATHER_INTERVAL;

//
define('XMLWEATHERTABLE', $xoopsDB->prefix("weather_xml"));

$xoopsOption['template_main']= 'xmlweather_index.html';
include(XOOPS_ROOT_PATH.'/header.php');

$xmlweather_module_header = '';
$xmlweather_module_header .= '
	<link rel="stylesheet" type="text/css" href="'.XMLWEATHERURL.'/xmlweather.css" />
	<script src="'.XMLWEATHERURL.'/xmlweather.js" type="text/javascript"></script>
	';
$xoops_module_header = $xmlweather_module_header; 
$xoopsTpl->assign('xoops_module_header', $xmlweather_module_header);

$xoopsTpl->assign('lang_xmlweather_enterdata', _MD_XMLWEATHER_ENTERDATA);
$xoopsTpl->assign('lang_xmlweather_search', _MD_XMLWEATHER_SEARCH);

$myts =& MyTextSanitizer::getInstance();

// Set Local variables
//search on location (city or zip)
// specific town id
// Forecast length
if ( empty($_POST['loc']) ) { $location = ""; } else { $location = urlencode($myts->stripSlashesGPC(trim($_POST['loc']))); }
if ( empty($_POST['id']) ) { $loc_id = ""; } else { $loc_id = $myts->stripSlashesGPC(trim($_POST['id'])); }
if ( isset($_POST['length']) ) { $length = intval($_POST['length']); } else { $length = 5; }

if ( isset($_GET['id']) ){ $loc_id = $myts->stripSlashesGPC(trim($_GET['id'])); }
if ( isset($_GET['length']) ) { $length = intval($_GET['length']); } 

if (!($length >= 1 && $length <= 5)) $length = 5;


if ( empty($location) && empty($loc_id) && !empty($default_location) )
{
$loc_id = $default_location;
$length = $default_length;
}


// First URL for searching, second for detail.
$search_url = "http://xoap.weather.com/search/search?where=$location";
$forecast_url = "http://xoap.weather.com/weather/local/$loc_id?cc=*&dayf=$length&link=xoap&prod=xoap&par=$partner_ID&key=$license_key";

/*
cc	Current Conditions OPTIONAL VALUE IGNORED
dayf	Multi-day forecast information for some or all forecast elements OPTIONAL VALUE = [ 1..10 ]
link	Links for weather.com pages OPTIONAL VALUE = xoap
par	Application developers Id assigned to you REQUIRED VALUE = {partner id}
prod	The XML Server product code REQUIRED VALUE = xoap
key	The license key assigned to you REQUIRED VALUE = {license key}
unit	Set of units. Standard or Metric OPTIONAL VALUES = [ s | m ] DEFAULT = s
*/


if (!empty($location)) // Determine URL to use. If location is passed, we're searching for a city or zip. Elese we're retrieving a forecast.
{
	$url = $search_url;
}
else
{
	$url = $forecast_url;
}

// If city, zip, or weather.com city id passed, do XML query. $loc_id is a weather.com city code, $location is user entered city or zip
if (!empty($location) || !empty($loc_id) ) 
{

	/* 
	query db for md5 of url
	if doesn't exist, insert into db
	if exists, check date, if under X hours use db content
	if older then X hours, pull from weather.com and update db

	to delete old data: when querying, delete all records older than X hours
	*/

	$datetime = date("Y-m-d h:i:s");
	$xml_url = md5($url);
	$expires = $interval*60*60;
	$expiredatetime = date("Y-m-d H:i:s", time() - $expires);

	// Delete expired records
	
	$result = $xoopsDB->queryF("DELETE FROM ".XMLWEATHERTABLE." WHERE last_updated < '$expiredatetime'") or exit("Delete Error");

	$result=$xoopsDB->query("SELECT * FROM ".XMLWEATHERTABLE." WHERE xml_url = '$xml_url'") or exit("Select Error");

	$row = $xoopsDB->fetchArray($result);
	//$time_diff = strtotime($datetime) - strtotime($row['last_updated']);

	// Data not in table - Add
	if ($xoopsDB->getRowsNum($result) < 1) 
	{
		$xml = XW_ReadContent($url);
		if ($xml === false) {
		echo _MD_XMLWEATHER_TEMPDOWN;
		include XOOPS_ROOT_PATH.'/footer.php';
		die();
		}
		// Fire up the built-in XML parser
		$parser = xml_parser_create(  ); 
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);

		// Set tag names and values
		xml_parse_into_struct($parser,$xml,$values,$index); 

		// Close down XML parser
		xml_parser_free($parser);

		$xml = str_replace("'","",$xml); // Added to handle cities with apostrophies in the name like T'Bilisi, Georgia

		if (!empty($loc_id)) // Only inserts forecast feed, not search results feed, into db
		{
			$xml = addslashes(trim($xml));
			$result=$xoopsDB->queryF("INSERT INTO ".XMLWEATHERTABLE." VALUES ('$xml_url', '$xml', '$datetime')") or exit("Insert Error");

		}

	}
	else // Data in table, and it is within expiration period - do not load from weather.com and use cached copy instead.
	{
		$xml = stripslashes(trim($row['xml_data']));

		// Fire up the built-in XML parser
		$parser = xml_parser_create(  ); 
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);

		// Set tag names and values
		xml_parse_into_struct($parser,$xml,$values,$index); 

		// Close down XML parser
		xml_parser_free($parser);
	}

	// Debugging output
	//echo "<pre>";
	//print_r($xml);
	//print_r($index);
	//print_r($values);
	//echo "</pre>";
}

if (!empty($loc_id)) // Location code selected - Display detail info. A specific city has been selected from the drop down menu. Get forecast.
{
	$city = htmlspecialchars($values[$index['dnam'][0]]['value']);
	$info_time = htmlspecialchars($values[$index['tm'][0]]['value']);
	$info_lat = htmlspecialchars($values[$index['lat'][0]]['value']);
	$info_long = htmlspecialchars($values[$index['lon'][0]]['value']);
	$unit_temp = htmlspecialchars($values[$index['ut'][0]]['value']);
	$unit_speed = htmlspecialchars($values[$index['us'][0]]['value']);
	$unit_precip = htmlspecialchars($values[$index['up'][0]]['value']);
	$unit_pressure = htmlspecialchars($values[$index['ur'][0]]['value']);
	$sunrise = htmlspecialchars($values[$index['sunr'][0]]['value']);
	$sunset = htmlspecialchars($values[$index['suns'][0]]['value']);
	$timezone = htmlspecialchars($values[$index['zone'][0]]['value']);
	$last_update = htmlspecialchars($values[$index['lsup'][0]]['value']);
	$curr_temp = htmlspecialchars($values[$index['tmp'][0]]['value']);
	$curr_flik = htmlspecialchars($values[$index['flik'][0]]['value']);
	$curr_text = htmlspecialchars($values[$index['t'][4]]['value']);
	$curr_icon = htmlspecialchars($values[$index['icon'][0]]['value']);
	//ver1.5
	$curr_observation_station = htmlspecialchars($values[$index['obst'][0]]['value']);
	$curr_humidity = htmlspecialchars($values[$index['hmid'][0]]['value']);
	$curr_visibility = htmlspecialchars($values[$index['vis'][0]]['value']);
	$curr_dew_point = htmlspecialchars($values[$index['dewp'][0]]['value']);
	$curr_uv_index = htmlspecialchars($values[$index['i'][0]]['value']);
	$curr_uv_text = htmlspecialchars($values[$index['t'][6]]['value']);
	$curr_moon_icon = htmlspecialchars($values[$index['icon'][1]]['value']);
	$curr_moon_text = htmlspecialchars($values[$index['t'][7]]['value']);
	$curr_uv_index = htmlspecialchars($values[$index['i'][0]]['value']);
	$curr_wind_speed = htmlspecialchars($values[$index['s'][0]]['value']);
	$curr_wind_gust = htmlspecialchars($values[$index['gust'][0]]['value']);
	$curr_wind_direction = htmlspecialchars($values[$index['d'][1]]['value']);
	$curr_wind_text = htmlspecialchars($values[$index['t'][5]]['value']);
	$curr_barometer = htmlspecialchars($values[$index['r'][0]]['value']);
	$curr_barometer_dir = htmlspecialchars($values[$index['d'][0]]['value']);
	$unit_dist = htmlspecialchars($values[$index['ud'][0]]['value']);
	$promo_link = array();
	$promo_link[0] = array ( $values[$index['t'][0]]['value'] , $values[$index['l'][0]]['value']);
	$promo_link[1] = array ( $values[$index['t'][1]]['value'] , $values[$index['l'][1]]['value']);
	$promo_link[2] = array ( $values[$index['t'][2]]['value'] , $values[$index['l'][2]]['value']);
	$promo_link[3] = array ( $values[$index['t'][3]]['value'] , $values[$index['l'][3]]['value']);

	$counter = 0;
	$row_counter = 2;

	$xoopsTpl->assign("xmlweather_detailinfo", true);
	$xoopsTpl->assign("xmlweather_city", $city);
	$xoopsTpl->assign("xmlweather_unit_temp", $unit_temp);
	$xoopsTpl->assign("xmlweather_unit_speed", $unit_speed);
	$xoopsTpl->assign("xmlweather_unit_precip", $unit_precip);
	$xoopsTpl->assign("xmlweather_unit_pressure", $unit_pressure);
	$xoopsTpl->assign("xmlweather_sunrise", $sunrise);
	$xoopsTpl->assign("xmlweather_sunset", $sunset);
	$xoopsTpl->assign("xmlweather_timezone", $timezone);
	$xoopsTpl->assign("xmlweather_last_updated", $last_update);
	$xoopsTpl->assign("xmlweather_curr_temp", $curr_temp);
	$xoopsTpl->assign("xmlweather_curr_flik", $curr_flik);
	$xoopsTpl->assign("xmlweather_curr_text", $curr_text);
	$xoopsTpl->assign("xmlweather_curr_icon", $curr_icon);
	//ver1.5
	$xoopsTpl->assign("xmlweather_humidity", $curr_humidity);
	$xoopsTpl->assign("xmlweather_visibility", $curr_visibility);
	$xoopsTpl->assign("xmlweather_dewpoint", $curr_dew_point);
	$xoopsTpl->assign("xmlweather_uvindex", $curr_uv_index);
	$xoopsTpl->assign("xmlweather_uvtext", $curr_uv_text);
	$xoopsTpl->assign("xmlweather_barometer", $curr_barometer.$unit_pressure." ".$curr_barometer_dir);
	$xoopsTpl->assign("xmlweather_moontext", $curr_moon_text);
	$xoopsTpl->assign("xmlweather_system", $unit_temp);
	$xoopsTpl->assign("xmlweather_unit_dist", $unit_dist);
	$xoopsTpl->assign("xmlweather_promo_link", $promo_link);
	if(is_numeric($curr_wind_speed)) {
	$xoopsTpl->assign("xmlweather_wind", $curr_wind_speed.$unit_speed." (".$curr_wind_text.")");
	}
	else {
	$xoopsTpl->assign("xmlweather_wind", $curr_wind_speed." (".$curr_wind_text.")");
	}
	$xoopsTpl->assign(array("lang_xmlweather_report" => _MD_XMLWEATHER_REPORT, "lang_xmlweather_lastupdated" => _MD_XMLWEATHER_LASTUPDATED, "lang_xmlweather_currently" => _MD_XMLWEATHER_CURRENTLY, "lang_xmlweather_feellike" => _MD_XMLWEATHER_FEELLIKE, "lang_xmlweather_currentconditon" => _MD_XMLWEATHER_CURRENTCONDITION, "lang_xmlweather_sunrise" => _MD_XMLWEATHER_SUNRISE,  "lang_xmlweather_sunset" => _MD_XMLWEATHER_SUNSET));
	//ver1.5
	$xoopsTpl->assign(array("lang_xmlweather_humidity" => _MD_XMLWEATHER_HUMIDITY, "lang_xmlweather_visibility" => _MD_XMLWEATHER_VISIBILITY, "lang_xmlweather_dewpoint" => _MD_XMLWEATHER_DEWPOINT));
	$xoopsTpl->assign(array("lang_xmlweather_uvindex" => _MD_XMLWEATHER_UVINDEX, "lang_xmlweather_barometer" => _MD_XMLWEATHER_BAROMETER, "lang_xmlweather_moon" => _MD_XMLWEATHER_MOON));
	$xoopsTpl->assign(array("lang_xmlweather_wind" => _MD_XMLWEATHER_WIND));

	$detailtable = "<table border=\"0\" cellpadding=\"4\" cellspacing=\"1\" bgcolor=\"#C0C0C0\"><tr><th>"._MD_XMLWEATHER_DATE."</th><th>"._MD_XMLWEATHER_HIGH."/"._MD_XMLWEATHER_LOW."</th><th>"._MD_XMLWEATHER_DAY."</th><th>"._MD_XMLWEATHER_NIGHT."</th></tr>";
	foreach ($index["day"] as $day)
	{
		if (@$values[$day]['attributes']["t"] != "")
		{
//			($row_counter%2==0) ? $row_color =  "#CCE6FF": $row_color = "#CCCDFF";
			$row_color = "#EEEECC";
			$img_day = ($counter + 1) * 2;
			$img_night = (($counter + 1) * 2) + 1;

			$day_text = (($counter + 1) * 3) + $counter + 5;
			$day_wind = ((($counter + 1) * 3) + $counter) + 2;
			$day_windspeed = (($counter + 1) * 2) - 1;
			$day_windgust = (($counter + 1) * 2) - 1;
			$day_winddir = ($counter + 1) * 2; // 2,4,6,...
			$day_humidity = (($counter + 1) * 2) - 1;
			$day_precip = $counter * 2;

			$night_text = ((($counter + 1) * 3) + $counter) + 7;
			$night_wind = ((($counter + 1) * 3) + $counter) + 4;
			$night_windspeed = ($counter + 1) * 2;
			$night_windgust = ($counter + 1) * 2;
			$night_winddir = (($counter + 1) * 2) + 1; // 3,5,7,...
			$night_humidity = ($counter + 1) * 2;
			$night_precip = ($counter * 2) + 1;

			if ($values[$index["hi"][$counter]]["value"] >= 0) $heat_color = "#CC99CC";
			if ($values[$index["hi"][$counter]]["value"] >= 10) $heat_color = "#9966FF";
			if ($values[$index["hi"][$counter]]["value"] >= 20) $heat_color = "#3399FF";
			if ($values[$index["hi"][$counter]]["value"] >= 30) $heat_color = "#99CCFF";
			if ($values[$index["hi"][$counter]]["value"] >= 40) $heat_color = "#66CC66";
			if ($values[$index["hi"][$counter]]["value"] >= 50) $heat_color = "#FFFF99";
			if ($values[$index["hi"][$counter]]["value"] >= 60) $heat_color = "#FFCC33";
			if ($values[$index["hi"][$counter]]["value"] >= 70) $heat_color = "#FF9933";
			if ($values[$index["hi"][$counter]]["value"] >= 80) $heat_color = "#FF6600";
			if ($values[$index["hi"][$counter]]["value"] >= 90) $heat_color = "#FF0000";
			if ($values[$index["hi"][$counter]]["value"] >= 100) $heat_color = "#990000";
			if ($values[$index["hi"][$counter]]["value"] == "N/A") $heat_color = "#EEEECC";

				
			$detailtable .= "<tr><td bgcolor=\"$heat_color\"><b>" . $values[$day]['attributes']['t'] . ", " . $values[$day]['attributes']['dt'] . "</b></td>";
			$detailtable .= "<td bgcolor=\"$row_color\"><b>"._MD_XMLWEATHER_HIGH.": " . $values[$index['hi'][$counter]]['value'] . "&#730; $unit_temp\n";
			$detailtable .= "<hr noshade height=\"1\">"._MD_XMLWEATHER_LOW.": " . $values[$index['low'][$counter]]['value'] . "&#730; $unit_temp</b></td>\n";

			$detailtable .= "<td bgcolor=\"$row_color\" nowrap><table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\"><tr><td><b>\n";
			$detailtable .= $values[$index['t'][$day_text]]['value'] . "</b><br /> ";
			$detailtable .= "<font size=1>"._MD_XMLWEATHER_SUNRISE.": " . $values[$index['sunr'][$counter+1]]['value'] . "<br />";
			$detailtable .= ""._MD_XMLWEATHER_WIND.": " . $values[$index['t'][$day_wind]]['value'] . " " . $values[$index['s'][$day_windspeed]]['value'] . " $unit_speed";
			$detailtable .= "<br />"._MD_XMLWEATHER_HUMIDITY.": " . $values[$index['hmid'][$day_humidity]]['value'] . "%<br />"._MD_XMLWEATHER_PRECIPITATION.": " . $values[$index['ppcp'][$day_precip]]['value'] . "%</font></td>";
			$detailtable .= "<td bgcolor=\"$row_color\" align=\"right\"><img border=\"1\" src=\"".XMLWEATHERIMAGEURL."/$image_size/" . $values[$index['icon'][$img_day]]['value'] . ".png\" alt=\"" . $values[$index['t'][$day_text]]['value'] . "\"></td></tr></table></td>";

			$detailtable .= "<td bgcolor=\"$row_color\" nowrap><table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\"><tr><td><b>\n";
			$detailtable .= $values[$index['t'][$night_text]]['value'] . "</b><br />";
			$detailtable .= "<font size=1>"._MD_XMLWEATHER_SUNSET.": " . $values[$index['suns'][$counter+1]]['value'] . "<br />";
			$detailtable .= ""._MD_XMLWEATHER_WIND.": " . $values[$index['t'][$night_wind]]['value'] . " " . $values[$index['s'][$night_windspeed]]['value'] . " $unit_speed";
			$detailtable .= "<br />"._MD_XMLWEATHER_HUMIDITY.": " . $values[$index['hmid'][$night_humidity]]['value'] . "%<br />"._MD_XMLWEATHER_PRECIPITATION.": " . $values[$index['ppcp'][$night_precip]]['value'] . "%</font></td>\n";
			$detailtable .= "<td bgcolor=\"$row_color\" align=\"right\"><img border=\"1\" src=\"".XMLWEATHERIMAGEURL."/$image_size/" . $values[$index['icon'][$img_night]]['value'] .".png\" alt=\"" . $values[$index['t'][$night_text]]['value'] . "\"></tr></table></td>";
			$detailtable .= "</tr>";

			$counter++;
			$row_counter++;
		}
	}
	$detailtable .= "</table>";
	$xoopsTpl->assign("xmlweather_detailtable", $detailtable);

}

if ( !empty($location) && is_array(@$index['loc'])) // A city name has been entered and data returned from weather.com, draw drop down menu of matches
{

	if (count($index['loc']) == 1) // If just one match returned, send to detail screen - no need to draw option box for one option.
	{
		$location_code = $values[$index['loc'][0]]['attributes']['id'];
		header("Location: index.php?id=$location_code&length=5"); // Nees html_header because of this redirect.
		exit();
	}


	$xoopsTpl->assign("xmlweather_detailsearchinfo", true);

	$detailsearch = "";
	$detailsearch .= "<form action=\"index.php\" method=\"post\">";
	$detailsearch .= ""._MD_XMLWEATHER_SELECTCITY.": <select size=\"1\" name=\"id\">\n";
	// Loop through the XML, setting values
	foreach ($index['loc'] as $key=>$val)
	{
		$detailsearch .= "<option value=\"";
		$detailsearch .= $values[$val]['attributes']['id']; // City code
		$detailsearch .= "\">";
		$detailsearch .= $values[$val]['value']; // City name
		$detailsearch .= "</option>\n";
	}
	$detailsearch .= "</select>";

	$detailsearch .= '
	<select size="1" name="length">
	<option selected="selected">5</option>
	<option>4</option>
	<option>3</option>
	<option>2</option>
	<option>1</option>
	</select>
	<input type="submit" value="'._MD_XMLWEATHER_GETWEATHER.'">
	</form>';

	$xoopsTpl->assign("xmlweather_detailsearch", $detailsearch);

}

elseif (!empty($location)) // City or zip entered but no match returned from weather.com
{
	$xoopsTpl->assign("xmlweather_nomatchinfo", true);
	$xoopsTpl->assign("lang_xmlweather_nomatch", _MD_XMLWEATHER_NOMATCH);
}

$xoopsTpl->assign("lang_xmlweather_poweredby", _MD_XMLWEATHER_POWEREDBY);
$xoopsTpl->assign("lang_xmlweather_copyright", _MD_XMLWEATHER_COPYRIGHT);

include XOOPS_ROOT_PATH.'/footer.php';

?>