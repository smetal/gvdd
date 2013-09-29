<?php
/**
 * @version   $Id: RokWeatherPlugin.php 9610 2013-04-24 02:35:20Z steph $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 */
class RokWeatherPlugin extends JPlugin
{

    public static $ROKWEATHER_ROOT;
    public static $SOURCE_DIR;

    public function __construct($parent = null)
    {
        if (!defined('ROKWEATHER')) define('ROKWEATHER', 'ROKWEATHER');

        // Set base dirs
        self::$ROKWEATHER_ROOT = JPATH_ROOT . '/modules/mod_rokweather';
        self::$SOURCE_DIR = self::$ROKWEATHER_ROOT . '/lib/RokWeather/Source';

        //load up the RTCommon
        require_once(self::$ROKWEATHER_ROOT . '/lib/include.php');
        require_once(self::$ROKWEATHER_ROOT . '/lib/RokSubfieldForm.php');

        parent::__construct($parent);
    }

    public function onContentPrepareForm($form, $data)
    {
        $app = JFactory::getApplication();
        if (!$app->isAdmin()) return;

        $option = JFactory::getApplication()->input->get('option');
        $layout = JFactory::getApplication()->input->get('layout');
        $task = JFactory::getApplication()->input->get('task');
        $id = JFactory::getApplication()->input->getInt('id', 0);

        $module = $this->getModuleType($data);


        if (in_array($option, array('com_modules', 'com_advancedmodules')) && $layout == 'edit' && $module == 'mod_rokweather')
        {
            JForm::addFieldPath(JPATH_ROOT . '/modules/mod_rokweather/fields');

            //Find Sources
            $sources = RokWeather_SourceLoader::getAvailableSources(self::$SOURCE_DIR);

            foreach ($sources as $source_name => $source)
            {
                if (file_exists($source->paramspath) && is_readable($source->paramspath))
                {
                    $form->loadFile($source->paramspath, false);
                    JForm::addFieldPath( dirname($source->paramspath) . "/" . $source->name );
                    //$this->element_dirs[] = dirname($source->paramspath) . "/" . $source->name;
                    $language =JFactory::getLanguage();
                    $language->load('com_'.$source->name, JPATH_ADMINISTRATOR);
                    $language->load($source->name, dirname($source->paramspath), $language->getTag(), true);
                }
            }

            $subfieldform = RokSubfieldForm::getInstanceFromForm($form);

            if (!empty($data) && isset($data->params)) $subfieldform->setOriginalParams($data->params);

            if ($task == 'save' || $task == 'apply')
            {
                $subfieldform->makeSubfieldsVisable();
            }
        }
    }

    protected function getModuleType(&$data)
    {
        if (is_array($data) && isset($data['module']))
        {
            return $data['module'];
        }
        elseif (is_array($data) && empty($data))
        {
            $form = JRequest::getVar('jform');
            if (is_array($form) && array_key_exists('module',$form))
            {
                return $form['module'];
            }
        }
        if (is_object($data) && method_exists( $data , 'get'))
        {
            return $data->get('module');
        }
        return '';
    }
}

