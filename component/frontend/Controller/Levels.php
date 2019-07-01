<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Controller;

defined('_JEXEC') or die;

use Akeeba\Subscriptions\Site\Model\Subscribe as SubscribeModel;
use FOF30\Container\Container;
use FOF30\Controller\DataController;

class Levels extends DataController
{
	/**
	 * Overridden. Limit the tasks we're allowed to execute.
	 *
	 * @param   Container  $container
	 * @param   array      $config
	 */
	public function __construct(Container $container, array $config = array())
	{
		// We can't cache anything since level prices may be user-specific and modified by session settings
		$config['cacheableTasks'] = [];

		parent::__construct($container, $config);

		if ($this->input->getBool('caching', true))
		{
			$this->cacheableTasks = ['browse'];
		}
		else
		{
			$this->cacheableTasks = [];
		}
	}

	/**
	 * Runs before the browse task
	 *
	 * @throws \Exception
	 */
	public function onBeforeBrowse()
	{
		/** @var \JApplicationSite $app */
		$app    = \JFactory::getApplication();
		$params = $app->getParams();

		$ids = $params->get('ids', '');

		if (is_array($ids) && !empty($ids))
		{
			$checkIds = implode(',', $ids);

			if (($checkIds === '0') || $checkIds === '')
			{
				$ids = '';
			}
		}
		else
		{
			$ids = '';
		}

		// Working around Progressive Caching
		$appInput = $app->input;

		if (!empty($ids))
		{
			$appInput->set('ids', $ids);
			$appInput->set('_x_userid', $this->container->platform->getUser()->id);
		}

		$this->registerUrlParams(array(
			'ids'                       => 'ARRAY',
			'no_clear'                  => 'BOOL',
			'_x_userid'                 => 'INT',
			'coupon'                    => 'STRING'
		));

		// Save a possible coupon code in the session
		$coupon = $this->input->getString('coupon');

		if (!empty($coupon))
		{
			$this->container->platform->setSessionVar('coupon', $coupon, 'com_akeebasubs');
		}

		// Are we told to hide notices?
		if (!$this->input->getBool('shownotices', true))
		{
			$view = $this->getView();
			$view->showNotices = false;
		}

		// Continue parsing page options
		/** @var \Akeeba\Subscriptions\Site\Model\Levels $model */
		$model = $this->getModel();
		$noClear = $this->input->getBool('no_clear', false);

		if (!$noClear)
		{
			$model
				->clearState()
				->savestate(0)
				->setIgnoreRequest(1)
				->limit(0)
				->limitstart(0)
				->enabled(1)
				->only_once(1)
				->filter_order('ordering')
				->filter_order_Dir('ASC');

			if (!empty($ids))
			{
				$model->id($ids);
			}
		}

		$model->access_user_id($this->container->platform->getUser()->id);
	}

	/**
	 * Runs before the read task
	 *
	 * @return bool
	 */
	public function onBeforeRead()
	{
		/** @var \JApplicationSite $app */
		$app      = \JFactory::getApplication();

		// Fetch the subscription slug from page parameters
		$params   = $app->getParams();
		$pageslug = $params->get('slug', '');
		$slug     = $this->input->getString('slug', null);

		if ($pageslug)
		{
			$slug = $pageslug;
			$this->input->set('slug', $slug);
		}

		/** @var \Akeeba\Subscriptions\Site\Model\Levels $model */
		$model = $this->getModel();

		$this->getIDsFromRequest($model, true);
		$model->access_user_id($this->container->platform->getUser()->id);
		$id = $model->getId();

		if (!$id && $slug)
		{
			// Note: do note replace $item with $model or read() won't see the loaded record because of how references
			// work in PHP.
			$item = $model
				->id(0)
				->slug([
					'method' => 'exact',
					'value' => $slug
				])
				->firstOrNew();

			$id = $item->getId();
		}

		// Make sure the level exists
		if ($id == 0)
		{
			return false;
		}

		// The level exists, load it.
		$model->find($id);

		// Working around Progressive Caching
		$app->input->set('slug', $slug);
		$app->input->set('id', $id);

		$this->registerUrlParams(array(
			'slug' => 'STRING',
			'id'   => 'INT',
		));

		// Make sure the level is published
		if (!$model->enabled)
		{
			return false;
		}

		// Check for "Forbid renewal" conditions
		if ($model->only_once)
		{
			$levels = $model->getClone()->savestate(false)->setIgnoreRequest(true)->clearState()->reset(true, true);
			$levels
				->slug([
					'method' => 'exact',
					'value' => $model->slug
				])
				->only_once(1)
				->get(true);

			if (!$levels->count())
			{
				// User trying to renew a level which is marked as only_once
				if ($model->renew_url)
				{
					$this->container->platform->redirect($model->renew_url);
				}

				return false;
			}
		}

		// If the reset flag is passed to the URL we need to reset the cached data EXCEPT for the coupon code
		$forceReset = $this->input->getBool('reset', false);
		$forceReset = $forceReset || $this->container->platform->getSessionVar('forcereset', false, 'com_akeebasubs');

		if ($forceReset)
		{
			$model->getContainer()->platform->setSessionVar('firstrun', true, 'com_akeebasubs');
		}

        /** @var \Akeeba\Subscriptions\Site\View\Level\Html $view */
		$view = $this->getView();

		// Load any cached user supplied information
		/** @var SubscribeModel $vModel */
		$vModel = $this->getModel('Subscribe');
		$vModel->slug($slug);
		$vModel->setState('id', $id);

		// Should we use the coupon code saved in the session?
		$sessionCoupon = $this->container->platform->getSessionVar('coupon', null, 'com_akeebasubs');
		$inputCoupon = $this->input->getString('coupon');

		if (empty($inputCoupon) && !empty($sessionCoupon))
		{
			$vModel->coupon($sessionCoupon);
			$this->container->platform->setSessionVar('coupon', null, 'com_akeebasubs');
		}

		/**
		 * Force the State Variables to re-initialize because we might have already changed the subscription level and
		 * coupon in the code above.
		 */
		$cache = (array)($vModel->getStateVariables(true));
		// Do the same for the validation. Otherwise the client ends up buying the WRONG SUSBCRIPTION LEVEL!
		$vModel->getValidation(true);

		$view->cache = (array)$cache;
		$view->validation = $vModel->getValidation();

		/**
		 * If this was a POST request (because someone pressed the APPLY button next to the Coupon field) do NOT apply
		 * the validation results.
		 */
		if ($this->input->getMethod() == 'POST')
		{
			$this->container->platform->setSessionVar('apply_validation.' . $id, 0, 'com_akeebasubs');
		}

		return true;
	}

	/**
	 * Registers page-identifying parameters to the application object. This is used by the Joomla! caching system to
	 * get the unique identifier of a page and decide its caching status (cached, not cached, cache expired).
	 *
	 * @param array $urlparams
	 */
	protected function registerUrlParams($urlparams = array())
	{
		$app = \JFactory::getApplication();

		$registeredurlparams = null;

		if (!empty($app->registeredurlparams))
		{
			$registeredurlparams = $app->registeredurlparams;
		}
		else
		{
			$registeredurlparams = new \stdClass;
		}

		foreach ($urlparams as $key => $value)
		{
			// Add your safe url parameters with variable type as value {@see JFilterInput::clean()}.
			$registeredurlparams->$key = $value;
		}

		$app->registeredurlparams = $registeredurlparams;
	}
}
