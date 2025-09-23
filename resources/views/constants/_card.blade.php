{{-- resources/views/constants/_card.blade.php --}}
@php
  $brand = '#007C38';
@endphp

<a
  href="{{ $indexRoute }}"
  data-constant-card
  data-title="{{ strip_tags($title) }}"
  data-desc="{{ strip_tags($desc) }}"
  class="group border-0 rounded-2xl shadow-md overflow-hidden my-3 bg-white  shadow-sm transition 
         hover:-translate-y-0.5 hover:shadow-xl transition-all duration-300"
>
  <div class="h-1 w-full" style="background:#007C38;"></div>

  <div class="flex items-center gap-3 p-4">
    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[{{ $brand }}] text-white shrink-0">
      <img src="{{ asset('web-assets/dashboard/icons/' . $icon) }}"
           alt=""
           class="h-5 w-5 invert brightness-0 mix-blend-luminosity">
    </div>

    <div class="min-w-0">
      <div class="text-base text-lg  font-semibold text-slate-800 group-hover:text-[{{ $brand }}] truncate">
        {{ $title }}
      </div>
      <p class="mt-1 text-slate-500">{{ $desc }}</p>
    </div>
</div>

</a>
