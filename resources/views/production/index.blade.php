{{-- resources/views/production/index.blade.php --}}
@extends('layouts.sidebar')

@section('content')
<div class="container mt-4">
    <h2><strong>Production Management</strong></h2>
    <p>Plan, schedule, and track production orders</p>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Tabs --}}
    @php
        $activeTab = request('tab', 'planned'); // ambil tab dari query parameter
    @endphp
    <ul class="nav nav-tabs mb-3" id="productionTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'planned' ? 'active' : '' }}" 
               id="planned-tab" data-bs-toggle="tab" href="#planned" role="tab">
               <i class="bi bi-calendar2-event me-1"></i> Planned
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'current' ? 'active' : '' }}" 
               id="current-tab" data-bs-toggle="tab" href="#current" role="tab">
               <i class="bi bi-play-circle me-1"></i> Current
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'completed' ? 'active' : '' }}" 
               id="completed-tab" data-bs-toggle="tab" href="#completed" role="tab">
               <i class="bi bi-check2-circle me-1"></i> Completed
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'all' ? 'active' : '' }}" 
               id="all-tab" data-bs-toggle="tab" href="#all" role="tab">
               <i class="bi bi-list-ul me-1"></i> All
            </a>
        </li>
    </ul>

    <div class="tab-content">
        {{-- Planned --}}
        <div class="tab-pane fade {{ $activeTab === 'planned' ? 'show active' : '' }}" id="planned" role="tabpanel">
            @include('production.partials.planned', ['planned' => $produksiPlanned])
        </div>

        {{-- Current --}}
        <div class="tab-pane fade {{ $activeTab === 'current' ? 'show active' : '' }}" id="current" role="tabpanel">
            @include('production.partials.current', ['current' => $produksiCurrent])
        </div>

        {{-- Completed --}}
        <div class="tab-pane fade {{ $activeTab === 'completed' ? 'show active' : '' }}" id="completed" role="tabpanel">
            @include('production.partials.completed', ['completed' => $produksiCompleted])
        </div>

        {{-- All --}}
        <div class="tab-pane fade {{ $activeTab === 'all' ? 'show active' : '' }}" id="all" role="tabpanel">
            @php
                $all = collect([]);
                if(isset($produksiPlanned)) $all = $all->merge($produksiPlanned);
                if(isset($produksiCurrent)) $all = $all->merge($produksiCurrent);
                if(isset($produksiCompleted)) $all = $all->merge($produksiCompleted);
            @endphp
            @include('production.partials.all', ['all' => $all])
        </div>
    </div>
</div>

{{-- Script untuk mengingat tab aktif saat reload --}}
@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    if(tab){
        const triggerEl = document.querySelector(`#${tab}-tab`);
        if(triggerEl){
            const tabInstance = new bootstrap.Tab(triggerEl);
            tabInstance.show();
        }
    }

    // Script untuk form completion
    const forms = document.querySelectorAll('.form-complete');
    forms.forEach(form => {
        const hasilInputs = form.querySelectorAll('.hasil-input');
        const gagalInputs = form.querySelectorAll('.gagal-input');

        function validateSum() {
            hasilInputs.forEach((hInput, idx) => {
                const gInput = gagalInputs[idx];
                const max = parseInt(hInput.dataset.max) || 0;
                let sum = parseInt(hInput.value || 0) + parseInt(gInput.value || 0);
                if(sum > max){
                    alert('Jumlah Berhasil + Gagal tidak boleh melebihi Jumlah Pesanan!');
                    hInput.value = 0;
                    gInput.value = 0;
                }
            });
        }

        hasilInputs.forEach(input => input.addEventListener('change', validateSum));
        gagalInputs.forEach(input => input.addEventListener('change', validateSum));

        form.addEventListener('submit', function(e){
            e.preventDefault();
            const prodId = form.dataset.prodId;
            const actionUrl = `/production/${prodId}/complete`;
            const data = new FormData(form);

            fetch(actionUrl, {
                method: 'POST',
                body: data,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                if(result.success){
                    alert(result.success);
                    let tab = result.redirect_tab || 'all';
                    window.location.href = `/production?tab=${tab}`;
                } else if(result.error){
                    alert(result.error);
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Terjadi kesalahan saat menyimpan hasil produksi: ' + err.message);
            });
        });
    });
});
</script>
@endpush
@endsection
