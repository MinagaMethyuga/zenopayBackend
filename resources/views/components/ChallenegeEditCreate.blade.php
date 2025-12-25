<div id="createPanel" class="absolute inset-y-0 right-0 w-full md:w-[480px] bg-[#121417] shadow-[-10px_0_30px_rgba(0,0,0,0.8)] border-l border-[#224932] transform translate-x-full transition-transform duration-300 flex flex-col z-50">
    <div class="flex items-center justify-between px-6 py-5 border-b border-[#224932]">
        <h3 id="panelTitle" class="text-white text-xl font-bold">Create Challenge</h3>
        <button onclick="closeCreatePanel()" class="size-8 flex items-center justify-center rounded-full hover:bg-white/10 text-white/70 hover:text-white transition-colors">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>

    <form id="challengeForm" class="flex-1 overflow-y-auto p-6 flex flex-col gap-6">
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
        <button onclick="closeCreatePanel()" class="flex-1 py-3 px-4 rounded-full border border-[#224932] text-white hover:bg-white/5 font-medium transition-colors">Cancel</button>
        <button onclick="saveChallenge()" class="flex-1 py-3 px-4 rounded-full bg-primary text-[#0D0F10] font-bold hover:bg-[#1ee86e] transition-colors shadow-neon flex items-center justify-center gap-2">
            <span class="material-symbols-outlined">save</span>
            <span id="saveButtonText">Save Challenge</span>
        </button>
    </div>
</div>
