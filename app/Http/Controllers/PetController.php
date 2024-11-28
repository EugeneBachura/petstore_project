<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PetService;
use Illuminate\Support\Facades\Storage;

/**
 * Provide CRUD functionality for pets.
 */
class PetController extends Controller
{
    protected $petService;

    /**
     * Constructor PetController.
     *
     * @param PetService $petService
     */
    public function __construct(PetService $petService)
    {
        $this->petService = $petService;
    }

    /**
     * Displays a list of pets.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $pets = $this->petService->getAllPets();

        $pets = array_map(function ($pet) {
            return array_map(fn($value) => is_string($value) ? e($value) : $value, $pet);
        }, $pets);

        return view('pets.index', compact('pets'));
    }

    /**
     * Shows the form to create a new pet.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('pets.create');
    }

    /**
     * Saves a new pet.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
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

        $tagsArray = [];
        if (!empty($validatedData['tags'])) {
            $tags = explode(',', $validatedData['tags']);
            $tagsArray = array_map(function ($tag) {
                return ['name' => trim($tag)];
            }, $tags);
        }

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

        return $pet
            ? redirect()->route('pets.show', $pet['id'])->with('success', 'Zwierzę zostało dodane.')
            : back()->withErrors(['Wystąpił błąd podczas dodawania zwierzęcia.']);
    }

    /**
     * Displays details of a pet.
     *
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        $pet = $this->petService->getPet($id);

        if ($pet) {
            $pet = array_map(fn($value) => is_string($value) ? e($value) : $value, $pet);
            return view('pets.show', compact('pet'));
        }

        return redirect()->route('pets.create')->withErrors(['Zwierzę nie zostało znalezione.']);
    }

    /**
     * Shows the form to edit a pet.
     *
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        $pet = $this->petService->getPet($id);

        if ($pet) {
            $pet = array_map(fn($value) => is_string($value) ? e($value) : $value, $pet);
            return view('pets.edit', compact('pet'));
        }

        return redirect()->route('pets.create')->withErrors(['Zwierzę nie zostało znalezione.']);
    }

    /**
     * Updates a pet.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
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

        $tagsArray = [];
        if (!empty($validatedData['tags'])) {
            $tags = explode(',', $validatedData['tags']);
            $tagsArray = array_map(fn($tag) => ['name' => trim($tag)], $tags);
        }

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
                $oldImagePath = str_replace('/storage', 'public', parse_url($pet['photoUrls'][0], PHP_URL_PATH));
                Storage::delete($oldImagePath);
            }
            $path = $request->file('photo')->store('photos', 'public');
            $data['photoUrls'] = [Storage::url($path)];
        }

        $pet = $this->petService->updatePet($data);

        return $pet
            ? redirect()->route('pets.show', $pet['id'])->with('success', 'Zwierzę zostało zaktualizowane.')
            : back()->withErrors(['Wystąpił błąd podczas aktualizacji zwierzęcia.']);
    }

    /**
     * Deletes a pet.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $pet = $this->petService->getPet($id);

        if (!empty($pet['photoUrls'][0])) {
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
