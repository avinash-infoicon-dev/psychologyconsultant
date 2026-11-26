<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PatientProfilePhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_upload_and_replace_profile_photo(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        Patient::query()->create(['user_id' => $user->id]);
        $profiles = app(ProfileService::class);

        $profiles->updatePatientProfile($user, [
            'avatar_image' => UploadedFile::fake()->image('first-photo.jpg'),
        ]);

        $firstPath = str_replace('storage/', '', $user->fresh()->avatar_path);
        Storage::disk('public')->assertExists($firstPath);

        $profiles->updatePatientProfile($user, [
            'avatar_image' => UploadedFile::fake()->image('updated-photo.png'),
        ]);

        $updatedUser = $user->fresh();
        $updatedPath = str_replace('storage/', '', $updatedUser->avatar_path);

        $this->assertNotSame($firstPath, $updatedPath);
        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertExists($updatedPath);
        $this->assertStringContainsString($updatedPath, $updatedUser->avatar_url);
    }
}
