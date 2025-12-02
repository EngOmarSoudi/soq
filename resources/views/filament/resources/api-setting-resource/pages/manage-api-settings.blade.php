<x-filament::page>
    <x-filament::form wire:submit="save">
        {{ $this->form }}

        <x-filament::button type="submit">
            Save Settings
        </x-filament::button>
    </x-filament::form>

    @if (session()->has('notification'))
        <x-filament::notification :status="session('notification.type')" :title="session('notification.title')" />
    @endif
</x-filament::page>