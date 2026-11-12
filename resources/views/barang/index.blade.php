{{-- filepath: resources/views/barang/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Data Stok Spare Part - Mechanic Engineer Inventory | PT Garuda Yamato Steel')

@section('content')
    {{-- =========================================================================
         1. PERHITUNGAN DATA RINGKASAN (KPI) & DAFTAR FILTER RAK
         ========================================================================= --}}
    @php
        $totalJenis  = $barangs->count();                                                  // Total jenis suku cadang terdaftar
        $totalStok   = $barangs->sum('stock');                                             // Akumulasi seluruh unit stok fisik
        $stokAman    = $barangs->where('stock', '>', 10)->count();                         // Jumlah barang dengan stok aman (> 10)
        $stokMenipis = $barangs->whereBetween('stock', [1, 10])->count();                  // Jumlah barang dengan stok menipis (1 - 10)
        $stokHabis   = $barangs->where('stock', '<=', 0)->count();                         // Jumlah barang yang stoknya habis (0)
        $perluRestok = $barangs->where('stock', '<=', 10)->count();                        // Total barang yang perlu restock (<= 10)
        $totalRak    = $barangs->pluck('id_rack')->unique()->filter()->count();            // Jumlah titik rak aktif yang terpakai
        $rackOptions = $barangs->pluck('rack')->filter()->unique('id_rack')->sortBy('nama_rack'); // Daftar rak untuk dropdown filter
    @endphp

    {{-- Container Utama Halaman Katalog --}}
    <div class="catalog-shell">

        {{-- =====================================================================
             2. BREADCRUMB (Navigasi Jejak Halaman)
             ===================================================================== --}}
        <ul class="breadcrumb-custom">
            <li class="d-inline-flex align-items-center gap-1.5">
                <i class="fa-solid fa-house"></i>
                <span>Beranda</span>
            </li>
            <li class="separator"><i class="fa-solid fa-chevron-right"></i></li>
            <li class="active">Katalog Spare Part</li>
        </ul>

        {{-- =====================================================================
             3. HEADER HALAMAN & TOMBOL AKSI CEPAT (Sesuai Role Login)
             ===================================================================== --}}
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
            {{-- Judul & Deskripsi Halaman --}}
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

            {{-- Tombol Navigasi Aksi: Admin bisa Tambah Barang & Kelola Rak, Staff hanya Lihat Rak & Riwayat --}}
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

        {{-- =====================================================================
             4. NOTIFIKASI FLASH MESSAGE (Pesan Sukses / Gagal dari Controller)
             ===================================================================== --}}
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

        {{-- =====================================================================
             5. KARTU RINGKASAN STATISTIK (4 KPI SUMMARY CARDS)
             ===================================================================== --}}
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

        {{-- =====================================================================
             6. KARTU UTAMA TABEL DATA BARANG (Pencarian, Filter, Tabel & Footer)
             ===================================================================== --}}
        <div class="card main-data-card">

            {{-- 6A. Toolbar Atas: Input Pencarian, Dropdown Filter Rak & Tab Filter Status Stok --}}
            <div class="card-header">
                <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
                    {{-- Bagian Kiri: Kotak Pencarian Teks & Dropdown Pilih Rak --}}
                    <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2.5 flex-grow-1">
                        <div class="search-gys-wrapper">
                            <i class="fa-solid fa-magnifying-glass search-gys-icon"></i>
                            <input type="search" id="tableSearchInput" class="search-gys-input"
                                placeholder="Cari nama part, kode #BRG, rak, atau spesifikasi..."
                                aria-label="Cari Suku Cadang">
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

                    {{-- Bagian Kanan: Tombol Filter Status Stok (Semua / Tersedia / Menipis / Habis) & Jumlah Baris --}}
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

                        {{-- Indikator Jumlah Item yang Sedang Ditampilkan --}}
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

            {{-- 6B. Tabel Daftar Inventaris Suku Cadang --}}
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
                        {{-- Looping Data Barang dari Database --}}
                        @forelse ($barangs as $index => $barang)
                            @php
                                // Menentukan warna badge & kode status stok berdasarkan jumlah unit
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
                                // Format kode part otomatis (contoh: #BRG-0001)
                                $partCode = '#BRG-' . str_pad($barang->id_barang, 4, '0', STR_PAD_LEFT);
                                $rackName = $barang->rack?->nama_rack ?? 'Belum Diatur';
                            @endphp

                            {{-- Baris Tabel Barang (data-status & data-rack digunakan untuk filter cepat) --}}
                            <tr class="inventory-row"
                                data-status="{{ $stockStatus }}"
                                data-rack="{{ strtolower($rackName) }}">
                                {{-- Kolom 1: Nomor Urut Baris --}}
                                <td class="ps-3 text-center text-muted fw-bold row-number" style="font-size: 12px;">
                                    {{ $index + 1 }}
                                </td>

                                {{-- Kolom 2: Ikon, Nama Barang, Kode Part (#BRG) & Spesifikasi/Deskripsi --}}
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

                                {{-- Kolom 3: Jumlah Stok Fisik + Badge Indikator Status --}}
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex align-items-baseline gap-1" style="min-width: 58px;">
                                            <span class="fw-bold text-dark fs-6">{{ number_format($barang->stock, 0, ',', '.') }}</span>
                                            <span class="text-muted" style="font-size: 11px;">Unit</span>
                                        </div>
                                        <span class="badge-stock {{ $badgeClass }}">{{ $stockLabel }}</span>
                                    </div>
                                </td>

                                {{-- Kolom 4: Nama / Kode Rak Penyimpanan --}}
                                <td>
                                    <span class="badge-rack-pill">
                                        <i class="fa-solid fa-inbox text-primary" style="font-size: 11px;"></i>
                                        <span>{{ $rackName }}</span>
                                    </span>
                                </td>

                                {{-- Kolom 5: Area / Lokasi Gudang --}}
                                <td>
                                    <span class="text-secondary small d-inline-flex align-items-center gap-1.5 fw-medium">
                                        <i class="fa-solid fa-location-dot text-danger opacity-75"
                                            style="font-size: 11px;"></i>
                                        <span>{{ $barang->rack?->lokasi ?? '-' }}</span>
                                    </span>
                                </td>

                                {{-- Kolom 6: Tombol Aksi (Ambil, Edit, dan Hapus) --}}
                                <td class="text-center pe-3">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        {{-- Tombol Ambil Barang: Aktif jika stok > 0, Nonaktif jika stok habis --}}
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

                                        {{-- Tombol Edit & Hapus: Hanya ditampilkan untuk Administrator --}}
                                        @if (auth()->user()->isAdmin())
                                            {{-- Tombol menuju halaman Edit Barang --}}
                                            <a href="{{ route('barang.edit', $barang->id_barang) }}"
                                                class="btn-action-edit" title="Edit Data Barang">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                                <span>Edit</span>
                                            </a>

                                            {{-- Tombol Pemicu Modal Hapus di Tengah Layar (Menggunakan atribut bawaan Bootstrap 5) --}}
                                            <button type="button" class="btn-action-delete border-0"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalHapusBarang-{{ $barang->id_barang }}"
                                                title="Hapus Data Barang">
                                                <i class="fa-solid fa-trash-can"></i>
                                                <span>Hapus</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- Tampilan Kosong: Muncul jika tabel barang di database masih belum ada isi --}}
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

                        {{-- Baris Kosong Pencarian: Muncul otomatis ketika filter/pencarian tidak menemukan hasil --}}
                        @if ($barangs->isNotEmpty())
                            <tr id="noFilterMatchRow" style="display: none;">
                                <td colspan="6" class="p-0">
                                    <div class="table-empty-box py-5">
                                        <div class="table-empty-icon" style="width: 54px; height: 54px; font-size: 22px;">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Suku Cadang Tidak Ditemukan</h6>
                                        <p class="text-secondary small mb-0">
                                            Tidak ada barang yang cocok dengan kata kunci pencarian atau filter yang dipilih.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- 6C. Footer Tabel: Keterangan Legenda Indikator Status Stok --}}
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

    {{-- =========================================================================
         7. MODAL KONFIRMASI HAPUS BARANG (POSISI DI TENGAH LAYAR)
         Menggunakan class "modal-dialog-centered" bawaan Bootstrap 5 sehingga
         tidak membutuhkan kode JavaScript tambahan untuk membuka/menutup popup.
         ========================================================================= --}}
    @if (auth()->user()->isAdmin())
        @foreach ($barangs as $barang)
            <div class="modal fade" id="modalHapusBarang-{{ $barang->id_barang }}" tabindex="-1"
                aria-labelledby="labelHapusBarang-{{ $barang->id_barang }}" aria-hidden="true">
                {{-- modal-dialog-centered membuat kotak konfirmasi tampil tepat di tengah layar --}}
                <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="modal-body p-4 text-center">

                            {{-- Ikon Peringatan Hapus (Merah) --}}
                            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                                style="width: 60px; height: 60px; background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca;">
                                <i class="fa-solid fa-trash-can fs-4"></i>
                            </div>

                            {{-- Judul & Pertanyaan Konfirmasi --}}
                            <h5 class="fw-bold text-dark mb-1" id="labelHapusBarang-{{ $barang->id_barang }}">
                                Konfirmasi Hapus Barang
                            </h5>
                            <p class="text-secondary small mb-3">
                                Apakah Anda yakin ingin menghapus barang ini dari katalog inventaris?
                            </p>

                            {{-- Ringkasan Detail Barang yang Akan Dihapus --}}
                            <div class="p-3 rounded-3 bg-light border text-start mb-3">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                    <span class="fw-bold text-dark" style="font-size: 13.5px;">{{ $barang->nama_barang }}</span>
                                    <span class="part-code-badge">#BRG-{{ str_pad($barang->id_barang, 4, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <div class="text-muted small d-flex align-items-center gap-3 flex-wrap">
                                    <span>
                                        <i class="fa-solid fa-cubes-stacked me-1 text-primary"></i>
                                        Stok: <strong class="text-dark">{{ $barang->stock }} Unit</strong>
                                    </span>
                                    <span>
                                        <i class="fa-solid fa-inbox me-1 text-secondary"></i>
                                        {{ $barang->rack?->nama_rack ?? 'Belum Diatur' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Form Control Hapus Barang (Method DELETE) dengan Pilihan "Tidak" atau "Ya, Hapus" --}}
                            <form action="{{ route('barang.destroy', $barang->id_barang) }}" method="POST"
                                class="d-flex justify-content-center gap-2 mt-3">
                                @csrf
                                @method('DELETE')

                                {{-- Tombol Batal / Tidak (Menutup modal secara otomatis dengan data-bs-dismiss="modal") --}}
                                <button type="button"
                                    class="btn btn-outline-secondary px-4 py-2 fw-semibold flex-fill"
                                    data-bs-dismiss="modal" style="font-size: 13px;">
                                    <i class="fa-solid fa-xmark me-1"></i> Tidak
                                </button>

                                {{-- Tombol Konfirmasi Ya, Hapus (Mengirim request DELETE ke BarangController@destroy) --}}
                                <button type="submit"
                                    class="btn btn-danger px-4 py-2 fw-bold shadow-sm flex-fill"
                                    style="font-size: 13px; border-radius: 10px;">
                                    <i class="fa-solid fa-trash-can me-1"></i> Ya, Hapus
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endsection

{{-- =============================================================================
     8. SCRIPT FILTER & PENCARIAN INSTAN TABEL (RINGKAS & TANPA RELOAD HALAMAN)
     ============================================================================= --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Mengambil elemen input pencarian, dropdown rak, tombol tab status, dan baris tabel
            const search = document.getElementById('tableSearchInput');
            const rack   = document.getElementById('rackFilterSelect');
            const pills  = document.querySelectorAll('.filter-pill-btn');
            const rows   = document.querySelectorAll('#inventoryTable tbody tr.inventory-row');
            const empty  = document.getElementById('noFilterMatchRow');
            const count  = document.getElementById('activeRowCount');
            let status   = 'all'; // Menyimpan status filter aktif ('all', 'safe', 'warning', 'danger')

            // Fungsi utama untuk menyaring baris tabel berdasarkan kata kunci, status stok, dan rak
            const filterTable = () => {
                const q = (search?.value || '').toLowerCase().trim();
                const r = rack?.value || 'all';
                let visible = 0;

                rows.forEach(row => {
                    // Cek kecocokan teks pencarian, status stok, dan posisi rak
                    const show = (!q || row.innerText.toLowerCase().includes(q))
                        && (status === 'all' || row.dataset.status === status)
                        && (r === 'all' || row.dataset.rack === r);

                    // Tampilkan atau sembunyikan baris serta perbarui nomor urut otomatis
                    row.style.display = show ? '' : 'none';
                    if (show) row.querySelector('.row-number').textContent = ++visible;
                });

                // Perbarui badge jumlah item yang tampil & tampilkan pesan kosong jika hasil = 0
                if (count) count.textContent = `${visible} Item`;
                if (empty) empty.style.display = (rows.length && !visible) ? '' : 'none';
            };

            // Jalankan filter saat mengetik di kolom pencarian atau memilih dropdown rak
            search?.addEventListener('input', filterTable);
            rack?.addEventListener('change', filterTable);

            // Jalankan filter saat salah satu tombol tab status stok diklik
            pills.forEach(btn => btn.addEventListener('click', () => {
                pills.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                status = btn.dataset.filter;
                filterTable();
            }));
        });
    </script>
@endpush
