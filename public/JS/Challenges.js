document.addEventListener('DOMContentLoaded', () => {

    /* =======================
       CSRF TOKEN (GLOBAL SAFE)
    ======================= */
    window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    if (!window.csrfToken) {
        console.error('CSRF token not found!');
    }


    /* =======================
       PANEL MANAGEMENT
    ======================= */
    window.openCreatePanel = function () {
        resetForm();
        document.getElementById('panelTitle').textContent = 'Create Challenge';
        document.getElementById('saveButtonText').textContent = 'Save Challenge';
        document.getElementById('createPanel').classList.remove('translate-x-full');
    };

    function openEditPanel() {
        document.getElementById('panelTitle').textContent = 'Edit Challenge';
        document.getElementById('saveButtonText').textContent = 'Update Challenge';
        document.getElementById('createPanel').classList.remove('translate-x-full');
    }

    window.closeCreatePanel = function () {
        document.getElementById('createPanel').classList.add('translate-x-full');
    };

    function resetForm() {
        const form = document.getElementById('challengeForm');
        form.reset();

        document.getElementById('challengeId').value = '';
        document.getElementById('unlockBadge').value = '0';
        document.getElementById('isActive').value = '1';

        updateBadgeToggle(false);
        updateActiveToggle(true);
    }


    /* =======================
       TOGGLES (FORM)
    ======================= */
    window.toggleBadge = function () {
        const input = document.getElementById('unlockBadge');
        input.value = input.value === '1' ? '0' : '1';
        updateBadgeToggle(input.value === '1');
    };

    function updateBadgeToggle(active) {
        const toggle = document.getElementById('badgeToggle');
        const knob = toggle.querySelector('span');

        toggle.classList.toggle('bg-primary', active);
        toggle.classList.toggle('bg-[#224932]', !active);

        knob.classList.toggle('left-7', active);
        knob.classList.toggle('left-1', !active);
    }

    window.toggleActive = function () {
        const input = document.getElementById('isActive');
        input.value = input.value === '1' ? '0' : '1';
        updateActiveToggle(input.value === '1');
    };

    function updateActiveToggle(active) {
        const toggle = document.getElementById('activeToggle');
        const knob = toggle.querySelector('span');

        toggle.classList.toggle('bg-primary', active);
        toggle.classList.toggle('bg-[#224932]', !active);

        knob.classList.toggle('right-1', active);
        knob.classList.toggle('left-1', !active);
    }


    /* =======================
       FREQUENCY MAPPING
    ======================= */
    function mapFrequency(value) {
        return {
            daily: 'Daily',
            weekly: 'Weekly',
            monthly: 'Monthly',
            quarterly: 'Quarterly',
            yearly: 'Yearly',
            one_time: 'One-Time'
        }[value] || 'Weekly';
    }

    function mapFrequencyToValue(value) {
        return {
            'daily': 'daily',
            'weekly': 'weekly',
            'monthly': 'monthly',
            'quarterly': 'quarterly',
            'yearly': 'yearly',
            'one-time': 'one_time'
        }[(value || '').toLowerCase()] || 'weekly';
    }


    /* =======================
       SAVE (CREATE / UPDATE)
    ======================= */
    window.saveChallenge = async function () {
        const form = document.getElementById('challengeForm');
        const formData = new FormData(form);
        const challengeId = document.getElementById('challengeId').value;

        const payload = {
            name: formData.get('name'),
            description: formData.get('description'),
            difficulty: formData.get('difficulty'),
            category: formData.get('category'),
            frequency: mapFrequency(formData.get('frequency')),
            target_value: formData.get('target_value'),
            duration: formData.get('duration'),
            icon: formData.get('icon') || '🎯',
            xp_reward: Number(formData.get('xp_reward')) || 0,
            unlock_badge: Number(formData.get('unlock_badge')),
            is_active: Number(formData.get('is_active')),
        };

        if (challengeId) payload._method = 'PUT';

        try {
            const response = await fetch(
                challengeId ? `/challenges/${challengeId}` : '/challenges',
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                }
            );

            const result = await response.json();

            if (!response.ok) {
                showNotification('Error', result.message || 'Validation failed', 'error');
                return;
            }

            showNotification('Success', 'Challenge saved successfully', 'success');
            closeCreatePanel();
            setTimeout(() => location.reload(), 700);
        } catch (error) {
            console.error('Save error:', error);
            showNotification('Error', 'Failed to save challenge', 'error');
        }
    };


    /* =======================
       EDIT CHALLENGE
    ======================= */
    window.editChallenge = async function (id) {
        try {
            const response = await fetch(`/challenges/${id}`, {
                headers: { 'Accept': 'application/json' }
            });

            const c = await response.json();

            document.getElementById('challengeId').value = c.id;
            document.getElementById('challengeName').value = c.name;
            document.getElementById('challengeDescription').value = c.description;
            document.getElementById('challengeDifficulty').value = c.difficulty;
            document.getElementById('challengeCategory').value = c.category;
            document.getElementById('challengeFrequency').value = mapFrequencyToValue(c.frequency);
            document.getElementById('challengeTarget').value = c.target_value || '';
            document.getElementById('challengeDuration').value = c.duration || '';
            document.getElementById('challengeIcon').value = c.icon || '🎯';
            document.getElementById('challengeXP').value = c.xp_reward;

            document.getElementById('unlockBadge').value = c.unlock_badge ? '1' : '0';
            document.getElementById('isActive').value = c.is_active ? '1' : '0';

            updateBadgeToggle(c.unlock_badge);
            updateActiveToggle(c.is_active);

            openEditPanel();
        } catch (error) {
            console.error('Edit error:', error);
            showNotification('Error', 'Failed to load challenge', 'error');
        }
    };


    /* =======================
       🔥 TOGGLE CHALLENGE STATUS (FIXED)
    ======================= */
    window.toggleChallengeStatus = async function (id) {
        console.log('Toggle called for ID:', id);

        const card = document.querySelector(`[data-challenge-id="${id}"]`);
        if (!card) {
            console.error('Card not found for ID:', id);
            showNotification('Error', 'Challenge card not found', 'error');
            return;
        }

        const button = card.querySelector('button[onclick*="toggleChallengeStatus"]');
        const statusWrap = card.querySelector('.flex.items-center.gap-2');
        const icon = button?.querySelector('.material-symbols-outlined');

        if (!button || !icon) {
            console.error('Button or icon not found', { button, icon });
            showNotification('Error', 'Button elements not found', 'error');
            return;
        }

        console.log('Elements found, making API call...');

        // Disable button during request
        button.disabled = true;
        button.classList.add('opacity-50', 'pointer-events-none');

        try {
            const response = await fetch(`/challenges/${id}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            console.log('Response status:', response.status);

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to toggle status');
            }

            const result = await response.json();
            console.log('Toggle result:', result);

            const isActive = result.is_active;

            // Update card data attribute
            card.dataset.status = isActive ? 'active' : 'inactive';

            // Update button icon and title
            icon.textContent = isActive ? 'power_settings_new' : 'play_arrow';
            button.title = isActive ? 'Disable' : 'Enable';

            // Update button hover classes
            button.classList.remove('hover:bg-red-500/20', 'hover:text-red-400', 'hover:bg-emerald-500/20', 'hover:text-emerald-400');
            if (isActive) {
                button.classList.add('hover:bg-red-500/20', 'hover:text-red-400');
            } else {
                button.classList.add('hover:bg-emerald-500/20', 'hover:text-emerald-400');
            }

            // Update card opacity
            if (isActive) {
                card.classList.remove('opacity-75', 'hover:opacity-100');
            } else {
                card.classList.add('opacity-75', 'hover:opacity-100');
            }

            // Update glow effect in background
            const glowDiv = card.querySelector('.absolute.top-0.right-0');
            if (glowDiv) {
                glowDiv.classList.remove('bg-slate-500/5', 'bg-primary/5');
                glowDiv.classList.add(isActive ? 'bg-primary/5' : 'bg-slate-500/5');
            }

            // Update XP reward color
            const xpReward = card.querySelector('.font-black.text-xl');
            if (xpReward) {
                xpReward.classList.remove('text-primary', 'text-white/50');
                xpReward.classList.add(isActive ? 'text-primary' : 'text-white/50');
            }

            // Update status indicator
            statusWrap.innerHTML = isActive
                ? `
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    <span class="text-sm text-white font-medium">Active</span>
                  `
                : `
                    <span class="h-3 w-3 rounded-full bg-slate-500"></span>
                    <span class="text-sm text-slate-400 font-medium">Inactive</span>
                  `;

            showNotification(
                'Success',
                isActive ? 'Challenge activated successfully' : 'Challenge deactivated successfully',
                'success'
            );

        } catch (err) {
            console.error('Toggle error:', err);
            showNotification('Error', err.message || 'Failed to update challenge status', 'error');
        } finally {
            button.disabled = false;
            button.classList.remove('opacity-50', 'pointer-events-none');
        }
    };


    /* =======================
       SEARCH FUNCTIONALITY
    ======================= */
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.challenge-card');

            cards.forEach(card => {
                const name = card.querySelector('h4').textContent.toLowerCase();
                const description = card.querySelector('.line-clamp-2').textContent.toLowerCase();

                if (name.includes(searchTerm) || description.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }


    /* =======================
       FILTER FUNCTIONALITY
    ======================= */
    window.filterChallenges = function(filter) {
        const cards = document.querySelectorAll('.challenge-card');
        const buttons = document.querySelectorAll('.filter-btn');

        // Update button styles
        buttons.forEach(btn => {
            if (btn.dataset.filter === filter) {
                btn.classList.add('bg-[#1A1D21]', 'text-white', 'border-primary/40', 'shadow-[0_0_10px_rgba(37,244,120,0.1)]');
                btn.classList.remove('text-[#90cba8]', 'border-transparent');
            } else {
                btn.classList.remove('bg-[#1A1D21]', 'text-white', 'border-primary/40', 'shadow-[0_0_10px_rgba(37,244,120,0.1)]');
                btn.classList.add('text-[#90cba8]', 'border-transparent');
            }
        });

        // Filter cards
        cards.forEach(card => {
            if (filter === 'all') {
                card.style.display = 'block';
            } else if (filter === 'active') {
                card.style.display = card.dataset.status === 'active' ? 'block' : 'none';
            } else if (filter === 'inactive') {
                card.style.display = card.dataset.status === 'inactive' ? 'block' : 'none';
            }
        });
    };


    /* =======================
       SORT FUNCTIONALITY
    ======================= */
    window.sortChallenges = function(sortBy) {
        const grid = document.getElementById('challengesGrid');
        const cards = Array.from(grid.querySelectorAll('.challenge-card'));

        cards.sort((a, b) => {
            const aXP = parseInt(a.querySelector('.font-black.text-xl').textContent.replace(/\D/g, ''));
            const bXP = parseInt(b.querySelector('.font-black.text-xl').textContent.replace(/\D/g, ''));
            return bXP - aXP; // Descending order
        });

        cards.forEach(card => grid.appendChild(card));
        showNotification('Sorted', 'Challenges sorted by XP', 'success');
    };


    /* =======================
       NOTIFICATION
    ======================= */
    function showNotification(title, message, type) {
        const note = document.createElement('div');
        note.className = `fixed top-4 right-4 z-50 bg-[#121417] border ${type === 'success' ? 'border-primary' : 'border-red-500'} rounded-xl p-4 shadow-2xl min-w-[300px] transition-opacity`;
        note.style.opacity = '0';
        note.innerHTML = `
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined ${type === 'success' ? 'text-primary' : 'text-red-500'}">
                    ${type === 'success' ? 'check_circle' : 'error'}
                </span>
                <div>
                    <strong class="text-white font-bold block mb-1">${title}</strong>
                    <span class="text-[#90cba8] text-sm">${message}</span>
                </div>
            </div>
        `;
        document.body.appendChild(note);

        // Animate in
        setTimeout(() => note.style.opacity = '1', 10);

        // Remove after 3 seconds
        setTimeout(() => {
            note.style.opacity = '0';
            setTimeout(() => note.remove(), 300);
        }, 3000);
    }

});
