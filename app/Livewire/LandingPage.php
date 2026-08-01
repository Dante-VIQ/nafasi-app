<?php

namespace App\Livewire;

use App\Models\InteractionOutcome;
use App\Models\Tenant\AssistanceRequest;
use App\Models\User;
use App\Notifications\CrisisSupportNotification;
use App\Notifications\FacilityDirectionsNotification;
use App\Services\Routing\SituationRouter;
use Livewire\Attributes\On;
use Livewire\Component;

class LandingPage extends Component
{
    public string $situation = '';

    public ?float $userLat = null;

    public ?float $userLng = null;

    public bool $locationGranted = false;

    public ?array $result = null;

    // // Clarification assistant (optional)
    // public bool $awaitingClarification = false;
    // public string $clarificationQuestion = '';
    // public string $clarificationInput = '';

    protected $rules = [
        'situation' => [
            'required',
            'string',
            'min:2',
            'max:500',
            'regex:/^[^<>]*$/',               // no HTML tags
            'not_regex:/(--|;|\/\*|\*\/)/',   // no SQL comments
        ],
    ];

    public function submit()
    {
        $this->validate();

        $router = app(SituationRouter::class);
        $result = $router->route($this->situation, $this->userLat, $this->userLng);

        // // If no facilities found and not an emergency, ask for clarification
        // if ($result['type'] === 'facilities' && empty($result['facilities'])) {
        //     $this->clarificationQuestion = 'Could you be more specific? For example: "pharmacy", "lab test", "maternity care".';
        //     $this->awaitingClarification = true;
        //     $this->result = null;          // keep the input visible
        //     return;
        // }

        // Special handling for dispatch requests
        if ($result['type'] === 'dispatch') {
            $this->createAssistanceRequest($result);
        }
        if ($result['type'] === 'crisis') {
            if ($phone = auth()->user()->phone ?? null) {
                $user = new User;
                $user->phone = $phone;
                $user->notify(new CrisisSupportNotification($result['detected_language'] ?? 'en'));
            }
        }
        $this->result = $result;

        // Inside submit(), right after $this->result = $result;
        if ($result['type'] === 'facilities') {
            InteractionOutcome::create([
                'tenant_id' => tenant()?->id,
                'session_id' => session()->getId(),
                'user_text' => $this->situation,
                'language' => $result['detected_language'] ?? null,
                'intent' => ['facility_hints' => $result['facility_hints'] ?? []],
                'confidence' => $result['confidence'] ?? 0.5,
                'facility_hints' => $result['facility_hints'] ?? [],
                'recommended_facility_id' => $result['facilities'][0]['id'] ?? null,
                'outcome_type' => 'none', // will be updated later
                'risk_assessment' => $result['risk'] ?? null,
                'decision' => $result['decision'] ?? null,
                'escalated' => $result['escalation']['should_escalate'] ?? false,
                'escalation_level' => $result['escalation']['escalation_level'] ?? null,
                'escalation_reasons' => $result['escalation']['reasons'] ?? null,
                'llm_verification' => $result['llm_verification'] ?? null,
            ]);
        }
        // $this->awaitingClarification = false;
    }

    public function clarifyAndSubmit()
    {
        if (empty(trim($this->clarificationInput))) {
            return;
        }

        // Combine original text with clarification
        $original = $this->situation;
        $this->situation = $original.' '.$this->clarificationInput;
        // $this->clarificationInput = '';
        // $this->awaitingClarification = false;
        $this->submit();
    }

    // public function dismissClarification()
    // {
    //     $this->awaitingClarification = false;
    //     $this->clarificationInput = '';
    // }

    protected function createAssistanceRequest(array $result)
    {
        AssistanceRequest::create([
            'session_id' => session()->getId(),
            'phone_number' => auth()->user()->phone ?? null,
            'preferred_language' => $result['detected_language'] ?? 'sw',
            'user_description' => $this->situation,
            'urgency' => in_array('emergency', $result['detected_tags'] ?? []) ? 'emergency' : 'routine',
            'detected_tags' => $result['detected_tags'] ?? [],
            'latitude' => $this->userLat,
            'longitude' => $this->userLng,
            'location_description' => null,
            'status' => 'pending',
        ]);

        $this->result = [
            'type' => 'dispatch_created',
            'message' => 'Your request has been received. A coordinator will reach out shortly.',
        ];
    }

    #[On('set-location')]
    public function setLocation(float $lat, float $lng)
    {
        $this->userLat = $lat;
        $this->userLng = $lng;
        $this->locationGranted = true;
    }

    public function requestLocation()
    {
        $this->dispatch('request-geolocation');
    }

    public function resetSearch()
    {
        $this->reset(['situation', 'result', 'userLat', 'userLng', 'locationGranted',
            'awaitingClarification', 'clarificationQuestion', 'clarificationInput']);
    }

    public function sendDirectionsSms(string $phone, array $facility)
    {
        $user = new User;
        $user->phone = $phone;
        $user->notify(new FacilityDirectionsNotification($facility));

        session()->flash('message', 'Directions sent to '.$phone);
    }

    public function trackAction(string $action, int $facilityId): void
    {
        InteractionOutcome::where('session_id', session()->getId())
            ->where('outcome_type', 'none')
            ->latest()
            ->first()
            ?->update([
                'outcome_type' => $action, // 'called' or 'directions'
                'outcome_facility_id' => $facilityId,
            ]);
    }

    public function render()
    {
        return view('livewire.landing-page')->layout('layouts.app');
    }
}
