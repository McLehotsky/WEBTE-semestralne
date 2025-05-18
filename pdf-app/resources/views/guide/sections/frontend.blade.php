<section class="max-w-3xl mx-auto p-6 bg-white shadow-sm rounded-md">
    <h2 class="text-xl font-semibold text-amber-700 border-b mb-4  flex items-center justify-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
        </svg> {{__('guide.section.title')}}</h2>
    <p class="text-gray-700 mb-6 text-center">
        {{__('guide.section.subtitle')}}
    </p>
    <div class="space-y-6 text-gray-800">
        
        <div>
            <h3 class="text-lg font-semibold text-amber-700 mb-1 flex gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                {{__('guide.login.title')}}</h3>
            <p>{{__('guide.login.subtitle')}}</p>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-amber-700 mb-1 flex gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                {{__('guide.home.title')}}</h3>
            <p>{{__('guide.home.subtitle')}}</p>
            <ul class="list-disc pl-6 mt-1">
                <li><span class="font-semibold text-amber-500">{{ __('guide.extract.title') }}:</span> {{ __('guide.extract.subtitle') }}</li>
                <li><span class="font-semibold text-amber-500">{{ __('guide.merge.title') }}:</span> {{ __('guide.merge.subtitle') }}</li>
                <li><span class="font-semibold text-amber-500">{{ __('guide.split.title') }}:</span> {{ __('guide.split.subtitle') }}</li>
                <li><span class="font-semibold text-amber-500">{{ __('guide.encrypt.title') }}:</span> {{ __('guide.encrypt.subtitle') }}</li>
                <li><span class="font-semibold text-amber-500">{{ __('guide.decrypt.title') }}:</span> {{ __('guide.decrypt.subtitle') }}</li>
                <li><span class="font-semibold text-amber-500">{{ __('guide.delete.title') }}:</span> {{ __('guide.delete.subtitle') }}</li>
                <li><span class="font-semibold text-amber-500">{{ __('guide.rotate.title') }}:</span> {{ __('guide.rotate.subtitle') }}</li>
                <li><span class="font-semibold text-amber-500">{{ __('guide.reorder.title') }}:</span> {{ __('guide.reorder.subtitle') }}</li>
                <li><span class="font-semibold text-amber-500">{{ __('guide.add-page.title') }}:</span> {{ __('guide.add-page.subtitle') }}</li>
                <li><span class="font-semibold text-amber-500">{{ __('guide.extract-text.title') }}:</span> {{ __('guide.extract-text.subtitle') }}</li>

            </ul>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-amber-700 mb-1 flex gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
                {{__('guide.docs.title')}}</h3>
            <p>{{__('guide.docs.subtitle')}}</p>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-amber-700 mb-1 flex gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                {{__('guide.history.title')}}</h3>
            <p>{{__('guide.history.subtitle')}}</p>
        </div>
    </div>
</section>