<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use FOF30\Container\Container;
use Joomla\CMS\Component\Router\RouterBase;
use Joomla\CMS\Factory;
use Joomla\CMS\Menu\MenuItem;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

defined('_JEXEC') or die();

include_once JPATH_LIBRARIES . '/fof30/include.php';

/**
 * Akeeba Subscriptions router.
 *
 * This used to be separate functions for building and parsing routes. It has been converted to a router class since it
 * is necessary for Joomla! 4. Router classes are supported since Joomla! 3.3, so no lost compatibility there.
 *
 * @since        7.0.1
 * @noinspection PhpIllegalPsrClassPathInspection
 */
class AkeebasubsRouter extends RouterBase
{
	private static $preprocessCache = [];
	/**
	 * The component container
	 *
	 * @var  Container|null
	 */
	private $container;

	/**
	 * Preprocess a URL query before building a SEF route.
	 *
	 * This method is used to add a missing Itemid, set a language parameter etc.
	 *
	 * This method is executed on each URL, regardless of the SEF mode status.
	 *
	 * @param   array  $query  An associative array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   7.1.1
	 */
	public function preprocess($query)
	{
		// Have I already processed this query before?
		ksort($query);

		$signature = md5(serialize($query));

		if (isset(self::$preprocessCache[$signature]))
		{
			return self::$preprocessCache[$signature];
		}

		// Do I have an Itemid in the query?
		$itemId = $query['Itemid'] ?? null;

		// If there's no Itemid we will try to get the active menu item's ID
		if (empty($itemId))
		{
			$activeMenuItem = $this->menu->getActive();
			$itemId         = (is_object($activeMenuItem) && property_exists($activeMenuItem, 'id')) ?
				$activeMenuItem->id : null;
		}

		// Do I have a menu item ID at all?
		$menuItem = null;

		// If I have a menu item ID let's load the presumptive menu item.
		if (!empty($itemId))
		{
			$menuItem = $this->menu->getItem($itemId);
		}

		// Check that the component matches
		if (!empty($menuItem))
		{
			if ($menuItem->component != $this->getContainer()->componentName)
			{
				$menuItem = null;
			}
		}

		// Check that the view matches our expectations
		if (!empty($menuItem))
		{
			$uri  = Uri::getInstance($menuItem->link);
			$view = $uri->getVar('view', null);

			if (!empty($view) && !in_array(strtolower($view), ['level', 'levels', 'new', 'subscribe', 'subscriptions', 'userinfo']))
			{
				$menuItem = null;
			}
		}

		// Otherwise, find a suitable menu -- exact match
		if (empty($menuItem))
		{
			$menuItem = $this->findMenu(array_merge($query, [
				'view' => $query['view'] ?? 'Levels',
			]));
		}

		// Otherwise, find a suitable menu -- root node
		if (empty($menuItem))
		{
			$menuItem = $this->findMenu([
				'view' => $query['view'] ?? 'Levels',
			]);
		}

		// Otherwise, find a suitable menu -- default view
		if (empty($menuItem))
		{
			$menuItem = $this->findMenu([
				'view' => 'Levels',
			]);
		}

		// We have an Itemid. Pass it along.
		if (!empty($menuItem))
		{
			$query['Itemid'] = $menuItem->id;
		}
		// I really don't have a valid Itemid. Let's kill it with fire from our $query.
		elseif (isset($query['Itemid']))
		{
			unset($query['Itemid']);
		}

		// Cache and return the result
		self::$preprocessCache[$signature] = $query;

		return $query;
	}

	/**
	 * Build method for URLs
	 * This method is meant to transform the query parameters into a more human
	 * readable form. It is only executed when SEF mode is switched on.
	 *
	 * @param   array  &$query  An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @throws Exception
	 * @since   7.0.1
	 */
	public function build(&$query)
	{
		$segments = [];

		// Default view
		$default   = 'Levels';
		$menuquery = [];

		// We need to find out if the menu item link has a view param
		if (array_key_exists('Itemid', $query))
		{
			$menu = Factory::getApplication()->getMenu()->getItem($query['Itemid']);

			if (is_object($menu))
			{
				parse_str(str_replace('index.php?', '', $menu->link), $menuquery); // remove "index.php?" and parse
			}
		}

		// Add the view
		$newView = array_key_exists('view', $query) ? $query['view'] :
			(array_key_exists('view', $menuquery) ? $menuquery['view'] : $default);

		$newView = ucfirst($newView);

		if ($newView == 'Level')
		{
			$newView = 'New';
		}
		elseif ($newView == 'Message')
		{
			$newView = 'ThankYou';

			unset($query['layout']);
			unset($query['task']);
		}
		elseif (($newView == 'Userinfo') || ($newView == 'UserInfo'))
		{
			$newView = 'UserInfo';

			if (!array_key_exists('layout', $query))
			{
				unset($query['layout']);
			}
		}

		$segments[] = strtolower($newView);
		unset($query['view']);

		// Add the slug
		if ($newView != 'UserInfo')
		{
			$container = $this->getContainer();

			if (array_key_exists('slug', $query) && ($container->inflector->isSingular($segments[0]) || ($segments[0] == 'new')))
			{
				$segments[1] = $query['slug'];
				unset($query['slug']);
			}
			elseif (array_key_exists('id', $query) && ($segments[0] == 'subscription'))
			{
				$segments[1] = $query['id'];
				unset($query['id']);
			}
		}

		// Add the subscription ID
		if (($newView == 'ThankYou') && isset($query['subid']))
		{
			$segments[] = $query['subid'];
			unset($query['subid']);
		}

		return $segments;
	}

	/**
	 * Parse method for URLs
	 * This method is meant to transform the human readable URL back into
	 * query parameters. It is only executed when SEF mode is switched on.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 * @throws  Exception
	 * @since   7.0.1
	 */
	public function parse(&$segments)
	{
		// accepted views:
		$views = [
			'new', 'thankyou', 'cancelled', 'level', 'levels', 'message', 'subscribe', 'subscription', 'subscriptions',
			'callback', 'validate', 'userinfo',
		];

		// accepted layouts:
		// TODO Now empty because it was holding references to Invoices, must refactor this
		$layoutsAccepted = [];

		// default view
		$default = 'levels';

		$mObject = Factory::getApplication()->getMenu()->getActive();
		$menu    = is_object($mObject) ? $mObject->query : [];

		// circumvent the auto-segment decoding
		$segments = str_replace(':', '-', $segments);

		$vars = [];

		if (!count($segments))
		{
			return $vars;
		}

		// if there's no view, but the menu item has view info, we use that
		if (!in_array(strtolower($segments[0]), $views))
		{
			$vars['view'] = array_key_exists('view', $menu) ? $menu['view'] : $default;
		}
		else
		{
			$vars['view'] = array_shift($segments);
		}

		switch ($vars['view'])
		{
			case 'New':
			case 'new':
				$vars['view'] = 'Level';
				$vars['task'] = 'read';
				break;

			case 'thankyou':
			case 'Thankyou':
			case 'ThankYou':
				$vars['view'] = 'Messages';
				$vars['task'] = 'show';
				break;

			case 'userinfo':
			case 'Userinfo':
			case 'UserInfo':
				$vars['view']   = 'UserInfo';
				$vars['task']   = 'read';
				$vars['layout'] = 'default';
				break;
		}

		// When we have a forced layout we need to push it to the segments for the next check to work.
		if (array_key_exists('layout', $vars))
		{
			array_unshift($segments, $vars['layout']);
		}

		// The next segment is likely to be the layout.
		$layouts        = array_key_exists($vars['view'], $layoutsAccepted) ? $layoutsAccepted[$vars['view']] : [];
		$vars['layout'] = count($segments) ? array_shift($segments) : 'default';

		/**
		 * If that was an invalid layout I probably read something I shouldn't, e.g. an ID or slug.
		 *
		 * Let me push what I read back to the segments array and apply the default layout for the view.
		 */
		if (!in_array($vars['layout'], $layouts))
		{
			array_unshift($segments, $vars['layout']);

			$vars['layout'] = array_key_exists('layout', $menu) ? $menu['layout'] : 'default';
		}

		// if we are in a singular view, the next item is the slug, unless we are in the userinfo view
		$container = $this->getContainer();

		if ($container->inflector->isSingular($vars['view']) && ($vars['view'] != 'UserInfo'))
		{
			if (in_array($vars['view'], ['Subscription']))
			{
				$vars['id'] = array_shift($segments);
			}
			else
			{
				$vars['slug'] = array_shift($segments);
			}
		}

		if (($vars['view'] == 'Messages') && !empty($segments))
		{
			$vars['subid'] = array_shift($segments);
		}

		// I need to remove all leftover segments in Joomla 4 to avoid a 404 error.
		$segments = [];

		return $vars;
	}

	/**
	 * Gets a temporary instance of the Akeeba Subscriptions container
	 *
	 * @return Container
	 *
	 * @since   7.1.1
	 */
	private function getContainer()
	{
		if (is_null($this->container))
		{
			$this->container = Container::getInstance('com_akeebasubs', [
				'tempInstance' => true,
			]);
		}

		return $this->container;
	}

	/**
	 * Finds a menu whose query parameters match those in $queryOptions
	 *
	 * @param   array  $queryOptions  The query parameters to look for
	 * @param   array  $params        The menu parameters to look for
	 *
	 * @return  null|MenuItem  Null if not found, or the menu item if we did find it
	 */
	private function findMenu(array $queryOptions = [], array $params = []): ?MenuItem
	{
		$menuitem = $this->menu->getActive();

		// First check the current menu item (fastest shortcut!)
		if (is_object($menuitem))
		{
			if (self::checkMenu($menuitem, $queryOptions, $params))
			{
				return $menuitem;
			}
		}

		foreach ($this->menu->getMenu() as $item)
		{
			if (self::checkMenu($item, $queryOptions, $params))
			{
				return $item;
			}
		}

		return null;
	}

	/**
	 * Checks if a menu item conforms to the query options and parameters specified
	 *
	 * @param   MenuItem  $menu          A menu item
	 * @param   array     $queryOptions  The query options to look for
	 * @param   array     $params        The menu parameters to look for
	 *
	 * @return  bool
	 */
	private function checkMenu(MenuItem $menu, array $queryOptions, array $params = []): bool
	{
		static $languages = [];

		if (empty($languages))
		{
			$languages = $this->getApplicationLanguages();
		}

		if (isset($queryOptions['lang']))
		{
			if (!empty($queryOptions['lang']))
			{
				$languages = [$queryOptions['lang']];
			}

			unset($queryOptions['lang']);
		}

		if (!empty($menu->language))
		{
			if (!in_array($menu->language, $languages))
			{
				return false;
			}
		}

		$query = $menu->query;

		foreach ($queryOptions as $key => $value)
		{
			// A null value was requested. Huh.
			if (is_null($value))
			{
				// If the key is set and is not empty it's not the menu item you're looking for
				if (isset($query[$key]) && !empty($query[$key]))
				{
					return false;
				}

				continue;
			}

			if (!isset($query[$key]))
			{
				return false;
			}

			if ($key == 'view')
			{
				// Treat views case-insensitive
				if (strtolower($query[$key]) != strtolower($value))
				{
					return false;
				}
			}
			elseif ($query[$key] != $value)
			{
				return false;
			}
		}

		if (empty($params))
		{
			return true;
		}

		$menuItemParams = $menu->getParams();
		$check          = $menuItemParams instanceof Registry ? $menuItemParams : $this->menu->getParams($menu->id);

		foreach ($params as $key => $value)
		{
			if (is_null($value))
			{
				continue;
			}

			if ($check->get($key) != $value)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Returns the application's languages, in the preferred order for the current user.
	 *
	 * @return array|string[]
	 *
	 * @since  7.1.1
	 */
	private function getApplicationLanguages(): array
	{
		$container      = $this->getContainer();
		$languages      = ['*'];
		$platform       = $container->platform;
		$isMultilingual = false;

		if (!$platform->isCli() && !$platform->isBackend() && !$platform->isApi())
		{
			$isMultilingual = method_exists($this->app, 'getLanguageFilter') ?
				$this->app->getLanguageFilter() : false;
		}

		if (!$isMultilingual)
		{
			return $languages;
		}

		// Get default site language
		$jLang = $this->app->getLanguage();

		return array_unique([
			$jLang->getTag(),
			$jLang->getDefault(),
			'*',
		]);
	}
}