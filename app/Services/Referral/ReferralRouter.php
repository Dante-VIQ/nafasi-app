<?php
// app/Services/Referral/ReferralRouter.php

namespace App\Services\Referral;

use App\Models\Tenant\Facility;
use App\Models\Tenant\Referral;
use App\Models\Tenant\ReferralFeedback;

class ReferralRouter
{
    /**
     * Find the best facility to accept a referral.
     */
    public function findBestDestination(
        Facility $referringFacility,
        string $referralType,
        string $urgency = 'routine'
    ): array {
        $query = Facility::query()
            ->where('is_active', true)
            ->where('registration_status', 'approved')
            ->where('accepts_referrals', true)
            ->where('accepting_referrals_now', true)
            ->where('id', '!=', $referringFacility->id)
            ->whereJsonContains('accepted_referral_types', $referralType);

        // Prioritize preferred destinations
        $preferredIds = collect($referringFacility->preferred_referral_destinations ?? [])
            ->filter(fn($dest) => ($dest['reason'] ?? '') === $referralType)
            ->pluck('facility_id')
            ->toArray();

        if (!empty($preferredIds)) {
            $query->orderByRaw("CASE WHEN id IN (" . implode(',', $preferredIds) . ") THEN 0 ELSE 1 END");
        }

        // Sort by referral congestion
        $query->orderByRaw("
            CASE 
                WHEN referral_congestion_status = 'low' THEN 1
                WHEN referral_congestion_status = 'moderate' THEN 2
                WHEN referral_congestion_status IS NULL THEN 3
                WHEN referral_congestion_status = 'high' THEN 4
                WHEN referral_congestion_status = 'at_capacity' THEN 5
                ELSE 6
            END
        ");

        // Distance
        if ($referringFacility->latitude && $referringFacility->longitude) {
            $query->selectRaw("
                *, 
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance
            ", [$referringFacility->latitude, $referringFacility->longitude, $referringFacility->latitude])
            ->orderBy('distance');
        }

        return $query->take(5)->get()->toArray();
    }

    /**
     * Create a referral request.
     */
    public function createReferral(array $data): Referral
    {
        $referral = Referral::create([
            'referring_facility_id' => $data['referring_facility_id'],
            'referring_staff_name' => $data['referring_staff_name'] ?? null,
            'referring_staff_role' => $data['referring_staff_role'] ?? null,
            'receiving_facility_id' => $data['receiving_facility_id'],
            'patient_reference_id' => $data['patient_reference_id'] ?? null,
            'patient_gender' => $data['patient_gender'] ?? null,
            'patient_age_group' => $data['patient_age_group'] ?? null,
            'patient_is_stable' => $data['patient_is_stable'] ?? true,
            'requires_ambulance' => $data['requires_ambulance'] ?? false,
            'referral_type' => $data['referral_type'],
            'urgency' => $data['urgency'] ?? 'routine',
            'reason_for_referral' => $data['reason_for_referral'],
            'clinical_summary' => $data['clinical_summary'] ?? null,
            'treatment_given' => $data['treatment_given'] ?? null,
            'additional_notes' => $data['additional_notes'] ?? null,
            'status' => 'pending',
        ]);

        return $referral;
    }

    /**
     * Accept a referral.
     */
    public function acceptReferral(Referral $referral): void
    {
        $referral->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        $referral->receivingFacility->increment('referrals_accepted_today');

        ReferralFeedback::create([
            'referral_id' => $referral->id,
            'facility_id' => $referral->receiving_facility_id,
            'was_appropriate' => true,
            'patient_accepted' => true,
        ]);
    }

    /**
     * Reject a referral.
     */
    public function rejectReferral(Referral $referral, string $reason): void
    {
        $referral->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        ReferralFeedback::create([
            'referral_id' => $referral->id,
            'facility_id' => $referral->receiving_facility_id,
            'was_appropriate' => false,
            'patient_accepted' => false,
            'feedback_notes' => $reason,
        ]);
    }
}