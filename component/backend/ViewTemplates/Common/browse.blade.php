<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 *
 * Template for Browse views
 *
 * Use this by extending it (I'm using -at- instead of the at-sign)
 * -at-extends('admin:com_akeebasubs/Common/browse')
 *
 * Override the following sections in your Blade template:
 *
 * browse-filters
 *      Filters to place above the table. They are placed inside an inline form. Wrap them in
 *      <div class="akeeba-filter-element akeeba-form-group">
 *
 * browse-table-header
 *      The table header. At the very least you need to add the table column headers. You can
 *      optionally add one or more <tr> with filters at the top.
 *
 * browse-table-body-withrecords
 *      ] Loop through the records and create <tr>s.
 *
 * browse-table-body-norecords
 *      [ Optional ] The <tr> to show when no records are present. Default is the "no records" text.
 *
 * browse-table-footer
 *      [ Optional ] The table footer. By default that's just the pagination footer.
 *
 * browse-hidden-fields
 *      [ Optional ] Any additional hidden INPUTs to add to the form. By default this is empty.
 *      The default hidden fields (option, view, task, ordering fields, boxchecked and token) can
 *      not be removed.
 *
 * Do not override any other section
 */

defined('_JEXEC') or die();

use FOF30\Utils\FEFHelper\Html as FEFHtml;

/** @var  FOF30\View\DataView\Html  $this */

// Make sure we have sort by fields. If they are not defined we determine them automatically.
if (!isset($this->lists->sortFields) || empty($this->lists->sortFields))
{
	$this->lists->sortFields = [];
	/** @var \FOF30\Model\DataModel $model */
	$model              = $this->getModel();
	$idField            = $model->getIdFieldName() ?: 'id';
	$defaultFieldLabels = [
		'publish_up'   => 'JGLOBAL_FIELD_PUBLISH_UP_LABEL',
		'publish_down' => 'JGLOBAL_FIELD_PUBLISH_DOWN_LABEL',
		'created_by'   => 'JGLOBAL_FIELD_CREATED_BY_LABEL',
		'created_on'   => 'JGLOBAL_FIELD_CREATED_LABEL',
		'modified_by'  => 'JGLOBAL_FIELD_MODIFIED_BY_LABEL',
		'modified_on'  => 'JGLOBAL_FIELD_MODIFIED_LABEL',
		'ordering'     => 'JGLOBAL_FIELD_FIELD_ORDERING_LABEL',
		'id'           => 'JGLOBAL_FIELD_ID_LABEL',
		'hits'         => 'JGLOBAL_HITS',
		'title'        => 'JGLOBAL_TITLE',
		'user_id'      => 'JGLOBAL_USERNAME',
		'username'     => 'JGLOBAL_USERNAME',
	];
	$componentName      = $this->getContainer()->componentName;
	$viewNameSingular   = $this->getContainer()->inflector->singularize($this->getName());
	$viewNamePlural     = $this->getContainer()->inflector->pluralize($this->getName());

	foreach ($model->getFields() as $field => $fieldDescriptor)
	{
		$possibleKeys = [
			$componentName . '_' . $viewNamePlural . '_FIELD_' . $field,
			$componentName . '_' . $viewNamePlural . '_' . $field,
			$componentName . '_' . $viewNameSingular . '_FIELD_' . $field,
			$componentName . '_' . $viewNameSingular . '_' . $field,
		];

		if (array_key_exists($field, $defaultFieldLabels))
		{
			$possibleKeys[] = $defaultFieldLabels[$field];
		}

		if ($field === $idField)
		{
			$possibleKeys[] = $defaultFieldLabels['id'];
		}

		$fieldLabel = '';

		foreach ($possibleKeys as $langKey)
		{
			$langKey    = strtoupper($langKey);
			$fieldLabel = JText::_($langKey);

			if ($fieldLabel !== $langKey)
			{
				unset($langKey);
				break;
			}

			$fieldLabel = '';
			unset($langKey);
		}

		if (!empty($fieldLabel))
		{
			$this->lists->sortFields[$field] = (new Joomla\Filter\InputFilter())->clean($fieldLabel);
		}

		unset ($possibleKeys, $fieldLabel);
	}
	unset($field, $label, $fieldDescriptor, $model, $defaultFieldLabels,
		$idField, $componentName, $viewNameSingular, $viewNamePlural);
}

$js = <<< JS

Joomla.orderTable = function()
{
		var table = document.getElementById("sortTable");
		var direction = document.getElementById("directionTable");
		var order = table.options[table.selectedIndex].value;
		var dirn = 'asc';

		if (order != '{$this->getModel()->getKeyName()}')
		{
			dirn = 'asc';
		}
		else {
			dirn = direction.options[direction.selectedIndex].value;
		}

		Joomla.tableOrdering(order, dirn);
	};
JS;

?>

@inlineJs($js)

@section('browse-filters')
{{-- Filters above the table. --}}
@stop

@section('browse-table-header')
{{-- Table header. Column headers and optional filters displayed above the column headers. --}}
@stop

@section('browse-table-body-norecords')
{{-- Table body shown when no records are present. --}}
<tr>
    <td colspan="99">
        @lang('COM_ARS_COMMON_NOITEMS_LABEL')
    </td>
</tr>
@stop

@section('browse-table-body-withrecords')
{{-- Table body shown when records are present. --}}
<?php $i = 0; ?>
@foreach($this->items as $row)
<tr>
    {{-- You need to implement me! --}}
</tr>
@endforeach
@stop

@section('browse-table-footer')
    {{-- Table footer. The default is showing the pagination footer. --}}
    <tr>
        <td colspan="11" class="center">
            {{ $this->pagination->getListFooter() }}
        </td>
    </tr>
@stop

@section('browse-hidden-fields')
    {{-- Put your additional hidden fields in this section --}}
@stop

{{-- Administrator form for browse views --}}
<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-form">
    {{-- Filters and ordering --}}
    <section class="akeeba-panel--33-66 akeeba-filter-bar-container">
        <div class="akeeba-filter-bar akeeba-filter-bar--left akeeba-form-section akeeba-form--inline">
            @yield('browse-filters')
        </div>

		<?php echo FEFHtml::selectOrderingBackend($this->getPagination(), $this->lists->sortFields, $this->lists->order, $this->lists->order_Dir)?>
    </section>

    <table class="akeeba-table akeeba-table--striped" id="itemsList">
        <thead>
        @yield('browse-table-header')
        </thead>
        <tfoot>
        @yield('browse-table-footer')
        </tfoot>
        <tbody>
        @unless(count($this->items))
        @yield('browse-table-body-norecords')
        @else
        @yield('browse-table-body-withrecords')
        @endunless
        </tbody>
    </table>

    {{-- Hidden form fields --}}
    <div class="akeeba-hidden-fields-container">
        @section('browse-default-hidden-fields')
            <input type="hidden" name="option" id="option" value="{{{ $this->getContainer()->componentName }}}"/>
            <input type="hidden" name="view" id="view" value="{{{ $this->getName() }}}"/>
            <input type="hidden" name="boxchecked" id="boxchecked" value="0"/>
            <input type="hidden" name="task" id="task" value="{{{ $this->getTask() }}}"/>
            <input type="hidden" name="filter_order" id="filter_order" value="{{{ $this->lists->order }}}"/>
            <input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="{{{ $this->lists->order_Dir }}}"/>
            <input type="hidden" name="@token()" value="1"/>
        @show
        @yield('browse-hidden-fields')
    </div>
</form>
