<script>
(function () {
    const heartbeatUrl = @json(route('session.heartbeat'));
    const loginUrl = @json(route('login', ['expired' => 1]));
    const wasAuthenticated = document.body?.dataset?.sessionAuth === '1';
    const staleAfterMs = 6 * 60 * 1000;
    let lastBeat = Date.now();
    let inFlight = null;
    let sentToLogin = false;

    function goToLogin() {
        if (sentToLogin) {
            return;
        }

        sentToLogin = true;
        window.location.href = loginUrl;
    }

    function applyCsrf(token) {
        if (!token) {
            return;
        }

        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) {
            meta.setAttribute('content', token);
        }

        document.querySelectorAll('input[name="_token"]').forEach((input) => {
            input.value = token;
        });
    }

    function heartbeat() {
        if (sentToLogin) {
            return Promise.resolve();
        }

        if (inFlight) {
            return inFlight;
        }

        inFlight = fetch(heartbeatUrl, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            cache: 'no-store',
        }).then((response) => {
            if (response.status === 401 || response.status === 419) {
                goToLogin();
                return null;
            }

            if (!response.ok) {
                return null;
            }

            return response.json();
        }).then((data) => {
            if (!data || sentToLogin) {
                return;
            }

            applyCsrf(data.csrf);
            lastBeat = Date.now();

            if (wasAuthenticated && data.authenticated === false) {
                goToLogin();
            }
        }).catch(() => {
            // Ignore network errors; the next submit/focus will retry.
        }).finally(() => {
            inFlight = null;
        });

        return inFlight;
    }

    function refreshIfStale() {
        if (document.visibilityState !== 'visible') {
            return;
        }

        if (Date.now() - lastBeat > staleAfterMs || inFlight) {
            heartbeat();
        }
    }

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            heartbeat();
        }
    });
    window.addEventListener('focus', refreshIfStale);
    setInterval(() => {
        if (document.visibilityState === 'visible') {
            heartbeat();
        }
    }, 5 * 60 * 1000);

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || sentToLogin) {
            if (sentToLogin) {
                event.preventDefault();
            }
            return;
        }

        if ((form.getAttribute('method') || 'GET').toUpperCase() === 'GET') {
            return;
        }

        if (form.dataset.sessionHeartbeat === 'done') {
            return;
        }

        const needsRefresh = inFlight || (Date.now() - lastBeat > staleAfterMs);
        if (!needsRefresh) {
            return;
        }

        event.preventDefault();
        heartbeat().finally(() => {
            if (sentToLogin) {
                return;
            }

            form.dataset.sessionHeartbeat = 'done';
            HTMLFormElement.prototype.submit.call(form);
        });
    }, true);

    document.addEventListener('livewire:init', () => {
        if (!window.Livewire) {
            return;
        }

        window.Livewire.hook('request', ({ fail }) => {
            fail(({ status, preventDefault }) => {
                if (status === 401 || status === 419) {
                    preventDefault();
                    goToLogin();
                }
            });
        });
    });
})();
</script>
