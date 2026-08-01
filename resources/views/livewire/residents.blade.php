<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Residents')] class extends Component
{
    public string $search = '';

    /**
     * @return array<int, string>
     */
    public function residents(): array
    {
        return [];
    }
};?>

<div class="flex flex-col gap-6 p-6">
    <div class="flex items-center justify-between gap-4">
        <flux:heading size="xl">{{ __('Residents') }}</flux:heading>

        {{-- Interactive, from a package, with no wiring in the host. --}}
        <flux:input wire:model.live.debounce="search" :placeholder="__('Search')" class="max-w-64" />
    </div>

    @forelse ($this->residents() as $resident)
        <flux:card>{{ $resident }}</flux:card>
    @empty
        <flux:callout icon="user-group">
            <flux:callout.heading>{{ __('Nobody lives here yet') }}</flux:callout.heading>
            <flux:callout.text>{{ __('Residents keep their own records on this server.') }}</flux:callout.text>
        </flux:callout>
    @endforelse
</div>
