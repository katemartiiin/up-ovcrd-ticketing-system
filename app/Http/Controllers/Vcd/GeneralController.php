<?php

namespace App\Http\Controllers\Vcd;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;

use Carbon\Carbon;
use Inertia\Inertia;

use App\Models\Office;
use App\Models\Tickets\Ticket;
use App\Models\Processes\Process;

use App\Http\Controllers\Controller;

class GeneralController extends Controller
{
    // Go to Default page
    public function defaultPage()
    {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }

    // Go to Dashboard
    public function dashboardPage()
    {
        return Inertia::render('Vcd/Dashboard');
    }

    public function fetchCharts(Request $request)
    {
        $user = auth()->user();

        $barData = [];
        $barText = "";
        $pieData = [];
        $graphData = [];

        $tickets = Ticket::query();

        if ($request->dates) {
            $dates = $request->dates;
            $from = Carbon::parse($dates[0])->format('Y-m-d');
            $to = Carbon::parse($dates[1])->format('Y-m-d');
            $tickets = $tickets->whereBetween('created_at', [$from, $to]);
        }
        
        // Director
        if ($user->role == 3) 
        {
            $barText = "Tickets per type";
            $tickets = $tickets->where('office_id', $user->office_id)->get();
            /* Bar Data */
            $processes = Process::where('office_id', $user->office_id)->get();

            foreach ($processes as $process) {
                $processCount = $tickets->where('process_id', $process->id)->count();
                $barData[] = ['name' => $process->name, 'y' => $processCount, 'drilldown' => $process->name];
            }
        } 
        // Vice-Chancellor
        else if ($user->role == 4) {
            $barText = "Tickets per section";
            $tickets = $tickets->get();
            $offices = Office::where('id', '!=', 1)->get();

            foreach ($offices as $office) {
                $officeCount = $tickets->where('office_id', $office->id)->count();
                $barData[] = ['name' => $office->short_code, 'y' => $officeCount, 'drilldown' => $office->short_code];
            }
        }

        /* Pie Data */
        $activeCount = $tickets->where('status', Ticket::STATUS_ACTIVE)->count();
        $completedCount = $tickets->where('status', Ticket::STATUS_COMPLETED)->count();
        $resolvedCount = $tickets->where('status', Ticket::STATUS_RESOLVED)->count();
        $pieData[] = ['Active', $activeCount];
        $pieData[] = ['Completed', $completedCount];
        $pieData[] = ['Resolved', $resolvedCount];

        /* Graph Data */
        $currentDate = Carbon::now();
        $year = $currentDate->format('Y');
        $firstMonth = '1';

        for ($index = $firstMonth; $index <= 12; $index++)
        {
            $formattedDate = $year . '-' . $index .'-1';
            $monthName = Carbon::parse($formattedDate)->format('M');
            $monthCount = $tickets->whereBetween('created_at', [Carbon::parse($formattedDate)->startOfMonth(), Carbon::parse($formattedDate)->endOfMonth()])->count();
            $graphData[] = ['name' => $monthName, 'y' => $monthCount, 'drilldown' => $monthName];
        }

        return response()->json([
            'barText' => $barText,
            'barData' => $barData,
            'pieData' => $pieData,
            'graphData' => $graphData
        ]);
    }

    public function filter(Request $request)
    {
        $user = auth()->user();

        $barData = [];
        $pieData = [];

        $tickets = Ticket::query();

        if ($request->dates) {
            $dates = $request->dates;
            $from = Carbon::parse($dates[0])->startOfDay();
            $to = Carbon::parse($dates[1])->endOfDay();
            $tickets = $tickets->whereBetween('created_at', [$from, $to]);
        }
        
        // Director
        if ($user->role == 3) 
        {
            $tickets = $tickets->where('office_id', $user->office_id)->get();
            /* Bar Data */
            $processes = Process::where('office_id', $user->office_id)->get();

            foreach ($processes as $process) {
                $processCount = $tickets->where('process_id', $process->id)->count();
                $barData[] = ['name' => $process->name, 'y' => $processCount, 'drilldown' => $process->name];
            }
        } 
        // Vice-Chancellor
        else if ($user->role == 4) {
            $tickets = $tickets->get();
            $offices = Office::where('id', '!=', 1)->get();

            foreach ($offices as $office) {
                $officeCount = $tickets->where('office_id', $office->id)->count();
                $barData[] = ['name' => $office->short_code, 'y' => $officeCount, 'drilldown' => $office->short_code];
            }
        }

        /* Pie Data */
        $activeCount = $tickets->where('status', Ticket::STATUS_ACTIVE)->count();
        $completedCount = $tickets->where('status', Ticket::STATUS_COMPLETED)->count();
        $resolvedCount = $tickets->where('status', Ticket::STATUS_RESOLVED)->count();
        $pieData[] = ['Active', $activeCount];
        $pieData[] = ['Completed', $completedCount];
        $pieData[] = ['Resolved', $resolvedCount];


        return response()->json([
            'barData' => $barData,
            'pieData' => $pieData,
        ]);
    }
    
}
