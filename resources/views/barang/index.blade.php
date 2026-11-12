{{-- filepath: resources/views/barang/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Data Stok Spare Part - Mechanic Engineer Inventory | PT Garuda Yamato Steel')

@section('content')
    @php
        $totalJenis  = $barangs->count();
        $totalStok   = $barangs->sum('stock');
        $stokAman    = $barangs->where('stock', '>', 10)->count();
        $stokMenipis = $barangs->whereBetween('stock', [1, 10])->count();
        $stokHabis   = $barangs->where('stock', '<=', 0)->count();
        $perluRestok = $barangs->where('stock', '<=', 10)->count();
        $totalRak    = $barangs->pluck('id_rack')->unique()->filter()->count();
        $rackOptions = $barangs->pluck('rack')->filter()->unique('id_rack')->sortBy('nama_rack');
    @endphp

    <div class="catalog-shell">
        {{-- Breadcrumb --}}
        <ul class="breadcrumb-custom">
            <li class="d-inline-flex align-items-center gap-1.5">
                <i class="fa-solid fa-house"></i>
                <span>Beranda</span>
            </li>
            <li class="separator"><i class="fa-solid fa-chevron-right"></i></li>
            <li class="active">Katalog Spare Part</li>
        </ul>

        {{-- Page Header --}}
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <h1 class="page-title mb-0">Katalog &amp; Stok Spare Part Mekanik</h1>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill fw-bold"
                        style="font-size: 11px;">
                        <i class="fa-solid fa-industry me-1"></i> Workshop Mekanik
                    </span>
                </div>
                <p class="page-subtitle">
                    Monitoring ketersediaan suku cadang dan lokasi penyimpanan workshop PT Garuda Yamato Steel.
                </p>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('barang.create') }}"
                        class="btn btn-primary d-inline-flex align-items-center gap-2 px-3.5 py-2 fw-semibold shadow-sm"
                        style="font-size: 13px;">
                        <i class="fa-solid fa-plus"></i>
                        <span>Tambah Barang</span>
                    </a>
                    <a href="{{ route('rack.index') }}"
                        class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 py-2 fw-semibold shadow-sm"
                        style="font-size: 13px;">
                        <i class="fa-solid fa-layer-group text-primary"></i>
                        <span>Kelola Rak</span>
                    </a>
                    <a href="{{ route('pengurangan.index') }}"
                        class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 py-2 fw-semibold shadow-sm"
                        style="font-size: 13px;">
                        <i class="fa-solid fa-clock-rotate-left text-warning"></i>
                        <span>Log Riwayat</span>
                    </a>
                @else
                    <a href="{{ route('pengurangan.index') }}"
                        class="btn btn-primary d-inline-flex align-items-center gap-2 px-3.5 py-2 fw-semibold shadow-sm"
                        style="font-size: 13px;">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Riwayat Pengambilan Saya</span>
                    </a>
                    <a href="{{ route('rack.index') }}"
                        class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 py-2 fw-semibold shadow-sm"
                        style="font-size: 13px;">
                        <i class="fa-solid fa-layer-group text-primary"></i>
                        <span>Lihat Rak</span>
                    </a>
                @endif
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

        {{-- KPI Summary Cards --}}
        <div class="row g-3 mb-4">
            {{-- KPI 1: Total Jenis Barang --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 kpi-card kpi-accent-blue">
                    <div class="card-body d-flex align-items-center gap-3 p-3.5">
                        <div class="kpi-icon-wrapper kpi-icon-blue">
                            <i class="fa-solid fa-box-archive"></i>
                        </div>
                        <div>
                            <div class="kpi-label">Total Jenis Part</div>
                            <div class="kpi-value">{{ number_format($totalJenis, 0, ',', '.') }}</div>
                            <div class="kpi-subtext">
                                <i class="fa-solid fa-circle-check text-primary" style="font-size: 10px;"></i>
                                <span>Item terdaftar di katalog</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPI 2: Total Unit Fisik --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 kpi-card kpi-accent-green">
                    <div class="card-body d-flex align-items-center gap-3 p-3.5">
                        <div class="kpi-icon-wrapper kpi-icon-green">
                            <i class="fa-solid fa-cubes-stacked"></i>
                        </div>
                        <div>
                            <div class="kpi-label">Akumulasi Unit Fisik</div>
                            <div class="kpi-value">{{ number_format($totalStok, 0, ',', '.') }}</div>
                            <div class="kpi-subtext">
                                <i class="fa-solid fa-shield-halved text-success" style="font-size: 10px;"></i>
                                <span>Total unit fisik tersedia</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPI 3: Stok Menipis / Perlu Restock --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 kpi-card kpi-accent-amber">
                    <div class="card-body d-flex align-items-center gap-3 p-3.5">
                        <div class="kpi-icon-wrapper kpi-icon-amber">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <div class="kpi-label">Perlu Restock</div>
                            <div class="kpi-value">{{ number_format($perluRestok, 0, ',', '.') }}</div>
                            <div class="kpi-subtext">
                                <i class="fa-solid fa-circle-exclamation text-warning" style="font-size: 10px;"></i>
                                <span>Stok &le; 10 unit ({{ $stokHabis }} habis)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPI 4: Rak Terpakai --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 kpi-card kpi-accent-purple">
                    <div class="card-body d-flex align-items-center gap-3 p-3.5">
                        <div class="kpi-icon-wrapper kpi-icon-purple">
                            <i class="fa-solid fa-warehouse"></i>
                        </div>
                        <div>
                            <div class="kpi-label">Titik Rak Aktif</div>
                            <div class="kpi-value">{{ number_format($totalRak, 0, ',', '.') }}</div>
                            <div class="kpi-subtext">
                                <i class="fa-solid fa-location-dot text-purple" style="font-size: 10px;"></i>
                                <span>Zonasi rak terpakai</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Data Barang --}}
        <div class="card main-data-card">
            {{-- Toolbar: Pencarian & Filter --}}
            <div class="card-header">
                <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
                    {{-- Kiri: Search Input + Filter Rak --}}
                    <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2.5 flex-grow-1">
                        <div class="search-gys-wrapper">
                            <i class="fa-solid fa-magnifying-glass search-gys-icon"></i>
                            <input type="text" id="tableSearchInput" class="search-gys-input"
                                placeholder="Cari nama part, kode #BRG, rak, atau spesifikasi..."
                                aria-label="Cari Suku Cadang">
                            <button type="button" id="clearSearchBtn" class="search-clear-btn" title="Bersihkan pencarian">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <select id="rackFilterSelect" class="form-select filter-select-gys" style="max-width: 220px;"
                            aria-label="Filter Berdasarkan Rak">
                            <option value="all">Semua Titik Rak ({{ $totalRak }})</option>
                            @foreach ($rackOptions as $rackOpt)
                                <option value="{{ strtolower($rackOpt->nama_rack) }}">
                                    {{ $rackOpt->nama_rack }} &mdash; {{ $rackOpt->lokasi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Kanan: Filter Status Pills & Total Counter --}}
                    <div class="d-flex flex-wrap align-items-center justify-content-between justify-content-xl-end gap-2.5">
                        <div class="filter-pills-group" role="group" aria-label="Filter Status Stok">
                            <button type="button" class="filter-pill-btn active" data-filter="all">
                                <span>Semua</span>
                                <span class="filter-pill-count">{{ $totalJenis }}</span>
                            </button>
                            <button type="button" class="filter-pill-btn" data-filter="safe">
                                <span>Tersedia</span>
                                <span class="filter-pill-count">{{ $stokAman }}</span>
                            </button>
                            <button type="button" class="filter-pill-btn" data-filter="warning">
                                <span>Menipis</span>
                                <span class="filter-pill-count">{{ $stokMenipis }}</span>
                            </button>
                            <button type="button" class="filter-pill-btn" data-filter="danger">
                                <span>Habis</span>
                                <span class="filter-pill-count">{{ $stokHabis }}</span>
                            </button>
                        </div>

                        <div class="d-flex align-items-center gap-1.5 small text-secondary ps-xl-2 border-start-xl">
                            <span class="fw-medium d-none d-sm-inline">Menampilkan:</span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold px-2.5 py-1 rounded-pill"
                                id="activeRowCount">
                                {{ $totalJenis }} Item
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel Inventaris --}}
            <div class="table-responsive">
                <table class="table-gys align-middle mb-0" id="inventoryTable">
                    <thead>
                        <tr>
                            <th class="ps-3 text-center" style="width: 55px;">No</th>
                            <th>Nama Suku Cadang / Material</th>
                            <th style="width: 185px;">Stok Gudang</th>
                            <th style="width: 185px;">Posisi Rak</th>
                            <th style="width: 210px;">Area / Lokasi</th>
                            <th class="text-center pe-3"
                                style="width: {{ auth()->user()->isAdmin() ? '230px' : '120px' }};">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($barangs as $index => $barang)
                            @php
                                if ($barang->stock > 10) {
                                    $stockStatus = 'safe';
                                    $stockLabel  = 'Tersedia';
                                    $badgeClass  = 'badge-stock-safe';
                                } elseif ($barang->stock > 0) {
                                    $stockStatus = 'warning';
                                    $stockLabel  = 'Menipis';
                                    $badgeClass  = 'badge-stock-warning';
                                } else {
                                    $stockStatus = 'danger';
                                    $stockLabel  = 'Habis';
                                    $badgeClass  = 'badge-stock-danger';
                                }
                                $partCode = '#BRG-' . str_pad($barang->id_barang, 4, '0', STR_PAD_LEFT);
                                $rackName = $barang->rack?->nama_rack ?? 'Belum Diatur';
                            @endphp

                            <tr class="inventory-row"
                                data-status="{{ $stockStatus }}"
                                data-rack="{{ strtolower($rackName) }}">
                                {{-- Nomor Urut --}}
                                <td class="ps-3 text-center text-muted fw-bold row-number" style="font-size: 12px;">
                                    {{ $index + 1 }}
                                </td>

                                {{-- Nama, Kode Part & Deskripsi Barang --}}
                                <td>
                                    <div class="d-flex align-items-start gap-2.5">
                                        <div class="item-icon-box mt-0.5"
                                            style="width: 36px; height: 36px; background-color: #eff6ff; color: var(--gys-primary); border-color: #dbeafe;">
                                            <i class="fa-solid fa-gears" style="font-size: 13px;"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <span class="item-title">{{ $barang->nama_barang }}</span>
                                                <span class="part-code-badge">{{ $partCode }}</span>
                                            </div>
                                            @if ($barang->deskripsi)
                                                <div class="item-desc text-truncate" style="max-width: 380px;"
                                                    title="{{ $barang->deskripsi }}">
                                                    <i class="fa-solid fa-circle-info text-primary opacity-75 me-1"
                                                        style="font-size: 10px;"></i>
                                                    {{ $barang->deskripsi }}
                                                </div>
                                            @else
                                                <div class="text-muted fst-italic" style="font-size: 11px;">
                                                    Belum ada spesifikasi teknis tambahan
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Jumlah Stok + Badge Status --}}
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex align-items-baseline gap-1" style="min-width: 58px;">
                                            <span class="fw-bold text-dark fs-6">{{ number_format($barang->stock, 0, ',', '.') }}</span>
                                            <span class="text-muted" style="font-size: 11px;">Unit</span>
                                        </div>
                                        <span class="badge-stock {{ $badgeClass }}">{{ $stockLabel }}</span>
                                    </div>
                                </td>

                                {{-- Posisi Rak --}}
                                <td>
                                    <span class="badge-rack-pill">
                                        <i class="fa-solid fa-inbox text-primary" style="font-size: 11px;"></i>
                                        <span>{{ $rackName }}</span>
                                    </span>
                                </td>

                                {{-- Area / Lokasi --}}
                                <td>
                                    <span class="text-secondary small d-inline-flex align-items-center gap-1.5 fw-medium">
                                        <i class="fa-solid fa-location-dot text-danger opacity-75"
                                            style="font-size: 11px;"></i>
                                        <span>{{ $barang->rack?->lokasi ?? '-' }}</span>
                                    </span>
                                </td>

                                {{-- Tombol Aksi --}}
                                <td class="text-center pe-3">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        {{-- Tombol Ambil (Semua User) --}}
                                        @if ($barang->stock > 0)
                                            <a href="{{ route('pengurangan.create', $barang->id_barang) }}"
                                                class="btn-action-take" title="Ambil Suku Cadang dari Gudang">
                                                <i class="fa-solid fa-hand-holding-box"></i>
                                                <span>Ambil</span>
                                            </a>
                                        @else
                                            <button type="button"
                                                class="btn btn-light btn-sm d-inline-flex align-items-center gap-1 px-2 py-1 text-muted border"
                                                disabled title="Stok habis (0 unit)" style="font-size: 11.5px;">
                                                <i class="fa-solid fa-ban text-danger"></i>
                                                <span>Habis</span>
                                            </button>
                                        @endif

                                        {{-- Tombol Edit & Hapus (Khusus Admin) --}}
                                        @if (auth()->user()->isAdmin())
                                            <a href="{{ route('barang.edit', $barang->id_barang) }}"
                                                class="btn-action-edit" title="Edit Data Barang">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                                <span>Edit</span>
                                            </a>
                                            <form action="{{ route('barang.destroy', $barang->id_barang) }}"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang \'{{ addslashes($barang->nama_barang) }}\'?');"
                                                class="d-inline m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn-action-delete border-0" type="submit"
                                                    title="Hapus Data Barang">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                    <span>Hapus</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-0">
                                    <div class="table-empty-box">
                                        <div class="table-empty-icon">
                                            <i class="fa-solid fa-box-open"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Belum Ada Data Suku Cadang</h6>
                                        <p class="text-secondary small mb-3">
                                            Katalog inventaris suku cadang mekanik masih belum memiliki data.
                                        </p>
                                        @if (auth()->user()->isAdmin())
                                            <a href="{{ route('barang.create') }}"
                                                class="btn btn-primary btn-sm px-3.5 py-2 fw-semibold">
                                                <i class="fa-solid fa-plus me-1"></i> Tambah Barang Pertama
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        {{-- Baris Kosong Saat Filter / Pencarian Tidak Ditemukan --}}
                        @if ($barangs->isNotEmpty())
                            <tr id="noFilterMatchRow" style="display: none;">
                                <td colspan="6" class="p-0">
                                    <div class="table-empty-box py-5">
                                        <div class="table-empty-icon" style="width: 54px; height: 54px; font-size: 22px;">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Suku Cadang Tidak Ditemukan</h6>
                                        <p class="text-secondary small mb-3">
                                            Tidak ada barang yang cocok dengan kata kunci pencarian atau filter yang dipilih.
                                        </p>
                                        <button type="button" id="resetFilterBtn"
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

            {{-- Card Footer: Legenda Status Stok --}}
            <div class="card-footer d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 small text-secondary">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <span class="fw-bold text-dark">Indikator Stok:</span>
                    <span class="d-inline-flex align-items-center gap-1.5">
                        <span class="badge-stock badge-stock-safe" style="padding: 2px 8px; font-size: 11px;">Tersedia</span>
                        <span>&gt; 10 unit</span>
                    </span>
                    <span class="d-inline-flex align-items-center gap-1.5">
                        <span class="badge-stock badge-stock-warning" style="padding: 2px 8px; font-size: 11px;">Menipis</span>
                        <span>1 &ndash; 10 unit</span>
                    </span>
                    <span class="d-inline-flex align-items-center gap-1.5">
                        <span class="badge-stock badge-stock-danger" style="padding: 2px 8px; font-size: 11px;">Habis</span>
                        <span>0 unit</span>
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
            const searchInput  = document.getElementById('tableSearchInput');
            const clearBtn     = document.getElementById('clearSearchBtn');
            const rackSelect   = document.getElementById('rackFilterSelect');
            const filterBtns   = document.querySelectorAll('.filter-pill-btn');
            const tableRows    = document.querySelectorAll('#inventoryTable tbody tr.inventory-row');
            const noMatchRow   = document.getElementById('noFilterMatchRow');
            const resetBtn     = document.getElementById('resetFilterBtn');
            const counterBadge = document.getElementById('activeRowCount');

            let activeStatus = 'all';

            function applyTableFilters() {
                const query    = searchInput ? searchInput.value.toLowerCase().trim() : '';
                const rackVal  = rackSelect ? rackSelect.value : 'all';
                let visibleIdx = 0;

                if (clearBtn) {
                    clearBtn.style.display = query.length > 0 ? 'inline-flex' : 'none';
                }

                tableRows.forEach(function(row) {
                    const rowText   = row.innerText.toLowerCase();
                    const rowStatus = row.getAttribute('data-status');
                    const rowRack   = row.getAttribute('data-rack');

                    const matchQuery  = !query || rowText.includes(query);
                    const matchStatus = activeStatus === 'all' || rowStatus === activeStatus;
                    const matchRack   = rackVal === 'all' || rowRack === rackVal;

                    if (matchQuery && matchStatus && matchRack) {
                        row.style.display = '';
                        visibleIdx++;
                        const numCell = row.querySelector('.row-number');
                        if (numCell) numCell.textContent = visibleIdx;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (counterBadge) {
                    counterBadge.textContent = visibleIdx + ' Item';
                }

                if (noMatchRow) {
                    noMatchRow.style.display = (tableRows.length > 0 && visibleIdx === 0) ? '' : 'none';
                }
            }

            if (searchInput) {
                searchInput.addEventListener('input', applyTableFilters);
            }

            if (clearBtn && searchInput) {
                clearBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    searchInput.focus();
                    applyTableFilters();
                });
            }

            if (rackSelect) {
                rackSelect.addEventListener('change', applyTableFilters);
            }

            filterBtns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    filterBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    activeStatus = this.getAttribute('data-filter') || 'all';
                    applyTableFilters();
                });
            });

            if (resetBtn) {
                resetBtn.addEventListener('click', function() {
                    if (searchInput) searchInput.value = '';
                    if (rackSelect) rackSelect.value = 'all';
                    activeStatus = 'all';
                    filterBtns.forEach(b => {
                        b.classList.toggle('active', b.getAttribute('data-filter') === 'all');
                    });
                    applyTableFilters();
                });
            }
        });
    </script>
@endpush
