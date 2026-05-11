<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;

class ServiceController extends Controller
{
    /**
     * Display all services.
     */
    public function index()
    {
        $services = Service::latest()->get();

        // Non-admins see read-only view
        if ($this->isAdmin()) {
            return view('index.children_views.services', compact('services'));
        }

        return view('index.children_views.serv_view_only', compact('services'));
    }

    /**
     * Store a new service (Admin+ only).
     */
    public function store(StoreServiceRequest $request)
    {
        Service::create($request->validated());

        return redirect()->back()->with('success', 'Service added successfully!');
    }

    /**
     * Update a service (Admin+ only).
     */
    public function update(UpdateServiceRequest $request, $id)
    {
        $service = Service::findOrFail($id);
        $service->update($request->validated());

        return redirect()->back()->with('success', 'Service updated successfully!');
    }

    /**
     * Delete a service (Admin+ only).
     */
    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return redirect()->back()->with('success', 'Service deleted successfully!');
    }
}
