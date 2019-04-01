<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Admin\Form\Field;

use FOF30\Form\Field\Text;
use JText;

defined('_JEXEC') or die;

class LevelPrice extends Text
{
	public function getRepeatable()
	{
		$currencyPos = $this->form->getContainer()->params->get('currencypos', 'before');
		$currencySymbol = $this->form->getContainer()->params->get('currencysymbol', '€');

		// Initialise
		$class             = $this->id;

		// Get field parameters
		if ($this->element['class'])
		{
			$class = (string) $this->element['class'];
		}

		// Start the HTML output
		$html = '<span class="' . $class . '">';

		// First line: regular price
		if ($currencyPos == 'before')
		{
			$html .= $currencySymbol;
		}

		$html .= ' ' . sprintf('%02.02f', (float)$this->value) . ' ';

		if ($currencyPos == 'after')
		{
			$html .= $currencySymbol;
		}

		// End the HTML output
		$html .= '</span>';

		return $html;
	}
}
