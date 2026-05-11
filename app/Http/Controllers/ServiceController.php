<?php

namespace App\Http\Controllers;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ServiceController extends Controller
{
    public function index()
    {
        $auth = Auth::user();

        if (($auth->level === 'developer') || ($auth->level === 'admin')) {
            // Full feature for dev and admin
            $services = Service::all();
            return view('index.children_views.services', compact('services'));
        } else {
            // Admins see only their own app_issue reports
            $services = Service::all();
            return view('index.children_views.serv_view_only', compact('services'));
        }
        
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
        ]);

        Service::create($request->all());
        return redirect()->back()->with('success', 'Service added successfully!');
    }

    public function destroy($id)
    {
        Service::findOrFail($id)->delete();
        return redirect()->back()->with('danger', 'Service deleted!');
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        $service->update($request->all());
        return redirect()->back()->with('success', 'Service updated!'); 
    }
}
