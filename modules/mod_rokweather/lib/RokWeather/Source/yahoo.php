<?php
/**
 * @version   $Id: yahoo.php 6565 2013-01-16 17:20:38Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('_JEXEC') or die('Restricted access');

class RokWeatherSourceYahoo extends RokWeather_SourceBase
{

    function getWeather(&$params)
    {
        $style = ($params->get("style", "blue")) ? $params->get("style", "blue"): 'blue';

        $weather = new stdClass();
        $weather->icon = str_replace('modules/mod_rokweather/modules/mod_rokweather/', 'modules/mod_rokweather/', JURI::base() . "modules/mod_rokweather/blue/images/weather/unknown.png");
        $weather->current_temp_f = "?";
        $weather->current_temp_c = "?";

        $location = RokWeather::getLocation($params->get("yahoo_default_location", "Golden, CO"), $params->get("enable_location_cookie", "1"));//"615702"
        $weather->display_location = ($params->get("yahoo_location_override", "")) ? $params->get("yahoo_location_override", "") : $location;
        $woeid = $this->getWoeid($location);
        if(!$woeid){
            $weather->error = "Error! Unable to Find Specified Location!";
            return $weather;
        }

        // build the url
        $weather_json = $this->getForecast($woeid);
        if(!isset($weather_json)){
            $weather->error = "Error! Unable to Retrieve Weather Forecast!";
            return $weather;
        }

        $weather->date = '';
        $weather->date_time = '';
        $weather->units = $weather_json->units->temperature;
        $weather->current_condition = $weather_json->item->condition->text;
        $weather->current_temp_f = ($weather_json->units->temperature=='F') ? $weather_json->item->condition->temp : $this->getTemp($weather_json->item->condition->temp, 'F');
        $weather->current_temp_c = ($weather_json->units->temperature=='C') ? $weather_json->item->condition->temp : $this->getTemp($weather_json->item->condition->temp, 'C');
        $weather->current_humidity = 'Humidity: ' . $weather_json->atmosphere->humidity . '%';
        $weather->icon = $this->fix_icon($weather_json->item->condition->code, $style);
        $weather->current_wind = $weather_json->wind->speed.' '.$weather_json->units->speed;
        $weather->forecast_show = $params->get('yahoo_forecast_show', 2);

        $weather->forecast = array();
        $i = 0;
        if(count($weather_json->item->forecast)){
            foreach ($weather_json->item->forecast as $forecast){
                $weather_forecast['day_of_week'] = $forecast->day;
                $weather_forecast['low'] = $forecast->low;
                $weather_forecast['high'] = $forecast->high;
                $weather_forecast['icon'] = $this->fix_icon($forecast->code, $style);
                $weather_forecast['condition'] = $forecast->text;
                $weather->forecast[$i] = $weather_forecast;
                $i++;
            }
        } else {
            $weather->forecast = 'No Weather Forecast Available';
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

    private function fix_forecast($icon)
    {
        $icon = strtolower($icon);
        $images_r = array(
            "0" => "tornado",
              "1" => "tropical storm",
              "2" => "hurricane",
              "3" => "severe thunderstorms",
              "4" => "thunderstorms",
              "5" => "mixed rain and snow",
              "6" => "mixed rain and sleet",
              "7" => "mixed snow and sleet",
              "8" => "freezing drizzle",
              "9" => "drizzle",
              "10" => "freezing rain",
              "11" => "showers",
              "12" => "showers",
              "13" => "snow flurries",
              "14" => "light snow showers",
              "15" => "blowing snow",
              "16" => "snow",
              "17" => "hail",
              "18" => "sleet",
              "19" => "dust",
              "20" => "foggy",
              "21" => "haze",
              "22" => "smoky",
              "23" => "blustery",
              "24" => "windy",
              "25" => "cold",
              "26" => "cloudy",
              "27" => "mostly cloudy (night)",
              "28" => "mostly cloudy (day)",
              "29" => "partly cloudy (night)",
              "30" => "partly cloudy (day)",
              "31" => "clear (night)",
              "32" => "sunny",
              "33" => "fair (night)",
              "34" => "fair (day)",
              "35" => "mixed rain and hail",
              "36" => "hot",
              "37" => "isolated thunderstorms",
              "38" => "scattered thunderstorms",
              "39" => "scattered thunderstorms",
              "40" => "scattered showers",
              "41" => "heavy snow",
              "42" => "scattered snow showers",
              "43" => "heavy snow",
              "44" => "partly cloudy",
              "45" => "thundershowers",
              "46" => "snow showers",
              "47" => "isolated thundershowers",
              "3200" => "not available",
        );

        //replace if a match is found
        foreach ($images_r as $key => $value) {
            if ((string)$icon == (string)$value){
                $icon = (int)$key;
            }
        }

        //didn't find specific weather condition so we try to match it
        if(!is_int($icon)){
            if(strpos($icon, 'shower')!==false){
                $icon = 12;
            }
            elseif(strpos($icon, 'thunderstorm')!==false){
                $icon = 45;
            }
            elseif(strpos($icon, 'cloud')!==false){
                $icon = 26;
            }
            elseif(strpos($icon, 'snow')!==false){
                $icon = 16;
            }
            elseif(strpos($icon, 'clear')!==false){
                $icon = 32;
            }
            elseif(strpos($icon, 'sunny')!==false){
                $icon = 32;
            }
            else {
                $icon = 3200;
            }
        }

        return $icon;
    }

    private function fix_icon($image, $style = 'grey')
    {
        $path_parts = pathinfo($image);
        $filename = $path_parts['filename'];

        if(!is_numeric($filename) && !is_null($filename)){
            $filename = $this->fix_forecast($filename);
            $fallback = 'http://l.yimg.com/a/i/us/we/52/'.$filename.'.gif';

        } else {
            $fallback = 'http://l.yimg.com/a/i/us/we/52/'.$image.'.gif';
        }

        $images_r = array(
            "0" =>  "storm",
            "1" =>  "storm",
            "2" =>  "storm",
            "3" =>  "thunderstorm",
            "4" =>  "thunderstorm",
            "5" =>  "rain_snow",
            "6" =>  "sleet",
            "7" =>  "snow",
            "8" =>  "rain",
            "9" =>  "rain",
            "10" => "rain",
            "11" => "rain",
            "12" => "rain",
            "13" => "snow",
            "14" => "snow",
            "15" => "snow",
            "16" => "snow",
            "17" => "hail",
            "18" => "sleet",
            "19" => "dust",
            "20"=>  "fog",
            "21" => "haze",
            "22" => "smoky",
            "23" => "windy",
            "24" => "windy",
            "25" => "cold",
            "26" => "cloudy",
            "27" => "mostlycloudy",
            "28" => "mostlycloudy",
            "29" => "partlycloudy",
            "30" => "partlycloudy",
            "31" => "sunny",
            "32" => "sunny",
            "33" => "sunny",
            "34" => "sunny",
            "35" => "sleet",
            "36" => "sunny",
            "37" => "thunderstorm",
            "38" => "scatteredstorms",
            "39" => "scatteredstorms",
            "40" => "scatteredshowers",
            "41" => "snow",
            "42" => "scatteredsnow",
            "43" => "snow",
            "44" => "partlycloudy",
            "45" => "thunderstorm",
            "46" => "snow",
            "47" => "thunderstorm",
            "3200" => "unknown",
        );

        //replace missing icon
        if (!strlen($filename)) $filename = "sunny.png";

        //replace if a match is found
        foreach ($images_r as $key => $value) {
            if ((int)$filename == (int)$key ){
                $filename = $value;
            }
        }

        $icon = JRoute::_(JURI::root() . 'modules/mod_rokweather/' . $style  . '/images/weather/' . $filename . '.png');
        //juri fix for ajax
        $icon = str_replace('modules/mod_rokweather/modules/mod_rokweather/', 'modules/mod_rokweather/', $icon);
        $icon_path = JPATH_SITE . '/modules/mod_rokweather/' . $style  . '/images/weather/' . $filename . '.png';
        if (!file_exists($icon_path)) {
            $icon = $fallback;
        }

        return $icon;
    }

    private function getWoeid($location)
    {
        //lookup WOEID
        $yql_url = 'http://query.yahooapis.com/v1/public/yql?q=';
        $yql_url .= urlencode('select woeid from geo.places where text="'.$location.'" limit 1');
        $yql_url .= '&format=json';
        $woeid_string = @file_get_contents($yql_url);
        $woeid_json = @json_decode($woeid_string);

        return @$woeid_json->query->results->place->woeid;
    }

    private function getForecast($woeid){

        //lookup forecast
        $yql_url = 'http://query.yahooapis.com/v1/public/yql?q=';
        $yql_url .= urlencode('select * from weather.forecast where woeid="'.$woeid.'" and u="f"');
        $yql_url .= '&format=json';
        $forecast_string = @file_get_contents($yql_url);
        $forecast_json = @json_decode($forecast_string);

        return @$forecast_json->query->results->channel;

    }

    function available()
    {
        return true;
    }
}
