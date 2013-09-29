<?php
/**
 * GCalendar is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * GCalendar is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GCalendar.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package		GCalendar
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2007 - 2013 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class Com_GCalendarInstallerScript{

	public function install($parent) {
	}

	function update($parent) {
		$version = $this->getParam('version');
		if (empty($version)) {
			return;
		}

		if (version_compare($version, '2.6.0') == -1) {
			$this->run("ALTER TABLE `#__gcalendar` ADD `access` TINYINT UNSIGNED NOT NULL DEFAULT '1';");
			$this->run("ALTER TABLE `#__gcalendar` ADD `access_content` TINYINT UNSIGNED NOT NULL DEFAULT '1';");
			$this->run("ALTER TABLE `#__gcalendar` ADD `username` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `magic_cookie`;");
			$this->run("ALTER TABLE `#__gcalendar` ADD `password` text NULL DEFAULT NULL AFTER `username`;");
		}
		if (version_compare($version, '2.6.2') == -1) {
			$this->run("update #__extensions set enabled=1 where type = 'plugin' and element = 'gcalendar'");
		}
		if (version_compare($version, '2.7.0') == -1) {
			foreach (JFolder::files(JPATH_ADMINISTRATOR.'/language', '.*gcalendar.*', true, true) as $file) {
				JFile::delete($file);
			}
			foreach (JFolder::files(JPATH_SITE.DS.'language', '.*gcalendar.*', true, true) as $file) {
				JFile::delete($file);
			}
		}
		if (version_compare($version, '3.0.0') == -1) {
			$this->run("update #__extensions set enabled=0 where type = 'plugin' and element = 'gcalendar'");
		}
	}

	public function uninstall($parent) {
	}

	public function preflight($type, $parent) {
	}

	public function postflight($type, $parent) {
	}

	private function run($query) {
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
	}

	private function getParam( $name ) {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_gcalendar"');
		$manifest = json_decode( $db->loadResult(), true );
		return $manifest[ $name ];
	}
}