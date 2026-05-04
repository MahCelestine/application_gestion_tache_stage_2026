<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prospect;
use App\Models\Client;
use App\Models\Equipe;
use App\Http\Requests\StoreProspectRequest;
use App\Http\Requests\UpdateProspectRequest;

class ProspectController extends Controller
{
    public function store(StoreProspectRequest $request)
    {
        $validated = $request->validated();

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

    public function update(UpdateProspectRequest $request, $id)
    {
        $prospect = Prospect::findOrFail($id);

        $validated = $request->validated();

        $prospect->update($validated);

        if ($request->filled('note')) {
            if ($prospect->notes->isNotEmpty()) {
                $prospect->notes->last()->update([
                    'description' => $validated['note']
                ]);
            } else {
                $prospect->notes()->create([
                    'description' => $validated['note'],
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
        return view('prospects.prospect');
    }
}
