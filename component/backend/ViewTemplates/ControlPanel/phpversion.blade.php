<?php
/**
 *  @package AkeebaSubs
 *  @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 *  @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;
?>

@section('phpVersionWarning')
    {{-- Old PHP version reminder --}}
    @include('admin:com_akeebasubs/Common/phpversion_warning', [
        'softwareName'  => 'Akeeba Subscriptions',
        'minPHPVersion' => '7.3.0',
    ])
@stop
