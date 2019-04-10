<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

use FOF30\Container\Container;
use Akeeba\Subscriptions\Admin\Model\Levels;
use Akeeba\Subscriptions\Admin\Helper\Price;
use Joomla\String\StringHelper;

class plgContentAsprice extends JPlugin
{
	/**
	 * Should this plugin be allowed to run? True if FOF can be loaded and the Akeeba Subscriptions component is enabled
	 *
	 * @var  bool
	 */
	private $enabled = true;

	/**
	 * List of currently enabled subscription levels
	 *
	 * @var   Levels[]
	 */
	protected static $levels = null;

	/**
	 * Maps subscription level titles to slugs
	 *
	 * @var   array
	 */
	protected static $slugs = null;

	/**
	 * Maps subscription level titles to UPPERCASE slugs
	 *
	 * @var   array
	 */
	protected static $upperSlugs = null;

	/**
	 * Maps subscription level IDs to pricing information
	 *
	 * @var   array
	 */
	protected static $prices = null;

	public function __construct(&$subject, $config = array())
	{
		if (!defined('FOF30_INCLUDED') && !@include_once(JPATH_LIBRARIES . '/fof30/include.php'))
		{
			$this->enabled = false;
		}

		// Do not run if Akeeba Subscriptions is not enabled
		if (!JComponentHelper::isEnabled('com_akeebasubs'))
		{
			$this->enabled = false;
		}

		parent::__construct($subject, $config);
	}

	/**
	 * Handles the content preparation event fired by Joomla!
	 *
	 * @param   mixed     $context     Unused in this plugin.
	 * @param   stdClass  $article     An object containing the article being processed.
	 * @param   mixed     $params      Unused in this plugin.
	 * @param   int       $limitstart  Unused in this plugin.
	 *
	 * @return  bool
	 */
	public function onContentPrepare($context, &$article, &$params, $limitstart = 0)
	{
		if (!$this->enabled)
		{
			return true;
		}

		$accceptableActions = [
			'asprice',
		];

		$mustProcess = false;

		foreach ($accceptableActions as $action)
		{
			// Check whether the plugin should process or not
			if (StringHelper::strpos($article->text, $action) !== false)
			{
				$mustProcess = true;

				break;
			}
		}

		if (!$mustProcess)
		{
			return true;
		}

		// {asprice MYLEVEL} ==> 10.00€
		$regex = "#{asprice (.*?)}#s";
		$article->text = preg_replace_callback($regex, array('self', 'processPrice'), $article->text);

		return true;
	}

	/**
	 * Gets the level ID out of a level title. If an ID was passed, it simply returns the ID.
	 * If a non-existent subscription level is passed, it returns -1.
	 *
	 * @param   string|int $title The subscription level title or ID
	 *
	 * @return  int  The subscription level ID
	 */
	private static function getId($title, $slug = false)
	{
		// Don't process invalid titles
		if (empty($title))
		{
			return -1;
		}

		// Fetch a list of subscription levels if we haven't done so already
		if (is_null(self::$levels))
		{
			/** @var Levels $levelsModel */
			$levelsModel      = Container::getInstance('com_akeebasubs', [], 'site')->factory->model('Levels')
			                                                                                 ->tmpInstance();
			self::$levels     = array();
			self::$slugs      = array();
			self::$upperSlugs = array();
			$list             = $levelsModel->get(true);

			if (count($list))
			{
				/** @var Levels $level */
				foreach ($list as $level)
				{
					$thisTitle                                  = strtoupper($level->title);
					self::$levels[$thisTitle]                   = $level->akeebasubs_level_id;
					self::$slugs[$thisTitle]                    = $level->slug;
					self::$upperSlugs[strtoupper($level->slug)] = $level->slug;
				}
			}
		}

		$title = strtoupper($title);

		if (array_key_exists($title, self::$levels))
		{
			// Mapping found
			return $slug ? self::$slugs[$title] : self::$levels[$title];
		}
		elseif (array_key_exists($title, self::$upperSlugs))
		{
			$mySlug = self::$upperSlugs[$title];

			if ($slug)
			{
				return $mySlug;
			}
			else
			{
				foreach (self::$slugs as $t => $s)
				{
					if ($s = $mySlug)
					{
						return self::$levels[$t];
					}
				}

				return -1;
			}
		}
		elseif ((int) $title == $title)
		{
			$id    = (int) $title;
			$title = '';

			// Find the title from the ID
			foreach (self::$levels as $t => $lid)
			{
				if ($lid == $id)
				{
					$title = $t;

					break;
				}
			}

			if (empty($title))
			{
				return $slug ? '' : -1;
			}
			else
			{
				return $slug ? self::$slugs[$title] : self::$levels[$title];
			}
		}
		else
		{
			// No match!
			return $slug ? '' : -1;
		}
	}

	/**
	 * Callback to preg_replace_callback in the onContentPrepare event handler of this plugin.
	 *
	 * @param   array  $match  A match to the {asprice} plugin tag
	 *
	 * @return  string  The processed result
	 */
	private static function processPrice($match)
	{
		$ret = '';

		$levelId = self::getId($match[1], false);

		if ($levelId <= 0)
		{
			return $ret;
		}

		$ret = self::getPrice($levelId);

		return $ret;
	}

	private static function getPrice($levelId)
	{
		static $prices = [];

		if (!array_key_exists($levelId, $prices))
		{
			$container = Container::getInstance('com_akeebasubs', [], 'site');
			/** @var \Akeeba\Subscriptions\Site\Model\Levels $level */
			$level = $container->factory->model('Levels');
			$level->load($levelId);

			/** @var Akeeba\Subscriptions\Site\View\Levels\Html $view */
			$view = $container->factory->view('Levels', 'html');
			$view->applyViewConfiguration();
			$priceInfo = $view->getLevelPriceInformation($level);

			$price = '';

			if ($view->renderAsFree && ($priceInfo->levelPrice < 0.01))
			{
				$price = JText::_('COM_AKEEBASUBS_LEVEL_LBL_FREE');
			}
			else
			{
				if ($container->params->get('currencypos','before') == 'before')
				{
					$price .= $container->params->get('currencysymbol','€');
				}

				$price .= $priceInfo->formattedPrice;

				if ($container->params->get('currencypos','before') == 'after')
				{
					$price .= $container->params->get('currencysymbol','€');
				}
			}

			$prices[$levelId] = $price;
		}

		return $prices[$levelId];
	}
}
