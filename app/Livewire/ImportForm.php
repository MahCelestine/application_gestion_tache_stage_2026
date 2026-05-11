<?php

namespace App\Livewire;

use League\Csv\Serializer\CastToArray;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Models\Task;
use App\Models\Equipe;
use App\Http\Requests\StoreTaskRequest;

class ImportForm extends Component
{
    public $label, $quote_number, $client_id, $new_client_name, $due_date, $billing_info, $prospect_id;

    public $evoliz_quote_id, $evoliz_item_id;

    public $estimated_h, $estimated_m;

    public $subtasks = [];

    public $equipe_ids = [];
    public $pendingLines = [];

    public function mount()
    {
        $sessionLines = session('pending_evoliz_lines', []);

        $existingKeys = Task::whereNotNull('evoliz_item_id')
            ->whereNotNull('evoliz_quote_id')
            ->select(DB::raw("CONCAT(evoliz_quote_id, '_', evoliz_item_id) as unique_key"))
            ->pluck('unique_key')
            ->toArray();

        $this->pendingLines = collect($sessionLines)->filter(function ($line) use ($existingKeys) {
            $key = $line['evoliz_quote_id'] . '_' . $line['evoliz_item_id'];
            return !in_array($key, $existingKeys);
        })->values()->all();

        session(['pending_evoliz_lines' => $this->pendingLines]);

        $this->loadLine();
    }

    public function LoadLine()
    {
        if (empty($this->pendingLines)) {
            return redirect()->route('tasks.index')->with('sucess', 'Synchronisation terminée !');
        }

        $current = $this->pendingLines[0];
        $this->label = $current['label'];
        $this->quote_number = $current['quote_number'];
        $this->evoliz_item_id = $current['evoliz_item_id'];
        $this->evoliz_quote_id = $current['evoliz_quote_id'];

        $this->estimated_h = null;
        $this->estimated_m = null;
        $this->due_date = null;

        $client = Client::where('nom', $current['client_name'])->first();

        if ($client) {
            $this->client_id = $client->id;
            $this->new_client_name = null;
        } else {
            $this->client_id = null;
            $this->new_client_name = $current['client_name'];
        }
    }

    public function save()
    {
        $rules = (new StoreTaskRequest())->rules();
        $this->validate($rules);
        
        $data = $this->all();

        $task = Task::createWithLogicAndSubtask($data);

        if (!empty($this->equipes_ids)) {
            $task->equipes()->sync($this->equipe_ids);
        }

        array_shift($this->pendingLines);
        session(['pending_evoliz_lines' => $this->pendingLines]);

        $this->LoadLine();
        $this->subtasks = [];
        $this->dispatch('hide-loader');
    }


    public function render()
    {

        return view('livewire.import-form', [
            'clients' => Client::all(),
            'equipes' => Equipe::all(),
            'totalPending' => count($this->pendingLines)
        ]);
    }
}
