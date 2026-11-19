<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>photos</title>
</head>
<body>
    <nav>
        <a href="{{ route('photos.index') }}">Galeria de imgs</a> |
        <a href="{{ route('docs.index') }}">Gestor de docs</a>
    </nav>

    <h1>SUBE TU IMG</h1>

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

    <form action="{{ route('photos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="name">nombre de la img</label>
            <input type="text" name="name" id="name" required>
        </div>
        <div>
            <label for="image">img</label>
            <input type="file" name="image" id="image" accept="image/*" required>
        </div>
        <br>
        <button type="submit">Subir img</button>
    </form>

    <h1>Galeria</h1>

    @foreach($photos as $photo)

        <div>

            <h3>{{ $photo->name }}</h3>
            <img src="{{ asset('storage/' . $photo->image_path) }}" alt="{{ $photo->name }}" width="250">
            <p>ruta: {{ $photo->image_path }}</p>
            <br>

            <form action="{{ route('photos.update', $photo) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div>
                    <label for="image-{{ $photo->id }}">nueva img</label>
                    <input type="file" name="image" id="image-{{ $photo->id }}" accept="image/*" required>
                </div>
                <br>
                <button type="submit">actualizar img</button>
            </form>

            <form action="{{ route('photos.destroy', $photo) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit">eliminar img</button>
            </form>

        </div>
    @endforeach

</body>
</html>
