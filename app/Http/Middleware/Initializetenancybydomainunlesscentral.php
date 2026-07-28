<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;

/**
 * Livewire registers a single, shared '/livewire/update' route that both
 * central-domain components (CentralCommunityAlertFeed) and tenant-domain
 * components (CommunityAlertFeed) post back to, using whatever host the
 * page was actually loaded on.
 *
 * InitializeTenancyByDomain has no concept of "central domains" — it
 * just looks the host up in the tenant `domains` table and throws
 * TenantCouldNotBeIdentifiedOnDomainException if nothing matches.
 * That's correct for tenant subdomains, but wrong for the central
 * domain, which will never be in that table.
 *
 * This middleware checks config('tenancy.central_domains') first:
 *   - central domain  -> skip tenant resolution entirely, pass through
 *   - anything else   -> defer to the real InitializeTenancyByDomain,
 *                        preserving its normal throw-on-failure behavior
 */
class InitializeTenancyByDomainUnlessCentral
{
    /**
     * Composition instead of inheritance: Laravel's container resolves
     * this constructor dependency automatically when the middleware is
     * invoked, so we don't need to extend InitializeTenancyByDomain
     * (and don't need to match its exact handle() signature, which can
     * vary across stancl/tenancy versions).
     */
    public function __construct(
        protected InitializeTenancyByDomain $inner
    ) {}

    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();

        if (in_array($host, config('tenancy.central_domains', []), true)) {
            return $next($request);
        }

        return $this->inner->handle($request, $next);
    }
}