<?php

// app/Livewire/Reporting/AnonymousReportForm.php

namespace App\Livewire\Reporting;

use App\Jobs\SendSmsNotification;
use App\Models\Tenant\AnonymousReport;
use App\Services\Reporting\AnonymousReportRouter;
use Livewire\Component;
use App\Notifications\AnonymousReportConfirmationNotification;

class AnonymousReportForm extends Component
{
    public string $report_type = '';

    public string $description = '';

    public string $location_description = '';

    public string $time_description = '';

    public string $additional_details = '';

    public bool $submitted = false;

    public ?string $reportUuid = null;

    public ?string $routedToName = null;

    protected $rules = [
        'report_type' => 'required|string',
        'description' => 'required|string|min:10|max:2000',
        'location_description' => 'nullable|string|max:500',
        'time_description' => 'nullable|string|max:200',
        'additional_details' => 'nullable|string|max:1000',
    ];

    protected $messages = [
        'description.required' => 'Please describe what happened.',
        'description.min' => 'Please provide at least 10 characters.',
    ];


public function submit()
{
    $this->validate();

    $router = app(AnonymousReportRouter::class);
    $report = $router->route([
        'report_type' => $this->report_type,
        'description' => $this->description,
        'location_description' => $this->location_description,
        'time_description' => $this->time_description,
        'additional_details' => $this->additional_details,
    ]);

    $this->reportUuid = substr($report->uuid, 0, 8);
    $this->routedToName = $report->routedToFacility?->name;
    $this->submitted = true;
}

    public function resetForm()
    {
        $this->reset();
    }

    public function render()
    {
        return view('livewire.reporting.anonymous-report-form', [
            'reportTypes' => AnonymousReport::reportTypes(),
        ])->layout('layouts.guest');
    }
}
