@extends('layouts.pos')

@section('content')

<h1 class="page-title">Pilih Outlet</h1>

<div class="form-card" style="max-width:500px; margin:auto;">

    <form action="{{ route('outlet.simpan') }}" method="POST">
        @csrf

        <div class="form-group">
            <label>Outlet Kerja Hari Ini</label>

            <select name="outlet" required>
                <option value="">-- Pilih Outlet --</option>
                <option value="hasanuddin">Hasanuddin</option>
                <option value="makmur">Makmur</option>
                <option value="event">Event / Luar Outlet</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit">
                Masuk Dashboard
            </button>
        </div>
    </form>

</div>

@endsection