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


if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;

if( ! defined( 'XMLWEATHER_CONFIG_INCLUDED' ) ) {
define( 'XMLWEATHER_CONFIG_INCLUDED' ,1 ) ;
//
$modulename = basename( dirname( __FILE__ ) );

define('XMLWEATHERPATH', XOOPS_ROOT_PATH."/modules/$modulename");
define('XMLWEATHERURL', XOOPS_URL."/modules/$modulename");
define('XMLWEATHERIMAGEPATH', XOOPS_ROOT_PATH."/modules/$modulename/images");
define('XMLWEATHERIMAGEURL', XOOPS_URL."/modules/$modulename/images");

// Sign up for Weather.com's free XML service at http://www.weather.com/services/xmloap.html
//partner_ID
define('XMLWEATHER_PID', "");
//license_key
define('XMLWEATHER_KEY', "");
//ver1.5 because of service change
// 31x31, 61x61, or 93x93 - size of daily weather images
define('XMLWEATHER_IMGSIZE', "61x61");
//default_location, customize it!
define('XMLWEATHER_LOC', "JAXX0085"); //ex:tokyo
//ver1.5 because of service change, up to 5days
define('XMLWEATHER_LENGTH', 5);
// Hours to keep data in db before being considered old
define('XMLWEATHER_INTERVAL', 12);
//snoopyconfig
define('XMLWEATHER_SNOOPYTIMEOUT', 30); //timeout
define('XMLWEATHER_SNOOPYPHOST', ""); //proxy_host
define('XMLWEATHER_SNOOPYPPORT', ""); //proxy_port
define('XMLWEATHER_SNOOPYPUSER', ""); //proxy_user
define('XMLWEATHER_SNOOPYPPASS', ""); //proxy_pass

}

?>