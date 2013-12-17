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

class ModGCalendarNextHelper  {

	public static function getCalendarItems($params	) {
		$calendarids = $params->get('calendarids');
		$results = GCalendarDBUtil::getCalendars($calendarids);
		if(empty($results)){
			JError::raiseWarning( 500, 'The selected calendar(s) were not found in the database.');
			return null;
		}

		$orderBy = $params->get( 'order', 1 ) == 1 ? GCalendarZendHelper::ORDER_BY_START_TIME : GCalendarZendHelper::ORDER_BY_LAST_MODIFIED;
		$maxEvents = $params->get('max_events', 10);
		$filter = $params->get('find', '');
		$titleFilter = $params->get('title_filter', '.*');

		$values = array();
		foreach ($results as $result) {
			$events = GCalendarZendHelper::getEvents($result, null, null, $maxEvents, $filter, $orderBy);
			if(!empty($events)){
				foreach ($events as $event) {
					if(!($event instanceof GCalendar_Entry)){
						continue;
					}
					$event->setParam('moduleFilter', $titleFilter);
					$values[] = $event;
				}
			}
		}

		usort($values, array("GCalendar_Entry", "compare"));

		$events = array_filter($values, array('ModGCalendarNextHelper', "filter"));

		$offset = $params->get('offset', 0);
		$numevents = $params->get('count', $maxEvents);

		return array_shift($values);
	}

	private static function filter($event) {
		if (!preg_match('/'.$event->getParam('moduleFilter').'/', $event->getTitle())) {
			return false;
		}
		if ($event->getEndDate()->format('U') > JFactory::getDate()->format('U')) {
			return true;
		}

		return false;
	}
}