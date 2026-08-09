@extends('layouts.pos')

@section('content')

<h1 class="page-title">Create User</h1>

@if ($errors->any())
    <div class="card">
        <ul style="color:red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-card">

<form action="/users" method="POST">
    @csrf

    <div class="form-group">
        <label>Nama</label>
        <input type="text"
               name="name"
               value="{{ old('name') }}"
               required>
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email"
               name="email"
               value="{{ old('email') }}"
               required>
    </div>

    <div class="form-group">
        <label>Role</label>
        <select name="role" required>
            <option value="">-- Pilih Role --</option>

            <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>
                Owner
            </option>

            <option value="operational_manager" {{ old('role') == 'operational_manager' ? 'selected' : '' }}>
                Operational Manager
            </option>

            <option value="logistik" {{ old('role') == 'logistik' ? 'selected' : '' }}>
                Logistik
            </option>

            <option value="barista" {{ old('role') == 'barista' ? 'selected' : '' }}>
                Barista
            </option>

            <option value="kasir" {{ old('role') == 'kasir' ? 'selected' : '' }}>
                Kasir
            </option>
        </select>
    </div>

    <div class="form-group">
        <label>Password</label>
        <input type="password"
               name="password"
               required>
    </div>

    <div class="form-group">
        <label>Konfirmasi Password</label>
        <input type="password"
               name="password_confirmation"
               required>
    </div>

    <div class="form-actions">
        <button type="submit">
            Simpan
        </button>

        <a href="/users" class="btn-secondary">
            ← Kembali
        </a>
    </div>

</form>

</div>

@endsection