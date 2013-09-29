<?php
/**
 * @version   $Id: Source.php 6565 2013-01-16 17:20:38Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('ROKWEATHER') or die('Restricted access');

/**
 *
 */
interface RokWeather_Source {
    /**
     * @abstract
     * @param $params
     */
    function getWeather(&$params);

    /**
     * Checks to see if the source is available to be used
     * @abstract
     * @return bool
     */
    function available();
}
