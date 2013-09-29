<?php 
/**
 * @version   $Id: default.php 11809 2013-06-27 16:56:14Z james $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$default_unit = $params->get('default_degree', 0);
$units_switch = $params->get('units_switch', 1);
$user_interaction = $params->get('user_interaction', 1);
//$location = (isset($weather->display_location)) ? $weather->display_location : RokWeather::getLocation('');

$interaction_cls = "";
$degf_style = "";
$degc_style = "";
$input_type = "text";
if ($user_interaction == 0) {
	$input_type = "hidden";
	$interaction_cls = "class='rokweather-nointeraction'";
	if ($default_unit == 1) {
		$degc_style = "style='display:block;'";
		$degf_style = "style='display:none;'";
	} else {
		$degf_style = "style='display:block;'";
		$degc_style = "style='display:none;'";
	}
}
?>
<div id="rokweather" <?php echo $interaction_cls; ?>>
	
    <div class="leftbit">
        <div class="icon">
            <?php if ($params->get('enable_icon')==1) : ?>
            <img src="<?php echo (isset($weather->icon)) ? $weather->icon : ''; ?>" alt="" />
            <?php endif; ?>
            <div class="degf" <?php echo $degf_style; ?>><?php echo (isset($weather->current_temp_f)) ? $weather->current_temp_f : '';?>&deg;</div>
            <div class="degc" <?php echo $degc_style; ?>><?php echo (isset($weather->current_temp_c)) ? $weather->current_temp_c : '';?>&deg;</div>
        </div>
			
		<?php if ($units_switch == 1) : ?>
        	<div class="degrees"><span class="active">&deg;F</span> | <span>&deg;C</span></div>
		<?php endif; ?>
		<?php if ($units_switch == 0 && $default_unit == 1) : ?>
        	<div class="degrees"><span style="display:none;">&deg;F</span><span class="active">&deg;C</span></div>
		<?php endif; ?>
		<?php if ($units_switch == 0 && $default_unit == 0) : ?>
        	<div class="degrees"><span class="active">&deg;F</span><span style="display:none;">&deg;C</span></div>
		<?php endif; ?>
    </div>
    
    
    <div class="content">
		<?php if ($user_interaction == 0) : ?>
		<h5><?php echo $weather->display_location; ?></h5>
		<?php endif; ?>
        <div class="location">
        <form action="<?php echo str_replace("&", "&amp;", $ajaxurl); ?>" method="post">
        <input type="<?php echo $input_type; ?>" id="weather_location" name="weather_location" value="<?php echo $weather->display_location; ?>" />
        </form>
        </div>
		<div class="rokweather-wrapper">
	        <?php if (isset($weather->error)) :?>
	        <div class="row error"><?php echo $weather->error; ?></div>    
	        <?php else: ?>
	        <div class="row"><?php echo $weather->current_condition; ?></div>
	        <?php if ($params->get('enable_humidity')==1) : ?>
	        <div class="row"><?php echo $weather->current_humidity; ?></div>
	        <?php endif; ?>
	        <?php if ($params->get('enable_wind')==1 && $weather->current_wind) : ?>
	        <div class="row"><?php echo RokWeather::getWindspeed($params->get('wind_speed', 0),$weather->current_wind); ?></div>
	        <?php endif; ?>
	        
			<?php if ((is_array($weather->forecast)) && ($params->get('enable_forecast')==1)): ?>
	        <div class="forecast">

			<?php
				$weather->forecast = array_slice($weather->forecast, 0, $weather->forecast_show);
			?>

	            <?php foreach ($weather->forecast as $day): ?>
	            <div class="day">
	                <span><?php echo $day['day_of_week']; ?></span><br />
	                   <img src="<?php echo $day['icon']; ?>" alt="<?php echo $day['condition']; ?>" title="<?php echo $day['condition']; ?>" /><br />
	                <div class="degf" <?php echo $degf_style; ?>>
	                    <span class="low"><?php echo RokWeather::getFTemp($day['low'],$weather->units); ?></span> |
	                    <span class="high"><?php echo RokWeather::getFTemp($day['high'],$weather->units); ?></span>
	                </div>
	                <div class="degc" <?php echo $degc_style; ?>>
	                    <span class="low"><?php echo RokWeather::getCTemp($day['low'],$weather->units); ?></span> |
	                    <span class="high"><?php echo RokWeather::getCTemp($day['high'],$weather->units); ?></span>
	                </div>
	            </div>
	            <?php endforeach; ?>
	        </div>
            <?php elseif(is_string($weather->forecast)): ?>
            <div class="day">
                <?php echo $weather->forecast; ?>
            </div>
	        <?php endif; ?>	
        <?php endif; ?>
		</div>
		<?php if ($params->get('source')=="wunderground") : ?>
		<div class="wunderground-logo">
			<img src="modules/mod_rokweather/images/wundergroundLogo_<?php echo $params->get('wunderground_logo_style'); ?>_horz.png" alt="wunderground.com® logo"/>
		</div>
		<?php endif; ?>
    </div>
</div>