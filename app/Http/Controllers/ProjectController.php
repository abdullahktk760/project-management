<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Actions\ProjectActions;
use App\Http\Resources\ProjectResource;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProjectController extends Controller
{
    public function index(Request $request, ProjectActions $projectActions)
    {
        $projects = $projectActions->getProjects($request);
        return $projects;
    }

    public function show($id)
    {
        try {
            $project = Project::with('attributes')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        $filteredProject = [
            'id'         =>$project->id,
            'name'       => $project->name,
            'status'     => $project->status,
            'attributes' => $project->attributes->map(function ($attribute) {
                return [
                    'name' => $attribute->name,
                    'type' => $attribute->type,
                ];
            }),
        ];

        return response()->json($filteredProject);
    }


    public function store(StoreProjectRequest $request)
    {
        try {
            $project = app(ProjectActions::class)->store($request->all());
        } catch (\Exception $e) {
            Log::error('Error creating project: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => 'There was an error processing your request.'], 500);
        }

        return new ProjectResource($project->load('attributeValues'));
    }



    public function update(UpdateProjectRequest $request, $id)
    {
        try {

            $project = Project::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Project not found'], 404);
        }
        $project = app(ProjectActions::class)->update($project, $request->all());

        return response()->json(new ProjectResource($project->load('attributeValues')));
    }


    public function destroy($id)
    {
        try {
            $project = Project::findOrFail($id);
            $project->delete();
            return response()->json(['message' => 'Project Deleted']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Project not found'], 404);
        }
    }
}
