<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CraftProject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CraftProjectController extends Controller
{
    private function validateProject(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => [
                'required',
                Rule::in([
                    'crochet', 'knitting', 'scrapbooking',
                    'painting', 'embroidery', 'beading',
                    'clay_craft', 'sewing', 'other',
                ]),
            ],
            'reference_image_url' => ['nullable', 'url', 'max:2048'],
            'tutorial_url' => ['nullable', 'url', 'max:2048'],
            'status' => [
                'required',
                Rule::in(['not_started', 'in_progress', 'completed']),
            ],
            'progress' => ['required', 'integer', 'between:0,100'],
            'start_date' => ['required', 'date'],
            'target_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'difficulty' => [
                'nullable',
                Rule::in(['beginner', 'intermediate', 'advanced']),
            ],
            'materials' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $progress = (int) $data['progress'];

        $validStatus = match ($data['status']) {
            'not_started' => $progress === 0,
            'in_progress' => $progress >= 1 && $progress <= 99,
            'completed' => $progress === 100,
        };

        if (!$validStatus) {
            throw ValidationException::withMessages([
                'progress' => [
                    'Progress must match the selected project status.',
                ],
            ]);
        }

        return $data;
    }

    // GET /api/projects
    public function index(Request $request): JsonResponse
    {
        $projects = CraftProject::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'data' => $projects,
        ]);
    }

    // POST /api/projects
    public function store(Request $request): JsonResponse
    {
        $data = $this->validateProject($request);

        $project = $request->user()
            ->craftProjects()
            ->create($data);

        return response()->json([
            'message' => 'Project created successfully',
            'data' => $project,
        ], 201);
    }

    // GET /api/projects/{id}
    public function show(Request $request, string $id): JsonResponse
    {
        $project = CraftProject::where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'data' => $project,
        ]);
    }

    // PUT /api/projects/{id}
    public function update(Request $request, string $id): JsonResponse
    {
        $project = CraftProject::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $data = $this->validateProject($request);

        $project->update($data);

        return response()->json([
            'message' => 'Project updated successfully',
            'data' => $project->fresh(),
        ]);
    }

    // DELETE /api/projects/{id}
    public function destroy(Request $request, string $id): JsonResponse
    {
        $project = CraftProject::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully',
        ]);
    }
}
