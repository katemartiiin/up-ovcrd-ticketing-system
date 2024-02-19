<?php

namespace App\Http\Controllers\Vcd;

use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;

use Carbon\Carbon;
use Inertia\Inertia;

use App\Models\Office;
use App\Models\Report;

use App\Http\Controllers\Controller;
use App\Models\Processes\Process;
use App\Models\ResearchIds\ResearchId;
use App\Models\Tickets\Ticket;
use Illuminate\Support\Facades\Storage;

class ReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $offices = Office::with('processes')->where('id', '!=', 1)->get();
        $researchIds = ResearchId::all();

        // Check role if admin is director or vc
        $user = auth()->user();
        $processes = [];
        $officeId = "";

        if ($user->role == 3) {
            $processes = Process::where('office_id', $user->office_id)->get();
            $officeId = $user->office_id;
        }
        

        return Inertia::render('Vcd/Report', [
            'office_id' => $officeId,
            'offices' => $offices,
            'processes' => $processes,
            'research_ids' => $researchIds
        ]);
    }

    /**
     * Fetch a listing of the resource.
     */
    public function fetch(Request $request)
    {
        $options = $request->options;
        $reports = Report::paginate($options['rowsPerPage'], ['*'], 'page', $options['page']);

        return response()->json(['items' => $reports]);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Generate a pdf file for stream.
     */
    public function generate(Request $request) {

        $tickets = Ticket::query();
        $reportName = "";
        
        if ($request->office_id !="") {
            $tickets = $tickets->where('office_id', $request->office_id);
            $office = Office::findOrFail($request->office_id);
            $reportName .= $office->short_code . '_';
        }

        if ($request->process_id != "") {
            $tickets = $tickets->where('process_id', $request->process_id);
            $process = Process::findOrFail($request->process_id);
            $reportName .= 'Type-' . $process->id . '_';
        }

        if ($request->research_id != "") {
            $tickets = $tickets->where('research_id', $request->research_id);
            $research = ResearchId::findOrFail($request->research_id);
            $reportName .= 'RS-' . $research->research_code . '_';
        }

        if ($request->status != "") {
            $tickets = $tickets->where('status', $request->status);
            $reportName .= 'S' . $request->status . '_';
        }

        if ($request->dates != "") {
            $dates = $request->dates;
            $from = Carbon::parse($dates[0]);
            $to = Carbon::parse($dates[1]);
            $tickets = $tickets->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()]);
            $reportName .= $from->format('Y-m-d') . '-to-' . $to->format('Y-m-d') . '-';
        }
        
        $tickets = $tickets->get();
        $reportName .= 'Report';

        $report = Report::create([
            'office_id' => $request->office_id,
            'process_id' => $request->process_id,
            'admin_id' => auth()->user()->id,
            'title' => $reportName,
            'path' => 'reports/' . $reportName . '.pdf'
        ]);
     
        $pdf = Pdf::loadView('reports.ticket', ['report' => $report, 'items' => $tickets])->save(storage_path('app/public/' . $report->path));
     
        // return $pdf->stream();
        if ($pdf) {
            return response()->json(['url' => Storage::url($report->path)]);
        } else {
            return response()->json(['message' => 'Something went wrong.'], 500);
        }
    }
}
