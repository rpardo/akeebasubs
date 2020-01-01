<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Joomla\CMS\Router\Route;defined('_JEXEC') or die();

/** @var \Akeeba\Subscriptions\Site\View\Subscriptions\Html $this */
?>

@if(empty($this->getContainer()->platform->getUser()->getParam('timezone', null)))
	<div class="akeeba-block--warning">
		<?php
		$user_id    = $this->getContainer()->platform->getUser()->id;
		$selfURL    = Route::_('index.php?option=com_akeebasubs&view=Subscriptions');
		$profileUrl = Route::_('index.php?option=com_users&task=profile.edit&user_id=' . $user_id . '&returnurl=' . base64_encode($selfURL));
		?>
		<h3>
			@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_TZWARNING_HEAD')
		</h3>
		<p>
			@sprintf(
				'COM_AKEEBASUBS_SUBSCRIPTIONS_TZWARNING_BODY',
				$this->getContainer()->params->get('offset', 'GMT'),
				$profileUrl
			)
		</p>
	</div>
@endif
