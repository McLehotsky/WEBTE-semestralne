<section>
    <header>
        <h2 class="text-xl font-semibold text-amber-700 mb-1 flex gap-2">
            {{ __('profile.api.title') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('profile.api.subtitle') }}
        </p>
    </header>

    <div class="mt-6 space-y-6">
        <div>
            <x-input-label for="name" :value="__('profile.api.button.label')" />

            <div class="mt-1 flex items-center overflow-hidden rounded-lg border border-gray-300 bg-white">
                <!-- Tlačidlo -->
                <button id="generate-button" 
                data-url="{{ route('api-token.generate') }}" 
                class="shrink-0 z-10 inline-flex items-center py-2.5 px-4 text-sm font-medium text-white
           bg-amber-600 hover:bg-amber-700 border border-amber-700 rounded-s-lg
           focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-800
           active:bg-amber-900 transition">
                {{ __('profile.api.button.generate') }}
            </button>
            <!-- Input -->
                <div class="relative w-full">
                    <input placeholder="{{ __('profile.api.button.placeholder') }}" 
                    id="url-shortener" 
                    type="text" 
                    aria-describedby="helper-text-explanation" 
                    class="bg-white text-gray-700 border border-gray-300 
                            text-sm block w-full p-2.5 rounded-none 
                            focus:border-amber-700 focus:ring-amber-500 focus:ring focus:ring-offset-2 shadow-sm" 
                    value="{{ $apiToken ?? '' }}" 
                    readonly />
                </div>

                 <!-- Kopírovacie tlačidlo -->
                <button data-tooltip-target="tooltip-url-shortener" 
                data-copy-to-clipboard-target="url-shortener" 
                data-copy-to-clipboard="true" 
                class="shrink-0 z-10 inline-flex items-center py-3 px-4 text-sm font-medium 
                        text-amber-600 bg-gray-100 border border-gray-300 rounded-e-lg 
                        hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-amber-200 transition"
                type="button">
                    <span id="default-icon">
                        <svg class="w-4 h-4" 
                        aria-hidden="true" 
                        xmlns="http://www.w3.org/2000/svg" 
                        fill="currentColor" 
                        viewBox="0 0 18 20">
                            <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z"/>
                        </svg>
                    </span>
                    <span id="success-icon" 
                    class="hidden">
                        <svg class="w-4 h-4" 
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" 
                        fill="none" 
                        viewBox="0 0 16 12">
                            <path 
                            stroke="currentColor" 
                            stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="2" 
                            d="M1 5.917 5.724 10.5 15 1.5"/>
                        </svg>
                    </span>
                </button>
                <!-- Tooltip -->
                <div id="tooltip-url-shortener" 
                role="tooltip" 
                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                    <span id="default-tooltip-message">{{ __('profile.api.button.copy.tooltip') }}</span>
                    <span id="success-tooltip-message" class="hidden">{{ __('profile.api.button.copy.done') }}</span>
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
            </div>
        </div>
    </div>

</section>

@once
    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const translations = {
                    "profile.api.generate.text": "{{ __('profile.api.button.generate.text') }}"
                };

                const $generateButton = document.getElementById('generate-button');
                const $input = document.getElementById('url-shortener');
                const $defaultIcon = document.getElementById('default-icon');
                const $successIcon = document.getElementById('success-icon');
                const $defaultTooltipMessage = document.getElementById('default-tooltip-message');
                const $successTooltipMessage = document.getElementById('success-tooltip-message');

                if (!$generateButton) return;

                setTimeout(() => {
                    const clipboard = FlowbiteInstances.getInstance('CopyClipboard', 'url-shortener');
                    const tooltip = FlowbiteInstances.getInstance('Tooltip', 'tooltip-url-shortener');

                    const showSuccess = () => {
                        $defaultIcon?.classList.add('hidden');
                        $successIcon?.classList.remove('hidden');
                        $defaultTooltipMessage?.classList.add('hidden');
                        $successTooltipMessage?.classList.remove('hidden');
                        tooltip.show();
                    }

                    const resetToDefault = () => {
                        $defaultIcon?.classList.remove('hidden');
                        $successIcon?.classList.add('hidden');
                        $defaultTooltipMessage?.classList.remove('hidden');
                        $successTooltipMessage?.classList.add('hidden');
                        tooltip.hide();
                    }

                    clipboard.updateOnCopyCallback(() => {
                        showSuccess();
                        setTimeout(resetToDefault, 2000);
                    });
                }, 200);

                $generateButton.addEventListener('click', async () => {
                    try {
                        const originalText = $input.value;
                        $input.value = translations["profile.api.generate.text"];
                        const response = await fetch($generateButton.dataset.url, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                "Accept": "application/json"
                            },
                            body: JSON.stringify({})
                        });

                        if (!response.ok) throw new Error("Network error");

                        const data = await response.json();
                        if (data.token) {
                            $input.value = data.token;
                        }
                    } catch (err) {
                        console.error("Failed to generate token:", err);
                        $input.value = originalText;
                    }
                });
            });
        </script>
    @endpush
@endonce
