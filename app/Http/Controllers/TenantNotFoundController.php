<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TenantNotFoundController extends Controller
{
    /**
     * Show the tenant not found page.
     */
    public function show(Request $request)
    {
        $requestedDomain = $request->getHost();
        $subdomain = $this->extractSubdomain($requestedDomain);

        return view('errors.tenant-not-found', [
            'requestedDomain' => $requestedDomain,
            'requestedSubdomain' => $subdomain,
        ]);
    }

    /**
     * Extract subdomain from full domain.
     */
    private function extractSubdomain(string $domain): ?string
    {
        $parts = explode('.', $domain);

        // For localhost domains like "tenant.localhost"
        if (count($parts) >= 2 && $parts[count($parts) - 1] === 'localhost') {
            return $parts[0] !== 'localhost' ? $parts[0] : null;
        }

        // For other domains, assume first part is subdomain
        return count($parts) > 2 ? $parts[0] : null;
    }
}
