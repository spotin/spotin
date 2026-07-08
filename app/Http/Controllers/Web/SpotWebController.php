<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Spot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SpotWebController extends Controller
{
    public function index(Request $request): View
    {
        $spots = $request->user()->spots()->latest()->get();

        return view('spots.index', compact('spots'));
    }

    public function create(): View
    {
        return view('spots.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric', 'min:-90', 'max:90'],
            'longitude' => ['nullable', 'numeric', 'min:-180', 'max:180'],
            'website_target' => ['nullable', 'url', 'max:255'],
            'direct_access_to_website_target' => ['boolean'],
            'configured' => ['boolean'],
            'public' => ['boolean'],
            'payload' => ['nullable', 'json'],
        ]);

        $spot = $request->user()->spots()->create($validated);

        return redirect()->route('spots.show', $spot)->with('success', 'Spot créé.');
    }

    public function show(Spot $spot): View
    {
        $this->authorize('view', $spot);

        return view('spots.show', compact('spot'));
    }

    public function edit(Spot $spot): View
    {
        $this->authorize('update', $spot);

        return view('spots.edit', compact('spot'));
    }

    public function update(Request $request, Spot $spot): RedirectResponse
    {
        $this->authorize('update', $spot);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric', 'min:-90', 'max:90'],
            'longitude' => ['nullable', 'numeric', 'min:-180', 'max:180'],
            'website_target' => ['nullable', 'url', 'max:255'],
            'direct_access_to_website_target' => ['boolean'],
            'configured' => ['boolean'],
            'public' => ['boolean'],
            'payload' => ['nullable', 'json'],
        ]);

        $spot->update($validated);

        return redirect()->route('spots.show', $spot)->with('success', 'Spot mis à jour.');
    }

    public function destroy(Spot $spot): RedirectResponse
    {
        $this->authorize('delete', $spot);

        $spot->delete();

        return redirect()->route('spots.index')->with('success', 'Spot supprimé.');
    }
}
