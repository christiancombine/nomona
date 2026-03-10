setInterval(() => {
  navigator.sendBeacon("/UserAPI/Presence.ashx?action=heartbeat");
}, 30000);
