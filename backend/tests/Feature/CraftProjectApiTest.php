<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CraftProjectApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Guest cannot access protected projects API.
     */
    public function test_guest_cannot_access_projects(): void
    {
        $response = $this->getJson('/api/projects');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    /**
     * Test 2: Authenticated user can create a project.
     */
    public function test_authenticated_user_can_create_project(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $projectData = [
            'name' => 'Crochet Flower Bouquet',
            'category' => 'crochet',
            'status' => 'in_progress',
            'progress' => 35,
            'start_date' => '2026-10-23',
            'target_date' => '2026-11-05',
            'difficulty' => 'beginner',
            'materials' => 'Cotton yarn, crochet hook',
            'notes' => 'Making crochet flowers',
        ];

        $response = $this->withToken($token)
            ->postJson('/api/projects', $projectData);

        $response->assertStatus(201)
            ->assertJsonPath(
                'message',
                'Project created successfully'
            )
            ->assertJsonPath(
                'data.name',
                'Crochet Flower Bouquet'
            );

        $this->assertDatabaseHas('craft_projects', [
            'user_id' => $user->id,
            'name' => 'Crochet Flower Bouquet',
            'category' => 'crochet',
            'status' => 'in_progress',
            'progress' => 35,
        ]);
    }
}
