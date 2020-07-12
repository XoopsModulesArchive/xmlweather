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

$xmlweatherdir = basename( dirname( dirname( dirname( __FILE__ ) ) ) );
include_once XOOPS_ROOT_PATH."/modules/$xmlweatherdir/config.php";

define('_MD_XMLWEATHER_REPORT','Weather report for ');
define('_MD_XMLWEATHER_LASTUPDATED','Last updated ');
define('_MD_XMLWEATHER_CURRENTLY','Currently');
define('_MD_XMLWEATHER_FEELLIKE','Feels Like');
define('_MD_XMLWEATHER_CURRENTCONDITION','Current conditions');
define('_MD_XMLWEATHER_SUNRISE','Sunrise');
define('_MD_XMLWEATHER_SUNSET','Sunset');
define('_MD_XMLWEATHER_DATE','Date');
define('_MD_XMLWEATHER_HIGH','High');
define('_MD_XMLWEATHER_LOW','Low');
define('_MD_XMLWEATHER_DAY','Day');
define('_MD_XMLWEATHER_NIGHT','Night');
define('_MD_XMLWEATHER_WIND','Wind');
define('_MD_XMLWEATHER_HUMIDITY','Humidity');
define('_MD_XMLWEATHER_PRECIPITATION','Precip');
define('_MD_XMLWEATHER_SELECTCITY','Select a clty');
define('_MD_XMLWEATHER_ENTERDATA','Enter a city or zip code');
define('_MD_XMLWEATHER_GETWEATHER','Get Weather');
define('_MD_XMLWEATHER_SEARCH','Search');
define('_MD_XMLWEATHER_NOMATCH','No city found. Please enter another city or zip code.');
define("_MD_XMLWEATHER_COPYRIGHT",'<a target="_blank" href="http://www.wanisys.net">xmlweather 1.5 </a> Based on <a target="_blank" href="http://notonebit.com/">weather_xml</a>');
define('_MD_XMLWEATHER_POWEREDBY', 'Weather data provided by <a href="http://www.weather.com/?prod=xoap&par='.XMLWEATHER_PID.'" target="_blank">weather.com&nbsp;<img src="images/logos/TWClogo_31px.png" alt="TWClogo" /></a>');
//ver1.5
define('_MD_XMLWEATHER_VISIBILITY','Visibility');
define('_MD_XMLWEATHER_DEWPOINT','Dew Point');
define('_MD_XMLWEATHER_UVINDEX','UV Index');
define('_MD_XMLWEATHER_BAROMETER','Barometer');
define('_MD_XMLWEATHER_MOON','Moon');
define("_MD_XMLWEATHER_TEMPDOWN","<center>Could not read from remote server, Please check back later.<br><br>Sorry for incovenience.</center>");

?>