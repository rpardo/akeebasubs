<?php
/**
 *  @package AkeebaSubs
 *  @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 *  @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

JHtml::_('behavior.tooltip');
JHtml::_('formbehavior.chosen', 'select');
?>

<?php echo $this->getRenderedForm(); ?>
