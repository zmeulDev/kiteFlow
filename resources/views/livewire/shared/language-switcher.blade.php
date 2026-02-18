<!-- projects/visiflow/resources/views/livewire/shared/language-switcher.blade.php -->
<div class="flex items-center space-x-2">
    @foreach($locales as $code => $name)
        <button 
            wire:click="setLocale('{{ $code }}')"
            class="px-2 py-1 text-[10px] font-bold uppercase rounded-lg transition-all {{ app()->getLocale() == $code ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-500 hover:bg-slate-200 dark:bg-slate-800' }}"
        >
            {{ $code }}
        </button>
    @endforeach
</div>
