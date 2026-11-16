{{-- filepath: resources/views/barang/edit.blade.php --}}
@extends('layouts.app')

@section('title',
    'Edit Suku Cadang #BRG-' .
    str_pad($barang->id_barang, 4, '0', STR_PAD_LEFT) .
    ' - Mechanic Engineer
    Inventory | PT Garuda Yamato Steel')

@section('nav_badge')
    <span class="badge bg-white text-secondary border px-3 py-1.5 fw-bold rounded-pill">
        <i class="fa-solid fa-pen-to-square text-primary me-1"></i> Mode Edit
    </span>
@endsection

@section('content')
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
            <li><a href="{{ route('barang.index') }}">Katalog Spare Part</a></li>
            <li class="separator"><i class="fa-solid fa-chevron-right"></i></li>
            <li class="active">Edit #BRG-{{ str_pad($barang->id_barang, 4, '0', STR_PAD_LEFT) }}</li>
        </ul>

        {{-- Page Header --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <div class="d-flex align-items-center gap-2.5 mb-1">
                    <h1 class="page-title mb-0">Edit Data Suku Cadang</h1>
                    <span
                        class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill fw-bold"
                        style="font-size: 11.5px;">
                        #BRG-{{ str_pad($barang->id_barang, 4, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
                <p class="page-subtitle">Perbarui spesifikasi teknis, stok fisik opname, atau lokasi penempatan rak
                    workshop.
                </p>
            </div>
            <a href="{{ route('barang.index') }}"
                class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3.5 py-2 fw-semibold shadow-sm"
                style="font-size: 13px;">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Katalog</span>
            </a>
        </div>

        {{-- Validasi Error --}}
        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm d-flex align-items-start gap-3 mb-4 p-3 rounded-3"
                role="alert">
                <i class="fa-solid fa-circle-exclamation fs-5 text-danger mt-0.5"></i>
                <div class="flex-grow-1">
                    <div class="fw-bold mb-1 text-dark" style="font-size: 13.5px;">Terdapat kesalahan pada isian formulir:
                    </div>
                    <ul class="mb-0 ps-3 small text-danger fw-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="row g-4">

            {{-- Kolom Kiri: Formulir Edit --}}
            <div class="col-lg-8">
                <div class="card form-card-clean">

                    {{-- Card Header --}}
                    <div class="card-header bg-white p-3.5 border-bottom d-flex align-items-center gap-3">
                        <div class="item-icon-box"
                            style="width: 38px; height: 38px; background-color: #eff6ff; color: #1d4ed8; border: none;">
                            <i class="fa-solid fa-pen-to-square fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Formulir Pembaruan Data Spare Part</h6>
                            <span class="text-muted small">Perubahan langsung terintegrasi secara realtime ke database
                                inventaris.</span>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('barang.update', $barang->id_barang) }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- Nama Barang --}}
                            <div class="mb-4">
                                <label for="nama_barang" class="form-label-gys">
                                    <span>Nama / Spesifikasi Suku Cadang <span class="text-danger">*</span></span>
                                </label>
                                <div class="input-group">
                                    <span class="input-icon-box"><i class="fa-solid fa-tag"></i></span>
                                    <input type="text"
                                        class="form-control form-control-custom @error('nama_barang') is-invalid @enderror"
                                        id="nama_barang" name="nama_barang"
                                        value="{{ old('nama_barang', $barang->nama_barang) }}"
                                        placeholder="Contoh: Bearing SKF 6205-2RSH" required autofocus>
                                </div>
                                @error('nama_barang')
                                    <div class="text-danger small mt-1.5 fw-medium">
                                        <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $message }}
                                    </div>
                                @else
                                    <div class="form-hint">
                                        Gunakan nama resmi spare part beserta ukuran, tipe, atau spesifikasi teknisnya.
                                    </div>
                                @enderror
                            </div>

                            <div class="row mb-4 ">
                                {{-- Jumlah Stok --}}
                                <div class="col-md-6">
                                    <label for="stock" class="form-label-gys">
                                        <span>Stok Fisik Aktual <span class="text-danger">*</span></span>
                                    </label>
                                    <div class="input-group w-50">
                                        <span class="input-icon-box"><i class="fa-solid fa-cubes-stacked"></i></span>
                                        <input type="number" min="0" step="1"
                                            class="form-control form-control-custom @error('stock') is-invalid @enderror"
                                            id="stock" name="stock" value="{{ old('stock', $barang->stock) }}"
                                            placeholder="Contoh: 25" required>
                                        <select
                                            class="form-select form-select-custom @error('kategori') is-invalid @enderror"
                                            name="kategori" id="kategori" required style="max-width: 80px;">
                                            <option value="" disabled>Measurement Unit</option>
                                            <option value="PCS"
                                                {{ old('kategori', $barang->kategori) == 'PCS' ? 'selected' : '' }}>PCS
                                            </option>
                                            <option value="KG"
                                                {{ old('kategori', $barang->kategori) == 'KG' ? 'selected' : '' }}>KG
                                            </option>
                                            <option value="LT"
                                                {{ old('kategori', $barang->kategori) == 'LT' ? 'selected' : '' }}>LT
                                            </option>
                                            <option value="MTR"
                                                {{ old('kategori', $barang->kategori) == 'MTR' ? 'selected' : '' }}>MTR
                                            </option>
                                            <option value="UNT"
                                                {{ old('kategori', $barang->kategori) == 'UNT' ? 'selected' : '' }}>UNT
                                            </option>
                                        </select>
                                    </div>
                                    @error('stock')
                                        <div class="text-danger small mt-1.5 fw-medium">
                                            <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- Rak & Lokasi --}}
                                <div class="col-md-6">
                                    <label for="id_rack" class="form-label-gys">
                                        <span>Posisi Rak & Area <span class="text-danger">*</span></span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-icon-box"><i class="fa-solid fa-warehouse"></i></span>
                                        <select
                                            class="form-select form-select-custom @error('id_rack') is-invalid @enderror"
                                            name="id_rack" id="id_rack" required>
                                            <option value="" disabled>-- Pilih Rak & Lokasi --</option>
                                            @foreach ($racks as $rack)
                                                <option value="{{ $rack->id_rack }}"
                                                    {{ old('id_rack', $barang->id_rack) == $rack->id_rack ? 'selected' : '' }}>
                                                    {{ $rack->nama_rack }} (Area: {{ $rack->lokasi }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('id_rack')
                                        <div class="text-danger small mt-1.5 fw-medium">
                                            <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $message }}
                                        </div>
                                    @else
                                        <div class="form-hint">
                                            Pilih rak penempatan spare part di workshop.
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Fungsi & Deskripsi --}}
                            <div class="mb-4">
                                <label for="deskripsi" class="form-label-gys">
                                    <span>Fungsi & Deskripsi Barang</span>
                                    <span class="badge bg-light text-muted border fw-normal"
                                        style="font-size: 10.5px;">Opsional</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-icon-box align-self-stretch"
                                        style="border-bottom-left-radius: 8px;">
                                        <i class="fa-solid fa-align-left"></i>
                                    </span>
                                    <textarea class="form-control form-control-custom @error('deskripsi') is-invalid @enderror" id="deskripsi"
                                        name="deskripsi" rows="3" placeholder="Jelaskan fungsi, kegunaan pada mesin, atau keterangan suku cadang">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
                                </div>
                                @error('deskripsi')
                                    <div class="text-danger small mt-1.5 fw-medium">
                                        <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $message }}
                                    </div>
                                @else
                                    <div class="form-hint">
                                        Deskripsi fungsi membantu teknisi memahami peruntukan spare part pada mesin.
                                    </div>
                                @enderror
                            </div>

                            {{-- Banner Riwayat / Log Pencatatan --}}
                            @php
                                $isUpdated = $barang->updated_at && $barang->updated_at != $barang->created_at;
                            @endphp
                            <div class="p-3.5 rounded-3 mb-4 bg-light border">
                                <div
                                    class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="item-icon-box"
                                            style="width: 30px; height: 30px; background-color: #eff6ff; color: var(--gys-primary); border-color: #dbeafe;">
                                            <i class="fa-solid fa-clock-rotate-left" style="font-size: 12px;"></i>
                                        </div>
                                        <span class="fw-bold text-dark" style="font-size: 13px;">Log Pencatatan
                                            Barang</span>
                                    </div>
                                    @if ($isUpdated)
                                        <span
                                            class="badge bg-warning-subtle text-dark border border-warning-subtle px-2.5 py-1 rounded-pill"
                                            style="font-size: 11px;">
                                            <i class="fa-solid fa-pen-clip me-1 text-warning"></i>
                                            Diperbarui {{ $barang->updated_at->locale('id')->diffForHumans() }}
                                        </span>
                                    @else
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill"
                                            style="font-size: 11px;">
                                            <i class="fa-solid fa-circle-check me-1"></i> Data Awal (Belum Diubah)
                                        </span>
                                    @endif
                                </div>

                                <div class="row g-3">
                                    {{-- Waktu Pendaftaran Awal --}}
                                    <div class="col-sm-6">
                                        <div
                                            class="p-2.5 rounded-2 bg-white border h-100 d-flex align-items-start gap-2.5">
                                            <div class="item-icon-box mt-0.5"
                                                style="width: 32px; height: 32px; background-color: #ecfdf5; color: #059669; border-color: #d1fae5;">
                                                <i class="fa-solid fa-calendar-plus" style="font-size: 13px;"></i>
                                            </div>
                                            <div>
                                                <div class="text-muted fw-bold text-uppercase"
                                                    style="font-size: 10.5px; letter-spacing: 0.04em;">
                                                    Pertama Didaftarkan
                                                </div>
                                                @if ($barang->created_at)
                                                    <div class="fw-bold text-dark mt-0.5" style="font-size: 13px;">
                                                        {{ $barang->created_at->locale('id')->translatedFormat('d F Y') }}
                                                    </div>
                                                    <div class="text-secondary" style="font-size: 11.5px;">
                                                        <i class="fa-regular fa-clock me-1 text-success"></i>
                                                        Pukul {{ $barang->created_at->format('H:i') }} WIB
                                                    </div>
                                                @else
                                                    <div class="text-muted small mt-0.5">Tidak tercatat</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Waktu Terakhir Diubah --}}
                                    <div class="col-sm-6">
                                        <div
                                            class="p-2.5 rounded-2 bg-white border h-100 d-flex align-items-start gap-2.5">
                                            <div class="item-icon-box mt-0.5"
                                                style="width: 32px; height: 32px; background-color: {{ $isUpdated ? '#fffbeb' : '#f1f5f9' }}; color: {{ $isUpdated ? '#d97706' : '#64748b' }}; border-color: {{ $isUpdated ? '#fef3c7' : '#e2e8f0' }};">
                                                <i class="fa-solid fa-file-pen" style="font-size: 13px;"></i>
                                            </div>
                                            <div>
                                                <div class="text-muted fw-bold text-uppercase"
                                                    style="font-size: 10.5px; letter-spacing: 0.04em;">
                                                    Terakhir Diperbarui
                                                </div>
                                                @if ($isUpdated)
                                                    <div class="fw-bold text-dark mt-0.5" style="font-size: 13px;">
                                                        {{ $barang->updated_at->locale('id')->translatedFormat('d F Y') }}
                                                    </div>
                                                    <div class="text-secondary" style="font-size: 11.5px;">
                                                        <i class="fa-regular fa-clock me-1 text-warning"></i>
                                                        Pukul {{ $barang->updated_at->format('H:i') }} WIB
                                                    </div>
                                                @else
                                                    <div class="fw-semibold text-secondary mt-0.5"
                                                        style="font-size: 12.5px;">
                                                        Belum ada perubahan
                                                    </div>
                                                    <div class="text-muted" style="font-size: 11px;">
                                                        Masih sesuai data pendaftaran awal
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4 border-light-subtle">

                            {{-- Tombol Aksi --}}
                            <div
                                class="d-flex flex-column-reverse flex-sm-row justify-content-between align-items-center gap-3">
                                <a href="{{ route('barang.index') }}"
                                    class="btn btn-outline-secondary px-4 py-2 fw-semibold w-100 w-sm-auto"
                                    style="font-size: 13px;">
                                    <i class="fa-solid fa-xmark me-1"></i> Batal
                                </a>
                                <button type="submit"
                                    class="btn btn-primary px-4 py-2.5 fw-bold shadow-sm w-100 w-sm-auto d-inline-flex align-items-center justify-content-center gap-2"
                                    style="font-size: 13px;">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    <span>Simpan Perubahan</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Ringkasan & Daftar Rak --}}
            <div class="col-lg-4">
                <div class="d-flex flex-column gap-4">

                    {{-- Ringkasan Data Saat Ini --}}
                    <div class="card form-card-clean">
                        <div
                            class="card-header bg-white p-3 border-bottom d-flex align-items-center justify-content-between">
                            <span class="fw-bold text-dark small d-inline-flex align-items-center gap-2">
                                <i class="fa-solid fa-box-archive text-primary"></i>
                                <span>Status Saat Ini</span>
                            </span>
                            <span class="badge bg-light text-secondary border font-monospace" style="font-size: 10.5px;">
                                #BRG-{{ str_pad($barang->id_barang, 4, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>
                        <div class="card-body p-3.5">
                            <div class="d-flex flex-column gap-3 small">
                                <div class="d-flex justify-content-between align-items-center pb-2.5 border-bottom">
                                    <span class="text-secondary">Stok Tersedia:</span>
                                    <span class="fw-bold text-dark fs-6">{{ $barang->stock }} Unit</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pb-2.5 border-bottom">
                                    <span class="text-secondary">Titik Rak:</span>
                                    <span class="badge-rack-pill">
                                        <i class="fa-solid fa-inbox text-secondary" style="font-size: 10px;"></i>
                                        <span>{{ $barang->rack?->nama_rack ?? 'Belum Ditentukan' }}</span>
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-secondary">Lokasi Gudang:</span>
                                    <span class="text-dark fw-semibold d-inline-flex align-items-center gap-1.5">
                                        <i class="fa-solid fa-location-dot text-danger opacity-75"
                                            style="font-size: 11px;"></i>
                                        <span>{{ $barang->rack?->lokasi ?? '-' }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Daftar Rak Tersedia --}}
                    <div class="card form-card-clean">
                        <div
                            class="card-header bg-white p-3 border-bottom d-flex align-items-center justify-content-between">
                            <span class="fw-bold text-dark small d-inline-flex align-items-center gap-2">
                                <i class="fa-solid fa-layer-group text-primary"></i>
                                <span>Daftar Titik Rak Tersedia</span>
                            </span>
                            <span
                                class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold px-2 py-1">
                                {{ $racks->count() }} Rak
                            </span>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush small" style="max-height: 220px; overflow-y: auto;">
                                @forelse ($racks as $rack)
                                    <div
                                        class="list-group-item px-3 py-2.5 d-flex align-items-center justify-content-between border-bottom {{ $rack->id_rack == $barang->id_rack ? 'bg-primary-subtle' : '' }}">
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 12.5px;">
                                                {{ $rack->nama_rack }}
                                                @if ($rack->id_rack == $barang->id_rack)
                                                    <span class="badge bg-primary text-white ms-1"
                                                        style="font-size: 9px;">Aktif</span>
                                                @endif
                                            </div>
                                            <div class="text-muted" style="font-size: 11px;">
                                                <i class="fa-solid fa-location-dot text-secondary me-1"></i>
                                                Area: {{ $rack->lokasi }}
                                            </div>
                                        </div>
                                        <span class="badge bg-light text-secondary border font-monospace"
                                            style="font-size: 10.5px;">
                                            #ID-{{ $rack->id_rack }}
                                        </span>
                                    </div>
                                @empty
                                    <div class="p-3 text-center text-muted">Belum ada rak terdaftar</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- SOP Pembaruan Data --}}
                    <div class="card form-card-clean">
                        <div class="card-header bg-white p-3 border-bottom">
                            <span class="fw-bold text-dark small d-inline-flex align-items-center gap-2">
                                <i class="fa-solid fa-clipboard-check text-primary"></i>
                                <span>SOP Pembaruan Data Suku Cadang</span>
                            </span>
                        </div>
                        <div class="card-body p-3.5">
                            <div class="d-flex flex-column gap-3 small text-secondary">
                                <div class="guideline-item">
                                    <div class="guideline-number">1</div>
                                    <div>
                                        <strong class="text-dark d-block mb-0.5">Rekonsiliasi Stok Fisik</strong>
                                        <p class="mb-0 text-muted" style="font-size: 11.5px;">
                                            Pastikan angka stok baru telah diverifikasi langsung lewat stok opname workshop.
                                        </p>
                                    </div>
                                </div>
                                <div class="guideline-item">
                                    <div class="guideline-number">2</div>
                                    <div>
                                        <strong class="text-dark d-block mb-0.5">Relokasi Rak Gudang</strong>
                                        <p class="mb-0 text-muted" style="font-size: 11.5px;">
                                            Jika fisik barang dipindahkan ke rak baru, pastikan pilihan rak pada form ini
                                            diubah.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
