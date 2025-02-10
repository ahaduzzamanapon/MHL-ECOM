importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');
let config = {
        apiKey: "AIzaSyDQ0IEycUR9VPzts5EZQ-sBGDA1QUVQRo8",
        authDomain: "unicom-c298a.firebaseapp.com",
        projectId: "unicom-c298a",
        storageBucket: "unicom-c298a.firebasestorage.app",
        messagingSenderId: "739041520183",
        appId: "1:739041520183:web:f2edecddda597c2bbd2db2",
        measurementId: "G-6J02FG1ZK3",
 };
firebase.initializeApp(config);
const messaging = firebase.messaging();
messaging.onBackgroundMessage((payload) => {
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: '/images/required/firebase-logo.png'
    };
    self.registration.showNotification(notificationTitle, notificationOptions);
});
