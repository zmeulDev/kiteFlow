<?php

namespace App\Livewire\Shared;

use Livewire\Component;

class LanguageSwitcher extends Component
{
    public $locales = [
        'en' => 'English',
        'ro' => 'Română',
        'fr' => 'Français',
        'it' => 'Italiano',
        'es' => 'Español',
    ];

    public function setLocale($lang)
    {
        if (array_key_exists($lang, $this->locales)) {
            session()->put('locale', $lang);
            return redirect(request()->header('Referer'));
        }
    }

    public function render()
    {
        return view('livewire.shared.language-switcher');
    }
}
