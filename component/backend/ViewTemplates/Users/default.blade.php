<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 *
 * @var  FOF30\View\DataView\Html $this
 * @var  Users                    $row
 * @var  Users                    $model
 */

use Akeeba\Subscriptions\Site\Model\Users;
use FOF30\Utils\FEFHelper\BrowseView;
use FOF30\Utils\SelectOptions;

defined('_JEXEC') or die();

$model = $this->getModel();
?>

@extends('admin:com_akeebasubs/Common/browse')

@section('browse-filters')
    <div class="akeeba-filter-element akeeba-form-group">
        @searchfilter('username')
    </div>
    <div class="akeeba-filter-element akeeba-form-group">
        @searchfilter('name')
    </div>
    <div class="akeeba-filter-element akeeba-form-group">
        @searchfilter('email')
    </div>
    <div class="akeeba-filter-element akeeba-form-group">
        @searchfilter('businessname')
    </div>
    <div class="akeeba-filter-element akeeba-form-group">
        @searchfilter('vatnumber')
    </div>
@stop

@section('browse-table-header')
    <tr>
        {{-- Row select --}}
        <th>
            @jhtml('FEFHelper.browse.checkall')
        </th>
        <th>
            @sortgrid('user_id', 'JGLOBAL_NUM')
        </th>
        <th>
            @fieldtitle('name')
        </th>
        <th>
            @fieldtitle('email')
        </th>
        <th>
            @sortgrid('businessname')
        </th>
        <th>
            @sortgrid('vatnumber')
        </th>
    </tr>
@stop

@section('browse-table-body-withrecords')
    {{-- Table body shown when records are present. --}}
	<?php $i = 0; ?>
    @foreach($this->items as $row)
		<?php $editUrl = JRoute::_('index.php?option=com_akeebasubs&view=Users&task=edit&id=' . (int) $row->getId()) ?>
        <tr>
            <td>
                @jhtml('FEFHelper.browse.id', ++$i, $row->getId())
            </td>
            <td>
                <a href="{{ $editUrl }}">
                    {{{ $row->getId() }}}
                </a>
            </td>
            <td>
                @include('admin:com_akeebasubs/Common/ShowUser', ['item' => $row, 'field' => 'user_id', 'link_url' => $editUrl])
            </td>
            <td>
                <a href="{{ $editUrl }}">
                    {{{ $row->user->email or '&mdash;&mdash;&mdash;'  }}}
                </a>
            </td>
            <td>
                @unless(empty($row->businessname))
                    <a href="{{ $editUrl }}">
                        {{{ $row->businessname }}}
                    </a>
                @else
                    &mdash;&mdash;&mdash;
                @endunless
            </td>
            <td>
                @unless(empty($row->vatnumber))
                    <a href="{{ $editUrl }}">
                        {{{ $row->country }}} {{{ $row->vatnumber }}}
                    </a>
                @else
                    &mdash;&mdash;&mdash;
                @endunless
            </td>
        </tr>
    @endforeach
@stop
