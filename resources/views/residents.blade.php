<x-layouts::app :title="__('Residents')">
    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between gap-4">
            <flux:heading size="xl">{{ __('Residents') }}</flux:heading>
            <flux:input disabled :placeholder="__('Search')" class="max-w-64" />
        </div>

        @forelse ([] as $resident)
            <flux:card>{{ $resident }}</flux:card>
        @empty
            <flux:callout icon="user-group">
                <flux:callout.heading>{{ __('Nobody lives here yet') }}</flux:callout.heading>
                <flux:callout.text>{{ __('Residents keep their own records on this server.') }}</flux:callout.text>
            </flux:callout>
        @endforelse
    </div>
</x-layouts::app>
