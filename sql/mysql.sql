#DB table for xmlweather-module
#2005/07/07
# --------------------------------------------------------

#
# Table structure for table `weather_xml`
#

CREATE TABLE `weather_xml` (
  `xml_url` varchar(150) NOT NULL default '',
  `xml_data` text NOT NULL,
  `last_updated` datetime NOT NULL default '0000-00-00 00:00:00',
  KEY `xml_url` (`xml_url`)
) TYPE=MyISAM;
