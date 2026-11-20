<x-layout :meta="$meta" page="stub">
    <x-page-head class="!min-h-[80svh] !justify-center" :tag="__('common.brand')" :title="__('site.pages.blog.title')" :lead="__('site.pages.blog.lead')">
        <div class="mt-10" data-reveal>
            <a href="{{ route('home') }}" class="cta">{{ __('common.back_home') }}</a>
        </div>
    </x-page-head>
</x-layout>
