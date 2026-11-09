<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->query('filter') === 'belum-dibaca' ? 'belum-dibaca' : 'semua';

        return view('admin.messages.index', [
            'filter' => $filter,
            'unreadCount' => ContactMessage::unread()->count(),
            'messages' => ContactMessage::query()
                ->when($filter === 'belum-dibaca', fn ($query) => $query->unread())
                ->latest('id')
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    /** Membuka pesan menandainya sudah dibaca. */
    public function show(ContactMessage $message): View
    {
        $wasUnread = ! $message->isRead();
        $message->markAsRead();

        return view('admin.messages.show', [
            'message' => $message,
            'wasUnread' => $wasUnread,
            'newer' => ContactMessage::where('id', '>', $message->id)->orderBy('id')->first(['id']),
            'older' => ContactMessage::where('id', '<', $message->id)->orderByDesc('id')->first(['id']),
        ]);
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        Activity::record("menghapus pesan masuk dari {$message->name}");

        return redirect()->route('admin.messages.index')->with('success', 'Pesan berhasil dihapus.');
    }

    /** Menghapus beberapa pesan sekaligus dari kotak centang di daftar. */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['integer', 'distinct'],
            'filter' => ['nullable', 'in:belum-dibaca'],
        ], [
            'ids.required' => 'Pilih minimal satu pesan untuk dihapus.',
            'ids.min' => 'Pilih minimal satu pesan untuk dihapus.',
            'ids.max' => 'Terlalu banyak pesan dipilih sekaligus (maksimal 100).',
        ]);

        $deleted = ContactMessage::whereIn('id', $data['ids'])->delete();

        if ($deleted > 0) {
            Activity::record("menghapus {$deleted} pesan masuk sekaligus");
        }

        return redirect()
            ->route('admin.messages.index', array_filter(['filter' => $data['filter'] ?? null]))
            ->with('success', "{$deleted} pesan berhasil dihapus.");
    }
}
