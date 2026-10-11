<x-layout title="Selecciona una empresa">
    <div class="card" style="max-width:480px; margin:40px auto; text-align:center;">
        <h2>¿Con cuál empresa vas a trabajar?</h2>
        <p style="color:#666; font-size:13px; margin-bottom:20px;">Puedes cambiar de empresa en cualquier momento desde el menú.</p>

        <div style="display:flex; flex-direction:column; gap:12px;">
            @foreach ($companies as $company)
                <form method="POST" action="{{ route('company.select.store') }}">
                    @csrf
                    <input type="hidden" name="company_id" value="{{ $company->id }}">
                    <button type="submit" class="btn btn-primary" style="width:100%; padding:14px; font-size:14px;">
                        {{ $company->short_name }}
                    </button>
                </form>
            @endforeach
        </div>
    </div>
</x-layout>
