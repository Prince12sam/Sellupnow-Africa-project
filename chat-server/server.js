const http = require('http');
const { Server } = require('socket.io');
const mysql = require('mysql2/promise');

// ── Config ──────────────────────────────────────────────
const PORT = 3000;
const DB_CONFIG = {
  host: '127.0.0.1',
  user: 'admin',
  password: 'nU7Ak80lRYUO4QkqPfxw',
  database: 'admin',
  waitForConnections: true,
  connectionLimit: 10,
};

// ── DB Pool ─────────────────────────────────────────────
const pool = mysql.createPool(DB_CONFIG);

// ── HTTP + Socket.io ────────────────────────────────────
const server = http.createServer((req, res) => {
  res.writeHead(200, { 'Content-Type': 'application/json' });
  res.end(JSON.stringify({ status: 'ok', service: 'sellupnow-chat' }));
});

const io = new Server(server, {
  cors: { origin: '*', methods: ['GET', 'POST'] },
  transports: ['websocket', 'polling'],
  pingTimeout: 60000,
  pingInterval: 25000,
});

// Track online users: globalRoom userId → Set<socketId>
const onlineUsers = new Map();

// ── Helpers ─────────────────────────────────────────────

/**
 * Find or create a chat_topic between sender/receiver for an ad.
 * Returns the chat_topic id.
 */
async function findOrCreateTopic(senderId, receiverId, adId, productTitle, productImage, productPrice) {
  // Check both directions (sender→receiver OR receiver→sender for same ad)
  const [rows] = await pool.query(
    `SELECT id FROM chat_topics
     WHERE ad_id = ? AND (
       (sender_id = ? AND receiver_id = ?)
       OR (sender_id = ? AND receiver_id = ?)
     ) LIMIT 1`,
    [adId, senderId, receiverId, receiverId, senderId]
  );

  if (rows.length > 0) return rows[0].id;

  // Create new topic
  const [result] = await pool.query(
    `INSERT INTO chat_topics (sender_id, receiver_id, ad_id, chat_type, product_title, product_image, product_price, last_message_time)
     VALUES (?, ?, ?, 1, ?, ?, ?, NOW())`,
    [senderId, receiverId, adId, productTitle || null, productImage || null, productPrice || null]
  );

  return result.insertId;
}

/**
 * Insert a message into chat_topic_messages and update the topic's last_message.
 */
async function storeMessage(topicId, senderId, messageType, message, image, audio, dateStr) {
  const [result] = await pool.query(
    `INSERT INTO chat_topic_messages (chat_topic_id, sender_id, message_type, message, image, audio, date)
     VALUES (?, ?, ?, ?, ?, ?, ?)`,
    [topicId, senderId, messageType || 1, message || null, image || null, audio || null, dateStr || null]
  );

  // Update topic's last message
  await pool.query(
    `UPDATE chat_topics SET last_message = ?, last_message_type = ?, last_message_time = NOW(), updated_at = NOW()
     WHERE id = ?`,
    [message || (image ? '[Image]' : audio ? '[Audio]' : ''), messageType || 1, topicId]
  );

  return result.insertId;
}

/**
 * Mark messages as read for a given topic from a specific sender.
 */
async function markMessagesRead(topicId, senderId) {
  await pool.query(
    `UPDATE chat_topic_messages SET is_read = 1 WHERE chat_topic_id = ? AND sender_id = ? AND is_read = 0`,
    [topicId, senderId]
  );
}

// ── Socket.io Connection ────────────────────────────────
io.on('connection', (socket) => {
  const query = socket.handshake.query || {};
  const globalRoom = query.globalRoom || '';
  // globalRoom format: "globalRoom:<userId>"
  const userId = globalRoom.replace('globalRoom:', '');

  if (userId) {
    socket.join(`user:${userId}`);

    if (!onlineUsers.has(userId)) onlineUsers.set(userId, new Set());
    onlineUsers.get(userId).add(socket.id);

    console.log(`[connect] user=${userId} socket=${socket.id} (${onlineUsers.get(userId).size} connections)`);
  }

  // ── messageSent ───────────────────────────────────────
  socket.on('messageSent', async (data) => {
    try {
      const senderId = data.senderId;
      const receiverId = data.receiverId;
      const adId = data.adId;
      const message = data.message;
      const messageType = data.messageType || 1;
      const image = data.image || null;
      const audio = data.audio || null;
      const dateStr = data.date || null;
      const productTitle = data.title || null;
      const productImage = data.primaryImage || null;
      const productPrice = data.price || null;

      // Find or create conversation topic
      const topicId = await findOrCreateTopic(senderId, receiverId, adId, productTitle, productImage, productPrice);

      // Store the message
      const msgId = await storeMessage(topicId, senderId, messageType, message, image, audio, dateStr);

      // Build response payload  
      const responseData = {
        _id: String(msgId),
        chatTopicId: String(topicId),
        senderId: senderId,
        receiverId: receiverId,
        messageType: messageType,
        message: message,
        image: image,
        audio: audio,
        isRead: false,
        date: dateStr,
        adId: adId,
      };

      const payload = {
        messageId: String(msgId),
        data: responseData,
      };

      // Emit to sender (confirm / replace optimistic)
      io.to(`user:${senderId}`).emit('messageSent', payload);

      // Emit to receiver
      io.to(`user:${receiverId}`).emit('messageSent', payload);

      console.log(`[messageSent] topic=${topicId} msg=${msgId} ${senderId} → ${receiverId}`);
    } catch (err) {
      console.error('[messageSent] error:', err.message);
    }
  });

  // ── messageSeen ───────────────────────────────────────
  socket.on('messageSeen', async (data) => {
    try {
      const messageId = data.messageId;
      const senderId = data.senderId;

      if (messageId) {
        // Get the topic from the message
        const [rows] = await pool.query(
          'SELECT chat_topic_id, sender_id FROM chat_topic_messages WHERE id = ? LIMIT 1',
          [messageId]
        );

        if (rows.length > 0) {
          const topicId = rows[0].chat_topic_id;
          const msgSenderId = rows[0].sender_id;

          // Mark all unread messages from that sender in this topic
          await markMessagesRead(topicId, msgSenderId);

          // Notify the sender their messages were read
          io.to(`user:${msgSenderId}`).emit('messageSeen', {
            chatTopicId: String(topicId),
            messageId: String(messageId),
            seenBy: userId,
          });
        }
      }
      console.log(`[messageSeen] messageId=${messageId}`);
    } catch (err) {
      console.error('[messageSeen] error:', err.message);
    }
  });

  // ── offerPlaced ───────────────────────────────────────
  socket.on('offerPlaced', async (data) => {
    try {
      const senderId = data.senderId;
      const receiverId = data.receiverId;

      // Forward to receiver
      io.to(`user:${receiverId}`).emit('offerReceived', data);

      console.log(`[offerPlaced] ${senderId} → ${receiverId}`);
    } catch (err) {
      console.error('[offerPlaced] error:', err.message);
    }
  });

  // ── disconnect ────────────────────────────────────────
  socket.on('disconnect', (reason) => {
    if (userId && onlineUsers.has(userId)) {
      onlineUsers.get(userId).delete(socket.id);
      if (onlineUsers.get(userId).size === 0) {
        onlineUsers.delete(userId);
      }
    }
    console.log(`[disconnect] user=${userId} socket=${socket.id} reason=${reason}`);
  });
});

// ── Start ───────────────────────────────────────────────
server.listen(PORT, '127.0.0.1', () => {
  console.log(`Chat server listening on 127.0.0.1:${PORT}`);
});
