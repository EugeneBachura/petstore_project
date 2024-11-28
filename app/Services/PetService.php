<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Service to communicate with Petstore API.
 */
class PetService
{
    protected $apiUrl;

    /**
     * Constructor PetService.
     */
    public function __construct()
    {
        $this->apiUrl = config('app.petstore_api_url');
    }

    /**
     * Get all available pets.
     *
     * @return array|null
     */
    public function getAllPets()
    {
        $response = Http::get("{$this->apiUrl}/pet/findByStatus", [
            'status' => 'available',
        ]);

        return $response->successful() ? $response->json() : null;
    }

    /**
     * Get a pet by ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getPet($id)
    {
        $response = Http::get("{$this->apiUrl}/pet/{$id}");

        return $response->successful() ? $response->json() : null;
    }

    /**
     * Add a new pet.
     *
     * @param array $data
     * @return array|null
     */
    public function addPet($data)
    {
        $response = Http::post("{$this->apiUrl}/pet", $data);

        return $response->successful() ? $response->json() : null;
    }

    /**
     * Update pet data.
     *
     * @param array $data
     * @return array|null
     */
    public function updatePet($data)
    {
        $response = Http::put("{$this->apiUrl}/pet", $data);

        return $response->successful() ? $response->json() : null;
    }

    /**
     * Delete a pet by ID.
     *
     * @param int $id
     * @return bool
     */
    public function deletePet($id)
    {
        $response = Http::delete("{$this->apiUrl}/pet/{$id}");

        return $response->successful();
    }
}
