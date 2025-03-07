<?php

namespace App\Actions;

use App\Models\Project;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ProjectResource;

class ProjectActions
{
    /**
     * Store a new project along with related users and dynamic attributes.
     *
     * @param array $data
     * @return Project
     */

     public function getProjects( $request)
     {
         $query = Project::query();

         // Check if filters are provided in the request
         if ($request->has('filters')) {
             $filters = $request->get('filters');

             foreach ($filters as $field => $filter) {
                 // For standard columns on the projects table
                 if (in_array($field, ['name', 'status'])) {
                     if (is_array($filter) && isset($filter['operator'], $filter['value'])) {
                         $query->where($field, $filter['operator'], $filter['value']);
                     } else {
                         $query->where($field, '=', $filter);
                     }
                 } else {
                     // For dynamic attributes (EAV filtering)
                     $operator = '=';
                     $value = $filter;

                     if (is_array($filter) && isset($filter['operator'], $filter['value'])) {
                         $operator = $filter['operator'];
                         $value = $filter['value'];
                     }

                     // Using nested whereHas calls for better readability
                     $query->whereHas('attributeValues', function ($q) use ($field, $operator, $value) {
                         $q->whereHas('attribute', function ($q2) use ($field) {
                             $q2->where('name', $field);
                         })->where('value', $operator, $value);
                     });
                 }
             }
         }

         $projects = $query->with('attributeValues.attribute')->get();

         return ProjectResource::collection($projects);
     }
    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Create the project.
            $project = Project::create([
                'name'   => $data['name'],
                'status' => $data['status'],
            ]);

            // Attach related users if provided.
            if (!empty($data['user_ids'])) {
                $project->users()->sync($data['user_ids']);
            }

            // Handle dynamic attribute values.
            if (!empty($data['attribute_values'])) {
                $attributeNames = array_keys($data['attribute_values']);
                $attributes = Attribute::whereIn('name', $attributeNames)
                    ->get()
                    ->keyBy('name');

                foreach ($data['attribute_values'] as $attrName => $value) {
                    if (isset($attributes[$attrName])) {
                        AttributeValue::updateOrCreate(
                            ['attribute_id' => $attributes[$attrName]->id, 'entity_id' => $project->id],
                            ['value' => $value]
                        );
                    }
                }
            }

            return $project;
        });
    }

    /**
     * Update an existing project along with its dynamic attributes.
     *
     * @param Project $project
     * @param array $data
     * @return Project
     */
    public function update(Project $project, array $data)
    {

        $updateData = array_filter([
            'name'   => $data['name'] ?? $project->name,
            'status' => $data['status'] ?? $project->status,
        ]);

        $project->update($updateData);

        if (!empty($data['attribute_values'])) {
            $attributeNames = array_keys($data['attribute_values']);
            $attributes = Attribute::whereIn('name', $attributeNames)
                ->get()
                ->keyBy('name');

            foreach ($data['attribute_values'] as $attrName => $value) {
                if (isset($attributes[$attrName])) {
                    AttributeValue::updateOrCreate(
                        ['attribute_id' => $attributes[$attrName]->id, 'entity_id' => $project->id],
                        ['value' => $value]
                    );
                }
            }
        }

        return $project;
    }


}
