<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * CRIT-02: Restrict Midtrans webhook endpoint to known Midtrans IP ranges.
 * Only enforced in production — all IPs allowed in local/testing.
 *
 * @see https://docs.midtrans.com/docs/ip-address
 */
class MidtransIpWhitelist
{
    /**
     * Midtrans notification server IP ranges.
     */
    private array $allowedCidrs = [
        // Midtrans Production
        '103.208.23.0/24',
        '103.208.23.10/32',
        // Midtrans Sandbox
        '103.127.16.0/23',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Only enforce in production
        if (app()->environment('production')) {
            $clientIp = $request->ip();

            if (!$this->isAllowed($clientIp)) {
                Log::warning('Midtrans webhook rejected: unauthorized IP', [
                    'ip' => $clientIp,
                    'user_agent' => $request->userAgent(),
                ]);

                abort(403, 'Unauthorized IP address.');
            }
        }

        return $next($request);
    }

    /**
     * Check if the given IP is within any of the allowed CIDR ranges.
     */
    private function isAllowed(string $ip): bool
    {
        foreach ($this->allowedCidrs as $cidr) {
            if ($this->ipInCidr($ip, $cidr)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if an IP address is within a CIDR range.
     */
    private function ipInCidr(string $ip, string $cidr): bool
    {
        if (!str_contains($cidr, '/')) {
            return $ip === $cidr;
        }

        [$subnet, $bits] = explode('/', $cidr);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - (int) $bits);

        return ($ip & $mask) === ($subnet & $mask);
    }
}
