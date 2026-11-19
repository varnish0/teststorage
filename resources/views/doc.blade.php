<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>doc gestion</title>
</head>
<body>
    <h1>SUBE TU DOC</h1>

    @if(session('success'))
    <p>{{ session('success') }}</p>
    @endif

    @if(session('error'))
    <p style="color: red">{{ session('error') }}</p>
    @endif

    <form action=" {{ route('photos.store') }}" method="POST" enctype="multipart/form-data" >
    @csrf
        <div>
            <label for="name">nombre de la img</label>
            <input type="text" name="name" id="name" required>
        </div>
        <div>
            <label for="image">img</label>
            <input type="file" name="image" id="image" accetp="image/*" required>
        </div>
        <br>
        <button type="submit">Subir img</button>
    </form>

    <h1>Galeria</h1>

    @foreach($photos as $photo)

        <div>

            <h3> {{ $photo->name }} </h3>
            <img src="{{ asset('storage/' . $photo->image_path) }}" alt="{{ $photo->name }}" width="250">
            <p>ruta: {{ $photo->image_path }} </p>
            <br>

            <form action=" {{ route('photos.update', $photo) }}" method="POST" enctype="multipart/form-data" >
                @csrf
                @method('PUT')
                <div>
                    <label for="image">img</label>
                    <input type="file" name="image" id="image" accetp="image/*" required>
                </div>
                <br>
                <button type="submit">actualizar img</button>
            </form>

             <form action=" {{ route('photos.destroy', $photo) }}" method="POST">
                @method('DELETE')
                @csrf
                <button type="submit">eliminar img</button>
            </form>

        </div>
    @endforeach


</body>
</html>