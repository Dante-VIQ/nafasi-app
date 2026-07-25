<?php

// app/Livewire/Admin/AiDashboard.php

namespace App\Livewire\Admin;

use App\Services\ML\MlServiceClient;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class AiDashboard extends Component
{
    protected MlServiceClient $ml;

    public array $insights = [];

    public array $predictions = [];

    public array $seasonalRisks = [];

    public string $trainingStatus = '';

    public int $trainingDays = 7;

    public bool $isTraining = false;

    public string $modelHealth = 'unknown';

    public string $testText = '';

    public array $classificationResult = [];

    public string $adminMessage = '';

    public array $adminMessages = [];

    public function boot(MlServiceClient $ml): void
    {
        $this->ml = $ml;
    }

    public function mount(MlServiceClient $ml): void
    {
        $this->ml = $ml;
        $this->loadDashboard();
    }

    public function getMlProperty(): MlServiceClient
    {
        return $this->ml ??= new MlServiceClient;
    }

    public function loadDashboard(): void
    {
        $this->modelHealth = $this->ml->isHealthy() ? 'healthy' : 'unhealthy';
        $this->loadInsights();
        $this->loadPredictions();
        $this->loadSeasonalRisks();
        $this->loadAdminMessages();
    }

    public function loadInsights(): void
    {
        try {
            $response = Http::timeout(5)
                ->get(config('services.ml_service.url').'/insights');
            if ($response->successful()) {
                $this->insights = $response->json();
            }
        } catch (\Exception $e) {
            $this->insights = [];
        }
    }

    public function loadPredictions(): void
    {
        try {
            $response = Http::timeout(5)
                ->get(config('services.ml_service.url').'/predict/demand');
            if ($response->successful()) {
                $this->predictions = $response->json();
            }
        } catch (\Exception $e) {
            $this->predictions = [];
        }
    }

    public function loadSeasonalRisks(): void
    {
        try {
            $response = Http::timeout(5)
                ->get(config('services.ml_service.url').'/context/seasonal-risks');
            if ($response->successful()) {
                $this->seasonalRisks = $response->json();
            }
        } catch (\Exception $e) {
            $this->seasonalRisks = [];
        }
    }

public function triggerTraining(): void
{
    $this->isTraining = true;
    $this->trainingStatus = 'Training in progress...';

    try {
        $response = \Illuminate\Support\Facades\Http::timeout(300)
            ->post(config('services.ml_service.url') . '/learn', [
                'days' => $this->trainingDays,
                'triggered_by' => 'super_admin',
            ]);

        if ($response->successful()) {
            $result = $response->json();

            if ($result['status'] === 'learned') {
                $this->trainingStatus = sprintf(
                    'Training complete. Analyzed %d interactions. Found %d new patterns.',
                    $result['interactions_analyzed'] ?? 0,
                    $result['new_patterns_found'] ?? 0
                );
            } elseif ($result['status'] === 'insufficient_data') {
                $this->trainingStatus = 'Not enough data to train. Need at least 50 interactions.';
            } else {
                $this->trainingStatus = 'Training finished: ' . ($result['status'] ?? 'unknown status');
            }
        } else {
            $this->trainingStatus = 'Training failed (HTTP ' . $response->status() . ')';
        }
    } catch (\Exception $e) {
        $this->trainingStatus = 'Error: ' . $e->getMessage();
    }

    $this->isTraining = false;
}

    public function testClassification(): void
    {
        if (empty($this->testText)) {
            return;
        }
        $this->classificationResult = $this->ml->classify($this->testText);
    }

    public function sendMessageToModel(): void
    {
        if (empty($this->adminMessage)) {
            return;
        }
        try {
            Http::timeout(5)
                ->post(config('services.ml_service.url').'/admin/message', [
                    'message' => $this->adminMessage,
                    'from' => auth()->user()->name,
                ]);
            $this->adminMessages[] = [
                'from' => auth()->user()->name,
                'message' => $this->adminMessage,
                'timestamp' => now()->toISOString(),
            ];
            $this->adminMessage = '';
            session()->flash('message', 'Message sent.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to send.');
        }
    }

    public function loadAdminMessages(): void
    {
        try {
            $response = Http::timeout(5)
                ->get(config('services.ml_service.url').'/admin/messages');
            if ($response->successful()) {
                $this->adminMessages = $response->json();
            }
        } catch (\Exception $e) {
            $this->adminMessages = [];
        }
    }

    public function render()
    {
        return view('livewire.admin.ai-dashboard')->layout('layouts.app');
    }
}
