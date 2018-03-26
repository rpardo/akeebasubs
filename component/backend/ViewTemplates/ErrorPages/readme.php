<?php defined('_JEXEC') or die; ?>

This folder contains error pages which are shown when the component cannot load at all. This happens when there are
libraries missing, your server configuration contains errors which are known to cause the component to fail or simply
when there is a catchable PHP error (this is the generic error handler which lets us provide more accurate support).

Since these files are loaded without being parsed by Joomla or our libraries they must be plain PHP (not Blade), they
must use inline CSS and/or render without any CSS present and they must be written in plain English (without going
through JText, in case using JText is what triggered an exception to begin with).

These files are loaded from the component's main .php file which can be found two directories up.
