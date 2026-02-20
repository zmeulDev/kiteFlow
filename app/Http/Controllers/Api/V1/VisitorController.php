<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use App\Models\Tenant;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = $tenant->visitors();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'data' => $query->get()
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenant $tenant, Visitor $visitor)
    {
        if ($visitor->tenant_id !== $tenant->id) {
            abort(403);
        }

        return response()->json(['data' => $visitor]);
    }
}
