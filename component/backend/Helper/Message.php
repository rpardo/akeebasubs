<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Admin\Helper;

use Akeeba\ReleaseSystem\Site\Helper\Filter as Filter;
use Akeeba\Subscriptions\Admin\Model\Levels;
use Akeeba\Subscriptions\Admin\Model\Subscriptions;
use Akeeba\Subscriptions\Admin\Model\Users;
use FOF30\Container\Container;
use FOF30\Date\Date;
use FOF30\Model\DataModel;
use JFactory;
use JLoader;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry as JRegistry;
use JText;

defined('_JEXEC') or die;

/**
 * A helper class for sending out emails
 */
abstract class Message
{
	/**
	 * The component's container
	 *
	 * @var   Container
	 */
	protected static $container;

	/**
	 * Returns the component's container
	 *
	 * @return  Container
	 */
	protected static function getContainer()
	{
		if (is_null(self::$container))
		{
			self::$container = Container::getInstance('com_akeebasubs');
		}

		return self::$container;
	}

	/**
	 * Pre-processes the message text in $text, replacing merge tags with those
	 * fetched based on subscription $sub
	 *
	 * @param   string         $text               The message to process
	 * @param   Subscriptions  $sub                A subscription object
	 *
	 * @return  string  The processed string
	 */
	public static function processSubscriptionTags($text, $sub, $extras = array())
	{
		// Get the user object for this subscription
		$joomlaUser = self::getContainer()->platform->getUser($sub->user_id);

		// Get the subscription level
		/** @var Levels $level */
		$level = $sub->level;

		if (
			!is_object($level)
			||
			(($sub->level instanceof Levels) && ($sub->level->akeebasubs_level_id != $sub->akeebasubs_level_id))
		)
		{
			/** @var Levels $levelModel */
			$levelModel = Container::getInstance('com_akeebasubs')->factory->model('Levels')->tmpInstance();
			$level      = $levelModel->id($sub->akeebasubs_level_id)->firstOrNew();
		}

		// Merge the user objects
		$userData = (array)$joomlaUser;

		// Create and replace merge tags for subscriptions. Format [SUB:KEYNAME]
		if ($sub instanceof DataModel)
		{
			$subData = (array)($sub->getData());
		}
		else
		{
			// Why am I here?!
			$subData = (array)$sub;
		}

		foreach ($subData as $k => $v)
		{
			if (is_array($v) || is_object($v))
			{
				continue;
			}

			if (substr($k, 0, 1) == '_')
			{
				continue;
			}

			if ($k == 'akeebasubs_subscription_id')
			{
				$k = 'id';
			}

			$tag = '[SUB:' . strtoupper($k) . ']';

			if (in_array($k, array(
				'net_amount',
				'gross_amount',
				'tax_amount',
				'fee_amount',
				'prediscount_amount',
				'discount_amount',
			)))
			{
				$v = sprintf('%.2f', $v);
			}

			$text = str_replace($tag, $v, $text);
		}

		// Create and replace merge tags for the subscription level. Format [LEVEL:KEYNAME]
		$levelData = [];

		if ($level instanceof Levels)
		{
			$levelData = (array)($level->getData());
		}

		foreach ($levelData as $k => $v)
		{
			if (is_array($v) || is_object($v))
			{
				continue;
			}

			if (substr($k, 0, 1) == '_')
			{
				continue;
			}

			if ($k == 'akeebasubs_level_id')
			{
				$k = 'id';
			}

			$tag  = '[LEVEL:' . strtoupper($k) . ']';
			$text = str_replace($tag, $v, $text);
		}

		// Create and replace merge tags for custom per-subscription data. Format [SUBCUSTOM:KEYNAME]
		if (array_key_exists('params', $subData))
		{
			if (is_string($subData['params']))
			{
				$custom = json_decode($subData['params'], true);
			}
			elseif (is_array($subData['params']))
			{
				$custom = $subData['params'];
			}
			elseif (is_object($subData['params']))
			{
				$custom = (array)$subData['params'];
			}
			else
			{
				$custom = array();
			}

			// Extra check for subcustom params: if you save a subscription form the backend,
			// custom fields are inside an array named subcustom
			if (is_array($custom) && isset($custom['subcustom']))
			{
				$custom = $custom['subcustom'];
			}

			if (is_object($custom))
			{
				$custom = (array)$custom;
			}

			if (!empty($custom))
			{
				foreach ($custom as $k => $v)
				{
					if (is_object($v))
					{
						continue;
					}

					if (substr($k, 0, 1) == '_')
					{
						continue;
					}

					$tag = '[SUBCUSTOM:' . strtoupper($k) . ']';

					if (is_array($v))
					{
						continue;
					}

					$text = str_replace($tag, $v, $text);
				}
			}
		}

		// Create and replace merge tags for user data. Format [USER:KEYNAME]
		foreach ($userData as $k => $v)
		{
			if (is_object($v) || is_array($v))
			{
				continue;
			}

			if (substr($k, 0, 1) == '_')
			{
				continue;
			}

			if ($k == 'akeebasubs_subscription_id')
			{
				$k = 'id';
			}

			$tag  = '[USER:' . strtoupper($k) . ']';
			$text = str_replace($tag, $v, $text);
		}

		// Create and replace merge tags for custom fields data. Format [CUSTOM:KEYNAME]
		if (array_key_exists('params', $userData))
		{
			if (is_string($userData['params']))
			{
				$custom = json_decode($userData['params']);
			}
			elseif (is_array($userData['params']))
			{
				$custom = $userData['params'];
			}
			elseif (is_object($userData['params']))
			{
				$custom = (array)$userData['params'];
			}
			else
			{
				$custom = array();
			}

			if (!empty($custom))
			{
				foreach ($custom as $k => $v)
				{
					if (substr($k, 0, 1) == '_')
					{
						continue;
					}

					$tag = '[CUSTOM:' . strtoupper($k) . ']';

					if ($v instanceof \stdClass)
					{
						$v = (array)$v;
					}

					if (is_array($v))
					{
						$v = implode(', ', $v);
					}

					$text = str_replace($tag, $v, $text);
				}
			}
		}

		// Extra variables replacement
		// -- Coupon code
		$couponCode = '';

		if ($sub->akeebasubs_coupon_id)
		{
			try
			{
				$couponData = self::getContainer()
					->factory->model('Coupons')->tmpInstance()
					->findOrFail($sub->akeebasubs_coupon_id);

				$couponCode = $couponData->coupon;
			}
			catch (\RuntimeException $e)
			{
				$couponCode = '';
			}
		}

		// -- Get the site name
		$config   = self::getContainer()->platform->getConfig();
		$sitename = $config->get('sitename');

		// -- First/last name
		$fullname  = $joomlaUser->name;
		$nameParts = explode(' ', $fullname, 2);
		$firstname = array_shift($nameParts);
		$lastname  = !empty($nameParts) ? array_shift($nameParts) : '';

		// -- Site URL
		$container = Container::getInstance('com_akeebasubs');
		$isCli     = $container->platform->isCli();
		$isAdmin   = $container->platform->isBackend();

		if ($isCli)
		{
			$baseURL    = self::getContainer()->params->get('siteurl', 'http://www.example.com');
			$temp       = str_replace('http://', '', $baseURL);
			$temp       = str_replace('https://', '', $temp);
			$parts      = explode($temp, '/', 2);
			$subpathURL = count($parts) > 1 ? $parts[1] : '';
		}
		else
		{
			$baseURL    = \JURI::base();
			$subpathURL = \JURI::base(true);
		}

		$baseURL    = str_replace('/administrator', '', $baseURL);
		$subpathURL = str_replace('/administrator', '', $subpathURL);
		$subpathURL = ltrim($subpathURL, '/');

		// -- My Subscriptions URL
		if ($isAdmin || $isCli)
		{
			$url = 'index.php?option=com_akeebasubs&view=Subscriptions';
		}
		else
		{
			$url =
				str_replace('&amp;', '&', \JRoute::_('index.php?option=com_akeebasubs&view=Subscriptions&layout=default'));
		}

		$url = ltrim($url, '/');

		if (substr($url, 0, strlen($subpathURL) + 1) == "$subpathURL/")
		{
			$url = substr($url, strlen($subpathURL) + 1);
		}

		$mysubsurl = rtrim($baseURL, '/') . '/' . ltrim($url, '/');

		// -- Renewal URL
		$slug = $level->slug;
		$url  = 'index.php?option=com_akeebasubs&view=Level&slug=' . $slug . '&layout=default';

		if (!$isAdmin && !$isCli)
		{
			$url = str_replace('&amp;', '&', \JRoute::_($url));
		}

		$url = ltrim($url, '/');

		if (substr($url, 0, strlen($subpathURL) + 1) == "$subpathURL/")
		{
			$url = substr($url, strlen($subpathURL) + 1);
		}

		$renewalURL = rtrim($baseURL, '/') . '/' . ltrim($url, '/');

		// Currency
		$currency     = self::getContainer()->params->get('currency', 'EUR');
		$symbol       = self::getContainer()->params->get('currencysymbol', 'EUR');

		// Dates
		$jFrom = new Date($sub->publish_up);
		$jTo   = new Date($sub->publish_down);

		// Download ID

		if (!class_exists('Akeeba\ReleaseSystem\Site\Helper\Filter') && file_exists(JPATH_SITE . '/components/com_ars/Helper/Filter.php'))
		{
			@include_once JPATH_SITE . '/components/com_ars/Helper/Filter.php';
		}

		$dlid = '';

		if (class_exists('Akeeba\ReleaseSystem\Site\Helper\Filter'))
		{
			$dlid = Filter::myDownloadID($sub->user_id);
		}

		// -- Message URL
		if ($sub->juser->block && $sub->juser->activation)
		{
			$urlAuth = 'activation=' . $sub->juser->activation;
		}
		else
		{
			$secret   = Factory::getConfig()->get('secret', '');
			$authCode = md5($sub->getId() . $sub->user_id . $secret);
			$urlAuth  = 'authorization=' . $authCode;
		}

		$messageUrl = Route::_('index.php?option=com_akeebasubs&view=Message&subid=' . $sub->akeebasubs_subscription_id . '&' . $urlAuth);

		if (!$isAdmin && !$isCli)
		{
			$messageUrl =
				str_replace('&amp;', '&', Route::_($messageUrl));
		}

		$messageUrl = ltrim($url, '/');

		if (substr($messageUrl, 0, strlen($subpathURL) + 1) == "$subpathURL/")
		{
			$messageUrl = substr($messageUrl, strlen($subpathURL) + 1);
		}

		$messageUrl = rtrim($baseURL, '/') . '/' . ltrim($messageUrl, '/');

		// -- The actual replacement
		$extras = array_merge(array(
			"\\n"                      => "\n",
			'[SITENAME]'               => $sitename,
			'[SITEURL]'                => $baseURL,
			'[FULLNAME]'               => $fullname,
			'[FIRSTNAME]'              => $firstname,
			'[LASTNAME]'               => $lastname,
			'[USERNAME]'               => $joomlaUser->username,
			'[USEREMAIL]'              => $joomlaUser->email,
			'[LEVEL]'                  => $level->title,
			'[SLUG]'                   => $level->slug,
			'[RENEWALURL]'             => $renewalURL,
			'[RENEWALURL:]'            => $renewalURL, // Malformed tag without a coupon code...
			'[MESSAGEURL]'             => $messageUrl,
			'[ENABLED]'                => JText::_('COM_AKEEBASUBS_SUBSCRIPTION_COMMON_' . ($sub->enabled ? 'ENABLED' :
					'DISABLED')),
			'[PAYSTATE]'               => JText::_('COM_AKEEBASUBS_SUBSCRIPTION_STATE_' . $sub->getFieldValue('state', 'N')),
			'[PUBLISH_UP]'             => Format::date($jFrom,JText::_('DATE_FORMAT_LC2') . ' T', $sub->user_id),
			'[PUBLISH_UP_EU]'          => Format::date($jFrom,'d/m/Y H:i:s T', $sub->user_id),
			'[PUBLISH_UP_USA]'         => Format::date($jFrom,'m/d/Y h:i:s a T', $sub->user_id),
			'[PUBLISH_UP_JAPAN]'       => Format::date($jFrom,'Y/m/d H:i:s T', $sub->user_id),
			'[PUBLISH_DOWN]'           => Format::date($jTo,JText::_('DATE_FORMAT_LC2'). ' T', $sub->user_id),
			'[PUBLISH_DOWN_EU]'        => Format::date($jTo,'d/m/Y H:i:s T', $sub->user_id),
			'[PUBLISH_DOWN_USA]'       => Format::date($jTo,'m/d/Y h:i:s a T', $sub->user_id),
			'[PUBLISH_DOWN_JAPAN]'     => Format::date($jTo,'Y/m/d H:i:s T', $sub->user_id),
			'[MYSUBSURL]'              => $mysubsurl,
			'[URL]'                    => $mysubsurl,
			'[CURRENCY]'               => $currency,
			'[$]'                      => $symbol,
			'[DLID]'                   => $dlid,
			'[COUPONCODE]'             => $couponCode,
			// Legacy keys
			'[NAME]'                   => $firstname,
			'[STATE]'                  => JText::_('COM_AKEEBASUBS_SUBSCRIPTION_STATE_' . $sub->getFieldValue('state', 'N')),
			'[FROM]'                   => Format::date($jFrom,JText::_('DATE_FORMAT_LC2'). ' T', $sub->user_id),
			'[TO]'                     => Format::date($jTo,JText::_('DATE_FORMAT_LC2'). ' T', $sub->user_id),
		), $extras);

		foreach ($extras as $key => $value)
		{
			$text = str_replace($key, $value, $text);
		}

		// Special replacement for RENEWALURL:COUPONCODE
		$text = self::substituteRenewalURLWithCoupon($text, $renewalURL);

		$container->platform->runPlugins('onAkeebasubsAfterProcessTags', array(&$text, $sub, $extras));

		return $text;
	}

	/**
	 * Processes the language merge tags ([IFLANG langCode], [/IFLANG]) in some
	 * block of text.
	 *
	 * @param   string  $text  The text to process
	 * @param   string  $lang  Which language to keep. Null means the default language.
	 *
	 * @return  string
	 */
	public static function processLanguage($text, $lang = null)
	{
		// Get the default language
		if (empty($lang))
		{
			$enableTranslation = JFactory::getApplication()->getLanguageFilter();

			if ($enableTranslation)
			{
				$lang = JFactory::getLanguage()->getTag();
			}
			else
			{
				$user = self::getContainer()->platform->getUser();

				if (property_exists($user, 'language'))
				{
					$lang = $user->language;
				}
				else
				{
					$params = $user->params;

					if (!is_object($params))
					{
						$params = new JRegistry($params);
					}

					$lang = $params->get('language', '');
				}
				if (empty($lang))
				{
					$lang = \JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
				}
			}
		}

		// Find languages
		$translations = array();

		while (strpos($text, '[IFLANG ') !== false)
		{
			$start                     = strpos($text, '[IFLANG ');
			$end                       = strpos($text, '[/IFLANG]');
			$langEnd                   = strpos($text, ']', $start);
			$langCode                  = substr($text, $start + 8, $langEnd - $start - 8);
			$langText                  = substr($text, $langEnd + 1, $end - $langEnd - 1);
			$translations[ $langCode ] = $langText;

			if ($start > 0)
			{
				$temp = substr($text, 0, $start - 1);
			}
			else
			{
				$temp = 0;
			}

			$temp .= substr($text, $end + 9);
			$text = $temp;
		}
		if (!empty($text))
		{
			if (!array_key_exists('*', $translations))
			{
				$translations['*'] = $text;
			}
		}

		$siteLang = \JComponentHelper::getParams('com_languages')->get('site', 'en-GB');

		if (array_key_exists($lang, $translations))
		{
			return $translations[ $lang ];
		}
		elseif (array_key_exists($siteLang, $translations))
		{
			return $translations[ $siteLang ];
		}
		elseif (array_key_exists('*', $translations))
		{
			return $translations['*'];
		}
		else
		{
			return $text;
		}
	}

	/**
	 * Substitutes the [RENEWALURL:couponcode] tag in messages
	 *
	 * @param   string  $text        The message text
	 * @param   string  $renewalURL  The base renewal URL (without a coupon code)
	 *
	 * @return  string  The text with the tag replaced with the proper URLs
	 */
	public static function substituteRenewalURLWithCoupon($text, $renewalURL)
	{
		// Find where the tag starts
		$nextPos      = 0;
		$tagStartText = '[RENEWALURL:';

		$uri = new \JUri($renewalURL);

		do
		{
			$pos = strpos($text, $tagStartText, $nextPos);

			if ($pos === false)
			{
				// Not found? No change.
				continue;
			}

			// Get the start position of the coupon name
			$couponStartPos = $pos + strlen($tagStartText);

			// Get the end position of the tag
			$endPos = strpos($text, ']', $couponStartPos);

			// If no end position is found, ignore the tag
			if ($endPos == $couponStartPos)
			{
				$nextPos = $couponStartPos + 1;
				continue;
			}

			// Get the coupon code
			$couponCode = substr($text, $couponStartPos, $endPos - $couponStartPos);

			// Create the URL
			$uri->setVar('coupon', $couponCode);

			$toReplace = substr($text, $pos, $endPos - $pos + 1);
			$text      = str_replace($toReplace, $uri->toString(), $text);
		}
		while ($pos !== false);

		return $text;
	}
}
