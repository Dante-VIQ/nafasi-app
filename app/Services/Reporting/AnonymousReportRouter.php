<?php
// app/Services/Reporting/AnonymousReportRouter.php

namespace App\Services\Reporting;

use App\Models\Tenant\Facility;
use App\Models\Tenant\AnonymousReport;

class AnonymousReportRouter
{
    /**
     * Route an anonymous report to the appropriate authority.
     * NEVER stores who submitted it.
     */
    public function route(array $data): AnonymousReport
    {
        $reportType = $data['report_type'];
        
        // Determine which type of facility should receive this
        $routedToType = $this->determineRouteTarget($reportType);
        
        // Find nearest appropriate facility
        $facility = $this->findNearestFacility($routedToType, $data['latitude'] ?? null, $data['longitude'] ?? null);
        
        // Create the report (anonymous — no user data)
        $report = AnonymousReport::create([
            'report_type' => $reportType,
            'description' => $data['description'],
            'location_description' => $data['location_description'] ?? null,
            'time_description' => $data['time_description'] ?? null,
            'additional_details' => $data['additional_details'] ?? null,
            'routed_to_facility_id' => $facility?->id,
            'routed_to_type' => $routedToType,
            'status' => 'submitted',
        ]);

        // In production: send notification to the facility
        // $this->notifyFacility($facility, $report);

        return $report;
    }

    protected function determineRouteTarget(string $reportType): string
    {
        return match($reportType) {
            'gbv', 'domestic_violence' => 'gbv_desk',
            'child_abuse' => 'child_protection',
            'corruption' => 'anti_corruption',
            'trafficking' => 'human_trafficking',
            default => 'police_station',
        };
    }

    protected function findNearestFacility(string $facilityType, ?float $lat, ?float $lng): ?Facility
    {
        $query = Facility::query()
            ->where('facility_type', $facilityType)
            ->where('is_active', true)
            ->where('registration_status', 'approved');

        if ($lat && $lng) {
            $query->selectRaw("
                *, 
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance
            ", [$lat, $lng, $lat])
            ->orderBy('distance')
            ->having('distance', '<', 50);
        }

        return $query->first();
    }
}