<div x-data="{ step: @entangle('step') }">
    <div class="w-full max-w-2xl mx-auto">
        <!-- Step 1: Welcome -->
        <div x-show="step === 1" x-transition>
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">Welcome, Visitor!</h1>
                <p class="text-gray-400">Please check in to continue</p>
            </div>

            <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-6 mb-6">
                <label class="block text-sm font-medium text-gray-300 mb-2">Enter your email, phone, or name to check in</label>
                <div class="flex gap-3">
                    <input type="text"
                           wire:model.live="searchQuery"
                           placeholder="Email, phone, or name"
                           class="flex-1 px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <button wire:click="lookupVisitor"
                            class="px-6 py-3 bg-primary-500 text-white font-semibold rounded-xl hover:bg-primary-600 transition-colors">
                        Search
                    </button>
                </div>
            </div>

            <div class="text-center">
                <button wire:click="createNewVisitor"
                        class="text-gray-400 hover:text-white transition-colors">
                    I'm a new visitor
                </button>
            </div>
        </div>

        <!-- Step 2: New Visitor Form -->
        <div x-show="step === 2" x-transition>
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">New Visitor</h1>
                <p class="text-gray-400">Please provide your information</p>
            </div>

            <form wire:submit="saveVisitorDetails" class="bg-white/10 backdrop-blur-lg rounded-2xl p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">First Name *</label>
                        <input type="text" wire:model="first_name" required
                               class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        @error('first_name') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Last Name *</label>
                        <input type="text" wire:model="last_name" required
                               class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        @error('last_name') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                    <input type="email" wire:model="email"
                           class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    @error('email') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Phone</label>
                    <input type="tel" wire:model="phone"
                           class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    @error('phone') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Company</label>
                    <input type="text" wire:model="company"
                           class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="button" wire:click="createNewVisitor"
                            class="flex-1 px-6 py-3 bg-gray-600 text-white font-semibold rounded-xl hover:bg-gray-700 transition-colors">
                        Back
                    </button>
                    <button type="submit"
                            class="flex-1 px-6 py-3 bg-primary-500 text-white font-semibold rounded-xl hover:bg-primary-600 transition-colors">
                        Continue
                    </button>
                </div>
            </form>
        </div>

        <!-- Step 3: Details Confirmation -->
        <div x-show="step === 3" x-transition>
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">Confirm Your Details</h1>
                <p class="text-gray-400">Please review and update if needed</p>
            </div>

            <form wire:submit="saveVisitorDetails" class="bg-white/10 backdrop-blur-lg rounded-2xl p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">First Name *</label>
                        <input type="text" wire:model="first_name" required
                               class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        @error('first_name') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Last Name *</label>
                        <input type="text" wire:model="last_name" required
                               class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        @error('last_name') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                    <input type="email" wire:model="email"
                           class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    @error('email') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Phone</label>
                    <input type="tel" wire:model="phone"
                           class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    @error('phone') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Company</label>
                    <input type="text" wire:model="company"
                           class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="button" wire:click="createNewVisitor"
                            class="flex-1 px-6 py-3 bg-gray-600 text-white font-semibold rounded-xl hover:bg-gray-700 transition-colors">
                        Back
                    </button>
                    <button type="submit"
                            class="flex-1 px-6 py-3 bg-primary-500 text-white font-semibold rounded-xl hover:bg-primary-600 transition-colors">
                        Complete Check-in
                    </button>
                </div>
            </form>
        </div>

        <!-- Step 5: Complete -->
        <div x-show="step === 5" x-transition>
            <div class="text-center">
                <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-check text-4xl text-white"></i>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Check-in Complete!</h1>
                <p class="text-gray-400 mb-6">{{ $message }}</p>
                @if($badgeNumber)
                <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-6 mb-6">
                    <p class="text-sm text-gray-300 mb-2">Your Badge Number</p>
                    <p class="text-4xl font-bold text-white">{{ $badgeNumber }}</p>
                </div>
                @endif
                <button wire:click="resetAll"
                        class="px-8 py-3 bg-primary-500 text-white font-semibold rounded-xl hover:bg-primary-600 transition-colors">
                    Done
                </button>
            </div>
        </div>
    </div>
</div>