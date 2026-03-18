/**
 * FormPilot — Universal Form Capture Snippet
 * Add this script to any page to automatically capture form submissions.
 *
 * Usage:
 *   <script src="https://yourapp.com/js/formpilot.js" data-fp-key="YOUR_API_KEY"></script>
 */
(function() {
    'use strict';

    // Get configuration from script tag
    var script = document.currentScript || document.querySelector('script[data-fp-key]');
    if (!script) return;

    var apiKey = script.getAttribute('data-fp-key');
    var endpoint = script.getAttribute('data-fp-endpoint') || script.src.replace('/js/formpilot.js', '/api/f/');
    var successMsg = script.getAttribute('data-fp-success') || 'Thank you! Your form has been submitted.';
    var errorMsg = script.getAttribute('data-fp-error') || 'Something went wrong. Please try again.';

    if (!apiKey) {
        console.warn('FormPilot: Missing data-fp-key attribute');
        return;
    }

    // Collect UTM parameters
    function getUTMParams() {
        var params = {};
        var search = window.location.search;
        ['utm_source', 'utm_medium', 'utm_campaign'].forEach(function(key) {
            var match = search.match(new RegExp('[?&]' + key + '=([^&]*)'));
            if (match) params[key] = decodeURIComponent(match[1]);
        });
        return params;
    }

    // Intercept all forms on the page
    document.addEventListener('submit', function(e) {
        var form = e.target;

        // Skip forms that opt out
        if (form.getAttribute('data-fp-ignore') === 'true') return;

        e.preventDefault();

        var formData = new FormData(form);
        var data = {};

        formData.forEach(function(value, key) {
            data[key] = value;
        });

        // Add tracking data
        var utm = getUTMParams();
        data.page_url = window.location.href;
        data.referrer = document.referrer;
        if (utm.utm_source) data.utm_source = utm.utm_source;
        if (utm.utm_medium) data.utm_medium = utm.utm_medium;
        if (utm.utm_campaign) data.utm_campaign = utm.utm_campaign;

        // Submit to FormPilot
        fetch(endpoint + apiKey, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(result) {
            if (result.success) {
                // Show success message
                var msgEl = document.createElement('div');
                msgEl.style.cssText = 'position:fixed;top:20px;right:20px;background:#10b981;color:#fff;padding:16px 24px;border-radius:8px;z-index:99999;font-family:sans-serif;box-shadow:0 4px 12px rgba(0,0,0,0.15);';
                msgEl.textContent = successMsg;
                document.body.appendChild(msgEl);
                setTimeout(function() { msgEl.remove(); }, 4000);
                form.reset();

                // Custom redirect if specified
                var redirect = form.getAttribute('data-fp-redirect');
                if (redirect) window.location.href = redirect;
            } else {
                showError();
            }
        })
        .catch(function() {
            showError();
        });

        function showError() {
            var msgEl = document.createElement('div');
            msgEl.style.cssText = 'position:fixed;top:20px;right:20px;background:#ef4444;color:#fff;padding:16px 24px;border-radius:8px;z-index:99999;font-family:sans-serif;box-shadow:0 4px 12px rgba(0,0,0,0.15);';
            msgEl.textContent = errorMsg;
            document.body.appendChild(msgEl);
            setTimeout(function() { msgEl.remove(); }, 4000);
        }
    }, true);
})();
