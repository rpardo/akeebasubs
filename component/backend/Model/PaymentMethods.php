<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Admin\Model;

defined('_JEXEC') or die;

use FOF30\Model\Model;
use JFactory;
use JLoader;
use JPluginHelper;

class PaymentMethods extends Model
{
    /**
     * Gets a list of payment plugins and their titles
     *
     * @return  array
     */
	public function getPaymentPlugins()
	{
		JLoader::import('joomla.plugin.helper');
		JPluginHelper::importPlugin('akpayment');

		$jResponse = $this->container->platform->runPlugins('onAKPaymentGetIdentity', []);

		$ret = array();

		foreach ($jResponse as $item)
		{
			if (is_object($item))
			{
				$ret[] = $item;
			}
			elseif (is_array($item))
			{
				if (array_key_exists('name', $item))
				{
					$ret[] = (object)$item;
				}
				else
				{
					foreach ($item as $anItem)
					{
						if (is_object($anItem))
						{
							$ret[] = $anItem;
						}
						else
						{
							$ret[] = (object)$anItem;
						}
					}
				}
			}
		}

		return $ret; // name, title
	}

    /**
     * Fetches the payment processor used in the last complted subscription
     *
     * @param   int     $userid     User id
     *
     * @return string
     */
    public function getLastPaymentPlugin($userid)
    {
	    // No userid? Well, then there's no payment plugin
	    if (!$userid)
	    {
		    return '';
	    }

	    // Let's get the last completed transaction
	    /** @var Subscriptions $subscriptions */
	    $subscriptions = $this->getContainer()->factory->model('Subscriptions')->tmpInstance();
	    $rows          = $subscriptions->user_id($userid)
		    ->state('C')
		    ->limit(1)
		    ->filter_order('created_on')
		    ->filter_order_Dir('DESC')
		    ->get();

	    // No completed subscription? Then no payment plugin
	    if (!count($rows))
	    {
		    return '';
	    }

	    /** @var Subscriptions $last */
	    $last      = $rows->first();
	    $processor = $last->processor;

	    // No stored processor? Well, that's strange, but it could happen...
	    if (!$processor)
	    {
		    return '';
	    }

	    $plugins = $this->getPaymentPlugins();

	    foreach ($plugins as $plugin)
	    {
		    if ($plugin->name == $processor)
		    {
			    return $plugin->name;
		    }
	    }

	    return '';
    }
}
