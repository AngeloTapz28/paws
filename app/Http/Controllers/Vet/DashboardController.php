<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\VaccinationRecord;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
{
    $totalPets        = Pet::count();
    $vaccinatedPets   = Pet::where('is_vaccinated', true)->count();
    $overdueVaccines  = VaccinationRecord::whereDate('next_due_date', '<', now())->count();

    $recentRecords    = MedicalRecord::with(['pet', 'vet'])
                            ->latest()
                            ->take(10)
                            ->get();

    return view('vet.dashboard', compact(
        'totalPets', 'vaccinatedPets',
        'overdueVaccines', 'recentRecords'
    ));
}
}