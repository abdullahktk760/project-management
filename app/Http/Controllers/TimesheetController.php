<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use App\Http\Resources\TimesheetResource;
use App\Http\Requests\StoreTimesheetRequest;
use App\Http\Requests\UpdateTimesheetRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TimesheetController extends Controller
{
    // GET /api/timesheets
    public function index()
    {
        $timesheets = Timesheet::with(['project'])->get();
        return TimesheetResource::collection($timesheets);
    }

    // GET /api/timesheets/{id}
    public function show(Timesheet $timesheet)
    {
        return new TimesheetResource($timesheet->load('user', 'project'));
    }

    // POST /api/timesheets
    public function store(StoreTimesheetRequest $request)
    {
        $timesheet = Timesheet::create($request->all());

        return new TimesheetResource($timesheet->load('user', 'project'));
    }

    // PUT /api/timesheets/{id}
    public function update(UpdateTimesheetRequest $request, $id)
    {
        try {
            $timesheet = Timesheet::findOrFail($id);
            $timesheet->update($request->all());
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Timesheet not found'], 404);
        }

        return new TimesheetResource($timesheet->load('project'));
    }

    // DELETE /api/timesheets/{id}
    public function destroy($id)
    {
        try {
            $timesheet = Timesheet::findOrFail($id);
            $timesheet->delete();
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Timesheet not found'], 404);
        }
        return response()->json(['message' => 'Timesheet deleted successfully.']);
    }
}
