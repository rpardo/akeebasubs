<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Admin\Helper;

use Akeeba\Subscriptions\Admin\Model\Levels;
use Akeeba\Subscriptions\Admin\Model\Subscriptions;
use Exception;
use FOF30\Container\Container;
use FOF30\Model\DataModel\Exception\RecordNotLoaded;
use JFactory;
use JMail;
use Joomla\CMS\Mail\Mail;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;

defined('_JEXEC') or die;

/**
 * A helper class for sending out emails
 */
abstract class Email
{
	/**
	 * The component's container
	 *
	 * @var   Container
	 */
	protected static $container;

	/**
	 * Allowed image file extensions to inline in sent emails
	 *
	 * @var   array
	 */
	private static $allowedImageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'];

	/**
	 * Loads an email template from the view templates in the Templates folder.
	 *
	 * @param   string       $key    The language key, in the form PLG_LOCATION_PLUGINNAME_TYPE e.g.
	 *                               plg_akeebasubs_subscriptionemails_paid
	 * @param   Levels|null  $level  The subscription level we're interested in
	 *
	 * @return  array  Returns [subject, body]
	 */
	public static function loadEmailTemplate(string $key, ?Levels $level = null, array $forceParams = [])
	{
		// Parse the key
		$key               = strtolower($key);
		$keyParts          = explode('_', $key, 4);
		$possibleTemplates = [
			// e.g. any:com_akeebasubs/Templates/subscriptionemails_paid
			'any:com_akeebasubs/Templates/' . $keyParts[2] . '_' . $keyParts[3],
		];

		// If we have a desired subscription level add its specific template first in the list
		if (!is_null($level) && ($level->getId()))
		{
			// e.g. any:com_akeebasubs/Templates/subscriptionemails_paid-ADMINTOOLS
			array_unshift($possibleTemplates, $possibleTemplates[0] . '-' . $level->title);
		}

		// Try to load the rendered view template
		$templateText = self::loadFirstViewTemplate($possibleTemplates, $forceParams);

		// If we got null or an empty string there's no email to send
		if (empty($templateText))
		{
			return ['', ''];
		}

		// Extract the subject from the email template text
		$subject = self::extractTitle($templateText);

		return [$subject, $templateText];
	}

	/**
	 * Creates a mailer instance, preloads its subject and body with your email
	 * data based on the key and extra substitution parameters and waits for
	 * you to send a recipient and send the email.
	 *
	 * @param   Subscriptions  $sub     The subscription record against which the email is sent
	 * @param   string         $key     The email key, in the form PLG_LOCATION_PLUGINNAME_TYPE
	 * @param   array          $extras  Any optional substitution strings you want to introduce
	 *
	 * @return  Mail|null  Null if we can't load the email template. A preloaded mailer otherwise.
	 */
	public static function getPreloadedMailer(Subscriptions $sub, string $key, array $extras = []): ?Mail
	{
		$container = self::getContainer();

		// Get the subscription level (if defined)
		$level = self::getLevelFromSubscription($sub);

		// Get the Joomla user object for the subscription's owner
		$user = null;

		if (!empty($sub) && ($sub->user_id != 0))
		{
			$user = $container->platform->getUser($sub->user_id);
		}

		// Load the email template
		[$subject, $templateText] = self::loadEmailTemplate($key, $level, [
			'subscription' => $sub,
			'level'        => $level,
			'user'         => $user,
		]);

		// An empty subject means that the email template was not found
		if (empty($subject))
		{
			return null;
		}

		// Replace text in the message
		$templateText = Message::processSubscriptionTags($templateText, $sub, $extras);
		$subject      = Message::processSubscriptionTags($subject, $sub, $extras);

		// Get and populate the mailer
		$mailer = self::getMailer(true);
		$mailer->setSubject($subject);

		// Include inline images
		$templateText = self::inlineImages($templateText, $mailer);

		// Set the email body
		$mailer->setBody($templateText);

		// Set the recipient, if the subscription record defines an owner user
		if (is_object($user) && ($user instanceof User))
		{
			$mailer->addRecipient($user->email, $user->name);
		}

		return $mailer;
	}

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
	 * Extract the email template title from the provided HTML
	 *
	 * @param   string  $contents  The HTML content of the email (full page)
	 *
	 * @return  string
	 */
	private static function extractTitle(string $contents): string
	{
		$titleRegEx = '#<title>(.*)</title>#su';
		$title      = '';

		if (preg_match($titleRegEx, $contents, $matches))
		{
			return trim($matches[1]);
		}

		return '';
	}

	/**
	 * Returns the rendering of the first valid view template in the provided list.
	 *
	 * It goes through the entire list. The first one of them that returns a rendering, even an empty string, results in
	 * an immediate return.
	 *
	 * @param   array  $viewTemplateNames
	 * @param   array  $forceParams
	 *
	 * @return string|null
	 *
	 * @since version
	 */
	private static function loadFirstViewTemplate(array $viewTemplateNames, array $forceParams = []): ?string
	{
		$container  = self::getContainer();
		$viewObject = $container->factory->view('Templates');

		foreach ($viewTemplateNames as $viewTemplateName)
		{
			try
			{
				return trim($viewObject->loadAnyTemplate($viewTemplateName, $forceParams));
			}
			catch (Exception $e)
			{
				// No problem, we're gonna try the next view template in the list
			}
		}

		return null;
	}

	/**
	 * Creates a PHPMailer instance
	 *
	 * @param   boolean  $isHTML
	 *
	 * @return  JMail  A mailer instance
	 */
	private static function &getMailer($isHTML = true)
	{
		$mailer = clone JFactory::getMailer();

		$mailer->IsHTML($isHTML);

		// Required in order not to get broken characters
		$mailer->CharSet = 'UTF-8';

		return $mailer;
	}

	/**
	 * Get the level object given a specific subscription record
	 *
	 * @param   Subscriptions|null  $sub
	 *
	 * @return  Levels|null
	 *
	 * @since   7.1.0
	 */
	private static function getLevelFromSubscription(?Subscriptions $sub): ?Levels
	{
		// No subscription? No level.
		if (is_null($sub) || ($sub->getId() == 0))
		{
			return null;
		}

		// First try to return the Levels object we get from the relation
		$level = $sub->level;

		if (is_object($level) && ($level instanceof Levels) && ($level->getId() > 0))
		{
			return $level;
		}

		// The relation wasn't available. Go through the Levels model.
		$container = self::getContainer();

		try
		{
			$level = $container->factory->model('Levels')->tmpInstance()
				->findOrFail($sub->akeebasubs_level_id);
		}
		catch (RecordNotLoaded $e)
		{
			// Could not find the record (possibly an already deleted level...?)
			return null;
		}

		// Sanity check.
		if (is_object($level) && ($level instanceof Levels) && ($level->getId() > 0))
		{
			return $level;
		}

		return null;
	}

	/**
	 * Attach and inline the referenced images in the email message
	 *
	 * @param   string  $templateText
	 * @param   Mail    $mailer
	 *
	 * @return  string
	 *
	 * @since   7.1.0
	 */
	private static function inlineImages(string $templateText, Mail $mailer): string
	{
		// RegEx patterns to detect images
		$patterns = [
			// srcset="**URL**" e.g. source tags
			'/srcset=\"?([^"]*)\"?/i',
			// src="**URL**" e.g. img tags
			'/src=\"?([^"]*)\"?/i',
			// url(**URL**) nad url("**URL**") i.e. inside CSS
			'/url\(\"?([^"\(\)]*)\"?\)/i',
		];

		// Cache of images so we don't inline them multiple times
		$foundImages = [];
		// Running counter of images, used to create the attachment IDs in the message
		$imageIndex = 0;

		// Run a RegEx search & replace for each pattern
		foreach ($patterns as $pattern)
		{
			// $matches[0]: the entire string matched by RegEx; $matches[1]: just the path / URL
			$templateText = preg_replace_callback($pattern, function (array $matches) use ($mailer, &$foundImages, &$imageIndex): string {
				// Abort if it's not a file type we can inline
				if (!self::isInlineableFileExtension($matches[1]))
				{
					return $matches[0];
				}

				// Try to get the local absolute filesystem path of the referenced media file
				$localPath = self::getLocalAbsolutePath(self::normalizeURL($matches[1]));

				// Abort if this was not a relative / absolute URL pointing to our own site
				if (empty($localPath))
				{
					return $matches[0];
				}

				// Abort if the referenced file does not exist
				if (!@file_exists($localPath) || !@is_file($localPath))
				{
					return $matches[0];
				}

				// Make sure the inlined image is cached; prevent inlining the same file multiple times
				if (!array_key_exists($localPath, $foundImages))
				{
					$imageIndex++;
					$mailer->AddEmbeddedImage($localPath, 'img' . $imageIndex, basename($localPath));
					$foundImages[$localPath] = $imageIndex;
				}

				return str_replace($matches[1], $toReplace = 'cid:img' . $foundImages[$localPath], $matches[0]);
			}, $templateText);
		}

		// Return the processed email content
		return $templateText;
	}

	/**
	 * Does this file / URL have an allowed image extension for inlining?
	 *
	 * @param   string  $fileOrUri
	 *
	 * @return  bool
	 *
	 * @since   7.1.0
	 */
	private static function isInlineableFileExtension(string $fileOrUri): bool
	{
		$dot = strrpos($fileOrUri, '.');

		if ($dot === false)
		{
			return false;
		}

		$extension = substr($fileOrUri, $dot + 1);

		return in_array(strtolower($extension), self::$allowedImageExtensions);
	}

	/**
	 * Normalizes an image relative or absolute URL as an absolute URL
	 *
	 * @param   string  $fileOrUri
	 *
	 * @return  string
	 *
	 * @since   7.1.0
	 */
	private static function normalizeURL(string $fileOrUri): string
	{
		// Empty file / URIs are returned as-is (obvious screw up)
		if (empty($fileOrUri))
		{
			return $fileOrUri;
		}

		// Remove leading / trailing slashes
		$fileOrUri = trim($fileOrUri, '/');

		// HTTPS URLs are returned as-is
		if (substr($fileOrUri, 0, 8) == 'https://')
		{
			return $fileOrUri;
		}

		// HTTP URLs are returned upgraded to HTTPS
		if (substr($fileOrUri, 0, 7) == 'http://')
		{
			return 'https://' . substr($fileOrUri, 7);
		}

		// Normalize URLs with a partial schema as HTTPS
		if (substr($fileOrUri, 0, 3) == '://')
		{
			return 'https://' . substr($fileOrUri, 3);
		}

		// This is a file. We assume it's relative to the site's root
		return rtrim(Uri::base(), '/') . '/' . $fileOrUri;
	}

	/**
	 * Return the path to the local file referenced by the URL, provided it's internal.
	 *
	 * @param   string  $url
	 *
	 * @return  string|null  The local file path. NULL if the URL is not internal.
	 *
	 * @since   7.1.0
	 */
	private static function getLocalAbsolutePath(string $url): ?string
	{
		$base     = rtrim(Uri::base(), '/');

		if (strpos($url, $base) !== 0)
		{
			return null;
		}

		return JPATH_ROOT . '/' . ltrim(substr($url, strlen($base) + 1), '/');
	}
}
