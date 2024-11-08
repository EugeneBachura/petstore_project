<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PetService;
use Illuminate\Support\Facades\Storage;

/**
 * Class PetController
 *
 * Kontroler do obsługi operacji na zwierzętach.
 */
class PetController extends Controller
{
    protected $petService;

    /**
     * Konstruktor klasy PetController.
     *
     * @param PetService $petService Serwis do komunikacji z API Petstore.
     */
    public function __construct(PetService $petService)
    {
        $this->petService = $petService;
    }

    /**
     * Wyświetla listę wszystkich zwierząt.
     *
     * @return \Illuminate\View\View Widok listy zwierząt.
     * 
     * Uwaga: Przed przekazaniem danych do widoku, pola tekstowe są ekraniowane funkcją `e()` 
     * dla zabezpieczenia przed atakami typu XSS.
     */
    public function index()
    {
        $pets = $this->petService->getAllPets();

        // Ekranowanie danych, aby zapobiec potencjalnym atakom XSS.
        $pets = array_map(function ($pet) {
            return array_map(fn($value) => is_string($value) ? e($value) : $value, $pet);
        }, $pets);

        return view('pets.index', compact('pets'));
    }

    /**
     * Wyświetla formularz tworzenia nowego zwierzęcia.
     *
     * @return \Illuminate\View\View Widok formularza.
     */
    public function create()
    {
        return view('pets.create');
    }

    /**
     * Zapisuje nowe zwierzę.
     *
     * @param Request $request Obiekt żądania HTTP z danymi zwierzęcia.
     * @return \Illuminate\Http\RedirectResponse Przekierowanie po zapisaniu.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|integer',
            'category_name' => 'required|string',
            'status' => 'required|string|in:available,pending,sold',
            'tags' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'Pole nazwa jest wymagane.',
            'name.string' => 'Pole nazwa musi być ciągiem znaków.',
            'category_id.required' => 'Pole kategoria (ID) jest wymagane.',
            'category_id.integer' => 'Pole kategoria (ID) musi być liczbą całkowitą.',
            'category_name.required' => 'Pole nazwa kategorii jest wymagane.',
            'status.required' => 'Pole status jest wymagane.',
            'status.in' => 'Pole status musi mieć jedną z wartości: dostępny, oczekujący, sprzedany.',
            'tags.string' => 'Pole tagi musi być ciągiem znaków.',
            'photo.image' => 'Zdjęcie musi być obrazem.',
            'photo.mimes' => 'Zdjęcie musi być w formacie: jpeg, png, jpg, gif.',
            'photo.max' => 'Zdjęcie nie może być większe niż 2048 KB.',
        ]);

        // Tags w array
        $tagsArray = [];
        if (!empty($validatedData['tags'])) {
            $tags = explode(',', $validatedData['tags']);
            $tagsArray = array_map(function ($tag) {
                return ['name' => trim($tag)];
            }, $tags);
        }

        // Oczyszczanie danych i ochrona przed atakami XSS
        $data = [
            'id' => $request->id ?? null,
            'name' => e($validatedData['name']),
            'category' => [
                'id' => $validatedData['category_id'],
                'name' => e($validatedData['category_name']),
            ],
            'photoUrls' => [],
            'tags' => $tagsArray,
            'status' => e($validatedData['status']),
        ];

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            $data['photoUrls'] = [Storage::url($path)];
        }

        $pet = $this->petService->addPet($data);

        if ($pet) {
            return redirect()->route('pets.show', $pet['id'])->with('success', 'Zwierzę zostało dodane.');
        } else {
            return back()->withErrors(['Wystąpił błąd podczas dodawania zwierzęcia.']);
        }
    }

    /**
     * Wyświetla szczegóły zwierzęcia.
     *
     * @param int $id Identyfikator zwierzęcia.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse Widok szczegółów lub przekierowanie z błędem.
     * 
     * Uwaga: Przed przekazaniem danych do widoku, pola tekstowe są ekraniowane funkcją `e()` 
     * dla zabezpieczenia przed atakami typu XSS.
     */
    public function show($id)
    {
        $pet = $this->petService->getPet($id);

        if ($pet) {
            // Ekranowanie danych, aby zapobiec potencjalnym atakom XSS.
            $pet = array_map(fn($value) => is_string($value) ? e($value) : $value, $pet);
            return view('pets.show', compact('pet'));
        } else {
            return redirect()->route('pets.create')->withErrors(['Zwierzę nie zostało znalezione.']);
        }
    }

    /**
     * Wyświetla formularz edycji zwierzęcia.
     *
     * @param int $id Identyfikator zwierzęcia.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse Widok formularza edycji lub przekierowanie z błędem.
     * 
     * Uwaga: Przed przekazaniem danych do widoku, pola tekstowe są ekraniowane funkcją `e()` 
     * dla zabezpieczenia przed atakami typu XSS.
     */
    public function edit($id)
    {
        $pet = $this->petService->getPet($id);

        if ($pet) {
            // Ekranowanie danych, aby zapobiec potencjalnym atakom XSS.
            $pet = array_map(fn($value) => is_string($value) ? e($value) : $value, $pet);
            return view('pets.edit', compact('pet'));
        } else {
            return redirect()->route('pets.create')->withErrors(['Zwierzę nie zostało znalezione.']);
        }
    }

    /**
     * Aktualizuje dane zwierzęcia.
     *
     * @param Request $request Obiekt żądania HTTP z danymi zwierzęcia.
     * @param int $id Identyfikator zwierzęcia.
     * @return \Illuminate\Http\RedirectResponse Przekierowanie po aktualizacji.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|integer',
            'category_name' => 'required|string',
            'status' => 'required|string|in:available,pending,sold',
            'tags' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'Pole nazwa jest wymagane.',
            'name.string' => 'Pole nazwa musi być ciągiem znaków.',
            'category_id.required' => 'Pole kategoria (ID) jest wymagane.',
            'category_id.integer' => 'Pole kategoria (ID) musi być liczbą całkowitą.',
            'category_name.required' => 'Pole nazwa kategorii jest wymagane.',
            'status.required' => 'Pole status jest wymagane.',
            'status.in' => 'Pole status musi mieć jedną z wartości: dostępny, oczekujący, sprzedany.',
            'tags.string' => 'Pole tagi musi być ciągiem znaków.',
            'photo.image' => 'Zdjęcie musi być obrazem.',
            'photo.mimes' => 'Zdjęcie musi być w formacie: jpeg, png, jpg, gif.',
            'photo.max' => 'Zdjęcie nie może być większe niż 2048 KB.',
        ]);

        // Tags w array
        $tagsArray = [];
        if (!empty($validatedData['tags'])) {
            $tags = explode(',', $validatedData['tags']);
            $tagsArray = array_map(function ($tag) {
                return ['name' => trim($tag)];
            }, $tags);
        }

        // Oczyszczanie danych i ochrona przed atakami XSS
        $data = [
            'id' => $request->id ?? null,
            'name' => e($validatedData['name']),
            'category' => [
                'id' => $validatedData['category_id'],
                'name' => e($validatedData['category_name']),
            ],
            'photoUrls' => [],
            'tags' => $tagsArray,
            'status' => e($validatedData['status']),
        ];

        if ($request->hasFile('photo')) {
            $pet = $this->petService->getPet($id);
            if (!empty($pet['photoUrls'][0])) {
                // Uwalniamy zasoby usuwając stare zdjęcie
                $oldImagePath = str_replace('/storage', 'public', parse_url($pet['photoUrls'][0], PHP_URL_PATH));
                Storage::delete($oldImagePath);
            }
            $path = $request->file('photo')->store('photos', 'public');
            $data['photoUrls'] = [Storage::url($path)];
        }

        $pet = $this->petService->updatePet($data);

        if ($pet) {
            return redirect()->route('pets.show', $pet['id'])->with('success', 'Zwierzę zostało zaktualizowane.');
        } else {
            return back()->withErrors(['Wystąpił błąd podczas aktualizacji zwierzęcia.']);
        }
    }

    /**
     * Usuwa zwierzę.
     *
     * @param int $id Identyfikator zwierzęcia.
     * @return \Illuminate\Http\RedirectResponse Przekierowanie po usunięciu.
     */
    public function destroy($id)
    {
        $pet = $this->petService->getPet($id);

        if (!empty($pet['photoUrls'][0])) {
            // Uwalniamy zasoby usuwając zdjęcie
            $imagePath = str_replace('/storage', 'public', parse_url($pet['photoUrls'][0], PHP_URL_PATH));
            Storage::delete($imagePath);
        }

        $deleted = $this->petService->deletePet($id);

        if ($deleted) {
            return redirect()->route('pets.create')->with('success', 'Zwierzę zostało usunięte.');
        } else {
            return back()->withErrors(['Wystąpił błąd podczas usuwania zwierzęcia.']);
        }
    }
}
