{{-- filepath: resources/views/Rack/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Manajemen Titik Rak - Mechanic Engineer Inventory | PT Garuda Yamato Steel')

@section('content')
    @php
        $totalRak       = $racks->count();
        $rakTerisi      = $racks->where('barangs_count', '>', 0)->count();
        $rakKosong      = $racks->where('barangs_count', '<=', 0)->count();
        $totalPartDiRak = $racks->sum('barangs_count');
        $lokasiOptions  = $racks->pluck('lokasi')->filter()->unique()->sort()->values();
        $totalZona      = $lokasiOptions->count();
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
            <li class="active">Manajemen Titik Rak</li>
        </ul>

        {{-- Page Header --}}
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <h1 class="page-title mb-0">Manajemen Titik Rak Penyimpanan</h1>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill fw-bold"
                        style="font-size: 11px;">
                        <i class="fa-solid fa-warehouse me-1"></i> Zonasi Gudang
                    </span>
                </div>
                <p class="page-subtitle">
                    Katalog penomoran rak dan zonasi area penyimpanan suku cadang workshop mekanik PT Garuda Yamato Steel.
                </p>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('rack.create') }}"
                        class="btn btn-primary d-inline-flex align-items-center gap-2 px-3.5 py-2 fw-semibold shadow-sm"
                        style="font-size: 13px;">
                        <i class="fa-solid fa-plus"></i>
                        <span>Tambah Rak Baru</span>
                    </a>
                    <a href="{{ route('barang.index') }}"
                        class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 py-2 fw-semibold shadow-sm"
                        style="font-size: 13px;">
                        <i class="fa-solid fa-boxes-stacked text-primary"></i>
                        <span>Katalog Barang</span>
                    </a>
                    <a href="{{ route('pengurangan.index') }}"
                        class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 py-2 fw-semibold shadow-sm"
                        style="font-size: 13px;">
                        <i class="fa-solid fa-clock-rotate-left text-warning"></i>
                        <span>Log Riwayat</span>
                    </a>
                @else
                    <a href="{{ route('barang.index') }}"
                        class="btn btn-primary d-inline-flex align-items-center gap-2 px-3.5 py-2 fw-semibold shadow-sm"
                        style="font-size: 13px;">
                        <i class="fa-solid fa-boxes-stacked"></i>
                        <span>Katalog Barang</span>
                    </a>
                    <a href="{{ route('pengurangan.index') }}"
                        class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 py-2 fw-semibold shadow-sm"
                        style="font-size: 13px;">
                        <i class="fa-solid fa-clock-rotate-left text-warning"></i>
                        <span>Riwayat Pengambilan Saya</span>
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
            {{-- KPI 1: Total Titik Rak --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 kpi-card kpi-accent-purple">
                    <div class="card-body d-flex align-items-center gap-3 p-3.5">
                        <div class="kpi-icon-wrapper kpi-icon-purple">
                            <i class="fa-solid fa-warehouse"></i>
                        </div>
                        <div>
                            <div class="kpi-label">Total Titik Rak</div>
                            <div class="kpi-value">{{ number_format($totalRak, 0, ',', '.') }}</div>
                            <div class="kpi-subtext">
                                <i class="fa-solid fa-layer-group text-purple" style="font-size: 10px;"></i>
                                <span>Rak terdaftar di sistem</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPI 2: Rak Terisi --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 kpi-card kpi-accent-green">
                    <div class="card-body d-flex align-items-center gap-3 p-3.5">
                        <div class="kpi-icon-wrapper kpi-icon-green">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <div>
                            <div class="kpi-label">Rak Terisi Aktif</div>
                            <div class="kpi-value">{{ number_format($rakTerisi, 0, ',', '.') }}</div>
                            <div class="kpi-subtext">
                                <i class="fa-solid fa-circle-check text-success" style="font-size: 10px;"></i>
                                <span>Menyimpan suku cadang</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPI 3: Rak Kosong --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 kpi-card kpi-accent-amber">
                    <div class="card-body d-flex align-items-center gap-3 p-3.5">
                        <div class="kpi-icon-wrapper kpi-icon-amber">
                            <i class="fa-solid fa-inbox"></i>
                        </div>
                        <div>
                            <div class="kpi-label">Rak Kosong</div>
                            <div class="kpi-value">{{ number_format($rakKosong, 0, ',', '.') }}</div>
                            <div class="kpi-subtext">
                                <i class="fa-solid fa-circle-info text-warning" style="font-size: 10px;"></i>
                                <span>Siap ditempati part baru</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPI 4: Total Jenis Part Tertata --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 kpi-card kpi-accent-blue">
                    <div class="card-body d-flex align-items-center gap-3 p-3.5">
                        <div class="kpi-icon-wrapper kpi-icon-blue">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </div>
                        <div>
                            <div class="kpi-label">Part Tertata di Rak</div>
                            <div class="kpi-value">{{ number_format($totalPartDiRak, 0, ',', '.') }}</div>
                            <div class="kpi-subtext">
                                <i class="fa-solid fa-location-dot text-primary" style="font-size: 10px;"></i>
                                <span>Tersebar di {{ $totalZona }} zona lokasi</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Data Rak --}}
        <div class="card main-data-card">
            {{-- Toolbar: Pencarian & Filter --}}
            <div class="card-header">
                <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
                    {{-- Kiri: Search Input + Filter Lokasi --}}
                    <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2.5 flex-grow-1">
                        <div class="search-gys-wrapper">
                            <i class="fa-solid fa-magnifying-glass search-gys-icon"></i>
                            <input type="search" id="rackSearchInput" class="search-gys-input"
                                placeholder="Cari nama rak, kode #RAK, atau lokasi area..."
                                aria-label="Cari Titik Rak">
                        </div>

                        <select id="lokasiFilterSelect" class="form-select filter-select-gys" style="max-width: 230px;"
                            aria-label="Filter Berdasarkan Lokasi">
                            <option value="all">Semua Zona Lokasi ({{ $totalZona }})</option>
                            @foreach ($lokasiOptions as $lokasiOpt)
                                <option value="{{ strtolower($lokasiOpt) }}">
                                    Area: {{ $lokasiOpt }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Kanan: Filter Status Pills & Total Counter --}}
                    <div class="d-flex flex-wrap align-items-center justify-content-between justify-content-xl-end gap-2.5">
                        <div class="filter-pills-group" role="group" aria-label="Filter Status Rak">
                            <button type="button" class="filter-pill-btn active" data-filter="all">
                                <span>Semua</span>
                                <span class="filter-pill-count">{{ $totalRak }}</span>
                            </button>
                            <button type="button" class="filter-pill-btn" data-filter="filled">
                                <span>Terisi</span>
                                <span class="filter-pill-count">{{ $rakTerisi }}</span>
                            </button>
                            <button type="button" class="filter-pill-btn" data-filter="empty">
                                <span>Kosong</span>
                                <span class="filter-pill-count">{{ $rakKosong }}</span>
                            </button>
                        </div>

                        <div class="d-flex align-items-center gap-1.5 small text-secondary ps-xl-2 border-start-xl">
                            <span class="fw-medium d-none d-sm-inline">Menampilkan:</span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold px-2.5 py-1 rounded-pill"
                                id="rackRowCount">
                                {{ $totalRak }} Rak
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel Rak --}}
            <div class="table-responsive">
                <table class="table-gys align-middle mb-0" id="rackTable">
                    <thead>
                        <tr>
                            <th class="ps-3 text-center" style="width: 55px;">No</th>
                            <th>Kode / Nama Titik Rak</th>
                            <th>Lokasi / Zonasi Workshop</th>
                            <th style="width: 220px;">Barang Tersimpan</th>
                            @if (auth()->user()->isAdmin())
                                <th class="text-center pe-3" style="width: 185px;">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($racks as $index => $rack)
                            @php
                                $isFilled = $rack->barangs_count > 0;
                                $rackStatus = $isFilled ? 'filled' : 'empty';
                                $rackCode = '#RAK-' . str_pad($rack->id_rack, 4, '0', STR_PAD_LEFT);
                                $lokasiName = $rack->lokasi ?: 'Belum Diatur';
                            @endphp

                            <tr class="rack-row"
                                data-status="{{ $rackStatus }}"
                                data-lokasi="{{ strtolower($lokasiName) }}">
                                <td class="ps-3 text-center text-muted fw-bold row-number" style="font-size: 12px;">
                                    {{ $index + 1 }}
                                </td>

                                {{-- Nama & Kode Rak --}}
                                <td>
                                    <div class="d-flex align-items-start gap-2.5">
                                        <div class="item-icon-box mt-0.5"
                                            style="width: 36px; height: 36px; background-color: #faf5ff; color: #7e22ce; border-color: #f3e8ff;">
                                            <i class="fa-solid fa-warehouse" style="font-size: 13px;"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <span class="item-title">{{ $rack->nama_rack }}</span>
                                                <span class="part-code-badge">{{ $rackCode }}</span>
                                            </div>
                                            <div class="item-desc">
                                                <i class="fa-solid fa-tag text-secondary opacity-75 me-1" style="font-size: 10px;"></i>
                                                Titik penyimpanan suku cadang workshop
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Lokasi / Zonasi --}}
                                <td>
                                    <span class="text-secondary small d-inline-flex align-items-center gap-1.5 fw-medium">
                                        <i class="fa-solid fa-location-dot text-danger opacity-75" style="font-size: 11.5px;"></i>
                                        <span>{{ $lokasiName }}</span>
                                    </span>
                                </td>

                                {{-- Jumlah Barang Tersimpan --}}
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex align-items-baseline gap-1" style="min-width: 55px;">
                                            <span class="fw-bold text-dark fs-6">{{ number_format($rack->barangs_count, 0, ',', '.') }}</span>
                                            <span class="text-muted" style="font-size: 11px;">Part</span>
                                        </div>
                                        @if ($isFilled)
                                            <span class="badge-stock badge-stock-safe">Terisi</span>
                                        @else
                                            <span class="badge-stock badge-stock-warning">Kosong</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Aksi (Admin Only) --}}
                                @if (auth()->user()->isAdmin())
                                    <td class="text-center pe-3">
                                        <div class="d-inline-flex align-items-center gap-1.5">
                                            <a href="{{ route('rack.edit', $rack->id_rack) }}"
                                                class="btn-action-edit" title="Edit Titik Rak">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                                <span>Edit</span>
                                            </a>
                                            <button type="button" class="btn-action-delete border-0"
                                                @if (!$isFilled)
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalHapusRak-{{ $rack->id_rack }}"
                                                @else
                                                    disabled style="opacity: 0.5; cursor: not-allowed;"
                                                @endif
                                                title="{{ $isFilled ? 'Tidak dapat dihapus: masih ada ' . $rack->barangs_count . ' barang di rak ini' : 'Hapus Titik Rak' }}">
                                                <i class="fa-solid fa-trash-can"></i>
                                                <span>Hapus</span>
                                            </button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->isAdmin() ? 5 : 4 }}" class="p-0">
                                    <div class="table-empty-box">
                                        <div class="table-empty-icon">
                                            <i class="fa-solid fa-warehouse"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Belum Ada Titik Rak</h6>
                                        <p class="text-secondary small mb-3">
                                            Data titik rak penyimpanan workshop masih kosong.
                                        </p>
                                        @if (auth()->user()->isAdmin())
                                            <a href="{{ route('rack.create') }}"
                                                class="btn btn-primary btn-sm px-3.5 py-2 fw-semibold">
                                                <i class="fa-solid fa-plus me-1"></i> Tambah Rak Pertama
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        {{-- Baris Kosong Saat Filter / Pencarian Tidak Ditemukan --}}
                        @if ($racks->isNotEmpty())
                            <tr id="noRackMatchRow" style="display: none;">
                                <td colspan="{{ auth()->user()->isAdmin() ? 5 : 4 }}" class="p-0">
                                    <div class="table-empty-box py-5">
                                        <div class="table-empty-icon" style="width: 54px; height: 54px; font-size: 22px;">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Titik Rak Tidak Ditemukan</h6>
                                        <p class="text-secondary small mb-0">
                                            Tidak ada rak yang cocok dengan kata kunci pencarian atau filter zona yang dipilih.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Card Footer: Legenda Status Rak --}}
            <div class="card-footer d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 small text-secondary">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <span class="fw-bold text-dark">Indikator Rak:</span>
                    <span class="d-inline-flex align-items-center gap-1.5">
                        <span class="badge-stock badge-stock-safe" style="padding: 2px 8px; font-size: 11px;">Terisi</span>
                        <span>&ge; 1 jenis part tersimpan</span>
                    </span>
                    <span class="d-inline-flex align-items-center gap-1.5">
                        <span class="badge-stock badge-stock-warning" style="padding: 2px 8px; font-size: 11px;">Kosong</span>
                        <span>0 part (dapat dihapus / diisi baru)</span>
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
         MODAL KONFIRMASI HAPUS RAK (POSISI DI TENGAH LAYAR)
         Menggunakan class "modal-dialog-centered" bawaan Bootstrap 5 sehingga
         tidak membutuhkan kode JavaScript tambahan untuk membuka/menutup popup.
         ========================================================================= --}}
    @if (auth()->user()->isAdmin())
        @foreach ($racks->where('barangs_count', '<=', 0) as $rack)
            <div class="modal fade" id="modalHapusRak-{{ $rack->id_rack }}" tabindex="-1"
                aria-labelledby="labelHapusRak-{{ $rack->id_rack }}" aria-hidden="true">
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
                            <h5 class="fw-bold text-dark mb-1" id="labelHapusRak-{{ $rack->id_rack }}">
                                Konfirmasi Hapus Titik Rak
                            </h5>
                            <p class="text-secondary small mb-3">
                                Apakah Anda yakin ingin menghapus titik rak kosong ini dari sistem?
                            </p>

                            {{-- Ringkasan Detail Rak yang Akan Dihapus --}}
                            <div class="p-3 rounded-3 bg-light border text-start mb-3">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                    <span class="fw-bold text-dark" style="font-size: 13.5px;">{{ $rack->nama_rack }}</span>
                                    <span class="part-code-badge">#RAK-{{ str_pad($rack->id_rack, 4, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <div class="text-muted small">
                                    <i class="fa-solid fa-location-dot me-1 text-danger"></i>
                                    Area: {{ $rack->lokasi ?: '-' }}
                                </div>
                            </div>

                            {{-- Form Control Hapus Rak (Method DELETE) dengan Pilihan "Tidak" atau "Ya, Hapus" --}}
                            <form action="{{ route('rack.destroy', $rack->id_rack) }}" method="POST"
                                class="d-flex justify-content-center gap-2 mt-3">
                                @csrf
                                @method('DELETE')

                                {{-- Tombol Batal / Tidak (Menutup modal otomatis) --}}
                                <button type="button"
                                    class="btn btn-outline-secondary px-4 py-2 fw-semibold flex-fill"
                                    data-bs-dismiss="modal" style="font-size: 13px;">
                                    <i class="fa-solid fa-xmark me-1"></i> Tidak
                                </button>

                                {{-- Tombol Konfirmasi Ya, Hapus (Mengirim request DELETE ke RackController@destroy) --}}
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
     SCRIPT FILTER & PENCARIAN INSTAN TABEL RAK (RINGKAS & TANPA RELOAD HALAMAN)
     ============================================================================= --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Mengambil elemen pencarian, dropdown lokasi, tombol tab status, dan baris tabel rak
            const search = document.getElementById('rackSearchInput');
            const lokasi = document.getElementById('lokasiFilterSelect');
            const pills  = document.querySelectorAll('.filter-pill-btn');
            const rows   = document.querySelectorAll('#rackTable tbody tr.rack-row');
            const empty  = document.getElementById('noRackMatchRow');
            const count  = document.getElementById('rackRowCount');
            let status   = 'all'; // Menyimpan status filter rak ('all', 'filled', 'empty')

            // Fungsi utama untuk menyaring baris rak berdasarkan kata kunci, status isi, dan zona lokasi
            const filterRack = () => {
                const q = (search?.value || '').toLowerCase().trim();
                const l = lokasi?.value || 'all';
                let visible = 0;

                rows.forEach(row => {
                    const show = (!q || row.innerText.toLowerCase().includes(q))
                        && (status === 'all' || row.dataset.status === status)
                        && (l === 'all' || row.dataset.lokasi === l);
                    row.style.display = show ? '' : 'none';
                    if (show) row.querySelector('.row-number').textContent = ++visible;
                });

                if (count) count.textContent = `${visible} Rak`;
                if (empty) empty.style.display = (rows.length && !visible) ? '' : 'none';
            };

            // Event listener saat input pencarian diketik, lokasi dipilih, atau tab status diklik
            search?.addEventListener('input', filterRack);
            lokasi?.addEventListener('change', filterRack);
            pills.forEach(btn => btn.addEventListener('click', () => {
                pills.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                status = btn.dataset.filter;
                filterRack();
            }));
        });
    </script>
@endpush
