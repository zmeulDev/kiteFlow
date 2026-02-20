<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $visitors = Visitor::query()
            ->with('tenant')
            ->when($request->tenant_id, fn($q, $id) => $q->where('tenant_id', $id))
            ->when($request->search, fn($q, $search) => 
                $q->where(function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%");
                })
            )
            ->orderBy('last_visit_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json($visitors);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tenant_id' => 'nullable|exists:tenants,id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
        ]);

        // Check if visitor exists by email or phone
        $visitor = Visitor::query()
            ->when($validated['email'] ?? null, fn($q, $email) => 
                $q->orWhere('email', $email)
            )
            ->when($validated['phone'] ?? null, fn($q, $phone) => 
                $q->orWhere('phone', $phone)
            )
            ->first();

        if ($visitor) {
            // Update existing visitor
            $visitor->update($validated);
            $message = 'Visitor found and updated';
        } else {
            // Create new visitor
            $visitor = Visitor::create($validated);
            $message = 'Visitor created successfully';
        }

        return response()->json([
            'message' => $message,
            'visitor' => $visitor->load('tenant'),
        ], 201);
    }

    public function show(Visitor $visitor): JsonResponse
    {
        return response()->json($visitor->load(['tenant', 'visits', 'checkIns']));
    }

    public function update(Request $request, Visitor $visitor): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'agreed_to_nda' => 'nullable|boolean',
            'agreed_to_terms' => 'nullable|boolean',
        ]);

        $visitor->update($validated);

        return response()->json([
            'message' => 'Visitor updated successfully',
            'visitor' => $visitor->fresh(),
        ]);
    }

    public function destroy(Visitor $visitor): JsonResponse
    {
        $visitor->delete();

        return response()->json([
            'message' => 'Visitor deleted successfully',
        ]);
    }

    /**
     * Quick lookup by email or phone for returning visitors
     */
    public function lookup(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
        ]);

        if (!$request->email && !$request->phone) {
            return response()->json([
                'message' => 'Email or phone required',
            ], 422);
        }

        $visitor = Visitor::query()
            ->when($request->email, fn($q, $email) => 
                $q->orWhere('email', $email)
            )
            ->when($request->phone, fn($q, $phone) => 
                $q->orWhere('phone', $phone)
            )
            ->first();

        if (!$visitor) {
            return response()->json([
                'message' => 'Visitor not found',
                'found' => false,
            ], 404);
        }

        return response()->json([
            'found' => true,
            'visitor' => $visitor->load('tenant'),
        ]);
    }

    /**
     * Record visitor signature
     */
    public function sign(Request $request, Visitor $visitor): JsonResponse
    {
        $request->validate([
            'signature' => 'required|string', // Base64
            'agree_nda' => 'nullable|boolean',
            'agree_terms' => 'nullable|boolean',
        ]);

        // Store signature
        $signatureData = base64_decode($request->signature);
        $filename = "signatures/{$visitor->id}_" . time() . '.png';
        \Storage::disk('public')->put($filename, $signatureData);

        $visitor->update([
            'signature_path' => $filename,
            'signature_signed_at' => now(),
            'agreed_to_nda' => $request->boolean('agree_nda', $visitor->agreed_to_nda),
            'agreed_to_terms' => $request->boolean('agree_terms', $visitor->agreed_to_terms),
        ]);

        return response()->json([
            'message' => 'Signature recorded successfully',
            'visitor' => $visitor->fresh(),
        ]);
    }
}
