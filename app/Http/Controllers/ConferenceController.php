<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Conference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ConferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $conferences = Conference::all();
            $conferences->transform(function ($conference) {
                $conference->image = $conference->image ? Storage::url($conference->image) : null;
                return $conference;
            });
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
    public function listConference()
    {
        try {
            $conferences = Conference::all();
            $conferences->transform(function ($conference) {
                $conference->image = $conference->image ? Storage::url($conference->image) : null;
                return $conference;
            });
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'description' => 'required',
                'image' => 'required',
                'venue' => 'required',
                'date_start' => 'required',
                'date_end' => 'required',
                'time_start' => 'required',
                'time_end' => 'required',
                'speaker' => 'required',
                'moderator' => 'required',
                'sum_table' => 'required',
            ]);

            DB::beginTransaction();
            $conferenceLogoPath = null;

            if ($request->hasFile('image')) {
                $conferenceLogoPath = $request->file('image')->store('conference-logos', 'public');
            }
            $conference = Conference::create([
                'name' => $request->name,
                'description' => $request->description,
                'image' => $conferenceLogoPath,
                'venue' => $request->venue,
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
                'time_start' => $request->time_start,
                'time_end' => $request->time_end,
                'speaker' => $request->speaker,
                'moderator' => $request->moderator,
                'sum_table' => $request->sum_table
            ]);
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

            DB::commit();
    
            return response()->json([
                'success' => 'true',
                'data' => $conference,
                'message' => 'Conference created successfully'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
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
            $conference->image = $conference->image ? Storage::url($conference->image) : null;
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
                'date_start' => 'required|date',
                'date_end' => 'required|date|after_or_equal:date_start',
                'time_start' => 'required',
                'time_end' => 'required|after_or_equal:time_start',
                'speaker' => 'required',
                'moderator' => 'required',
                'sum_table' => 'required|integer|min:1',
            ]);
    
            $conference->update($request->all());
    
            $conference->tables()->delete();
    
            $start = new \DateTime($request->date_start);
            $end = new \DateTime($request->date_end);
            $end->modify('+1 day'); 
            $interval = new \DateInterval('P1D');
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
                'success' => true,
                'data' => $conference,
                'message' => 'Conference updated successfully, and tables updated accordingly.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => $th->getMessage(),
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
