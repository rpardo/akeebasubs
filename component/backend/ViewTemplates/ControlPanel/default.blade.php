<?php
/**
 *  @package AkeebaSubs
 *  @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 *  @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;
?>

@include('admin:com_akeebasubs/ControlPanel/phpversion')
@include('admin:com_akeebasubs/ControlPanel/graphs')
@include('admin:com_akeebasubs/ControlPanel/stats')
@include('admin:com_akeebasubs/ControlPanel/geoip')

@yield('phpVersionWarning', '')

@yield('geoip', '')

<div class="akeeba-container--50-50">
    <div>
        @yield('graphs', '')
    </div>
    <div>
        @yield('stats', '')

        @modules('akeebasubscriptionsstats')
    </div>
</div>

<div class="akeeba-container--100">
    <div>
        @include('admin:com_akeebasubs/ControlPanel/footer')
        @yield('footer')
    </div>
</div>