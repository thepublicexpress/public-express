// ============================================================
// 🔥 FIREBASE SERVICE WORKER (Mobile + Desktop)
// ============================================================

importScripts('https://www.gstatic.com/firebasejs/12.16.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/12.16.0/firebase-messaging-compat.js');

const firebaseConfig = {
    apiKey: "AIzaSyAnKz3f7FCITWYU8zXIYXaBO2Y82i9BK9Q",
    authDomain: "the-public-express-bb2f9.firebaseapp.com",
    projectId: "the-public-express-bb2f9",
    storageBucket: "the-public-express-bb2f9.firebasestorage.app",
    messagingSenderId: "379587060266",
    appId: "1:379587060266:web:d2a0107fc1a39e7b5e9953",
    measurementId: "G-DCV84SRRDV"
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// ============================================================
// ✅ BACKGROUND MESSAGE HANDLER (Mobile & Desktop)
// ============================================================

messaging.onBackgroundMessage(function(payload) {
    console.log('📨 Background message received:', payload);
    
    const notificationTitle = payload.notification?.title || 'द पब्लिक एक्सप्रेस';
    const notificationOptions = {
        body: payload.notification?.body || 'ताजा खबर!',
        icon: '/images/logo.png',
        badge: '/images/badge.png',
        data: payload.data || {},
        vibrate: [200, 100, 200],
        requireInteraction: true,
        actions: [
            { action: 'open', title: '📖 पढ़ें' },
            { action: 'close', title: '❌ बंद करें' }
        ]
    };

    // ✅ For mobile - Add sound
    if (payload.data?.sound) {
        notificationOptions.sound = payload.data.sound;
    }

    return self.registration.showNotification(notificationTitle, notificationOptions);
});

// ============================================================
// ✅ NOTIFICATION CLICK HANDLER
// ============================================================

self.addEventListener('notificationclick', function(event) {
    console.log('🔔 Notification clicked:', event);
    event.notification.close();

    const url = event.notification.data?.click_action || '/';
    const action = event.action;

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(function(clientList) {
                // If "open" action or no action, open the URL
                if (action === 'open' || !action) {
                    // Check if any client is already open
                    for (let client of clientList) {
                        if (client.url === url && 'focus' in client) {
                            return client.focus();
                        }
                    }
                    // Open new window if no existing client
                    if (clients.openWindow) {
                        return clients.openWindow(url);
                    }
                }
            })
    );
});

// ============================================================
// ✅ PUSH EVENT HANDLER (Fallback)
// ============================================================

self.addEventListener('push', function(event) {
    console.log('📨 Push event received:', event);
    
    let data = {};
    try {
        data = event.data.json();
    } catch (e) {
        try {
            data = JSON.parse(event.data.text());
        } catch (e2) {
            data = { notification: { title: 'द पब्लिक एक्सप्रेस', body: 'ताजा खबर!' } };
        }
    }

    const options = {
        body: data.notification?.body || 'ताजा खबर!',
        icon: '/images/logo.png',
        badge: '/images/badge.png',
        data: data.data || {},
        vibrate: [200, 100, 200],
        requireInteraction: true,
        actions: [
            { action: 'open', title: '📖 पढ़ें' },
            { action: 'close', title: '❌ बंद करें' }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(
            data.notification?.title || 'द पब्लिक एक्सप्रेस',
            options
        )
    );
});

console.log('✅ Firebase Service Worker loaded (Mobile + Desktop)');