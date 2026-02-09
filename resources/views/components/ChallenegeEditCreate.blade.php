<div id="createPanel" class="absolute inset-y-0 right-0 w-full md:w-[480px] bg-[#121417] shadow-[-10px_0_30px_rgba(0,0,0,0.8)] border-l border-[#224932] transform translate-x-full transition-transform duration-300 flex flex-col z-50">
    <div class="flex items-center justify-between px-6 py-5 border-b border-[#224932]">
        <h3 id="panelTitle" class="text-white text-xl font-bold">Create Challenge</h3>
        <button type="button" onclick="closeCreatePanel()" class="size-8 flex items-center justify-center rounded-full hover:bg-white/10 text-white/70 hover:text-white transition-colors">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>

    {{-- ✅ IMPORTANT: method + action must exist --}}
    <form
        id="challengeForm"
        method="POST"
        action="{{ route('challenges.store') }}"
        enctype="multipart/form-data"
        class="flex-1 overflow-y-auto p-6 flex flex-col gap-6"
    >
        @csrf
        <input type="hidden" id="challengeId" name="id">

        <div class="flex flex-col gap-4">
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold uppercase text-[#90cba8] tracking-wider">Challenge Name</label>
                <input id="challengeName" name="name" required class="w-full bg-[#0D0F10] border border-[#224932] rounded-lg px-4 py-3 text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none placeholder-[#3d5e4a]" placeholder="Ex: 'Save 500 LKR'" type="text"/>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold uppercase text-[#90cba8] tracking-wider">Short Description</label>
                <textarea id="challengeDescription" name="description" required class="w-full bg-[#0D0F10] border border-[#224932] rounded-lg px-4 py-3 text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none placeholder-[#3d5e4a] h-24 resize-none" placeholder="Describe the challenge goal..."></textarea>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold uppercase text-[#90cba8] tracking-wider">Difficulty</label>
                <div class="relative">
                    <select id="challengeDifficulty" name="difficulty" required class="w-full bg-[#0D0F10] border border-[#224932] rounded-lg px-4 py-3 text-white appearance-none focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                        <option value="Easy">Easy</option>
                        <option value="Medium">Medium</option>
                        <option value="Hard">Hard</option>
                        <option value="Expert">Expert</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-[#90cba8] pointer-events-none">expand_more</span>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold uppercase text-[#90cba8] tracking-wider">Category</label>
                <div class="relative">
                    <select id="challengeCategory" name="category" required class="w-full bg-[#0D0F10] border border-[#224932] rounded-lg px-4 py-3 text-white appearance-none focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                        <option value="Income">Income</option>
                        <option value="Savings">Savings</option>
                        <option value="Budgeting">Budgeting</option>
                        <option value="Investing">Investing</option>
                        <option value="Learning">Learning</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-[#90cba8] pointer-events-none">expand_more</span>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-2">
            <label class="text-xs font-bold uppercase text-[#90cba8] tracking-wider">Frequency</label>
            <div class="relative">
                <select id="challengeFrequency" name="frequency" required class="w-full bg-[#0D0F10] border border-[#224932] rounded-lg px-4 py-3 text-white appearance-none focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="yearly">Yearly</option>
                    <option value="one_time">One Time</option>
                </select>

                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-[#90cba8] pointer-events-none">expand_more</span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold uppercase text-[#90cba8] tracking-wider">Target Value</label>
                <input id="challengeTarget" name="target_value" class="w-full bg-[#0D0F10] border border-[#224932] rounded-lg px-4 py-3 text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none placeholder-[#3d5e4a]" placeholder="500 LKR" type="text"/>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold uppercase text-[#90cba8] tracking-wider">Duration</label>
                <input id="challengeDuration" name="duration" class="w-full bg-[#0D0F10] border border-[#224932] rounded-lg px-4 py-3 text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none placeholder-[#3d5e4a]" placeholder="7 Days" type="text"/>
            </div>
        </div>

        {{-- Win condition: which transactions count toward progress --}}
        <div class="p-4 rounded-xl bg-[#0D0F10] border border-[#224932] flex flex-col gap-4">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">flag</span>
                <span class="text-white font-bold">Win Condition</span>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold uppercase text-[#90cba8] tracking-wider">Transaction Type</label>
                    <select id="winConditionType" name="win_condition_transaction_type" class="w-full bg-[#121417] border border-[#224932] rounded-lg px-4 py-3 text-white focus:ring-2 focus:ring-primary outline-none">
                        <option value="">—</option>
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                    </select>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold uppercase text-[#90cba8] tracking-wider">Category (match)</label>
                    <input id="winConditionCategory" name="win_condition_transaction_category" class="w-full bg-[#121417] border border-[#224932] rounded-lg px-4 py-3 text-white focus:ring-2 focus:ring-primary outline-none placeholder-[#3d5e4a]" placeholder="e.g. Allowance" type="text" maxlength="80"/>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold uppercase text-[#90cba8] tracking-wider">Min Amount (optional)</label>
                    <input id="winConditionMinAmount" name="win_condition_min_amount" class="w-full bg-[#121417] border border-[#224932] rounded-lg px-4 py-3 text-white focus:ring-2 focus:ring-primary outline-none placeholder-[#3d5e4a]" placeholder="0" type="number" min="0" step="0.01"/>
                </div>
                <div class="flex items-center gap-2 pt-6">
                    <input type="checkbox" id="winConditionSumAmount" name="win_condition_sum_amount" value="1" class="rounded border-[#224932] bg-[#121417] text-primary focus:ring-primary"/>
                    <label for="winConditionSumAmount" class="text-sm text-white">Sum amount toward progress</label>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-2">
            <label class="text-xs font-bold uppercase text-[#90cba8] tracking-wider">Icon (Emoji)</label>
            <input id="challengeIcon" name="icon" class="w-full bg-[#0D0F10] border border-[#224932] rounded-lg px-4 py-3 text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none placeholder-[#3d5e4a]" placeholder="🎯" type="text" maxlength="2"/>
        </div>

        <div class="p-4 rounded-xl bg-[#0D0F10] border border-[#224932] flex flex-col gap-4">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">emoji_events</span>
                <span class="text-white font-bold">Rewards Configuration</span>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold uppercase text-[#90cba8] tracking-wider">XP Amount</label>
                <div class="relative">
                    <input id="challengeXP" name="xp_reward" required class="w-full bg-[#121417] border border-[#224932] rounded-lg pl-4 pr-12 py-3 text-primary font-bold text-lg focus:ring-1 focus:ring-primary outline-none" type="number" value="150" min="0"/>
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[#90cba8] text-sm font-medium">XP</span>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <span class="text-sm text-white">Unlock Badge?</span>
                <button type="button" id="badgeToggle" onclick="toggleBadge()" class="w-12 h-6 bg-[#224932] rounded-full relative transition-colors cursor-pointer">
                    <span class="absolute left-1 top-1 size-4 bg-[#90cba8] rounded-full transition-all"></span>
                </button>
                <input type="hidden" id="unlockBadge" name="unlock_badge" value="0">
            </div>

            {{-- ✅ ADDED: badge upload section (hidden unless toggle ON) --}}
            <div id="badgeUploadWrap" class="mt-4 hidden">
                <label class="text-xs font-bold uppercase text-[#90cba8] tracking-wider">Badge PNG</label>

                <div class="mt-2 flex items-center gap-3">
                    <input
                        id="badgeImage"
                        name="badge_image"
                        type="file"
                        accept="image/png"
                        class="w-full bg-[#0D0F10] border border-[#224932] rounded-lg px-4 py-3 text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
                    />

                    <div class="size-12 rounded-xl border border-[#224932] bg-[#0D0F10] overflow-hidden flex items-center justify-center">
                        <img id="badgePreview" src="" alt="Badge" class="hidden w-full h-full object-contain"/>
                        <span id="badgePreviewPlaceholder" class="material-symbols-outlined text-[#90cba8]">image</span>
                    </div>
                </div>

                <div class="mt-2 text-xs text-[#90cba8]">PNG only. Max 2MB.</div>
            </div>
        </div>

        <div class="flex items-center justify-between py-2 border-t border-[#224932] mt-2">
            <div class="flex flex-col">
                <span class="text-white font-medium">Active Status</span>
                <span class="text-xs text-[#90cba8]">Visible to students immediately</span>
            </div>
            <button type="button" id="activeToggle" onclick="toggleActive()" class="w-12 h-6 bg-primary rounded-full relative transition-colors cursor-pointer shadow-neon">
                <span class="absolute right-1 top-1 size-4 bg-white rounded-full transition-all shadow-sm"></span>
            </button>
            <input type="hidden" id="isActive" name="is_active" value="1">
        </div>
    </form>

    <div class="p-6 border-t border-[#224932] bg-[#121417] flex gap-3">
        <button type="button" onclick="closeCreatePanel()" class="flex-1 py-3 px-4 rounded-full border border-[#224932] text-white hover:bg-white/5 font-medium transition-colors">Cancel</button>

        <button type="button" onclick="saveChallenge()" class="flex-1 py-3 px-4 rounded-full bg-primary text-[#0D0F10] font-bold hover:bg-[#1ee86e] transition-colors shadow-neon flex items-center justify-center gap-2">
            <span class="material-symbols-outlined">save</span>
            <span id="saveButtonText">Save Challenge</span>
        </button>
    </div>
</div>

<script>
    // Badge toggle + preview (NO UI changes)
    function setBadgeUI(on, imageUrl = "") {
        const hidden = document.getElementById('unlockBadge');
        const wrap = document.getElementById('badgeUploadWrap');
        const toggle = document.getElementById('badgeToggle');
        const knob = toggle ? toggle.querySelector('span') : null;

        const preview = document.getElementById('badgePreview');
        const placeholder = document.getElementById('badgePreviewPlaceholder');

        const isOn = !!on;

        if (hidden) hidden.value = isOn ? "1" : "0";
        if (wrap) wrap.classList.toggle('hidden', !isOn);

        if (toggle && knob) {
            toggle.classList.toggle('bg-primary', isOn);
            toggle.classList.toggle('bg-[#224932]', !isOn);

            knob.classList.toggle('left-1', !isOn);
            knob.classList.toggle('right-1', isOn);
            knob.classList.toggle('bg-white', isOn);
            knob.classList.toggle('bg-[#90cba8]', !isOn);
        }

        if (preview && placeholder) {
            if (isOn && imageUrl) {
                preview.src = imageUrl;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            } else {
                preview.src = "";
                preview.classList.add('hidden');
                placeholder.classList.remove('hidden');
            }
        }
    }

    function toggleBadge() {
        const hidden = document.getElementById('unlockBadge');
        const current = hidden ? (hidden.value === "1") : false;
        const next = !current;

        if (!next) {
            const file = document.getElementById('badgeImage');
            if (file) file.value = "";
        }

        setBadgeUI(next);
    }

    document.addEventListener('change', function (e) {
        const input = e.target;
        if (!input || input.id !== 'badgeImage') return;

        const file = input.files && input.files[0];
        const preview = document.getElementById('badgePreview');
        const placeholder = document.getElementById('badgePreviewPlaceholder');
        if (!preview || !placeholder) return;

        if (file) {
            const url = URL.createObjectURL(file);
            preview.src = url;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        } else {
            preview.src = "";
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');
        }
    });

    // expose for edit mode
    window.setBadgeUI = setBadgeUI;
</script>
