// Test condition: the marker attribute proves the JS bundle is loaded via the Vite dev server
document.documentElement.dataset.jsLoaded = 'true';

// Auto-dismiss flash messages (progressive enhancement, see .alert[data-auto-dismiss])
document.querySelectorAll('.alert[data-auto-dismiss]').forEach((alert) => {
    setTimeout(() => alert.remove(), 4000);
});

/*
 * AJAX helpers (Fetch API) shared by the show-page modules.
 *
 * Thes helpers send the CSRF token that the introduced form carries,
 * so regular Laravel validation and middleware accept the request.
 */
async function sendFormUrlencoded(form, params) {
    const body = new URLSearchParams(new FormData(form));

    for (const [name, value] of Object.entries(params)) {
        body.set(name, value);
    }

    return fetch(form.action, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body,
    }).then((response) => response.json());
}

/* Insert an alert into the notifications region and schedule its
   removal, mirroring the server-rendered flash messages. */
function notify(message, variant) {
    const region = document.querySelector('[data-notifications]');

    if (!region) {
        return;
    }

    region.innerHTML = '';
    region.insertAdjacentHTML(
        'beforeend',
        `<div class="alert alert-${variant}" role="status">${message}</div>`,
    );

    window.setTimeout(() => region.innerHTML = '', 4000);
}

/* Escape a string so it can be interpolated into generated HTML. */
function escapeHtml(value) {
    const probe = document.createElement('span');

    probe.textContent = value;

    return probe.innerHTML;
}

/*
 * Stock control (delivery / purchase): upgrade the #stock-form pair of
 * buttons to background requests. The form keeps working without JS —
 * JS only prevents the default reload and speaks JSON with the server.
 */
document.querySelectorAll('form.stock-form').forEach((form) => {
    const quantity = document.querySelector('[data-quantity]');

    form.addEventListener('submit', async (event) => {
        const submitter = event.submitter;

        if (submitter) {
            event.preventDefault();
        } else {
            // No submitter (keyboard-less corner cases): reload normally.
            return;
        }

        const action = submitter.value;

        submitter.disabled = true;
        document.querySelectorAll(`form#stock-form [type=submit]`).forEach((button) => {
            button.disabled = true;
        });

        try {
            const response = await sendFormUrlencoded(form, { action });

            if (response.errors) {
                notify(escapeHtml(response.message ?? Object.values(response.errors)[0][0] ?? 'Failed.'), 'error');
                return;
            }

            if (quantity && response.quantity !== undefined) {
                quantity.textContent = `${response.quantity} pcs`;
            }

            notify(escapeHtml(response.status ?? 'Done.'), 'success');
        } catch (error) {
            notify('Could not reach the server.', 'error');
        } finally {
            document.querySelectorAll(`form#stock-form [type=submit]`).forEach((button) => {
                button.disabled = false;
            });
        }
    });
});

/*
 * Tag editor on the show view.
 *
 *  - typing queries existing tag names over the Fetch API and shows
 *    suggestions to click, with Enter picking the first one;
 *  - accepted tags become chips with an X button that removes them
 *    from the list without a page reload;
 *  - nothing hits the database until the user clicks Update, which
 *    performs a classic full-page PUT with all chips as tags[] inputs.
 */
document.querySelectorAll('[data-tag-editor]').forEach((editor) => {
    const list = editor.querySelector('[data-tag-list]');
    const input = editor.querySelector('[data-tag-input]');
    const suggestions = editor.querySelector('[data-tag-suggestions]');
    const searchUrl = editor.dataset.tagsSearchUrl ?? '/tags';

    let debounce = null;
    let pending = [];

    function chipExists(name) {
        return [...list.querySelectorAll('input[type=hidden][name="tags[]"]')]
            .some((field) => field.value === name);
    }

    function dismissSuggestions() {
        suggestions.hidden = true;
        suggestions.innerHTML = '';
        pending = [];
    }

    function addTag(name) {
        const clean = name.trim();

        if (clean === '' || chipExists(clean) || clean.length > 50) {
            dismissSuggestions();
            input.value = '';
            return;
        }

        const chip = document.createElement('li');
        chip.className = 'tag-chip';
        chip.dataset.tagChip = '';
        chip.innerHTML = `
            <span class="tag-name">${escapeHtml(clean)}</span>
            <button class="tag-remove" type="button" data-tag-remove
                    aria-label="Remove tag ${escapeHtml(clean)}">&times;</button>
            <input type="hidden" name="tags[]" value="${escapeHtml(clean)}">`;

        list.appendChild(chip);
        input.value = '';
        dismissSuggestions();
        input.focus();
    }

    async function lookUp(query) {
        const url = new URL(searchUrl, window.location.href);
        url.searchParams.set('query', query);

        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        }).then((res) => res.json());

        pending = (response.suggestions ?? []).filter((name) => name !== query && !chipExists(name));

        suggestions.innerHTML = '';

        if (pending.length === 0) {
            dismissSuggestions();
            return;
        }

        pending.forEach((name, index) => {
            const option = document.createElement('button');

            option.type = 'button';
            option.className = 'tag-suggestion';
            option.textContent = name;
            option.dataset.suggestion = '';
            if (index === 0) {
                option.dataset.primary = '';
            }
            option.addEventListener('click', () => addTag(name));

            const item = document.createElement('li');
            item.appendChild(option);
            suggestions.appendChild(item);
        });

        suggestions.hidden = false;
    }

    input.addEventListener('input', () => {
        clearTimeout(debounce);

        const query = input.value.trim();

        if (query === '') {
            dismissSuggestions();
            return;
        }

        debounce = window.setTimeout(() => lookUp(query), 150);
    });

    input.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter') {
            return;
        }

        event.preventDefault();

        // Enter completes the tag from the first suggestion, or adds
        // the typed text once the suggestions have arrived.
        if (pending.length > 0) {
            addTag(pending[0]);

            return;
        }

        if (input.value.trim() !== '') {
            addTag(input.value);
        }
    });

    editor.addEventListener('click', (event) => {
        const remove = event.target.closest('[data-tag-remove]');

        if (remove) {
            remove.closest('[data-tag-chip]').remove();
            dismissSuggestions();
        }
    });

    editor.addEventListener('focusout', (event) => {
        if (!editor.contains(event.relatedTarget)) {
            window.setTimeout(dismissSuggestions, 120);
        }
    });
});

// Dark mode toggle: flips the root theme attribute and remembers the choice
const themeToggle = document.querySelector('[data-theme-toggle]');

if (themeToggle) {
    themeToggle.addEventListener('click', () => {
        const next = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';

        document.documentElement.dataset.theme = next;

        try {
            localStorage.setItem('theme', next);
        } catch {
            // Private mode may block storage; the choice still applies for this page view
        }
    });
}
