<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $services = Service::query()
            ->when($search !== '', function ($query) use ($search) {
                // Karakter wildcard dari input dianggap teks biasa.
                $term = '%'.addcslashes($search, '%_\\').'%';

                $query->where(fn ($q) => $q
                    ->where('title', 'like', $term)
                    ->orWhere('summary', 'like', $term));
            })
            ->orderBy('id')
            ->get();

        return view('admin.services.index', compact('services', 'search'));
    }

    public function create(): View
    {
        return view('admin.services.create');
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['title', 'summary', 'detail', 'status']);
        $data['slug'] = Service::uniqueSlug($data['title']);
        $data['image'] = $request->file('image')->store('services', 'public');

        Service::create($data);

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit(Service $service): View
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        $data = $request->safe()->only(['title', 'summary', 'detail', 'status']);

        if ($request->hasFile('image')) {
            $service->deleteImageFile();
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        // Slug sengaja tidak diubah agar tautan ke halaman detail tetap valid.
        $service->update($data);

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->deleteImageFile();
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil dihapus.');
    }
}
