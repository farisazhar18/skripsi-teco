@if($items->count() > 0)
    <section class="menu-section">
        <h2>{{ $title }}</h2>

        <div class="grid">
            @foreach($items as $produk)
                @php
                    // Cek ketersediaan bahan baku produk ini di outlet terkait
                    $statusProduk = $produk->statusOtomatis($outlet);
                @endphp
                
                <div class="product-card" style="position: relative; {{ $statusProduk != 'Aktif' ? 'opacity: 0.6; pointer-events: none; filter: grayscale(80%);' : '' }}">
                    
                    @if($statusProduk != 'Aktif')
                        <div style="position: absolute; top: 35%; left: 50%; transform: translate(-50%, -50%); background: #c62828; color: white; padding: 8px 15px; border-radius: 8px; font-weight: bold; font-size: 18px; z-index: 10; box-shadow: 0 4px 10px rgba(0,0,0,0.3); border: 2px solid white; text-align: center;">
                            SOLD OUT
                        </div>
                    @endif

                    <!-- INI BAGIAN FOTO YANG DIUBAH BANG -->
                    <div class="product-image">
                        @if($produk->foto)
                            <img src="{{ asset($produk->foto) }}" alt="{{ $produk->nama_produk }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <!-- Kalau fotonya belum di-upload, tampilin ikon default -->
                            <span style="color: #8a8073; font-size: 30px;">☕</span>
                        @endif
                    </div>

                    <h3>{{ $produk->nama_produk }}</h3>

                    @if(!empty($produk->deskripsi))
                        <div class="product-desc">
                            {{ $produk->deskripsi }}
                        </div>
                    @endif

                    <div class="category">
                        {{ ucfirst($produk->kategori) }}
                    </div>

                    <div class="price">
                        @if($produk->tipe_produk == 'vendor')
                            Rp {{ number_format($produk->harga_reguler) }}
                        @else
                            Reguler: Rp {{ number_format($produk->harga_reguler) }} <br>
                            Large: Rp {{ number_format($produk->harga_large) }}
                        @endif
                    </div>

                    @if($statusProduk == 'Aktif')
                        <button class="btn"
                            onclick="openModal(
                                '{{ $produk->id }}',
                                '{{ addslashes($produk->nama_produk) }}',
                                '{{ $produk->tipe_produk }}',
                                '{{ $produk->tersedia_hot }}',
                                '{{ $produk->tersedia_ice }}',
                                {{ json_encode($produk->varianTersedia($outlet)) }},
                                {{ $produk->bisa_extra_syrup ? '1' : '0' }},
                                '{{ addslashes($produk->deskripsi ?? '') }}'
                            )">
                            + Pesan
                        </button>
                    @else
                        <button class="btn" style="background: #ccc; color: #555; cursor: not-allowed;">
                            Habis
                        </button>
                    @endif

                </div>
            @endforeach
        </div>
    </section>
@endif