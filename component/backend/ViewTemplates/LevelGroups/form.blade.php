<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 *
 * @var  FOF30\View\DataView\Html $this
 * @var  LevelGroups              $model
 */

use \Akeeba\Subscriptions\Site\Model\LevelGroups;

defined('_JEXEC') or die();

$model = $this->getModel();
?>

@extends('admin:com_akeebasubs/Common/edit')

@section('edit-form-body')
    <div class="akeeba-form-group">
        <label for="title">
            @fieldtitle('title')
        </label>
        <input type="text" class="title" name="title" id="title" value="{{{ $item->title }}}">
    </div>

    <div class="akeeba-form-group">
        <label for="enabled">
            @lang('JPUBLISHED')
        </label>
        @jhtml('FEFHelper.select.booleanswitch', 'enabled', $item->enabled)
    </div>
@stop
