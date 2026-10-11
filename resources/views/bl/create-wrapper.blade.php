<x-layout title="Nuevo BL">
    @if (auth()->user()->activeCompany()->pdf_template === 'global')
        @livewire('global-bill-of-lading-form', ['billOfLading' => $billOfLading ?? null])
    @else
        @livewire('bill-of-lading-form', ['billOfLading' => $billOfLading ?? null])
    @endif
</x-layout>
