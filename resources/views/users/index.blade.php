@extends('layouts.pos')

@section('content')

<style>
    /* Efek hover baris tabel super smooth */
    .row-item:hover {
        background-color: #f0f5f3;
        transition: background-color 0.2s ease-in-out;
    }
    .row-item td {
        border-bottom: 1px solid #e5e7eb;
    }
    .badge-modern {
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
        display: inline-block;
        text-align: center;
    }
</style>

<!-- JUDUL TENGAH -->
<h1 class="page-title">User Management</h1>

@if(session('success'))
    <div style="background-color: #d1fae5; color: #065f46; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border: 1px solid #a7f3d0; text-align: center;">
        ✅ {{ session('success') }}
    </div>
@endif

<!-- ACTION BAR: TOMBOL CREATE USER DIBIKIN TINGGI 42px -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
    <div>
        <a href="/users/create" class="btn" style="height: 42px; display: flex; align-items: center; padding: 0 16px; margin: 0; border-radius: 6px; text-decoration: none;">
            + Create User
        </a>
    </div>
</div>

<div class="table-card" style="overflow-x: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 10px;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                <th style="padding: 12px 15px; text-align: center; width: 5%;">No</th>
                <th style="padding: 12px 15px; text-align: left; width: 30%;">Nama</th>
                <th style="padding: 12px 15px; text-align: left; width: 30%;">Email</th>
                <th style="padding: 12px 15px; text-align: center; width: 15%;">Role</th>
                <th style="padding: 12px 15px; text-align: center; width: 20%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr class="row-item">
                <td style="padding: 12px 15px; text-align: center; color: #475569; vertical-align: middle;">{{ $loop->iteration }}</td>
                
                <td style="padding: 12px 15px; text-align: left; vertical-align: middle;">
                    <strong style="color: #1e293b; font-size: 15px;">{{ $user->name }}</strong>
                </td>
                
                <td style="padding: 12px 15px; text-align: left; vertical-align: middle; color: #64748b; font-size: 14px;">
                    {{ $user->email }}
                </td>
                
                <td style="padding: 12px 15px; text-align: center; vertical-align: middle;">
                    @if($user->role == 'owner')
                        <span class="badge-modern" style="background: #d1fae5; color: #047857; border: 1px solid #6ee7b7;">Owner</span>
                    @elseif($user->role == 'operational_manager')
                        <span class="badge-modern" style="background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe;">Operational Manager</span>
                    @elseif($user->role == 'logistik')
                        <span class="badge-modern" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">Logistik</span>
                    @elseif($user->role == 'barista')
                        <span class="badge-modern" style="background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe;">Barista</span>
                    @elseif($user->role == 'kasir')
                        <span class="badge-modern" style="background: #f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff;">Kasir</span>
                    @else
                        <span class="badge-modern" style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;">{{ ucfirst($user->role) }}</span>
                    @endif
                </td>
                
                <td style="padding: 12px 15px; text-align: center; vertical-align: middle;">
                    <div style="display: flex; gap: 8px; align-items: center; justify-content: center;">
                        <a href="/users/{{ $user->id }}/edit" style="background: #f1f5f9; color: #334155; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; border: 1px solid #cbd5e1;">
                            Edit
                        </a>

                        @if(auth()->id() != $user->id)
                            <form action="/users/{{ $user->id }}" method="POST" style="margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin mau hapus user ini?')" style="background: #fee2e2; color: #b91c1c; padding: 6px 12px; border-radius: 6px; border: 1px solid #fecaca; font-size: 13px; font-weight: 600; cursor: pointer;">
                                    Hapus
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 30px; color: #6b7280; font-style: italic;">
                    📁 Belum ada data user.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection