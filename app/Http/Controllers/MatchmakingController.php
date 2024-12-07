<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Matchmaking;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MatchmakingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function getMatchmakingByCompanyBook() 
     {
        try{
            $result = Matchmaking::where('company_id_book', '=', Auth::user()->company->id)->with(['company_match:id,company_name','table:id,name_table,date'])->get();
            return response()->json([
                'success' => 'true',
                'data' => $result,
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

     public function getMatchmakingByCompanyMatch() 
     {
        try{
            $result = Matchmaking::where('company_id_match', '=', Auth::user()->company->id)->with(['company_book:id,company_name','table:id,name_table,date'])->get();
            return response()->json([
                'success' => 'true',
                'data' => $result,
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

     //  Getting only states
     public function matchMakingApproval(Request $request, $matchmaking_id)
     {
        try {
            $matchmaking = Matchmaking::findOrFail($matchmaking_id);
            if (Auth::user()->role == 'admin') {
                $validatedData = $request->validate([
                    'approved_admin' => 'required'
                ]);
                $matchmaking->update($validatedData);
            } elseif (Auth::user()->role == 'company') {
                $validatedData = $request->validate([
                    'approved_company' => 'required'
                ]);
                $matchmaking->update($validatedData);
            }

            return response()->json([
                'success' => 'true',
                'message' => 'Data berhasil diubah'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => 'false',
                'data' => [],
                'message' => $th->getMessage()
            ]);
        }
     }

     public function getApprovedMatchmakingByCompany()
     {
        try {
            $result = Matchmaking::where('approved_company', '=', 1)->with([
                'company_book:id,company_name',
                'company_match:id,company_name',
                'table:id,name_table,date'])
                ->get(); 
            return response()->json([
                'success' => 'true',
                'data' => $result,
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

     public function dashboardMeja(Request $request) {
        try {
            $conferenceId = $request->input('conference');
            $date = $request->input('date'); // Format: YYYY-MM-DD (sesuai input Anda)
    
            $result = Matchmaking::with([
                'company_book:id,company_name',
                'company_match:id,company_name',
                'table:id,name_table,date'
            ])
            ->where('approved_admin', '=', 1)
            ->when($conferenceId, function ($query) use ($conferenceId) {
                $query->whereHas('company_book', function ($subQuery) use ($conferenceId) {
                    $subQuery->where('conference_id', $conferenceId);
                })
                ->orWhereHas('company_match', function ($subQuery) use ($conferenceId) {
                    $subQuery->where('conference_id', $conferenceId);
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereHas('table', function ($subQuery) use ($date) {
                    $subQuery->where('date', $date);
                });
            })
            ->get();
    
            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Success'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => $th->getMessage()
            ]);
        }
    }
    


    public function getMatchmakingByCompanyCalendar($company_id)
    {
        try{
            $result = Matchmaking::where('company_id_book', '=', $company_id)->with(['company_book:id,company_name','table:id,name_table,date'])->get();
            return response()->json([
                'success' => 'true',
                'data' => $result,
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
            $validatedData = $request->validate([
                'company_id_match' => 'required',
                'table_id' => 'required',
                'time_start' => 'required',
                'time_end' => 'required',
                'approved_company' => 'sometimes',
                'approved_admin' => 'sometimes',
            ]);

            $company_id_book = Auth::user()->company->id;
            $validatedData['company_id_book'] = $company_id_book;

            $matchmaking = Matchmaking::create($validatedData);

            return response()->json([
                'success' => 'true',
                'data' => $matchmaking,
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
     * Display the specified resource.
     */
    public function show(Matchmaking $matchmaking)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Matchmaking $matchmaking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Matchmaking $matchmaking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Matchmaking $matchmaking)
    {
        //
    }
}
