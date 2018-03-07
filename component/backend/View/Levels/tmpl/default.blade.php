<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\Subscriptions\Admin\Model\Levels;
use FOF30\Utils\FEFHelper\Html as FEFHtml;

defined('_JEXEC') or die();

/** @var  FOF30\View\DataView\Html  $this */
/** @var  Levels  $row */
$model = $this->getModel();
?>

@extends('admin:com_akeebasubs/Common/browse')

@section('browse-table-header')
<tr>
    <th width="20px">
        <a href="#" onclick="Joomla.tableOrdering('ordering','asc','');return false;" class="hasPopover" title="" data-content="Select to sort by this column" data-placement="top" data-original-title="Ordering"><i class="icon-menu-2"></i></a>
    </th>
    <th width="32">
        <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/>
    </th>
</tr>
@stop

@section('browse-table-body-withrecords')
    {{-- Table body shown when records are present. --}}
	<?php $i = 0; ?>
    @foreach($this->items as $row)
        <tr>
            <td>
			    <?php echo FEFHtml::dragDropReordering($this, 'ordering', $row->ordering)?>
            </td>
            <td>
			    <?php echo \JHtml::_('grid.id', ++$i, $row->id); ?>
            </td>

        </tr>
    @endforeach
@stop

@section('browse-table-record')
@stop
