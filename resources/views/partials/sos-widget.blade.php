<!-- SOS Floating Button -->
<div class="fixed bottom-20 right-4 z-[100]">
    <button onclick="toggleSOS()" class="bg-red-600 text-white w-14 h-14 rounded-full shadow-2xl flex items-center justify-center animate-pulse border-4 border-white">
        <span class="font-bold text-xs">SOS</span>
    </button>
</div>

<!-- SOS Modal -->
<div id="sosModal" class="fixed inset-0 bg-black/50 z-[110] hidden flex items-end justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-t-3xl p-6 animate-slide-up">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-red-600">आपातकालीन सहायता (SOS)</h2>
            <button onclick="toggleSOS()" class="text-gray-400 text-2xl">&times;</button>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-6">
            <a href="tel:112" class="bg-gray-50 p-4 rounded-2xl text-center border border-gray-100 shadow-sm">
                <p class="text-2xl mb-1">🚨</p>
                <p class="text-sm font-bold">पुलिस (112)</p>
            </a>
            <a href="tel:108" class="bg-gray-50 p-4 rounded-2xl text-center border border-gray-100 shadow-sm">
                <p class="text-2xl mb-1">🚑</p>
                <p class="text-sm font-bold">एम्बुलेंस (108)</p>
            </a>
            <a href="tel:101" class="bg-gray-50 p-4 rounded-2xl text-center border border-gray-100 shadow-sm">
                <p class="text-2xl mb-1">🔥</p>
                <p class="text-sm font-bold">दमकल (101)</p>
            </a>
            <a href="tel:1090" class="bg-gray-50 p-4 rounded-2xl text-center border border-gray-100 shadow-sm">
                <p class="text-2xl mb-1">👩</p>
                <p class="text-sm font-bold">महिला हेल्पलाइन</p>
            </a>
        </div>
        
        <button onclick="toggleSOS()" class="w-full py-3 bg-gray-100 text-gray-600 rounded-xl font-bold">बंद करें</button>
    </div>
</div>

<script>
    function toggleSOS() {
        const modal = document.getElementById('sosModal');
        modal.classList.toggle('hidden');
    }
</script>

<style>
    @keyframes slide-up {
        from { transform: translateY(100%); }
        to { transform: translateY(0); }
    }
    .animate-slide-up { animation: slide-up 0.3s ease-out; }
</style>