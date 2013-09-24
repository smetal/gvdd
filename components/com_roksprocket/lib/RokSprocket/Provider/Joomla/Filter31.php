<?php
 /**
  * @version   $Id: Filter31.php 11067 2013-06-02 02:02:44Z steph $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */
 
class RokSprocket_Provider_Joomla_Filter31 extends  RokSprocket_Provider_Joomla_Filter {

    protected function setBaseQuery(){

        parent::setBaseQuery();

        $this->query->select('CONCAT_WS(",", t.id) AS tag_ids, CONCAT_WS(",", t.title) AS tags');
        $this->query->join('LEFT', '#__contentitem_tag_map AS ct ON ct.content_item_id = a.id');
        $this->query->join('LEFT', '#__tags AS t ON t.id = ct.tag_id');
    }

    /**
   	 * @param $data
   	 *
   	 * @return void
   	 */
   	protected function tag($data)
   	{
        $wheres = array();
        foreach ($data as $match) {
            $wheres[] = $match . ' IN (CONCAT_WS(",", t.id))';
        }
        $this->filter_where[] = '(' . implode(' OR ', $wheres) . ')';
   	}
}
