<?php

namespace Tests\Feature;

use App\Models\Experience;
use App\Models\Professional;
use App\Models\ProfessionalSocialLink;
use App\Models\ProfessionalType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PsychologistProfileExperienceAndSocialLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_experience_requires_end_date_when_not_currently_working(): void
    {
        $this->actingAs($this->psychologistUser());

        $this->post(route('frontend.psychologist.profile.experiences.store'), [
            'form_context' => 'experience_add',
            'designation' => 'Therapist',
            'organization' => 'Care Clinic',
            'start_date' => '2020-01-01',
        ])->assertSessionHasErrors('end_date');

        $this->assertDatabaseCount('experiences', 0);
    }

    public function test_current_experience_can_be_added_without_an_end_date(): void
    {
        $user = $this->psychologistUser();
        $this->actingAs($user);

        $this->post(route('frontend.psychologist.profile.experiences.store'), [
            'form_context' => 'experience_add',
            'designation' => 'Therapist',
            'organization' => 'Care Clinic',
            'start_date' => '2020-01-01',
            'currently_working' => '1',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('experiences', [
            'professional_id' => $user->professional->id,
            'currently_working' => true,
            'end_date' => null,
        ]);
    }

    public function test_unchecked_social_link_is_saved_and_displayed_as_inactive(): void
    {
        $user = $this->psychologistUser();
        $this->actingAs($user);

        $this->post(route('frontend.psychologist.profile.social-links.store'), [
            'form_context' => 'social_add',
            'platform' => 'Website',
            'url' => 'https://example.test',
        ])->assertRedirect(route('frontend.psychologist.profile.show', ['tab' => 'social']));

        $link = ProfessionalSocialLink::query()->firstOrFail();
        $this->assertFalse($link->status);

        $this->get(route('frontend.psychologist.profile.show', ['tab' => 'social']))
            ->assertSee(__('frontend.inactive'));
    }

    private function psychologistUser(): User
    {
        Role::query()->firstOrCreate(
            ['slug' => 'psychologist'],
            ['name' => 'Psychologist', 'is_active' => true],
        );

        $user = User::factory()->create();
        $user->assignRbacRole('psychologist');

        $type = ProfessionalType::query()->create([
            'name' => 'Psychologist',
            'slug' => 'psychologist-'.uniqid(),
            'status' => true,
        ]);

        Professional::query()->create([
            'user_id' => $user->id,
            'professional_type_id' => $type->id,
            'first_name' => 'Test',
            'last_name' => 'Psychologist',
            'slug' => 'test-psychologist-'.uniqid(),
            'is_verified' => true,
            'is_accepting_patients' => true,
            'status' => true,
        ]);

        return $user;
    }
}
