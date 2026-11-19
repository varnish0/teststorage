<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>docs</title>
</head>
<body>
    <nav>
        <a href="{{ route('photos.index') }}">Galeria de imgs</a> |
        <a href="{{ route('docs.index') }}">Gestor de docs</a>
    </nav>

    <h1>SUBE TU DOC</h1>

    @if(session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if(session('error'))
        <p>{{ session('error') }}</p>
    @endif

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('docs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="name">nombre del doc</label>
            <input type="text" name="name" id="name" required>
        </div>
        <div>
            <label for="doc">doc (pdf)</label>
            <input type="file" name="doc" id="doc" accept="application/pdf" required>
        </div>
        <br>
        <button type="submit">Subir doc</button>
    </form>

    <h1>Documentos</h1>

    @foreach($docs as $doc)

        <div>

            <h3>
                <a href="{{ asset('storage/' . $doc->doc_path) }}" target="_blank">{{ $doc->name }}</a>
            </h3>
            <p>ruta: {{ $doc->doc_path }}</p>
            <br>

            <form action="{{ route('docs.update', $doc) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div>
                    <label for="doc-{{ $doc->id }}">nuevo doc (pdf)</label>
                    <input type="file" name="doc" id="doc-{{ $doc->id }}" accept="application/pdf" required>
                </div>
                <br>
                <button type="submit">actualizar doc</button>
            </form>

            <form action="{{ route('docs.destroy', $doc) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit">eliminar doc</button>
            </form>

        </div>
    @endforeach

</body>
</html>
