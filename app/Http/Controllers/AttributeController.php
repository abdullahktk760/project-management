<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\UpdateAttributeRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AttributeController extends Controller
{
    // GET /api/attributes
    public function index()
    {
        return response()->json(Attribute::all());
    }

    public function show($id)
    {
        try {
            $attribute = Attribute::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Attribute not found'], 404);
        }
        return response()->json($attribute);
    }

    // POST /api/attributes
    public function store(StoreAttributeRequest $request)
    {
        $attribute = Attribute::create($request->all());
        return response()->json($attribute, 201);
    }

    // PUT /api/attributes/{id}
    public function update(UpdateAttributeRequest $request, $id)
    {
        try {
            $attribute = Attribute::findOrFail($id);
            $attribute->update($request->all());
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Attribute not found'], 404);
        }
        return response()->json($attribute);
    }

    // DELETE /api/attributes/{attribute}
    public function destroy(Attribute $attribute)
    {
        $attribute->delete();
        return response()->json(['message' => 'Attribute deleted successfully.'], 200);
    }
}
