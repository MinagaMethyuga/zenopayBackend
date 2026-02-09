// public/JS/Challenges.js

function $(id) {
    return document.getElementById(id);
}

function csrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

window.openCreatePanel = function openCreatePanel() {
    const panel = $('createPanel');
    if (!panel) return;

    // Reset panel title + button text (guarded)
    const title = $('panelTitle');
    if (title) title.textContent = 'Create Challenge';

    const btnText = $('saveButtonText');
    if (btnText) btnText.textContent = 'Save Challenge';

    // Reset form fields
    const form = $('challengeForm');
    if (form) {
        form.setAttribute('action', form.getAttribute('action') || '');
        form.reset();
    }

    if ($('challengeId')) $('challengeId').value = '';

    // Win condition defaults
    if ($('winConditionType')) $('winConditionType').value = '';
    if ($('winConditionCategory')) $('winConditionCategory').value = '';
    if ($('winConditionMinAmount')) $('winConditionMinAmount').value = '';
    if ($('winConditionSumAmount')) $('winConditionSumAmount').checked = false;

    // Default toggles
    if ($('isActive')) $('isActive').value = '1';
    if ($('unlockBadge')) $('unlockBadge').value = '0';

    // Ensure badge UI hides + clears preview
    if (window.setBadgeUI) window.setBadgeUI(false, '');

    // Open panel
    panel.classList.remove('translate-x-full');
    panel.classList.add('translate-x-0');
};

window.closeCreatePanel = function closeCreatePanel() {
    const panel = $('createPanel');
    if (!panel) return;
    panel.classList.add('translate-x-full');
    panel.classList.remove('translate-x-0');
};

window.toggleActive = function toggleActive() {
    const hidden = $('isActive');
    const toggle = $('activeToggle');
    if (!hidden || !toggle) return;

    const isOn = hidden.value === '1';
    hidden.value = isOn ? '0' : '1';

    // keep same UI style (just flip knob)
    const knob = toggle.querySelector('span');
    if (!knob) return;

    if (hidden.value === '1') {
        toggle.classList.add('bg-primary');
        knob.classList.add('right-1');
        knob.classList.remove('left-1');
    } else {
        toggle.classList.remove('bg-primary');
        knob.classList.add('left-1');
        knob.classList.remove('right-1');
    }
};

window.saveChallenge = async function saveChallenge() {
    const form = $('challengeForm');
    if (!form) return;

    if (!form.reportValidity()) return;

    const action = form.getAttribute('action');
    if (!action) {
        console.error('Form action missing');
        return;
    }

    const fd = new FormData(form);

    try {
        const res = await fetch(action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken(),
                'Accept': 'application/json'
            },
            body: fd,
            credentials: 'same-origin'
        });

        if (res.status === 419) {
            alert('CSRF token mismatch. Refresh the page and try again.');
            return;
        }

        if (!res.ok) {
            const text = await res.text();
            console.error('Save failed:', res.status, text);
            alert('Failed to save. Check console.');
            return;
        }

        window.location.reload();
    } catch (err) {
        console.error(err);
        alert('Network error while saving.');
    }
};

// Called by edit icon
window.editChallenge = async function editChallenge(id) {
    const panel = $('createPanel');
    if (!panel) return;

    // Set panel title + button text
    const title = $('panelTitle');
    if (title) title.textContent = 'Edit Challenge';

    const btnText = $('saveButtonText');
    if (btnText) btnText.textContent = 'Update Challenge';

    // Open panel
    panel.classList.remove('translate-x-full');
    panel.classList.add('translate-x-0');

    try {
        const res = await fetch(`/challenges/${id}`, {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin'
        });

        if (!res.ok) {
            const t = await res.text();
            console.error('Fetch challenge failed:', res.status, t);
            alert('Failed to load challenge.');
            return;
        }

        const data = await res.json();

        if ($('challengeId')) $('challengeId').value = data.id ?? '';
        if ($('challengeName')) $('challengeName').value = data.name ?? '';
        if ($('challengeDescription')) $('challengeDescription').value = data.description ?? '';
        if ($('challengeDifficulty')) $('challengeDifficulty').value = data.difficulty ?? 'Easy';
        if ($('challengeCategory')) $('challengeCategory').value = data.category ?? 'Savings';

        // frequency mapping (DB might store "Daily" but UI options are lowercase)
        if ($('challengeFrequency')) {
            const freq = (data.frequency || '').toLowerCase().replace('-', '_');
            $('challengeFrequency').value = freq || 'weekly';
        }

        if ($('challengeTarget')) $('challengeTarget').value = data.target_value ?? '';
        if ($('challengeDuration')) $('challengeDuration').value = data.duration ?? '';
        if ($('challengeIcon')) $('challengeIcon').value = data.icon ?? '';
        if ($('challengeXP')) $('challengeXP').value = data.xp_reward ?? 0;

        // Win condition fields
        var wc = data.win_conditions || {};
        if ($('winConditionType')) $('winConditionType').value = wc.transaction_type ?? '';
        if ($('winConditionCategory')) $('winConditionCategory').value = wc.transaction_category ?? '';
        if ($('winConditionMinAmount')) $('winConditionMinAmount').value = wc.min_amount ?? '';
        if ($('winConditionSumAmount')) $('winConditionSumAmount').checked = !!wc.sum_amount;

        if ($('isActive')) $('isActive').value = data.is_active ? '1' : '0';

        // 🔥 IMPORTANT: show/hide upload section + preview on edit
        if (window.setBadgeUI) {
            window.setBadgeUI(!!data.unlock_badge, data.badge_image_url || '');
        } else if ($('unlockBadge')) {
            $('unlockBadge').value = data.unlock_badge ? '1' : '0';
        }

        // Set form action to update endpoint (POST + _method=PUT)
        const form = $('challengeForm');
        if (form) {
            form.setAttribute('action', `/challenges/${id}`);

            // ensure method spoof exists
            let methodInput = form.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                form.appendChild(methodInput);
            }
            methodInput.value = 'PUT';
        }
    } catch (err) {
        console.error(err);
        alert('Network error while loading challenge.');
    }
};

window.toggleChallengeStatus = async function toggleChallengeStatus(id) {
    try {
        const res = await fetch(`/challenges/${id}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken(),
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        });

        if (!res.ok) {
            const t = await res.text();
            console.error('Toggle status failed:', res.status, t);
            alert('Failed to toggle status.');
            return;
        }

        window.location.reload();
    } catch (err) {
        console.error(err);
        alert('Network error while toggling status.');
    }
};

// If your UI uses filters/sorts (safe no-ops if not implemented)
window.filterChallenges = window.filterChallenges || function () {};
window.sortChallenges = window.sortChallenges || function () {};
