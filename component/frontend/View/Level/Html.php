<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\View\Level;

defined('_JEXEC') or die;

use Akeeba\Subscriptions\Site\Model\Levels;

class Html extends \FOF30\View\DataView\Html
{
	/**
	 * The record loaded (read, edit, add views)
	 *
	 * @var  Levels
	 */
	protected $item = null;

	/**
	 * Should I apply validation? Please note that this is a string, not a boolean! It's used directly inside the
	 * Javascript.
	 *
	 * @var  string  "true" or "false". This is NOT a boolean, it's a string.
	 */
	public $apply_validation = '';

	/**
	 * Some component parameters used in this view
	 *
	 * @var  object
	 */
	public $cparams = null;

    /**
     * Current user params
     *
     * @var object
     */
    public $userparams = null;

    /**
     * The result of the validation
     *
     * @var object
     */
    public $validation = null;

	/**
	 * Executes before the read task, allows us to push data to the view
	 */
	protected function onBeforeRead()
	{
		parent::onBeforeRead();

		// Make sure the layout exists. Otherwise use the "default" layout
		try
		{
			// Read the layout from the request and try to apply it
			$this->layout	= $this->input->getCmd('layout', $this->layout);
			$uri            = "auto:com_akeebasubs/Level/{$this->layout}";
			$uri            = isset($this->viewTemplateAliases[$uri]) ? $this->viewTemplateAliases[$uri] : $uri;
			$layoutTemplate = $this->getLayoutTemplate();
			$extraPaths     = empty($this->templatePaths) ? array() : $this->templatePaths;
			$path           = $this->viewFinder->resolveUriToPath($uri, $this->getLayoutTemplate(), $extraPaths);
		}
		catch (\Exception $e)
		{
			$this->setLayout('default');
		}

		// Get component parameters and pass them to the view
		$componentParams = (object)array(
			'currencypos'           => $this->container->params->get('currencypos', 'before'),
			'stepsbar'              => $this->container->params->get('stepsbar', 1),
			'currencysymbol'        => $this->container->params->get('currencysymbol', '€'),
			'hidelonepaymentoption' => $this->container->params->get('hidelonepaymentoption', 1),
			'reqcoupon'             => $this->container->params->get('reqcoupon', 0),
		);

		$this->cparams = $componentParams;

		$this->apply_validation = $this->container->platform->getSessionVar('apply_validation.' . $this->item->akeebasubs_level_id, 0, 'com_akeebasubs') ? 'true' : 'false';

		// Makes sure SiteGround's SuperCache doesn't cache the subscription page
		\JFactory::getApplication()->setHeader('X-Cache-Control', 'False', true);
	}

	/**
	 * Get the value of a field from the session cache. If it's empty use the value from the user parameters cache.
	 *
	 * @param   string  $fieldName    The name of the field
	 * @param   array   $emptyValues  A list of values considered to be "empty" for the purposes of this method
	 *
	 * @return  mixed  The field value
	 */
	public function getFieldValue($fieldName, array $emptyValues = [])
	{
		$cacheValue = null;
		$userparamsValue = null;

		if (isset($this->cache[$fieldName]))
		{
			$cacheValue = $this->cache[$fieldName];
		}

		if (isset($this->userparams->{$fieldName}))
		{
			$userparamsValue = $this->userparams->{$fieldName};
		}

		if (is_null($cacheValue))
		{
			return $userparamsValue;
		}

		if (!empty($emptyValues) && in_array($cacheValue, $emptyValues))
		{
			return $userparamsValue;
		}

		return $cacheValue;
	}
}
