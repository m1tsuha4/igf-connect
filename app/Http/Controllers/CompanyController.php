<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Matchmaking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $companies = Company::with([
                'keyProductLine:id,company_id,name',
                'bizMatch:id,company_id,name',
                'preferredPlatform:id,company_id,name',
                'schedule:id,company_id,date,time_start,time_end',
            ])->get();

            // Append full logo URL
            $companies->transform(function ($company) {
                $company->company_logo = $company->company_logo 
                    ? Storage::url($company->company_logo) 
                    : null;
                return $company;
            });

            return response()->json([
                'success' => true,
                'data' => $companies,
                "message" => "Data berhasil ditemukan",
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function getCompanyByConference(Request $request)
    {
        try {
            $conference_id = Auth::user()->company->conference_id;

            $companies = Company::with([
                'keyProductLine:id,company_id,name',
                'bizMatch:id,company_id,name',
                'preferredPlatform:id,company_id,name',
                'schedule:id,company_id,date,time_start,time_end',
            ])->where('conference_id', $conference_id)->get();

            // Append full logo URL
            $companies->transform(function ($company) {
                $company->company_logo = $company->company_logo 
                    ? Storage::url($company->company_logo) 
                    : null;
                return $company;
            });

            return response()->json([
                'success' => true,
                'data' => $companies,
                "message" => "Data berhasil ditemukan",
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'conference_id' => 'required',
                'email' => 'required',
                'password' => 'required',
                'confirm_password' => 'required|same:password',
                'username' => 'required',
                'phone_number' => 'required',
                'company_name' => 'required',
                'representative_name' => 'required',
                'address' => 'required',
                'company_logo' => 'sometimes',
                'about_us' => 'sometimes',
                'company_type' => 'required',
                'country' => 'required',
                'key_product_lines' => 'sometimes|array',
                'key_product_lines.*.name' => 'sometimes',
                'biz_matchings' => 'sometimes|array',
                'biz_matchings.*.name' => 'sometimes',
                'preferred_platforms' => 'sometimes|array',
                'preferred_platforms.*.name' => 'sometimes',
                'schedules' => 'sometimes|array',
                'schedules.*.date' => 'sometimes',
                'schedules.*.time_start' => 'sometimes',
                'schedules.*.time_end' => 'sometimes',
            ]);
            DB::beginTransaction();
            $user = User::create([
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'username' => $request->username,
                'phone_number' => $request->phone_number,
            ]);

            $companyLogoPath = null;
            if ($request->hasFile('company_logo')) {
                $companyLogoPath = $request->file('company_logo')->store('company_logos', 'public');
            }
    
            $company = Company::create([
                'conference_id' => $request->conference_id,
                'user_id' => $user->id,
                'company_name' => $request->company_name,
                'representative_name' => $request->representative_name,
                'address' => $request->address,
                'company_logo' => $companyLogoPath,
                'about_us' => $request->about_us,
                'company_type' => $request->company_type,
                'country' => $request->country,
                'status' => 1,
            ]);
    
            if (isset($validatedData['key_product_lines'])) {
                foreach ($validatedData['key_product_lines'] as $prod) {
                    $company->keyProductLine()->create([
                        'name' => $prod['name'],
                        'company_id' => $company->id
                    ]);
                }
            }
            
            if (isset($validatedData['biz_matchings'])) {
                foreach ($validatedData['biz_matchings'] as $bizmatch) {
                    $company->bizMatch()->create([
                        'name' => $bizmatch['name'],
                        'company_id' => $company->id
                    ]);
                }
            }
            
            if (isset($validatedData['preferred_platforms'])) {
                foreach ($validatedData['preferred_platforms'] as $prefplat) {
                    $company->preferredPlatform()->create([
                        'name' => $prefplat['name'],
                        'company_id' => $company->id
                    ]);
                }
            }

            if (isset($validatedData['schedules'])) {
                foreach ($validatedData['schedules'] as $schedule) {
                    $company->schedule()->create([
                        'date' => $schedule["date"],
                        'time_start' => $schedule["time_start"],
                        'time_end' => $schedule["time_end"],
                    ]);
                }
            }
            
    
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil ditambah',
            ], 201);
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
    public function show(Company $company)
    {
        try {
            // Load related data
            $company->load([
                'keyProductLine:id,company_id,name',
                'bizMatch:id,company_id,name',
                'preferredPlatform:id,company_id,name',
                'schedule:id,company_id,date,time_start,time_end'
            ]);

            // Get all matchmaking data for the current company
            $matchmaking = Matchmaking::where('company_id_match', $company->id)->get();

            // Iterate through the company's schedules and add the status
            $company->schedule->transform(function ($schedule) use ($matchmaking) {
                $status = $matchmaking->contains(function ($match) use ($schedule) {
                    return $match->table->date === $schedule->date &&
                        $match->time_start === $schedule->time_start &&
                        $match->time_end === $schedule->time_end;
                });

                // Add status to the schedule
                $schedule->status = $status ? 1 : 0;

                return $schedule;
            });

            $company->company_logo = $company->company_logo
                ? Storage::url($company->company_logo)
                : null;

            return response()->json([
                'success' => true,
                'data' => $company,
                'message' => 'Success Get Data'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => $th->getMessage()
            ]);
        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        try {
            $request->validate([
                'status' => 'required'
            ]);

            $company->update($request->all());
            return response()->json([
                'success' => 'true',
                'data' => [],
                'message' => 'Company updated successfully'
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
    public function destroy(Company $company)
    {
        try {
            $user = User::find($company->user_id);
            if ($user) {
                $user->delete();
            }
            $company->delete();
            return response()->json([
                'success' => 'true',
                'data' => [],
                'message' => 'Company deleted successfully'
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
