<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class DeleteConfirmationModal extends Component
{
    public $isOpen = false;
    public $title = '';
    public $message = '';
    public $formId = '';
    public $label = 'Supprimer';

    #[On('open-delete-modal')]
    public function open($title, $message, $label, $formId)
    {
        $this->title = $title;
        $this->message = $message;
        $this->formId = $formId;
        $this->label = $label;
        $this->isOpen = true;
    }

    public function confirm() {
        $this->dispatch('do-submit-delete', formId: $this->formId);
        $this->isOpen = false;
    }
    public function render()
    {
        return view('livewire.delete-confirmation-modal');
    }
}
