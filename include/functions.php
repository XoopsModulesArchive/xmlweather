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

if( ! function_exists( 'XW_ReadContent' ) ) {

function XW_ReadContent($url) {

/*
// case1: snoopy for allow_url_open = Off
$snoopyconfig['timeout'] = XMLWEATHER_SNOOPYTIMEOUT;
$snoopyconfig['proxy_host'] = XMLWEATHER_SNOOPYPHOST;
$snoopyconfig['proxy_port'] = XMLWEATHER_SNOOPYPPORT;
$snoopyconfig['proxy_user'] = XMLWEATHER_SNOOPYPUSER;
$snoopyconfig['proxy_pass'] = XMLWEATHER_SNOOPYPPASS;


require_once(XOOPS_ROOT_PATH."/class/snoopy.php");
$snoopy = new Snoopy;
$snoopy->read_timeout = $snoopyconfig['timeout'];
if( trim( $snoopyconfig['proxy_host'] ) != '' ) {
	$snoopy->proxy_host = $snoopyconfig['proxy_host'] ;
	$snoopy->proxy_port = $snoopyconfig['proxy_port'] > 0 ? intval( $snoopyconfig['proxy_port'] ) : 8080 ;
	$snoopy->user = $snoopyconfig['proxy_user'] ;
	$snoopy->pass = $snoopyconfig['proxy_pass'] ;
	}
if( ! $snoopy->fetch($url) || !$snoopy->results ) {
	return false;
}
else {
	$xmldata = '';
	$xmldata .= $snoopy->results;
	return $xmldata;

}
//
*/

/*
// case2: not snoopy and for allow_url_open = Off
$timeout = 20;
$domain_url = str_replace("http://", "", $url);
$domainarry = explode("/", $domain_url);
$domain = $domainarry[0];
$encodedurl = encodecategory($url);
$finalfile = str_replace("http://" . $domain, "", $encodedurl);

$fp = @fsockopen($domain, 80, $errno, $errstr, $timeout);
if(! $fp) {
	return false;
}
else {
	fwrite($fp, "GET $finalfile HTTP/1.0\r\n");
	fwrite($fp, "Host: $domain\n");
	fwrite($fp, "User-Agent: Mozilla/2.0 (compatible; DWodp live 1.2.4)\r\n\r\n");
	while(! feof($fp)) {
		$result .= fread($fp, 512);
	}
	fclose($fp);
	$pieces = explode("\r\n\r\n", $result);
	$headers = $pieces[0];
	$response = $pieces[1];
	$xmldata = $response;
	return $xmldata;
}
//
*/

//case3: for allow_url_open = On
if ($xmldata = @file_get_contents($url)) { 
return $xmldata; 
}
else {
return false;
}
//


}

}

/////////////////////
if( ! function_exists( 'encodecategory' ) ) {

function encodecategory($string) {
	$encodedurl = urlencode($string);
	$CodeSearch = array (
		"%2F",
		"%3A",
		"%3F",
		"%3D",
		"%2C",
		"%26"
	);

	$CodeReplace = array (
		"/",
		":",
		"?",
		"=",
		",",
		"&"
	);

	return $encodedurl = str_replace ($CodeSearch, $CodeReplace, $encodedurl);
}

}

?>