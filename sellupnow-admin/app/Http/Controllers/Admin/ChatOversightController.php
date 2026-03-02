<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopUser;
use App\Models\ShopUserChats;
use Illuminate\Http\Request;

class ChatOversightController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $threads = ShopUser::query()
            ->with([
                'user:id,name,phone,email',
                'shop:id,name',
                'latestMessage',
            ])
            ->withCount([
                'shopUserChats as unread_messages_count' => fn ($query) => $query->where('is_seen', 0),
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%'.$search.'%')
                            ->orWhere('phone', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%');
                    })->orWhereHas('shop', function ($shopQuery) use ($search) {
                        $shopQuery->where('name', 'like', '%'.$search.'%');
                    });
                });
            })
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.chat-oversight.index', compact('threads', 'search'));
    }

    public function show(ShopUser $shopUser)
    {
        $shopUser->load([
            'user:id,name,phone,email',
            'shop:id,name',
        ]);

        $messages = ShopUserChats::query()
            ->where('shop_user_id', $shopUser->id)
            ->with(['user:id,name', 'shop:id,name'])
            ->latest('id')
            ->paginate(50);

        return view('admin.chat-oversight.show', compact('shopUser', 'messages'));
    }

    public function markSeen(ShopUser $shopUser)
    {
        ShopUserChats::query()
            ->where('shop_user_id', $shopUser->id)
            ->where('is_seen', 0)
            ->update(['is_seen' => 1]);

        return back()->withSuccess(__('Conversation marked as seen'));
    }
}
