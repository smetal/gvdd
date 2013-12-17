<?php
/**
 * @version   $Id: RokWeather.php 8644 2013-03-21 02:24:40Z steph $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('ROKWEATHER') or die('Restricted access');

class RokWeather
{

    public function loadScripts(&$params)
    {
        JHtml::_('behavior.framework', true);
        $defaultDegree = $params->get('default_degree', 0);

        $doc = JFactory::getDocument();
        $doc->addStyleSheet(JURI::Root(true) . '/modules/mod_rokweather/rokweather.css');

        if ($params->get('user_interaction', 1) == 1) {
            $doc->addScript(JURI::Root(true) . '/modules/mod_rokweather/tmpl/js/rokweather' . self::_getJSVersion() . '.js');
            $doc->addScriptDeclaration("window.addEvent('domready', function() {new RokWeather({defaultDegree: {$defaultDegree}});});");
        }
    }

    public function getWeather(JRegistry &$params)
    {
        $weather_post = (string)strtolower(JFactory::getApplication()->input->getString("weather_location", ""));
        $weather_cookie = (string)strtolower(JFactory::getApplication()->input->getString("rokweather_location", ''));

        $conf = JFactory::getConfig();
        //if its a new location we don't want to use the cache
        if ($conf->get('caching') && $params->get('module_cache') && ($weather_cookie == $weather_post)) {
            $user = JFactory::getUser();
            $cache = JFactory::getCache('mod_rokweather');
            $cache->setCaching(true);
            $args = array($params);
            $checksum = md5($params->__toString());
            $weather = $cache->get(array($this, '_getWeather'), $args, 'mod_rokweather-' . $user->get('aid', 0) . '-' . $checksum);
        } else {
            //get new weather if we can
            $weather = $this->_getWeather($params);
            if (!$weather) {
                //bad call we get the cache anyways
                $user = JFactory::getUser();
                $cache = JFactory::getCache('mod_rokweather');
                $cache->setCaching(true);
                $args = array($params);
                $checksum = md5($params->__toString());
                $weather = $cache->get(array($this, '_getWeather'), $args, 'mod_rokweather-' . $user->get('aid', 0) . '-' . $checksum);
            }
        }

        return $weather;
    }

    public function _getWeather($params)
    {
        $weather = null;

        $sources = RokWeather_SourceLoader::getAvailableSources(ROKWEATHER_ROOT . "/lib/RokWeather/Source");
        $selected_source = $params->get('source', '');

        foreach ($sources as $key => $source) {
            if ($key == $selected_source) {
                $weather = $source->source->getWeather($params);
            }
        }

        return $weather;
    }

    public static function addSourcesPath($path)
    {
        try {
            if (!RTCommon_ClassLoader::isLoaderRegistered('RokWeatherSources')) {
                $sourcesLoader = new RokWeather_SourceLoader();
                RTCommon_ClassLoader::registerLoader('RokWeatherSources', $sourcesLoader);
            } else {
                $sourcesLoader = RTCommon_ClassLoader::getLoader('RokWeatherSources');
            }
        } catch (Exception $le) {
            throw $le;
        }

        try {
            $sourcesLoader->addSourcePath($path);
        } catch (RTCommon_Cache_Exception $ce) {
            throw $ce;
        }
    }

    public static function getFTemp($temp, $units)
    {
        if ($units == "SI") {
            return intval((9 / 5) * intval($temp) + 32);
        } else {
            return $temp;
        }
    }

    public static function getCTemp($temp, $units)
    {
        if ($units == "SI") {
            return $temp;
        } else {
            return intval((5 / 9) * (intval($temp) - 32));
        }
    }

    public static function getWindspeed($type, $speed)
    {
        if (!$speed) return;
        switch ($type) {
            case 1:
                $number = preg_replace("/[^0-9]/", '', $speed);
                $speed = str_replace($number, floor($number * 1.609344), $speed);
                return str_replace('mph', 'kph', $speed);
                break;
            case 2:
                $number = preg_replace("/[^0-9]/", '', $speed);
                $speed = str_replace($number, floor($number * 0.868976242), $speed);
                return str_replace('mph', 'kts', $speed);
                break;
            default:
                return $speed;
                break;
        }
    }


    public static function getLocation($default_location, $enable_cookie = "0")
    {
        $weather_location = $default_location;

        //cookies not enabled
        if(!(int)$enable_cookie) {
           setcookie("rokweather_user_location", false, 0);
           unset($_COOKIE["rokweather_user_location"]);

            //post location overrides default location
            if (isset($_POST['weather_location'])) {
                $weather_location = JFactory::getApplication()->input->getString("weather_location", '');
            }

        //cookies are enabled
        } else {

            //post location overrides default location
            if (isset($_POST['weather_location'])) {
                $weather_location = JFactory::getApplication()->input->getString("weather_location", '');
            }
            //no post location, cookie overrides default location
            elseif (isset($_COOKIE["rokweather_user_location"])) {
                $weather_location = JFactory::getApplication()->input->getString("rokweather_user_location", '');
            }
            //set cookie
            setcookie("rokweather_user_location", $weather_location, time() + 31536000, '/', false);

        }

        return $weather_location;
    }


    public static function _getJSVersion()
    {
        return "";
    }

    public static function overrideImage($icon, $path=null)
    {
        if(RokWeather::isExternal($icon)) return $icon;

        $fallback = $icon;
        $icon = JRoute::_(JURI::root() . $path . basename($icon));
        //juri fix for ajax
        $icon = str_replace('modules/mod_rokweather/', '', $icon);

        $icon_path = JPATH_SITE . '/'. $path . basename($icon);
        //juri fix for ajax
        $icon_path = str_replace('modules/mod_rokweather/', '', $icon_path);

        if (!file_exists($icon_path)) {
            $icon = $fallback;
        }

        return $icon;
    }

    protected static function isExternal($url)
    {
        $root_url = rtrim(JURI::Root(), "/");
        $url_uri = parse_url($url);

        //if the url does not have a scheme must be internal
        if (isset($url_uri['scheme']) && (strtolower($url_uri['scheme']) == 'http' || strtolower($url_uri['scheme'] == 'https'))) {
            $site_uri = parse_url($root_url);
            if (isset($url_uri['host']) && strtolower($url_uri['host']) == strtolower($site_uri['host'])) return false;
        }
        // cover external urls like //foo.com/foo.js
        if (!isset($url_uri['host']) && !isset($url_uri['scheme']) && isset($url_uri['path']) && substr($url_uri['path'], 0, 2) != '//') return false;
        //the url has a host and it isn't internal
        return true;
    }
}
