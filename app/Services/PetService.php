<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Class PetService
 *
 * Serwis do komunikacji z API Petstore.
 */
class PetService
{
    protected $apiUrl;

    /**
     * Konstruktor klasy PetService.
     */
    public function __construct()
    {
        $this->apiUrl = config('app.petstore_api_url');
    }

    /**
     * Pobiera listę wszystkich zwierząt.
     *
     * @return array|null Lista zwierząt lub null w przypadku błędu.
     */
    public function getAllPets()
    {
        $response = Http::get("{$this->apiUrl}/pet/findByStatus", [
            'status' => 'available'
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    /**
     * Pobiera informacje o zwierzęciu.
     *
     * @param int $id ID zwierzęcia.
     * @return array|null Dane zwierzęcia lub null w przypadku błędu.
     */
    public function getPet($id)
    {
        $response = Http::get("{$this->apiUrl}/pet/{$id}");

        if ($response->successful()) {
            return $response->json();
        } else {
            // Obsługa błędu
            return null;
        }
    }

    /**
     * Dodaje nowe zwierzę.
     *
     * @param array $data Dane zwierzęcia.
     * @return array|null Dane nowego zwierzęcia lub null w przypadku błędu.
     */
    public function addPet($data)
    {
        $response = Http::post("{$this->apiUrl}/pet", $data);

        if ($response->successful()) {
            return $response->json();
        } else {
            // Obsługa błędu
            return null;
        }
    }

    /**
     * Aktualizuje dane zwierzęcia.
     *
     * @param array $data Dane zwierzęcia.
     * @return array|null Zaktualizowane dane zwierzęcia lub null w przypadku błędu.
     */
    public function updatePet($data)
    {
        $response = Http::put("{$this->apiUrl}/pet", $data);

        if ($response->successful()) {
            return $response->json();
        } else {
            // Obsługa błędu
            return null;
        }
    }

    /**
     * Usuwa zwierzę.
     *
     * @param int $id ID zwierzęcia.
     * @return bool True w przypadku sukcesu, false w przypadku błędu.
     */
    public function deletePet($id)
    {
        $response = Http::delete("{$this->apiUrl}/pet/{$id}");

        if ($response->successful()) {
            return true;
        } else {
            // Obsługa błędu
            return false;
        }
    }
}