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

JLoader::import( 'joomla.application.component.model' );

class GCalendarModelJSONFeed extends JModelLegacy {

	public function getGoogleCalendarFeeds() {
		$startDate = JRequest::getVar('start', null, 'GET');
		$endDate = JRequest::getVar('end', null, 'GET');

		$calendarids = '';
		if (JRequest::getVar('gcids', null) != null) {
			if(is_array(JRequest::getVar('gcids', null)))
				$calendarids = JRequest::getVar('gcids', null);
			else
				$calendarids = explode(',', JRequest::getVar('gcids', null));
		} else {
			$calendarids = JRequest::getVar('gcid', null);
		}
		$results = GCalendarDBUtil::getCalendars($calendarids);
		if(empty($results))
			return null;

		$calendars = array();
		foreach ($results as $result) {
			if(empty($result->calendar_id))
				continue;

			$events = GCalendarZendHelper::getEvents($result, $startDate, $endDate, 1000);
			if ($events == null) {
				continue;
			}
			$calendars[] = $events;
		}

		return $calendars;
	}
}