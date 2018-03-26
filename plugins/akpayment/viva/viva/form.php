<?php defined('_JEXEC') or die(); ?>
<?php
$t1 = JText::_('COM_AKEEBASUBS_LEVEL_REDIRECTING_HEADER');
$t2 = JText::_('COM_AKEEBASUBS_LEVEL_REDIRECTING_BODY');
?>

<div class="akeeba-panel--info">
    <header class="akeeba-block-header">
        <h3><?php echo JText::_('COM_AKEEBASUBS_LEVEL_REDIRECTING_HEADER') ?></h3>
    </header>

    <p><?php echo JText::_('COM_AKEEBASUBS_LEVEL_REDIRECTING_BODY') ?></p>

    <form action="<?php echo htmlentities($url) ?>" method="post" id="paymentForm">
        <input type="submit" class="akeeba-btn--primary"/>
    </form>
</div>
