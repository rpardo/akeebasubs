<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\Validation;

use Akeeba\Subscriptions\Site\Model\PaymentMethods;

defined('_JEXEC') or die;

class PaymentMethod extends Base
{
	/**
	 * Validate the Payment method
	 *
	 * @return  bool
	 */
	protected function getValidationResult()
	{
		$paymentmethod = trim($this->state->paymentmethod);

		// I have to access to the plugin params, so I have to load them all and pick the correct one
		/** @var PaymentMethods $pluginsModel */
		$pluginsModel = $this->container->factory->model('PaymentMethods')->tmpInstance();

		//First of all, let's get the whole list of plugins
		$country       = $this->state->country;
		$plugins       = $pluginsModel->getPaymentPlugins($country);
		$defaultMethod = null;

		foreach ($plugins as $plugin)
		{
			if (is_null($defaultMethod))
			{
				$defaultMethod = $plugin->name;
			}

			if (empty($paymentmethod))
			{
				$this->state->paymentmethod = $defaultMethod;
				$paymentmethod              = $this->state->paymentmethod;
			}

			// Did I find the payment method I was looking for? If so let's return true
			if ($plugin->name == $paymentmethod)
			{
				return true;
			}
		}

		// An invalid method was being used. Please use the default payment method instead.
		$this->state->paymentmethod = $defaultMethod;

		return true;
	}
}
