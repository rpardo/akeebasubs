<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Controller;

defined('_JEXEC') or die;

use Akeeba\Subscriptions\Admin\Controller\Mixin;
use Akeeba\Subscriptions\Site\Model\Subscribe as ModelSubscribe;
use Akeeba\Subscriptions\Site\Model\Subscriptions;
use FOF30\Container\Container;
use FOF30\Controller\Controller;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

class Subscribe extends Controller
{
	use Mixin\PredefinedTaskList;

	/**
	 * Overridden. Limit the tasks we're allowed to execute.
	 *
	 * @param   Container $container
	 * @param   array     $config
	 */
	public function __construct(Container $container, array $config = array())
	{
		$config['modelName'] = 'Subscribe';
		$config['cacheableTasks'] = [];

		parent::__construct($container, $config);

		$this->predefinedTaskList = ['subscribe'];

		$this->cacheableTasks = [];
	}

	/**
	 * Handles the POST of the subscription form. It will try to create a new subscription (and user, if the POST came
	 * from a guest and there's valid information).
	 *
	 * It returns a JSON response containing the keys method and url.
	 *
	 * The method is only of 'redirect' or 'overlay' and conveys the usage intent of the URL passed in the url param.
	 *
	 * In case of an error the URL is null. This means that an error message had been set in the session. The frontend
	 * JavaScript needs to reload the page to display it.
	 *
	 * @return  void
	 */
	public function subscribe()
	{
		$ret = [
			'method' => 'redirect',
			'url'    => null,
		];

		// Load the models

		/** @var ModelSubscribe $model */
		$model = $this->getModel();

		/** @var \Akeeba\Subscriptions\Site\Model\Levels $levelsModel */
		$levelsModel = $this->getModel('Levels');

		// Load the id and slug. Either one defines which level we shall load
		$id   = $model->getState('id', 0, 'int');
		$slug = $model->getState('slug', null, 'string');

		// If the ID is not set but slug is let's try to find the level by slug
		if (!$id && $slug)
		{
			// Note: do note replace $item with $levelsModel or the view won't see the loaded record because of how
			// references work in PHP.
			$level = $levelsModel
				->id(0)
				->slug([
					'method' => 'exact',
					'value'  => $slug,
				])
				->firstOrNew();
		}
		else
		{
			$level = $levelsModel->find($id);
		}

		// If we do not have a valid level ID cause an error to be displayed
		if (!$level->getId())
		{
			$ret['info'] = 'Cannot find level ID';

			$this->enqueueMessage(Text::_('COM_AKEEBASUBS_LEVEL_ERR_NOSUCHLEVEL'), 'error');

			echo json_encode($ret);

			$this->container->platform->closeApplication();
		}

		// If the level is marked as only_once we need to make sure we're allowed to access it
		if ($level->only_once)
		{
			/** @var Subscriptions $subscriptions */
			$subscriptions = $this->getModel('subscriptions');
			$subscriptions
				->level($level->akeebasubs_level_id)
				->enabled(1)
				->get(true);

			if ($subscriptions->count())
			{
				$ret['info'] = 'Cannot resubscribe to "only once" level';

				// User trying to renew a level which is marked as only_once
				$this->enqueueMessage(Text::_('COM_AKEEBASUBS_LEVEL_ERR_ONLYONCE'), 'error');

				echo json_encode($ret);

				$this->container->platform->closeApplication();
			}
		}

		// Check the Joomla! View Access Level for this subscription level
		$accessLevels = $this->container->platform->getUser()->getAuthorisedViewLevels();

		if (!in_array($level->access, $accessLevels))
		{
			$ret['info'] = 'Not authorised (permissions)';

			// User trying to renew a level which is marked as only_once
			$this->enqueueMessage(Text::_('COM_AKEEBASUBS_LEVEL_ERR_NOTAUTHORISED'), 'error');

			echo json_encode($ret);

			$this->container->platform->closeApplication();
		}

		// Try to create a new subscription record
		$model->setState('id', $id);

		try
		{
			$details         = '';
			$newSubscription = $model->createNewSubscription();
		}
		catch (\Exception $e)
		{
			$details         = $e->getCode() . ":" . $e->getMessage();
			$newSubscription = null;
		}

		// Did we fail to create a new subscription?
		if (is_null($newSubscription))
		{
			$this->container->platform->setSessionVar('firstrun', false, 'com_akeebasubs');
			$this->container->platform->setSessionVar('forcereset', false, 'com_akeebasubs');
			$helpCode = basename($model->getLogFilename(), '.php');
			$layout   = $this->input->getCmd('layout', 'default');


			$msg = Text::sprintf('COM_AKEEBASUBS_LEVEL_ERR_VALIDATIONOVERALL_HELPCODE', $helpCode);

			$resetUrl = str_replace('&amp;', '&', \JRoute::_('index.php?option=com_akeebasubs&view=Level&layout=' . $layout . '&slug=' . $model->slug . '&reset=1'));
			$msg      .= ' ' . Text::sprintf('COM_AKEEBASUBS_LEVEL_ERR_VALIDATIONOVERALL_RESET', $resetUrl);


			$ret['info'] = $details;
			$this->enqueueMessage($msg, 'error');

			echo json_encode($ret);

			$this->container->platform->closeApplication();
		}

		$demoPayment = $this->container->params->get('demo_payment', 0);

		if ($newSubscription->gross_amount < 0.01)
		{
			// If the subscription is free redirect to the Thank You page immediately.
			$ret = [
				'method' => 'redirect',
				'url'    => Route::_('index.php?option=com_akeebasubs&view=Message&slug=' . $slug . '&layout=order&subid=' . $newSubscription->akeebasubs_subscription_id),
				'info'   => 'Free',
			];
		}
		elseif ($demoPayment)
		{
			// If it's a demo payment redirect to the callback page (which redirects us to Thank You)
			$ret = [
				'method' => 'redirect',
				'url'    => Route::_('index.php?option=com_akeebasubs&view=callback&alert_name=akeebasubs_none&passthrough=' . $newSubscription->getId()),
				'info'   => 'Demo payment',
			];
		}
		else
		{
			$ret['method'] = 'overlay';
			$ret['info']   = 'Regular payment';
			// TODO Go through the Paddle API to get the custom checkout URL.
		}

		// Finally, return the information back to the caller
		echo json_encode($ret);

		$this->container->platform->closeApplication();
	}

	/**
	 * Enqueue a Joomla application message and persist the queue in the session (because Joomla won't do that unless
	 * you redirect).
	 *
	 * @param   string  $message  The message to enqueue
	 * @param   string  $type     The message type, default is 'error'
	 *
	 * @throws \Exception
	 */
	private function enqueueMessage(string $message, string $type = 'error')
	{
		$app     = Factory::getApplication();
		$session = Factory::getSession();

		$app->enqueueMessage($message, $type);
		$session->set('application.queue', $app->getMessageQueue());
	}
}