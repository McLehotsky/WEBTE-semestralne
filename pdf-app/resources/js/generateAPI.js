window.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
            const clipboard = FlowbiteInstances.getInstance('CopyClipboard', 'url-shortener');
            const tooltip = FlowbiteInstances.getInstance('Tooltip', 'tooltip-url-shortener');

            const showSuccess = () => {
                $defaultIcon.classList.add('hidden');
                $successIcon.classList.remove('hidden');
                $defaultTooltipMessage.classList.add('hidden');
                $successTooltipMessage.classList.remove('hidden');
                tooltip.show();
            }

            const resetToDefault = () => {
                $defaultIcon.classList.remove('hidden');
                $successIcon.classList.add('hidden');
                $defaultTooltipMessage.classList.remove('hidden');
                $successTooltipMessage.classList.add('hidden');
                tooltip.hide();
            }

            clipboard.updateOnCopyCallback(() => {
                showSuccess();
                setTimeout(resetToDefault, 2000);
            });
        }, 2000);

    const $defaultIcon = document.getElementById('default-icon');
    const $successIcon = document.getElementById('success-icon');
    const $defaultTooltipMessage = document.getElementById('default-tooltip-message');
    const $successTooltipMessage = document.getElementById('success-tooltip-message');
    const $generateButton = document.getElementById('generate-button');
    const generateUrl = $generateButton.dataset.url;
    const $input = document.getElementById('url-shortener');
  

    $generateButton.addEventListener('click', async () => {
        try {
            const originalText = $input.value;
            $input.value = "Generating token...";
            const response = await fetch(generateUrl, {
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
            console.log("Response from backend: ", data);
            if (data.token) {
                $input.value = data.token;
            }
        } catch (err) {
            console.error("Failed to generate token:", err);
            $input.value = originalText;
        }
    });
});