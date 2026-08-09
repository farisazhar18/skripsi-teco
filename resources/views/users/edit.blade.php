@extends('layouts.pos')

@section('content')

<h1 class="page-title">Edit User</h1>

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

<form action="/users/{{ $user->id }}" method="POST">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label>Nama</label>
        <input type="text"
               name="name"
               value="{{ old('name', $user->name) }}"
               required>
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email"
               name="email"
               value="{{ old('email', $user->email) }}"
               required>
    </div>

    <div class="form-group">
        <label>Role</label>
        <select name="role" required>
            <option value="owner" {{ old('role', $user->role) == 'owner' ? 'selected' : '' }}>
                Owner
            </option>

            <option value="operational_manager" {{ old('role', $user->role) == 'operational_manager' ? 'selected' : '' }}>
                Operational Manager
            </option>

            <option value="logistik" {{ old('role', $user->role) == 'logistik' ? 'selected' : '' }}>
                Logistik
            </option>

            <option value="barista" {{ old('role', $user->role) == 'barista' ? 'selected' : '' }}>
                Barista
            </option>

            <option value="kasir" {{ old('role', $user->role) == 'kasir' ? 'selected' : '' }}>
                Kasir
            </option>
        </select>
    </div>

    <div class="form-group">
        <label>Password Baru</label>
        <input type="password"
               name="password"
               placeholder="Kosongkan jika tidak ingin mengganti password">
    </div>

    <div class="form-group">
        <label>Konfirmasi Password Baru</label>
        <input type="password"
               name="password_confirmation"
               placeholder="Kosongkan jika tidak ingin mengganti password">
    </div>

    <div class="form-actions">
        <button type="submit">
            Update
        </button>

        <a href="/users" class="btn-secondary">
            ← Kembali
        </a>
    </div>

</form>

</div>

@endsection