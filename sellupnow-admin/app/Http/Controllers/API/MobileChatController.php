<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MobileChatController extends Controller
{
    /**
     * Get chat list for the logged-in user.
     * Flutter sends: start, limit, chatType (1=buying, 2=selling)
     * Returns: { status, message, chatList: [...] }
     */
    public function getChatList(Request $request)
    {
        $auth = Auth::guard('api')->user();
        if (!$auth) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $userId = $auth->id;
        $start = max(1, (int) $request->input('start', 1));
        $limit = min(50, max(1, (int) $request->input('limit', 20)));
        $chatType = (int) $request->input('chatType', 1);
        $offset = ($start - 1) * $limit;

        // Get topics where the user is either sender or receiver
        $topics = DB::table('chat_topics')
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
            })
            ->orderByDesc('last_message_time')
            ->offset($offset)
            ->limit($limit)
            ->get();

        $chatList = [];

        foreach ($topics as $topic) {
            // Determine the "other" user
            $otherUserId = ($topic->sender_id == $userId)
                ? $topic->receiver_id
                : $topic->sender_id;

            $otherUser = User::find($otherUserId);

            // Count unread messages from the other user
            $unreadCount = DB::table('chat_topic_messages')
                ->where('chat_topic_id', $topic->id)
                ->where('sender_id', $otherUserId)
                ->where('is_read', 0)
                ->count();

            // Determine chat type for this user:
            // If user is the sender (initiated chat), it's "buying" (1)
            // If user is the receiver (received inquiry), it's "selling" (2)
            $topicChatType = ($topic->sender_id == $userId) ? 1 : 2;

            // Filter by chatType if provided
            if ($chatType && $topicChatType !== $chatType) {
                continue;
            }

            $chatList[] = [
                '_id' => (string) $topic->id,
                'receiverId' => (string) $otherUserId,
                'adId' => (string) $topic->ad_id,
                'name' => $otherUser->name ?? 'User',
                'profileImage' => $otherUser->image ?? '',
                'isOnline' => false,
                'chatTopic' => (string) $topic->id,
                'chatType' => $topicChatType,
                'senderId' => (string) $userId,
                'messageType' => $topic->last_message_type ?? 1,
                'message' => $topic->last_message ?? '',
                'lastChatMessageTime' => $topic->last_message_time,
                'productTitle' => $topic->product_title ?? '',
                'productImage' => $topic->product_image ?? '',
                'productPrice' => (float) ($topic->product_price ?? 0),
                'unreadCount' => $unreadCount,
                'time' => $topic->last_message_time,
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'Chat list fetched',
            'chatList' => $chatList,
        ]);
    }

    /**
     * Get chat history (messages) for a conversation.
     * Flutter sends: start, limit, receiverId, adId
     * Returns: { status, message, chatTopic, chat: [...] }
     */
    public function getChatHistory(Request $request)
    {
        $auth = Auth::guard('api')->user();
        if (!$auth) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $userId = $auth->id;
        $receiverId = $request->input('receiverId');
        $adId = $request->input('adId');
        $start = max(1, (int) $request->input('start', 1));
        $limit = min(50, max(1, (int) $request->input('limit', 20)));
        $offset = ($start - 1) * $limit;

        // Find the chat topic
        $topic = DB::table('chat_topics')
            ->where('ad_id', $adId)
            ->where(function ($q) use ($userId, $receiverId) {
                $q->where(function ($q2) use ($userId, $receiverId) {
                    $q2->where('sender_id', $userId)->where('receiver_id', $receiverId);
                })->orWhere(function ($q2) use ($userId, $receiverId) {
                    $q2->where('sender_id', $receiverId)->where('receiver_id', $userId);
                });
            })
            ->first();

        if (!$topic) {
            return response()->json([
                'status' => true,
                'message' => 'No chat history',
                'chatTopic' => null,
                'chat' => [],
            ]);
        }

        // Get messages ordered by newest first
        $messages = DB::table('chat_topic_messages')
            ->where('chat_topic_id', $topic->id)
            ->orderByDesc('id')
            ->offset($offset)
            ->limit($limit)
            ->get();

        $chat = [];
        foreach ($messages as $msg) {
            $chat[] = [
                '_id' => (string) $msg->id,
                'chatTopicId' => (string) $msg->chat_topic_id,
                'senderId' => (string) $msg->sender_id,
                'messageType' => $msg->message_type,
                'message' => $msg->message,
                'image' => $msg->image,
                'audio' => $msg->audio,
                'giftType' => null,
                'giftCount' => null,
                'isRead' => (bool) $msg->is_read,
                'callId' => null,
                'callDuration' => null,
                'date' => $msg->date,
                'createdAt' => $msg->created_at,
                'updatedAt' => $msg->updated_at,
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'Chat history fetched',
            'chatTopic' => (string) $topic->id,
            'chat' => $chat,
        ]);
    }

    /**
     * Send a chat message (image/audio upload via REST).
     * Flutter sends: adId, receiverId, messageType, image/audio file
     * Returns: { status, message, chat: {...} }
     */
    public function sendChatMessage(Request $request)
    {
        $auth = Auth::guard('api')->user();
        if (!$auth) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $userId = $auth->id;
        $receiverId = $request->input('receiverId');
        $adId = $request->input('adId');
        $messageType = (int) $request->input('messageType', 1);

        // Find or create chat topic
        $topic = DB::table('chat_topics')
            ->where('ad_id', $adId)
            ->where(function ($q) use ($userId, $receiverId) {
                $q->where(function ($q2) use ($userId, $receiverId) {
                    $q2->where('sender_id', $userId)->where('receiver_id', $receiverId);
                })->orWhere(function ($q2) use ($userId, $receiverId) {
                    $q2->where('sender_id', $receiverId)->where('receiver_id', $userId);
                });
            })
            ->first();

        if (!$topic) {
            $topicId = DB::table('chat_topics')->insertGetId([
                'sender_id' => $userId,
                'receiver_id' => $receiverId,
                'ad_id' => $adId,
                'chat_type' => 1,
                'last_message_time' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $topicId = $topic->id;
        }

        $imagePath = null;
        $audioPath = null;
        $message = $request->input('message', '');

        // Handle file uploads
        if ($messageType == 2 && $request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/chat/images', $filename);
            $imagePath = '/storage/chat/images/' . $filename;
            $message = $message ?: '[Image]';
        }

        if ($messageType == 3 && $request->hasFile('audio')) {
            $file = $request->file('audio');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/chat/audio', $filename);
            $audioPath = '/storage/chat/audio/' . $filename;
            $message = $message ?: '[Audio]';
        }

        $dateStr = now()->format('n/j/Y, g:i:s A');

        // Insert message
        $msgId = DB::table('chat_topic_messages')->insertGetId([
            'chat_topic_id' => $topicId,
            'sender_id' => $userId,
            'message_type' => $messageType,
            'message' => $message,
            'image' => $imagePath,
            'audio' => $audioPath,
            'date' => $dateStr,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update topic last message
        DB::table('chat_topics')->where('id', $topicId)->update([
            'last_message' => $message,
            'last_message_type' => $messageType,
            'last_message_time' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Message sent',
            'chat' => [
                '_id' => (string) $msgId,
                'chatTopicId' => (string) $topicId,
                'senderId' => (string) $userId,
                'messageType' => $messageType,
                'message' => $message,
                'image' => $imagePath,
                'audio' => $audioPath,
                'isRead' => false,
                'date' => $dateStr,
            ],
        ]);
    }
}
