<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PetControllerTest extends TestCase
{
    /** @test */
    public function it_can_create_a_pet()
    {
        // Symulowanie odpowiedzi API przy tworzeniu zwierzęcia
        Http::fake([
            '*/pet' => Http::response(['id' => 1, 'name' => 'Test Pet', 'status' => 'available'], 201)
        ]);

        // Wysyłanie żądania POST do utworzenia zwierzęcia
        $response = $this->post('/pets', [
            'name' => 'Test Pet',
            'category_id' => 1,
            'category_name' => 'Dogs',
            'status' => 'available',
            'tags' => 'tag1, tag2',
            'photo' => UploadedFile::fake()->image('pet.jpg'),
        ]);

        $response->assertStatus(302); // Sprawdzenie przekierowania
        $response->assertRedirect('/pets/1');

        // Sprawdzenie, czy żądanie zostało wysłane do API
        Http::assertSent(function ($request) {
            return $request->url() === config('app.petstore_api_url') . '/pet' &&
                $request['name'] === 'Test Pet' &&
                $request['status'] === 'available';
        });
    }

    /** @test */
    public function it_can_update_a_pet()
    {
        // Symulowanie odpowiedzi API przy aktualizacji zwierzęcia
        Http::fake([
            '*/pet/1' => Http::response(['id' => 1, 'name' => 'Updated Pet', 'status' => 'pending'], 200)
        ]);

        Storage::fake('public');
        $uploadedFile = UploadedFile::fake()->image('updated_pet.jpg');

        // Wysyłanie żądania PUT do aktualizacji zwierzęcia
        $response = $this->put('/pets/1', [
            'name' => 'Updated Pet',
            'category_id' => 1,
            'category_name' => 'Dogs',
            'status' => 'pending',
            'tags' => 'tag1, tag2',
            'photo' => $uploadedFile,
        ]);

        $response->assertStatus(302); // Sprawdzenie przekierowania
        $response->assertRedirect('/pets/1');

        // Sprawdzenie, czy plik został zapisany
        Storage::disk('public')->assertExists('photos/' . $uploadedFile->hashName());

        // Sprawdzenie, czy żądanie zostało wysłane do API
        Http::assertSent(function ($request) {
            return $request->url() === config('app.petstore_api_url') . '/pet' &&
                $request['name'] === 'Updated Pet' &&
                $request['status'] === 'pending';
        });
    }

    /** @test */
    public function it_can_delete_a_pet()
    {
        // Symulowanie odpowiedzi API przy usuwaniu zwierzęcia
        Http::fake([
            '*/pet/1' => Http::response(null, 200)
        ]);

        // Wysyłanie żądania DELETE do usunięcia zwierzęcia
        $response = $this->delete('/pets/1');

        $response->assertStatus(302); // Sprawdzenie przekierowania
        $response->assertRedirect('/pets/create');

        // Sprawdzenie, czy żądanie DELETE zostało wysłane do API
        Http::assertSent(function ($request) {
            return $request->method() === 'DELETE' &&
                $request->url() === config('app.petstore_api_url') . '/pet/1';
        });
    }

    /** @test */
    public function it_can_create_and_delete_a_pet_with_image()
    {
        // Symulowanie odpowiedzi API przy tworzeniu i usuwaniu zwierzęcia
        Http::fake([
            '*/pet' => Http::response(['id' => 1, 'name' => 'Test Pet', 'status' => 'available'], 201),
            '*/pet/1' => Http::response(null, 200),
        ]);

        Storage::fake('public');

        // Tworzenie nowego pliku obrazu
        $uploadedFile = UploadedFile::fake()->image('pet.jpg');
        $path = $uploadedFile->store('photos', 'public');
        $publicUrl = Storage::url($path);

        // Wysyłanie żądania POST do utworzenia zwierzęcia
        $responseCreate = $this->post('/pets', [
            'name' => 'Test Pet',
            'category_id' => 1,
            'category_name' => 'Dogs',
            'status' => 'available',
            'tags' => 'tag1, tag2',
            'photo' => $uploadedFile,
        ]);

        $responseCreate->assertStatus(302); // Sprawdzenie przekierowania
        $responseCreate->assertRedirect('/pets/1');

        // Sprawdzenie, czy plik istnieje
        Storage::disk('public')->assertExists($path);

        // Wysyłanie żądania DELETE do usunięcia zwierzęcia
        $responseDelete = $this->delete('/pets/1');

        $responseDelete->assertStatus(302); // Sprawdzenie przekierowania
        $responseDelete->assertRedirect('/pets/create');

        // Sprawdzenie, czy plik został usunięty
        $imagePathForDeletionCheck = str_replace('/storage', 'public', parse_url($publicUrl, PHP_URL_PATH));
        Storage::disk('public')->assertMissing($imagePathForDeletionCheck);

        // Sprawdzenie, czy żądanie DELETE zostało wysłane do API
        Http::assertSent(function ($request) {
            return $request->method() === 'DELETE' &&
                $request->url() === config('app.petstore_api_url') . '/pet/1';
        });
    }
}
