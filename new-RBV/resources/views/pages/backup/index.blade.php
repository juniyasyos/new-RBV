@extends('layouts.app')

@section('content')

<div class="container">

    <h2>Backup Database</h2>

    {{-- EXPORT --}}
    <form action="{{ route('backup.export') }}" method="POST">
        @csrf

        <button type="submit" class="btn btn-primary">
            Export Backup
        </button>
    </form>

    <hr>

    {{-- IMPORT --}}
    <form action="{{ route('backup.import') }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf

        <input type="file" name="backup_file" required>

        <button type="submit" class="btn btn-success">
            Import Backup
        </button>

    </form>

</div>

@endsection