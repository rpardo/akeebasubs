<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Admin\Form\Field;

use FOF30\Form\Field\Text;
use JText;

defined('_JEXEC') or die;

class PaymentStatus extends Text
{
	public function getRepeatable()
	{
		$state      = $this->value;
		$stateLower = strtolower($state);
		$stateLabel = htmlspecialchars(JText::_('COM_AKEEBASUBS_SUBSCRIPTION_STATE_' . $this->value));

		$processor    = htmlspecialchars($this->item->processor);
		$processorKey = htmlspecialchars($this->item->processor_key);

		$html = <<< HTML
<span class="akeebasubs-payment akeebasubs-payment-$stateLower hasTip"
	title="$stateLabel::$processor &bull; $processorKey">
</span>

<span class="akeebasubs-subscription-processor">
	$processor
</span>
HTML;

		if (!empty($this->item->ua))
		{
			$labelClass = $this->item->mobile ? 'label-success' : '';
			$iconClass  = $this->item->mobile ? 'icon-mobile' : 'icon-screen';
			$originText = htmlspecialchars(JText::_('COM_AKEEBASUBS_SUBSCRIPTIONS_UA'));
			$ua         = htmlspecialchars($this->item->ua);

			$html .= <<< HTML
<span class="akeebasubs-subscription-ua hasTip" title="$originText::$ua">
	<span class="label $labelClass">&nbsp;&nbsp;<span class="icon $iconClass" /></span>
</span>

HTML;

		}

		return $html;

	}
}
