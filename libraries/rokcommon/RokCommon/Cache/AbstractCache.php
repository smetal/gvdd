<?php
/**
 * @version   $Id$
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

abstract class RokCommon_Cache_AbstractCache implements RokCommon_ICache
{
	/**
	 * Constructor
	 *
	 * @param int $lifeTime
	 */
	public function __construct($lifeTime = self::DEFAULT_LIFETIME)
	{
		$this->lifeTime = $lifeTime;
	}

	/**
	 * @var int
	 */
	protected $lifeTime = self::DEFAULT_LIFETIME;

	/**
	 * Sets the lifetime of the cache
	 *
	 * @abstract
	 *
	 * @param  int $lifeTime Lifetime of the cache
	 *
	 * @return void
	 */
	public function setLifeTime($lifeTime)
	{
		$this->lifeTime = $lifeTime;
	}
}
