@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'logo')
<img src="{{ asset('img/logo.png') }}" class="logo" alt="smk xaverius logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
