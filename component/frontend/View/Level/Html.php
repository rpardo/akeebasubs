<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\View\Level;

defined('_JEXEC') or die;

use Akeeba\Subscriptions\Site\Model\Levels;
use Akeeba\Subscriptions\Site\Model\Subscribe;
use Joomla\CMS\Factory;

class Html extends \FOF30\View\DataView\Html
{
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
	 * The result of the validation
	 *
	 * @var object
	 */
	public $validation = null;

	/**
	 * Subscription levels I can upsell the user to
	 *
	 * @var   array
	 * @since 7.0.0
	 */
	public $upsellLevels = [];

	/**
	 * The record loaded (read, edit, add views)
	 *
	 * @var  Levels
	 */
	public $item = null;

	/**
	 * Get the value of a field from the session cache. If it's empty use the value from the user parameters cache.
	 *
	 * @param string $fieldName   The name of the field
	 * @param array  $emptyValues A list of values considered to be "empty" for the purposes of this method
	 *
	 * @return  mixed  The field value
	 */
	public function getFieldValue($fieldName, array $emptyValues = [])
	{
		$cacheValue      = null;
		$userparamsValue = null;

		if (isset($this->cache[$fieldName]))
		{
			$cacheValue = $this->cache[$fieldName];
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
			// WARNING -- DO NOT REMOVE THIS BLOCK -- It looks useless but it will throw an exception if the layout is=
			// not found. This is what we need here!
			$this->layout   = $this->input->getCmd('layout', $this->layout);
			$uri            = "auto:com_akeebasubs/Level/{$this->layout}";
			$uri            = isset($this->viewTemplateAliases[$uri]) ? $this->viewTemplateAliases[$uri] : $uri;
			$layoutTemplate = $this->getLayoutTemplate();
			$extraPaths     = empty($this->templatePaths) ? [] : $this->templatePaths;
			$path           = $this->viewFinder->resolveUriToPath($uri, $this->getLayoutTemplate(), $extraPaths);
		}
		catch (\Exception $e)
		{
			$this->setLayout('default');
		}

		// Get component parameters and pass them to the view
		$localisePrice   = $this->container->params->get('localisePrice', 1);
		$isTaxAllowed    = $localisePrice && $this->container->params->get('showEstimatedTax', 1);
		$componentParams = (object) [
			'currencypos'    => $this->container->params->get('currencypos', 'before'),
			'stepsbar'       => $this->container->params->get('stepsbar', 1),
			'currencysymbol' => $this->container->params->get('currencysymbol', '€'),
			'localisePrice'  => $localisePrice,
			'isTaxAllowed'   => $isTaxAllowed,
		];

		$this->cparams = $componentParams;

		$this->apply_validation = $this->container->platform->getSessionVar('apply_validation.' . $this->item->akeebasubs_level_id, 0, 'com_akeebasubs') ? 'true' : 'false';

		/** @var Subscribe $subModel */
		$subModel           = $this->container->factory->model('Subscribe')->tmpInstance();
		$this->validation   = $subModel->getValidation();
		$this->upsellLevels = $subModel->getRelatedLevelUpsells();

		// Makes sure SiteGround's SuperCache doesn't cache the subscription page
		Factory::getApplication()->setHeader('X-Cache-Control', 'False', true);
	}
}
