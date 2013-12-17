<?php
/**
 * @version   $Id: wunderground.php 6565 2013-01-16 17:20:38Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('_JEXEC') or die('Restricted access');

class RokWeatherSourceWunderground extends RokWeather_SourceBase
{

    function getWeather(&$params)
    {
        $weather = new stdClass();
        $weather->icon = str_replace('modules/mod_rokweather/modules/mod_rokweather/', 'modules/mod_rokweather/', JURI::base() . "modules/mod_rokweather/blue/images/weather/unknown.png");
        $weather->current_temp_f = "?";
        $weather->current_temp_c = "?";

        $style = ($params->get("style", "blue")) ? $params->get("style", "blue"): 'blue';
        $loc_string = '';
        $location = RokWeather::getLocation($params->get("wunderground_default_location", "Golden, CO"), $params->get("enable_location_cookie", "1"));//"615702"
        if (is_numeric($location)){
            $loc_string .= '/'.trim($location);
        } else {
            $location_parts = explode(',',$location);
            $location_parts = array_reverse($location_parts);
            foreach($location_parts as $loc){
                $loc_string .='/'.trim($loc);
            }
        }
        $weather->display_location = ($params->get("wunderground_location_override", "")) ? $params->get("wunderground_location_override", "") : $location;

        $wunderground_api_key = $params->get("wunderground_api_key", "");
        if($wunderground_api_key==null||$wunderground_api_key==""){
            $weather->error = "You need to enter an API Key for Wunderground";
            return $weather;
        }

        $weather_api_url = 'http://api.wunderground.com/api/'.$wunderground_api_key.'/geolookup/conditions/q'.$loc_string.'.json';
        $forecast_api_url = 'http://api.wunderground.com/api/'.$wunderground_api_key.'/forecast10day/q'.$loc_string.'.json';

        $weather_string = file_get_contents($weather_api_url);
        $weather_json = json_decode($weather_string);

        $forecast_string = file_get_contents($forecast_api_url);
        $forecast_json = json_decode($forecast_string);

        if((isset($weather_json->response->results)) || (!isset($weather_json->current_observation))){
            $weather->error = "Invalid Location Provided";
            return $weather;
        }

        if(isset($weather_json->error)){
            $weather->error = $weather_json->error->description;
            return $weather;
        }
        $weather->units = 'F';

        $weather->current_condition = $weather_json->current_observation->weather;
        $weather->current_temp_f = round($weather_json->current_observation->temp_f);
        $weather->current_temp_c = round($weather_json->current_observation->temp_c);
        $weather->current_humidity = 'Humidity: ' . $weather_json->current_observation->relative_humidity;
        $weather->icon = $this->fix_icon($weather_json->current_observation->icon, $style);
        $weather->current_wind = $weather_json->current_observation->wind_mph.' mph';
        $weather->forecast_show = $params->get('wunderground_forecast_show', 4);

        $weather->forecast = array();
        $forecast = $forecast_json->forecast->simpleforecast->forecastday;
        foreach ($forecast as $fc){
            $weather_forecast['day_of_week'] = $fc->date->weekday_short;
            $weather_forecast['low'] = round($fc->low->fahrenheit);
            $weather_forecast['high'] = round($fc->high->fahrenheit);
            $weather_forecast['icon'] = $this->fix_icon($fc->icon, $style);
            $weather_forecast['condition'] = $fc->conditions;
            $weather->forecast[] = $weather_forecast;
        }

        return $weather;
    }

    public static function getTemp($temp, $units)
    {
        if ($units == "F") {
            return intval((9 / 5) * intval($temp) + 32);
        } else {
            return intval((5 / 9) * (intval($temp) - 32));
        }
    }

    private function fix_icon($image, $style = 'grey')
    {
        $fallback = 'http://icons.wxug.com/i/c/g/'.$image.'.gif';

        $images_r = array(
            "rainysometimescloudy" => "chance_of_rain",
            "rainy" => "rain",
            "drizzle" => "chance_of_rain",
            "snowy" => "snow",
            "sand" => "dust",
            "thunderstorms" => "thunderstorm",
            "scatteredthunderstorm" => "scatteredthunderstorms",
            "scatteredsnowshowers" => "scatteredshowers",
            "mostly_cloudy" => 'mostlycloudy',
            "mostly_sunny" => 'mostlysunny',
            "partly_cloudy" => 'partlycloudy',
            "clear" => 'sunny',
            "chancetstorms" => 'chance_of_storm',
            'chancerain'=> "chance_of_rain",
        );

        //replace missing icon
        if (!strlen($image)) $icon = "sunny.png";

        foreach ($images_r as $key => $value) {
            if ($image == $key ){
                $image = $value;
            }
        }

        $icon = JRoute::_(JURI::root() . 'modules/mod_rokweather/' . $style  . '/images/weather/' . $image . '.png');
        //juri fix for ajax
        $icon = str_replace('modules/mod_rokweather/modules/mod_rokweather/', 'modules/mod_rokweather/', $icon);
        $icon_path = JPATH_SITE . '/modules/mod_rokweather/' . $style  . '/images/weather/' . $image . '.png';
        if (!file_exists($icon_path)) {
            $icon = $fallback;
        }

        return $icon;
    }

    function available()
    {
        return true;
    }
}
