<?php

namespace App\Livewire\Coordinator;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\InteractionOutcome;

class OutcomeVerifier extends Component
{
    use WithPagination;

    public string $filterOutcome = '';
    public ?InteractionOutcome $selected = null;
    public ?bool $wasCorrect = null;
    public string $verificationNotes = '';

    protected $rules = [
        'wasCorrect'        => 'required|boolean',
        'verificationNotes' => 'nullable|string|max:500',
    ];

    public function verify(int $id)
    {
        $outcome = InteractionOutcome::findOrFail($id);
        $outcome->update([
            'was_correct'       => $this->wasCorrect,
            'verified_by'       => auth()->id(),
            'verified_at'       => now(),
            'verification_notes' => $this->verificationNotes,
        ]);

        session()->flash('message', 'Outcome verified.');
        $this->reset(['selected', 'wasCorrect', 'verificationNotes']);
    }

    public function render()
    {
        $outcomes = InteractionOutcome::query()
            ->when($this->filterOutcome, fn($q) => $q->where('outcome_type', $this->filterOutcome))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('livewire.coordinator.outcome-verifier', [
            'outcomes' => $outcomes,
        ])->layout('layouts.app');
    }
}