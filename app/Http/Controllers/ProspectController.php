<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prospect;
use App\Models\Client;
use App\Models\Equipe;

class ProspectController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'status' => 'required|in:RDV à prendre,Date de RDV,OK',
            'source' => 'nullable|string|max:255',
            'rdv_date' => 'nullable|date',
            'note' => 'nullable|string',
        ]);

        $prospect = Prospect::create([
            'nom' => $validated['nom'],
            'status' => $validated['status'],
            'source' => $validated['source'],
            'rdv_date' => $validated['rdv_date'],
            'response_type' => null,
            'quote_number' => null,
            'is_followup' => 'NON',
        ]);

        if ($request->filled('note')) {
            $prospect->notes()->create([
                'description' => $request->input('note'),
                'is_finish' => true,
            ]);
        }

        return redirect()->route('prospects.prospect');
    }

    public function create()
    {
        return view('prospects.form_prospect');
    }

    public function edit($id)
    {
        $prospect = Prospect::findOrFail($id);

        return view('prospects.form_edit_prospect', compact('prospect'));
    }

    public function update(Request $request, $id)
    {
        $prospect = Prospect::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'source' => 'nullable|string|max:255',
            'status' => 'required|in:RDV à prendre,Date de RDV,OK',
            'rdv_date' => 'required_if:status,Date de RDV,OK|nullable|date',
            'response_type' => 'nullable|in:OUI,NON,DEVIS',
            'quote_number' => 'nullable|string',
            'is_followup' => 'nullable|in:OUI,NON',
            'note' => 'nullable|string'
        ]);

        $prospect->update($validated);

        if ($request->filled('note')) {
            if ($prospect->notes->isNotEmpty()) {
                $prospect->notes->last()->update([
                    'description' => $request->input('note')
                ]);
            } else {
                $prospect->notes()->create([
                    'description' => $request->input('note'),
                    'is_finish' => true
                ]);
            }
        }

        return redirect()->route('prospects.prospect');
    }

    public function transform($id)
    {
        $prospect = Prospect::findOrFail($id);

        return redirect()->route('tasks.create', ['prospect_id' => $id]);
    }

    public function destroy(Request $request, Prospect $prospect)
    {
        $prospect->delete();

        return redirect()->route('prospects.prospect');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $filterStatus = $request->input('filter_status');
        $sortNom = $request->input('sort_nom');

        $query = Prospect::with('notes');

        if($request->filled('filter_status')) {
            $query->where('status', $filterStatus);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%");
            });
        }

        if ($filterStatus) {
            $query->where('status', $filterStatus);
        }

        $query->orderByRaw("
        CASE
        WHEN rdv_date IS NOT NULL THEN 1
        WHEN status = 'RDV à prendre' THEN 2
        WHEN status = 'Date de RDV' THEN 3
        WHEN status = 'OK' THEN 4
        ELSE 5
        END ASC
        ");

        $query->orderByRaw("
        CASE
        WHEN status = 'OK' AND is_followup = 'NON' THEN 1
        WHEN status = 'OK' AND is_followup = 'OUI' THEN 2
        ELSE 3
        END ASC
        ");

        if ($sortNom) {
            $query->orderBy('nom', $sortNom);
        } else {
            $query->orderBy('rdv_date', 'asc');
        }

        $prospects = $query->get();

        return view('prospects.prospect', compact('prospects', 'sortNom', 'search', 'filterStatus'));
    }
}
