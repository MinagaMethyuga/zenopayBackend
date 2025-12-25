<!DOCTYPE html>
<html class="dark" lang="en"><head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>User Management - Zenopay</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#30e87a",
                        "background-light": "#f6f8f7",
                        "background-dark": "#0D0E12",
                        "surface-dark": "#16171C",
                    },
                    fontFamily: {
                        "display": ["Spline Sans", "sans-serif"]
                    },
                    borderRadius: {"DEFAULT": "1rem", "lg": "1.25rem", "xl": "1.5rem", "full": "9999px"},
                    boxShadow: {
                        'glow-primary': '0 0 15px 0 rgba(48, 232, 122, 0.5)',
                        'glow-primary-hover': '0 0 25px 3px rgba(48, 232, 122, 0.6)',
                    }
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings:
                'FILL' 0,
                'wght' 400,
                'GRAD' 0,
                'opsz' 24
        }
    </style>
</head>
<body class="font-display bg-background-dark text-gray-200 flex">
<x-dashboardNavBar/>
<div class="relative flex min-h-screen w-full flex-col overflow-x-hidden">
    <div class="flex h-full grow flex-col">
        <div class="flex flex-1 justify-center p-4 sm:p-6 lg:p-8">
            <div class="flex w-full max-w-7xl flex-col gap-8">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <h1 class="text-4xl font-bold tracking-tight text-white">User Management</h1>
                    <button class="flex h-12 min-w-[84px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-full bg-primary px-6 text-base font-bold text-background-dark shadow-glow-primary transition-shadow hover:shadow-glow-primary-hover">
                        <span class="material-symbols-outlined">add</span>
                        <span>Add User</span>
                    </button>
                </div>
                <div class="flex flex-col gap-4 md:flex-row md:items-center">
                    <div class="flex-grow">
                        <label class="flex h-12 w-full flex-col">
                            <div class="group flex h-full w-full flex-1 items-stretch rounded-full bg-surface-dark ring-1 ring-primary/50 transition-all focus-within:ring-2 focus-within:ring-primary focus-within:shadow-glow-primary">
                                <div class="flex items-center justify-center pl-4 text-primary">
                                    <span class="material-symbols-outlined">search</span>
                                </div>
                                <input class="form-input h-full min-w-0 flex-1 resize-none overflow-hidden border-none bg-transparent px-3 text-base font-normal leading-normal text-white placeholder:text-gray-400 focus:outline-none focus:ring-0" placeholder="Search by name, email, or ID..."/>
                            </div>
                        </label>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex h-10 shrink-0 cursor-pointer items-center justify-center gap-x-2 rounded-full bg-primary px-5">
                            <p class="text-sm font-bold text-background-dark">All</p>
                        </div>
                        <div class="flex h-10 shrink-0 cursor-pointer items-center justify-center gap-x-2 rounded-full bg-surface-dark px-5 text-sky-400 ring-1 ring-sky-400/50 hover:bg-sky-400/10 hover:ring-sky-400">
                            <p class="text-sm font-medium">Active</p>
                        </div>
                        <div class="flex h-10 shrink-0 cursor-pointer items-center justify-center gap-x-2 rounded-full bg-surface-dark px-5 text-yellow-400 ring-1 ring-yellow-400/50 hover:bg-yellow-400/10 hover:ring-yellow-400">
                            <p class="text-sm font-medium">Low Activity</p>
                        </div>
                        <div class="flex h-10 shrink-0 cursor-pointer items-center justify-center gap-x-2 rounded-full bg-surface-dark px-5 text-pink-400 ring-1 ring-pink-400/50 hover:bg-pink-400/10 hover:ring-pink-400">
                            <p class="text-sm font-medium">Inactive</p>
                        </div>
                    </div>
                </div>
                <div class="w-full overflow-x-auto">
                    <div class="overflow-hidden rounded-xl border border-gray-800 bg-surface-dark">
                        <table class="min-w-full table-auto">
                            <thead class="bg-black/20">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">User</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Challenges Completed</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Savings Goals</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Registration Date</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400"></th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800">
                            <tr class="transition-colors hover:bg-black/10">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 flex-shrink-0 rounded-full bg-cover bg-center" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuA0FQIzXXHEjJnpv4tLhl_KKjtJfHg05vtV_quxvIDgec6mKEF7jeB8UUUuEatpj7rTbw2RX5I_NPpI9DPfHh_n2EzTHX6S2275dlqY0410x2eQbBAGfyYaVW135m7HJQRvD86K7qHQB4CDVYqi1GtnY_4V-p4ttWHoUz9-RNDOuqvT1cParHfjRU4GLcgug4-y6DohNL20qQtWNgwLkPz0e649cBFCNYSrYBFZlvFSrXK0IgRMbA-P0vkQxXNYv5no81i_pAFv3ns");'></div>
                                        <div>
                                            <div class="text-sm font-medium text-white">Olivia Rhye</div>
                                            <div class="text-sm text-gray-400">olivia@zenopay.com</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2 rounded-full bg-sky-500/10 px-3 py-1 text-xs font-medium text-sky-400">
                                        <span class="h-2 w-2 rounded-full bg-sky-500"></span>
                                        Active
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-24 overflow-hidden rounded-full bg-gray-700"><div class="h-1.5 rounded-full bg-primary" style="width: 85%;"></div></div>
                                        <p class="text-sm font-medium text-white">75 / 88</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-24 overflow-hidden rounded-full bg-gray-700"><div class="h-1.5 rounded-full" style="width: 68%; background-color: #38bdf8;"></div></div>
                                        <p class="text-sm font-medium text-white">60 / 88</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">Jan 15, 2024</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button class="text-gray-400 hover:text-white"><span class="material-symbols-outlined">more_vert</span></button>
                                </td>
                            </tr>
                            <tr class="transition-colors hover:bg-black/10">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 flex-shrink-0 rounded-full bg-cover bg-center" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAUjXfXoVdFMa3fMlMnvvLwzFd-4Q72BLik6wV_EsLaGSaUtPH6n_NF4aJZxz9Tbk0w2W6wv9wU9HKNvv1_IBXPBTdW4r4j3ZmEe3SwkGcUDc-c1zg2I_1iBJfWkYXTIWPxPgCxYujeDWQ3H1_kIcz9NJCb3zDzqe2VZl6xHrss63S-fyR__2-6SyT_oSTYDahvUAjPrCnLi25HLowKp1ic0JVzurZQj7Ap7dL38JyQQ3S9bTZuhbvAIzHiq19MyxBMz_6MEKyvFd4");'></div>
                                        <div>
                                            <div class="text-sm font-medium text-white">Phoenix Baker</div>
                                            <div class="text-sm text-gray-400">phoenix@zenopay.com</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2 rounded-full bg-yellow-500/10 px-3 py-1 text-xs font-medium text-yellow-400">
                                        <span class="h-2 w-2 rounded-full bg-yellow-500"></span>
                                        Low activity
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-24 overflow-hidden rounded-full bg-gray-700"><div class="h-1.5 rounded-full bg-primary" style="width: 28%;"></div></div>
                                        <p class="text-sm font-medium text-white">25 / 88</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-24 overflow-hidden rounded-full bg-gray-700"><div class="h-1.5 rounded-full" style="width: 90%; background-color: #a855f7;"></div></div>
                                        <p class="text-sm font-medium text-white">80 / 88</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">Jan 12, 2024</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button class="text-gray-400 hover:text-white"><span class="material-symbols-outlined">more_vert</span></button>
                                </td>
                            </tr>
                            <tr class="transition-colors hover:bg-black/10">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 flex-shrink-0 rounded-full bg-cover bg-center" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCD8VtBiV6CLtGksrdoDVO6-yBuUKaJNjZbH6S0lmKiq8--Q0eVR_SbDfRiFiL8KFdoKKWTrOSQjkSkfUUszofzex8wZaSxuBEnTr7A3kFYpHEnMEqMMST8hm7ZWvQlBKCmRE1d7Gal9VR0p70oNpAfyQPfpvj0QhO-irr3xwPbd8xURJ78q_6VhCLMc0jCjWSHmUWTuhIbrLUHxy6etq9Hb5fnIw8tfResSAThR2bZXCc6OrBnKEGbWVMDjKIk25PVgF54yanJ6ZY");'></div>
                                        <div>
                                            <div class="text-sm font-medium text-white">Lana Steiner</div>
                                            <div class="text-sm text-gray-400">lana@zenopay.com</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2 rounded-full bg-pink-500/10 px-3 py-1 text-xs font-medium text-pink-400">
                                        <span class="h-2 w-2 rounded-full bg-pink-500"></span>
                                        Inactive
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-24 overflow-hidden rounded-full bg-gray-700"><div class="h-1.5 rounded-full bg-primary" style="width: 56%;"></div></div>
                                        <p class="text-sm font-medium text-white">50 / 88</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-24 overflow-hidden rounded-full bg-gray-700"><div class="h-1.5 rounded-full" style="width: 45%; background-color: #38bdf8;"></div></div>
                                        <p class="text-sm font-medium text-white">40 / 88</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">Jan 10, 2024</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button class="text-gray-400 hover:text-white"><span class="material-symbols-outlined">more_vert</span></button>
                                </td>
                            </tr>
                            <tr class="transition-colors hover:bg-black/10">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 flex-shrink-0 rounded-full bg-cover bg-center" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAR_cxzeTpwuovXyS6pdJAg1piV5JGfkEGHxb9hN9OoSF5G_2XCIDgutuhjCnahWkPiBih57sUEuHHlG2Ghc26Ej9pXdtFs_8bneQx7FUwecZPuZ6wyxsQMr7FdPU66j6wUxt9h5irGpfyIjzx5xqiR_FTmDe3dpXqwMepOWj-MqZz-EzDjBJmuVTZaTnsKMmWdTbg73Fge4t4iukfqMIefWvSzJk_5A2ep9djiw1l1MFZB5Mse9OhQKk-JZu7FpdZ-mtB9ONbPANc");'></div>
                                        <div>
                                            <div class="text-sm font-medium text-white">Demi Wilkinson</div>
                                            <div class="text-sm text-gray-400">demi@zenopay.com</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2 rounded-full bg-sky-500/10 px-3 py-1 text-xs font-medium text-sky-400">
                                        <span class="h-2 w-2 rounded-full bg-sky-500"></span>
                                        Active
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-24 overflow-hidden rounded-full bg-gray-700"><div class="h-1.5 rounded-full bg-primary" style="width: 100%;"></div></div>
                                        <p class="text-sm font-medium text-white">88 / 88</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-24 overflow-hidden rounded-full bg-gray-700"><div class="h-1.5 rounded-full" style="width: 100%; background-color: #a855f7;"></div></div>
                                        <p class="text-sm font-medium text-white">88 / 88</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">Jan 8, 2024</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button class="text-gray-400 hover:text-white"><span class="material-symbols-outlined">more_vert</span></button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body></html>
