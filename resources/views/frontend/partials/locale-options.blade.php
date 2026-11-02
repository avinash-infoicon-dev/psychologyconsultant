@php
  use App\Support\Locales;
  $selectedLocale = $selected ?? old('locale', config('app.locale'));
@endphp
@foreach (Locales::all() as $code => $meta)
  <option value="{{ $code }}" @selected($selectedLocale === $code)>{{ $meta['label'] }}</option>
@endforeach
