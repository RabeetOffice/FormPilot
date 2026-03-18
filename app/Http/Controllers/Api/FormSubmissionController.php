<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Services\FormIngestionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FormSubmissionController extends Controller
{
    public function __construct(protected FormIngestionService $ingestionService) {}

    /**
     * Direct POST endpoint: POST /api/f/{api_key}
     */
    public function submit(Request $request, string $apiKey): JsonResponse
    {
        $domain = Domain::where('api_key', $apiKey)->where('is_active', true)->first();

        if (!$domain) {
            return response()->json(['error' => 'Invalid API key'], 403);
        }

        // Origin validation
        if (!$this->validateOrigin($request, $domain)) {
            return response()->json(['error' => 'Origin not allowed'], 403);
        }

        $payload = $request->all();
        $submission = $this->ingestionService->process($domain, $payload, $request);

        return response()->json([
            'success' => true,
            'message' => 'Form submitted successfully',
            'id' => $submission->id,
        ], 201);
    }

    /**
     * Webhook endpoint: POST /api/webhook/{api_key}
     */
    public function webhook(Request $request, string $apiKey): JsonResponse
    {
        $domain = Domain::where('api_key', $apiKey)->where('is_active', true)->first();

        if (!$domain) {
            return response()->json(['error' => 'Invalid API key'], 403);
        }

        // Webhooks can have JSON or form-encoded data
        $payload = $request->isJson() ? $request->json()->all() : $request->all();

        $submission = $this->ingestionService->process($domain, $payload, $request);

        return response()->json([
            'success' => true,
            'message' => 'Webhook received',
            'id' => $submission->id,
        ], 201);
    }

    /**
     * Test endpoint for the setup page
     */
    public function test(Request $request, string $apiKey): JsonResponse
    {
        $domain = Domain::where('api_key', $apiKey)->where('is_active', true)->first();

        if (!$domain) {
            return response()->json(['error' => 'Invalid API key', 'valid' => false], 403);
        }

        return response()->json([
            'valid' => true,
            'domain' => $domain->domain,
            'brand' => $domain->brand->name,
        ]);
    }

    protected function validateOrigin(Request $request, Domain $domain): bool
    {
        $origin = $request->header('Origin') ?? $request->header('Referer');

        // No origin restriction if allowed_origins is empty
        if (empty($domain->allowed_origins)) {
            return true;
        }

        if (!$origin) {
            return true; // Allow server-side submissions
        }

        $originHost = parse_url($origin, PHP_URL_HOST);

        foreach ($domain->allowed_origins as $allowed) {
            $allowedHost = parse_url($allowed, PHP_URL_HOST) ?? $allowed;
            if ($originHost === $allowedHost || str_ends_with($originHost, '.' . $allowedHost)) {
                return true;
            }
        }

        return false;
    }
}
