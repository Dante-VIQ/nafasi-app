<?php

namespace App\Livewire\Coordinator;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\InteractionOutcome;
use Illuminate\Support\Facades\Auth;

class EscalationDashboard extends Component
{
    use WithPagination;

    public string $filterLevel = '';
    public ?int $selectedId = null;
    public string $handlerNotes = '';

    public function updatingFilterLevel(): void
    {
        $this->resetPage();
    }

    /**
     * Claim and handle an escalated case.
     */
    public function claim(int $id): void
    {
        $outcome = InteractionOutcome::where('escalated', true)
            ->whereNull('escalation_handler_id')
            ->findOrFail($id);

        $outcome->update([
            'escalation_handler_id' => Auth::id(),
        ]);

        session()->flash('message', 'Case #' . substr($outcome->uuid, 0, 8) . ' claimed. You are now handling it.');
    }

    /**
     * Resolve a case.
     */
    public function resolve(int $id): void
    {
        $outcome = InteractionOutcome::where('escalation_handler_id', Auth::id())
            ->findOrFail($id);

        $outcome->update([
            'escalation_resolved_at' => now(),
            'verification_notes'     => $this->handlerNotes,
        ]);

        $this->reset(['selectedId', 'handlerNotes']);
        session()->flash('message', 'Case resolved.');
    }

    public function render()
    {
        $escalatedCases = InteractionOutcome::where('escalated', true)
            ->when($this->filterLevel, fn($q) => $q->where('escalation_level', $this->filterLevel))
            ->orderByRaw("FIELD(escalation_level, 'immediate', 'priority', 'standard')")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.coordinator.escalation-dashboard', [
            'escalatedCases' => $escalatedCases,
        ])->layout('layouts.app');
    }
}