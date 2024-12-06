<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Conference;
use Illuminate\Http\Request;

class ConferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $conferences = Conference::all();
            return response()->json([
                'success' => 'true',
                'data' => $conferences,
                'message' => 'Success'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => 'false',
                'data' => [],
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'description' => 'required',
                'venue' => 'required',
                'date_start' => 'required',
                'date_end' => 'required',
                'time' => 'required',
                'speaker' => 'required',
                'moderator' => 'required',
                'sum_table' => 'required',
            ]);

            $conference = Conference::create($request->all());
            $start = new \DateTime($request->date_start);
            $end = new \DateTime($request->date_end);
            $end->modify('+1 day'); // Include the end date
            $interval = new \DateInterval('P1D'); // 1-day interval
            $datePeriod = new \DatePeriod($start, $interval, $end);

            foreach ($datePeriod as $date) {
                for ($i = 0; $i < $request->sum_table; $i++) {
                    Table::create([
                        'conference_id' => $conference->id,
                        'name_table' => 'Table ' . ($i + 1),
                        'date' => $date->format('Y-m-d'), 
                    ]);
                }
            }
    
            return response()->json([
                'success' => 'true',
                'data' => $conference,
                'message' => 'Conference created successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => 'false',
                'data' => [],
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Conference $conference)
    {
        try {
            $conference = Conference::find($conference->id);
            return response()->json([
                'success' => 'true',
                'data' => $conference,
                'message' => 'Success'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => 'false',
                'data' => [],
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Conference $conference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Conference $conference)
    {
        try {
            $request->validate([
                'name' => 'required',
                'description' => 'required',
                'venue' => 'required',
                'date_start' => 'required',
                'date_end' => 'required',
                'time' => 'required',
                'speaker' => 'required',
                'moderator' => 'required',
                'sum_table' => 'required',
            ]);

            $conference->update($request->all());

            return response()->json([
                'success' => 'true',
                'data' => $conference,
                'message' => 'Conference updated successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => 'false',
                'data' => [],
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Conference $conference)
    {
        try {
            $conference->delete();
            return response()->json([
                'success' => 'true',
                'data' => [],
                'message' => 'Conference deleted successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => 'false',
                'data' => [],
                'message' => $th->getMessage()
            ]);
        }
    }
}
