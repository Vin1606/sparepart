{{-- filepath: resources/views/Pengurangan/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Log Riwayat Pengambilan - Mechanic Engineer Inventory | PT Garuda Yamato Steel')

@section('content')
    @php
        $totalTransaksi    = $riwayats->count();
        $transaksiHariIni  = $riwayats->filter(fn($r) => $r->tanggal_ambil && $r->tanggal_ambil->isToday())->count();
        $unitHariIni       = $riwayats->filter(fn($r) => $r->tanggal_ambil && $r->tanggal_ambil->isToday())->sum('jumlah');
        $jenisPartTerpakai = $riwayats->pluck('id_barang')->unique()->filter()->count();
        $totalMekanik      = $riwayats->pluck('nama_mekanik')->unique()->filter()->count();

        $shift1Count   = $riwayats->filter(fn($r) => str_contains(strtolower($r->shift ?? ''), 'shift 1') || str_contains(strtolower($r->shift ?? ''), 'regu 1'))->count();
        $shift2Count   = $riwayats->filter(fn($r) => str_contains(strtolower($r->shift ?? ''), 'shift 2') || str_contains(strtolower($r->shift ?? ''), 'regu 2'))->count();
        $shift3Count   = $riwayats->filter(fn($r) => str_contains(strtolower($r->shift ?? ''), 'shift 3') || str_contains(strtolower($r->shift ?? ''), 'regu 3'))->count();
        $nonShiftCount = $totalTransaksi - ($shift1Count + $shift2Count + $shift3Count);

        $partOptions = $riwayats->pluck('barang')->filter()->unique('id_barang')->sortBy('nama_barang');
    @endphp

    <div class="catalog-shell">
        {{-- Breadcrumb --}}
        <ul class="breadcrumb-custom">
            <li>
                <a href="{{ route('barang.index') }}" class="d-inline-flex align-items-center gap-1.5">
                    <i class="fa-solid fa-house"></i>
                    <span>Beranda</span>
                </a>
            </li>
            <li class="separator"><i class="fa-solid fa-chevron-right"></i></li>
            <li class="active">{{ auth()->user()->isAdmin() ? 'Log Riwayat Pengambilan' : 'Riwayat Pengambilan Saya' }}</li>
        </ul>

        {{-- Page Header --}}
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <h1 class="page-title mb-0">
                        {{ auth()->user()->isAdmin() ? 'Log Riwayat Pengambilan Suku Cadang' : 'Riwayat Pengambilan Suku Cadang Saya' }}
                    </h1>
                    @if (auth()->user()->isAdmin())
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill fw-bold"
                            style="font-size: 11px;">
                            <i class="fa-solid fa-shield-halved me-1"></i> Seluruh Mekanik
                        </span>
                    @else
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill fw-bold"
                            style="font-size: 11px;">
                            <i class="fa-solid fa-user me-1"></i> Data Pribadi
                        </span>
                    @endif
                </div>
                <p class="page-subtitle">
                    {{ auth()->user()->isAdmin()
                        ? 'Catatan audit pengambilan spare part oleh seluruh tim mekanik untuk perbaikan dan perawatan mesin workshop PT Garuda Yamato Steel.'
                        : 'Daftar suku cadang yang telah Anda ambil dari gudang beserta status shift dan peruntukan mesin.' }}
                </p>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('barang.index') }}"
                    class="btn btn-primary d-inline-flex align-items-center gap-2 px-3.5 py-2 fw-semibold shadow-sm"
                    style="font-size: 13px;">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    <span>Katalog Suku Cadang</span>
                </a>
                <a href="{{ route('rack.index') }}"
                    class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 py-2 fw-semibold shadow-sm"
                    style="font-size: 13px;">
                    <i class="fa-solid fa-layer-group text-primary"></i>
                    <span>{{ auth()->user()->isAdmin() ? 'Kelola Rak' : 'Lihat Rak' }}</span>
                </a>
            </div>
        </div>

        {{-- Flash Alert --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2.5 mb-4 py-3 px-3.5 rounded-3"
                role="alert">
                <i class="fa-solid fa-circle-check fs-5 text-success"></i>
                <div class="fw-semibold text-dark">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2.5 mb-4 py-3 px-3.5 rounded-3"
                role="alert">
                <i class="fa-solid fa-circle-exclamation fs-5 text-danger"></i>
                <div class="fw-semibold text-dark">{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        {{-- KPI Summary Cards (4 Kolom Seragam) --}}
        <div class="row g-3 mb-4">
            {{-- KPI 1: Total Transaksi --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 kpi-card kpi-accent-blue">
                    <div class="card-body d-flex align-items-center gap-3 p-3.5">
                        <div class="kpi-icon-wrapper kpi-icon-blue">
                            <i class="fa-solid fa-clipboard-list"></i>
                        </div>
                        <div>
                            <div class="kpi-label">Total Transaksi</div>
                            <div class="kpi-value">{{ number_format($totalTransaksi, 0, ',', '.') }}</div>
                            <div class="kpi-subtext">
                                <i class="fa-solid fa-clock-rotate-left text-primary" style="font-size: 10px;"></i>
                                <span>Kali pengambilan tercatat</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPI 2: Total Unit Keluar --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 kpi-card kpi-accent-amber">
                    <div class="card-body d-flex align-items-center gap-3 p-3.5">
                        <div class="kpi-icon-wrapper kpi-icon-amber">
                            <i class="fa-solid fa-boxes-packing"></i>
                        </div>
                        <div>
                            <div class="kpi-label">Total Unit Keluar</div>
                            <div class="kpi-value">{{ number_format($totalItemDiambil, 0, ',', '.') }}</div>
                            <div class="kpi-subtext">
                                <i class="fa-solid fa-cubes-stacked text-warning" style="font-size: 10px;"></i>
                                <span>Unit suku cadang terpakai</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPI 3: Aktivitas Hari Ini --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 kpi-card kpi-accent-green">
                    <div class="card-body d-flex align-items-center gap-3 p-3.5">
                        <div class="kpi-icon-wrapper kpi-icon-green">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <div>
                            <div class="kpi-label">Transaksi Hari Ini</div>
                            <div class="kpi-value">{{ number_format($transaksiHariIni, 0, ',', '.') }}</div>
                            <div class="kpi-subtext">
                                <i class="fa-solid fa-circle-check text-success" style="font-size: 10px;"></i>
                                <span>{{ number_format($unitHariIni, 0, ',', '.') }} unit keluar hari ini</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPI 4: Jenis Part & Mekanik --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 kpi-card kpi-accent-purple">
                    <div class="card-body d-flex align-items-center gap-3 p-3.5">
                        <div class="kpi-icon-wrapper kpi-icon-purple">
                            <i class="fa-solid fa-screwdriver-wrench"></i>
                        </div>
                        <div>
                            <div class="kpi-label">Jenis Part Terpakai</div>
                            <div class="kpi-value">{{ number_format($jenisPartTerpakai, 0, ',', '.') }}</div>
                            <div class="kpi-subtext">
                                <i class="fa-solid fa-user-gear text-purple" style="font-size: 10px;"></i>
                                <span>Oleh {{ $totalMekanik }} teknisi mekanik</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Riwayat Pengambilan --}}
        <div class="card main-data-card">
            {{-- Toolbar: Pencarian & Filter --}}
            <div class="card-header">
                <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
                    {{-- Kiri: Search Input + Filter Suku Cadang --}}
                    <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2.5 flex-grow-1">
                        <div class="search-gys-wrapper">
                            <i class="fa-solid fa-magnifying-glass search-gys-icon"></i>
                            <input type="text" id="logSearchInput" class="search-gys-input"
                                placeholder="Cari part, mekanik, tanggal, atau keperluan mesin..."
                                aria-label="Cari Log Riwayat">
                            <button type="button" id="clearLogSearchBtn" class="search-clear-btn" title="Bersihkan pencarian">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <select id="partFilterSelect" class="form-select filter-select-gys" style="max-width: 240px;"
                            aria-label="Filter Berdasarkan Suku Cadang">
                            <option value="all">Semua Suku Cadang ({{ $jenisPartTerpakai }})</option>
                            @foreach ($partOptions as $partOpt)
                                <option value="{{ $partOpt->id_barang }}">
                                    {{ $partOpt->nama_barang }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Kanan: Filter Shift Pills & Total Counter --}}
                    <div class="d-flex flex-wrap align-items-center justify-content-between justify-content-xl-end gap-2.5">
                        <div class="filter-pills-group" role="group" aria-label="Filter Shift Kerja">
                            <button type="button" class="filter-pill-btn active" data-filter="all">
                                <span>Semua</span>
                                <span class="filter-pill-count">{{ $totalTransaksi }}</span>
                            </button>
                            <button type="button" class="filter-pill-btn" data-filter="shift-1">
                                <span>Shift 1</span>
                                <span class="filter-pill-count">{{ $shift1Count }}</span>
                            </button>
                            <button type="button" class="filter-pill-btn" data-filter="shift-2">
                                <span>Shift 2</span>
                                <span class="filter-pill-count">{{ $shift2Count }}</span>
                            </button>
                            <button type="button" class="filter-pill-btn" data-filter="shift-3">
                                <span>Shift 3</span>
                                <span class="filter-pill-count">{{ $shift3Count }}</span>
                            </button>
                            <button type="button" class="filter-pill-btn" data-filter="non-shift">
                                <span>Non-Shift</span>
                                <span class="filter-pill-count">{{ $nonShiftCount }}</span>
                            </button>
                        </div>

                        <div class="d-flex align-items-center gap-1.5 small text-secondary ps-xl-2 border-start-xl">
                            <span class="fw-medium d-none d-sm-inline">Menampilkan:</span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold px-2.5 py-1 rounded-pill"
                                id="logRowCount">
                                {{ $totalTransaksi }} Data
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel Log --}}
            <div class="table-responsive">
                <table class="table-gys align-middle mb-0" id="logTable">
                    <thead>
                        <tr>
                            <th class="ps-3 text-center" style="width: 55px;">No</th>
                            <th style="width: 165px;">Waktu &amp; Tanggal</th>
                            <th>Suku Cadang Terpakai</th>
                            <th style="width: 155px;">Jumlah Diambil</th>
                            <th style="width: 185px;">Mekanik Pengambil</th>
                            <th style="width: 135px;">Shift Kerja</th>
                            <th>Peruntukan / Keperluan</th>
                            @if (auth()->user()->isAdmin())
                                <th class="pe-3" style="width: 145px;">Akun Login</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($riwayats as $index => $riwayat)
                            @php
                                $shiftRaw = strtolower($riwayat->shift ?? '');
                                if (str_contains($shiftRaw, 'shift 1') || str_contains($shiftRaw, 'regu 1')) {
                                    $shiftGroup = 'shift-1';
                                } elseif (str_contains($shiftRaw, 'shift 2') || str_contains($shiftRaw, 'regu 2')) {
                                    $shiftGroup = 'shift-2';
                                } elseif (str_contains($shiftRaw, 'shift 3') || str_contains($shiftRaw, 'regu 3')) {
                                    $shiftGroup = 'shift-3';
                                } else {
                                    $shiftGroup = 'non-shift';
                                }

                                $logCode  = '#LOG-' . str_pad($riwayat->id_pengurangan, 4, '0', STR_PAD_LEFT);
                                $partCode = $riwayat->id_barang ? '#BRG-' . str_pad($riwayat->id_barang, 4, '0', STR_PAD_LEFT) : null;
                            @endphp

                            <tr class="log-row"
                                data-shift="{{ $shiftGroup }}"
                                data-part="{{ $riwayat->id_barang ?? '' }}">
                                <td class="ps-3 text-center text-muted fw-bold row-number" style="font-size: 12px;">
                                    {{ $index + 1 }}
                                </td>

                                {{-- Waktu & Tanggal --}}
                                <td>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex align-items-center gap-1.5">
                                            <span class="fw-bold text-dark" style="font-size: 12.5px;">
                                                {{ $riwayat->tanggal_ambil ? $riwayat->tanggal_ambil->format('d/m/Y') : '-' }}
                                            </span>
                                            <span class="part-code-badge" style="font-size: 9.5px; padding: 0 5px;">{{ $logCode }}</span>
                                        </div>
                                        <span class="text-muted mt-0.5" style="font-size: 11px;">
                                            <i class="fa-regular fa-clock me-1 text-primary opacity-75"></i>
                                            {{ $riwayat->tanggal_ambil ? $riwayat->tanggal_ambil->format('H:i') . ' WIB' : '-' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Suku Cadang Terpakai --}}
                                <td>
                                    <div class="d-flex align-items-start gap-2.5">
                                        <div class="item-icon-box mt-0.5"
                                            style="width: 36px; height: 36px; background-color: #eff6ff; color: var(--gys-primary); border-color: #dbeafe;">
                                            <i class="fa-solid fa-gears" style="font-size: 13px;"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <span class="item-title">
                                                    {{ $riwayat->barang?->nama_barang ?? 'Barang Telah Dihapus' }}
                                                </span>
                                                @if ($partCode)
                                                    <span class="part-code-badge">{{ $partCode }}</span>
                                                @endif
                                            </div>
                                            <div class="item-desc">
                                                <i class="fa-solid fa-inbox text-primary opacity-75 me-1" style="font-size: 10px;"></i>
                                                Rak: <strong class="text-secondary">{{ $riwayat->barang?->rack?->nama_rack ?? '-' }}</strong>
                                                &bull; Area: {{ $riwayat->barang?->rack?->lokasi ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Jumlah Diambil --}}
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge-stock badge-stock-danger" style="font-size: 12px; font-weight: 700;">
                                            -{{ number_format($riwayat->jumlah, 0, ',', '.') }} Unit
                                        </span>
                                    </div>
                                </td>

                                {{-- Nama Mekanik --}}
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="item-icon-box"
                                            style="width: 28px; height: 28px; background-color: #eff6ff; color: var(--gys-primary); border-radius: 50%; font-size: 11px; border-color: #dbeafe;">
                                            <i class="fa-solid fa-user-gear"></i>
                                        </div>
                                        <span class="fw-bold text-dark" style="font-size: 12.5px;">{{ $riwayat->nama_mekanik }}</span>
                                    </div>
                                </td>

                                {{-- Shift Kerja --}}
                                <td>
                                    <span class="badge-rack-pill">
                                        <i class="fa-regular fa-clock text-primary" style="font-size: 11px;"></i>
                                        <span>{{ $riwayat->shift ?: 'Non-Shift' }}</span>
                                    </span>
                                </td>

                                {{-- Keperluan / Peruntukan --}}
                                <td>
                                    <div class="text-secondary small fw-medium" style="max-width: 280px;">
                                        <i class="fa-solid fa-wrench text-muted opacity-75 me-1" style="font-size: 10px;"></i>
                                        {{ $riwayat->keperluan ?: '-' }}
                                    </div>
                                </td>

                                {{-- Akun Login (Admin Only) --}}
                                @if (auth()->user()->isAdmin())
                                    <td class="pe-3">
                                        <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill" style="font-size: 11px;">
                                            <i class="fa-solid fa-circle-user text-primary me-1"></i>
                                            {{ $riwayat->user?->name ?? 'User Terhapus' }}
                                        </span>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->isAdmin() ? 8 : 7 }}" class="p-0">
                                    <div class="table-empty-box">
                                        <div class="table-empty-icon">
                                            <i class="fa-solid fa-clipboard-check"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Belum Ada Riwayat Pengambilan</h6>
                                        <p class="text-secondary small mb-3">
                                            Belum ada suku cadang yang dicatat pengambilannya di sistem.
                                        </p>
                                        <a href="{{ route('barang.index') }}"
                                            class="btn btn-primary btn-sm px-3.5 py-2 fw-semibold">
                                            <i class="fa-solid fa-boxes-stacked me-1"></i> Buka Katalog Barang
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        {{-- Baris Kosong Saat Filter / Pencarian Tidak Ditemukan --}}
                        @if ($riwayats->isNotEmpty())
                            <tr id="noLogMatchRow" style="display: none;">
                                <td colspan="{{ auth()->user()->isAdmin() ? 8 : 7 }}" class="p-0">
                                    <div class="table-empty-box py-5">
                                        <div class="table-empty-icon" style="width: 54px; height: 54px; font-size: 22px;">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Catatan Riwayat Tidak Ditemukan</h6>
                                        <p class="text-secondary small mb-3">
                                            Tidak ada riwayat pengambilan yang cocok dengan kata kunci pencarian atau filter shift yang dipilih.
                                        </p>
                                        <button type="button" id="resetLogFilterBtn"
                                            class="btn btn-outline-secondary btn-sm px-3 py-1.5 fw-semibold">
                                            <i class="fa-solid fa-rotate-left me-1"></i> Reset Filter &amp; Pencarian
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Card Footer: Legenda Audit Log --}}
            <div class="card-footer d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 small text-secondary">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <span class="fw-bold text-dark">Status Pencatatan:</span>
                    <span class="d-inline-flex align-items-center gap-1.5">
                        <span class="badge-stock badge-stock-danger" style="padding: 2px 8px; font-size: 11px;">Stok Keluar</span>
                        <span>Otomatis memotong stok fisik rak</span>
                    </span>
                    <span class="d-inline-flex align-items-center gap-1.5">
                        <i class="fa-regular fa-clock text-primary"></i>
                        <span>Waktu tercatat dalam WIB (UTC+7)</span>
                    </span>
                </div>
                <div class="text-muted d-flex align-items-center gap-1.5">
                    <i class="fa-solid fa-circle-check text-success"></i>
                    <span>Database Sinkronisasi Realtime</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput  = document.getElementById('logSearchInput');
            const clearBtn     = document.getElementById('clearLogSearchBtn');
            const partSelect   = document.getElementById('partFilterSelect');
            const filterBtns   = document.querySelectorAll('.filter-pill-btn');
            const tableRows    = document.querySelectorAll('#logTable tbody tr.log-row');
            const noMatchRow   = document.getElementById('noLogMatchRow');
            const resetBtn     = document.getElementById('resetLogFilterBtn');
            const counterBadge = document.getElementById('logRowCount');

            let activeShift = 'all';

            function applyLogFilters() {
                const query    = searchInput ? searchInput.value.toLowerCase().trim() : '';
                const partVal  = partSelect ? partSelect.value : 'all';
                let visibleIdx = 0;

                if (clearBtn) {
                    clearBtn.style.display = query.length > 0 ? 'inline-flex' : 'none';
                }

                tableRows.forEach(function(row) {
                    const rowText  = row.innerText.toLowerCase();
                    const rowShift = row.getAttribute('data-shift');
                    const rowPart  = row.getAttribute('data-part');

                    const matchQuery = !query || rowText.includes(query);
                    const matchShift = activeShift === 'all' || rowShift === activeShift;
                    const matchPart  = partVal === 'all' || rowPart === partVal;

                    if (matchQuery && matchShift && matchPart) {
                        row.style.display = '';
                        visibleIdx++;
                        const numCell = row.querySelector('.row-number');
                        if (numCell) numCell.textContent = visibleIdx;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (counterBadge) {
                    counterBadge.textContent = visibleIdx + ' Data';
                }

                if (noMatchRow) {
                    noMatchRow.style.display = (tableRows.length > 0 && visibleIdx === 0) ? '' : 'none';
                }
            }

            if (searchInput) {
                searchInput.addEventListener('input', applyLogFilters);
            }

            if (clearBtn && searchInput) {
                clearBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    searchInput.focus();
                    applyLogFilters();
                });
            }

            if (partSelect) {
                partSelect.addEventListener('change', applyLogFilters);
            }

            filterBtns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    filterBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    activeShift = this.getAttribute('data-filter') || 'all';
                    applyLogFilters();
                });
            });

            if (resetBtn) {
                resetBtn.addEventListener('click', function() {
                    if (searchInput) searchInput.value = '';
                    if (partSelect) partSelect.value = 'all';
                    activeShift = 'all';
                    filterBtns.forEach(b => {
                        b.classList.toggle('active', b.getAttribute('data-filter') === 'all');
                    });
                    applyLogFilters();
                });
            }
        });
    </script>
@endpush
