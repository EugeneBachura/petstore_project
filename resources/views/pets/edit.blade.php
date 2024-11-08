@extends('layouts.app')

@section('content')
    <h1>Edytuj zwierzę</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pets.update', $pet['id']) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <label for="name">Nazwa:</label>
        <input type="text" name="name" id="name" value="{{ $pet['name'] ?? '' }}" required><br><br>

        <label for="category">Kategoria:</label>
        <select name="category_id" id="category" required>
            <option value="1" data-name="Dogs"
                {{ isset($pet['category']['id']) && $pet['category']['id'] == 1 ? 'selected' : '' }}>Psy</option>
            <option value="2" data-name="Cats"
                {{ isset($pet['category']['id']) && $pet['category']['id'] == 2 ? 'selected' : '' }}>Koty</option>
            <option value="3" data-name="Birds"
                {{ isset($pet['category']['id']) && $pet['category']['id'] == 3 ? 'selected' : '' }}>Ptaki</option>
        </select><br><br>
        <input type="hidden" name="category_name" id="category_name" value="{{ $pet['category']['name'] ?? 'Dogs' }}">

        <label for="status">Status:</label>
        <select name="status" id="status" required>
            <option value="available" {{ isset($pet['status']) && $pet['status'] == 'available' ? 'selected' : '' }}>
                Dostępny</option>
            <option value="pending" {{ isset($pet['status']) && $pet['status'] == 'pending' ? 'selected' : '' }}>Oczekujący
            </option>
            <option value="sold" {{ isset($pet['status']) && $pet['status'] == 'sold' ? 'selected' : '' }}>Sprzedany
            </option>
        </select><br><br>

        <label for="tags">Tagi:</label>
        <input type="text" name="tags" id="tags"
            value="{{ implode(', ', array_map(fn($tag) => $tag['name'] ?? 'Brak danych', $pet['tags'] ?? [])) }}"
            placeholder="Wprowadź tagi oddzielone przecinkami"><br><br>

        @if (!empty($pet['photoUrls'][0]))
            <label>Aktualne zdjęcie:</label><br>
            <img src="{{ $pet['photoUrls'][0] }}" alt="Zdjęcie zwierzęcia" style="max-width: 200px;"><br><br>
        @else
            <p>Brak zdjęcia</p>
        @endif

        <label for="photo">Zmień zdjęcie:</label>
        <input type="file" name="photo" id="photo" accept="image/*"><br><br>

        <button type="submit">Zapisz zmiany</button>
    </form>

    <a href="{{ route('pets.index') }}">Wróć do listy</a>

    <script>
        document.getElementById('category').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('category_name').value = selectedOption.getAttribute('data-name');
        });
    </script>
@endsection
