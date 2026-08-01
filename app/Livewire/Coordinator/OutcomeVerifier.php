<?php

namespace App\Livewire\Coordinator;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\InteractionOutcome;

class OutcomeVerifier extends Component
{
    use WithPagination;

    public string $filterOutcome = '';
    public ?int $selectedId = null;
    public ?bool $wasCorrect = null;
    public string $verificationNotes = '';

    protected $rules = [
        'wasCorrect'        => 'required|boolean',
        'verificationNotes' => 'nullable|string|max:500',
    ];

    public function updatingFilterOutcome(): void
    {
        $this->resetPage();
    }

    public function selectOutcome(int $id): void
    {
        $this->selectedId = $id;
        $outcome = InteractionOutcome::findOrFail($id);
        $this->wasCorrect = $outcome->was_correct;
        $this->verificationNotes = $outcome->verification_notes ?? '';
    }

    public function closeModal(): void
    {
        $this->reset(['selectedId', 'wasCorrect', 'verificationNotes']);
    }

    public function verify(): void
    {
        $this->validate();

        $outcome = InteractionOutcome::findOrFail($this->selectedId);
        $outcome->update([
            'was_correct'        => $this->wasCorrect,
            'verified_by'        => auth()->id(),
            'verified_at'        => now(),
            'verification_notes' => $this->verificationNotes,
        ]);

        session()->flash('message', 'Outcome #' . substr($outcome->uuid, 0, 8) . ' verified.');
        $this->closeModal();
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