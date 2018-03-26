<?php
/**
 * @package        akeebasubs
 * @copyright      Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

use Akeeba\Subscriptions\Site\Model\Levels;
use Akeeba\Subscriptions\Site\Model\Subscriptions;
use FOF30\Container\Container;

defined('_JEXEC') or die();

/**
 * Google Analytics e-commerce integration for Akeeba Subscriptions.
 *
 * This plugin implements the analytics.js method. It won't work with gtag.js (tag manager).
 *
 * The code is only added in the thank-you page, e.g. something like
 * http://www.example.com/index.php?option=com_akeebasubs&view=Message&task=thankyou&slug=SILVER&subid=3
 */
class plgAkeebasubsGacommerce extends JPlugin
{
	/**
	 * The DI container for Akeeba Subscriptions
	 *
	 * @var  Container
	 */
	protected $container = null;

	/**
	 * Public constructor
	 *
	 * @param   object $subject        The object to observe
	 * @param   array  $config         An optional associative array of configuration settings.
	 *                                 Recognized key values include 'name', 'group', 'params', 'language',
	 *                                 'templatePath' (this list is not meant to be comprehensive).
	 */
	public function __construct(&$subject, $config = [])
	{
		if (!array_key_exists('params', $config))
		{
			$config['params'] = new JRegistry('');
		}

		if (!is_object($config['params']))
		{
			$config['params'] = new JRegistry($config['params']);
		}

		parent::__construct($subject, $config);

		$name = $config['name'];

		// Load the language files
		$jLanguage = JFactory::getLanguage();
		$jLanguage->load('plg_akeebasubs_' . $name, JPATH_ADMINISTRATOR, 'en-GB', true);
		$jLanguage->load('plg_akeebasubs_' . $name, JPATH_ADMINISTRATOR, $jLanguage->getDefault(), true);
		$jLanguage->load('plg_akeebasubs_' . $name, JPATH_ADMINISTRATOR, null, true);

		// Load the container
		$this->container = Container::getInstance('com_akeebasubs');
	}

	/**
	 * Add the Google Analytics code to track the subscription on the order success page.
	 *
	 * @param   Subscriptions $subscription
	 *
	 * @return  void
	 *
	 * @see https://developers.google.com/analytics/devguides/collection/analyticsjs/ecommerce#transaction
	 */
	public function onOrderMessage($subscription)
	{
		// Get the subscription level (we need it for the level's name)
		$level    = $subscription->level;

		// If the subscription level was not available as a relationship get it the hard way.
		if (!is_object($level) || is_null($level) || !($level instanceof Levels))
		{
			/** @var Levels $level */
			$level = $this->container->factory->model('Levels')->tmpInstance();

			try
			{
				$level->findOrFail($subscription->akeebasubs_level_id);
			}
			catch (Exception $e)
			{
				// Dunno what happened here. Maybe abort instead of embarassing ourselves?
				return;
			}
		}

		// Get the local currency code such as EUR, USD etc. This is passed to Google Analytics.
		$currencyCode = $this->container->params->get('currency', 'EUR');

		/**
		 * Create the Google Analytics for E-Commerce integration code.
		 *
		 * Note: the ";//" is intentionally there to prevent badly written 3PD plugins without a trailing line in the
		 * JavaScript from causing an error with our code.
		 */
		$js = <<<JS
;//

var akeebaSubscriptionsGAEcommerceDone = false;
var akeebaSubscriptionsGAEcommerceTimerID = null;

function akeebasubs_gacommerce_submit()
{
    if (akeebaSubscriptionsGAEcommerceDone === true) {
        console.log("AkeebaSubs GACommerce: Abort run; already done");
        
        if ((akeebaSubscriptionsGAEcommerceTimerID !== null) && (akeebaSubscriptionsGAEcommerceTimerID > 0))  {
        	clearInterval(akeebaSubscriptionsGAEcommerceTimerID);
        }
        
        return;
    }
    
    if (typeof ga !== 'function') {
        console.log("AkeebaSubs GACommerce: Postpone run; ga.js not loaded yet");
        
        return;
    }
    
    console.log("AkeebaSubs GACommerce: Submitting e-commerce information using ga.js");
    akeebaSubscriptionsGAEcommerceDone = true;
    clearInterval(akeebaSubscriptionsGAEcommerceTimerID);
    
    ga('require', 'ecommerce');
    ga('ecommerce:addTransaction', {
        'id': '{$subscription->akeebasubs_subscription_id}',
        'revenue': '{$subscription->gross_amount}',
        'tax': '{$subscription->tax_amount}',
        'currency': '$currencyCode'
    });
    ga('ecommerce:addItem', {
        'id': '{$subscription->akeebasubs_subscription_id}',
        'name': '{$level->title}',
        'sku': '$subscription->akeebasubs_level_id',
        'price': '{$level->price}',
        'currency': '$currencyCode'
    });
    ga('ecommerce:send');
}

window.jQuery(document).ready(function() {
	akeebaSubscriptionsGAEcommerceTimerID = window.setInterval(akeebasubs_gacommerce_submit, 250);
});



JS;

		// Add the JS code to the page. And we're done, kind sir!
		$this->container->template->addJSInline($js);
	}
}
