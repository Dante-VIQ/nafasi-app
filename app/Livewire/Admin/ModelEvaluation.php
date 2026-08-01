<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\InteractionOutcome;
use Illuminate\Support\Facades\DB;

class ModelEvaluation extends Component
{
    public int $days = 30;
    public array $accuracyTrend = [];
    public array $confidenceDistribution = [];
    public array $escalationStats = [];
    public array $riskLevelDistribution = [];
    public array $languageDistribution = [];
    public float $overallAccuracy = 0;
    public int $totalVerified = 0;

    public function mount(): void
    {
        $this->loadMetrics();
    }

    public function updatedDays(): void
    {
        $this->loadMetrics();
    }

    public function loadMetrics(): void
    {
        $this->loadAccuracyTrend();
        $this->loadConfidenceDistribution();
        $this->loadEscalationStats();
        $this->loadRiskDistribution();
        $this->loadLanguageDistribution();
        $this->calculateOverallAccuracy();
    }

    protected function loadAccuracyTrend(): void
    {
        $this->accuracyTrend = InteractionOutcome::whereNotNull('was_correct')
            ->where('created_at', '>=', now()->subDays($this->days))
            ->select(DB::raw('DATE(created_at) as date'),
                     DB::raw('count(*) as total'),
                     DB::raw('SUM(CASE WHEN was_correct = 1 THEN 1 ELSE 0 END) as correct'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($row) => [
                'date'      => $row->date,
                'accuracy'  => $row->total > 0 ? round(($row->correct / $row->total) * 100, 1) : 0,
                'total'     => $row->total,
            ])
            ->toArray();
    }

    protected function loadConfidenceDistribution(): void
    {
        $this->confidenceDistribution = InteractionOutcome::whereNotNull('was_correct')
            ->where('created_at', '>=', now()->subDays($this->days))
            ->select('confidence', 'was_correct')
            ->get()
            ->groupBy(fn($o) => match(true) {
                $o->confidence >= 0.9 => '90-100%',
                $o->confidence >= 0.7 => '70-89%',
                $o->confidence >= 0.5 => '50-69%',
                default               => '<50%',
            })
            ->map(fn($group, $bucket) => [
                'bucket'   => $bucket,
                'total'    => $group->count(),
                'correct'  => $group->where('was_correct', true)->count(),
                'accuracy' => round(($group->where('was_correct', true)->count() / max(1, $group->count())) * 100, 1),
            ])
            ->values()
            ->toArray();
    }

    protected function loadEscalationStats(): void
    {
        $this->escalationStats = InteractionOutcome::where('escalated', true)
            ->where('created_at', '>=', now()->subDays($this->days))
            ->select('escalation_level',
                     DB::raw('count(*) as total'),
                     DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, escalation_resolved_at)) as avg_resolution_minutes'))
            ->groupBy('escalation_level')
            ->get()
            ->toArray();
    }

    protected function loadRiskDistribution(): void
    {
        $this->riskLevelDistribution = InteractionOutcome::whereNotNull('risk_assessment')
            ->where('created_at', '>=', now()->subDays($this->days))
            ->select(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(risk_assessment, '$.level')) as level"),
                     DB::raw('count(*) as total'))
            ->groupBy('level')
            ->orderByRaw("FIELD(level, 'critical', 'high', 'medium', 'low', 'routine')")
            ->get()
            ->toArray();
    }

    protected function loadLanguageDistribution(): void
    {
        $this->languageDistribution = InteractionOutcome::where('created_at', '>=', now()->subDays($this->days))
            ->select('language', DB::raw('count(*) as total'))
            ->groupBy('language')
            ->orderByDesc('total')
            ->get()
            ->toArray();
    }

    protected function calculateOverallAccuracy(): void
    {
        $verified = InteractionOutcome::whereNotNull('was_correct')
            ->where('created_at', '>=', now()->subDays($this->days));

        $this->totalVerified = $verified->count();
        $correct = (clone $verified)->where('was_correct', true)->count();

        $this->overallAccuracy = $this->totalVerified > 0
            ? round(($correct / $this->totalVerified) * 100, 1)
            : 0;
    }

    public function render()
    {
        return view('livewire.admin.model-evaluation')->layout('layouts.app');
    }
}