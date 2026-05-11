<?php

namespace App\Http\Controllers\Api;
use App\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        // This pulls all rows from your 'services' table
        $services = Service::all();

        return response()->json([
            'status' => 'success',
            'data' => $services
        ], 200);
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            // Add other columns from your manual table here
        ]);

        $service = Service::create($fields);

        return response()->json([
            'status' => 'service created',
            'service' => $service
        ], 201);
    }
}
