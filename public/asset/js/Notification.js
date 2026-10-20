window.ProspectFlow.Notification = {
    notifiedStorageKey: 'ProspectFlowNotifiedIds',

    labels: function() {
        const container = document.getElementById('notifications-toast-container');
        const data = container ? container.dataset : {};

        return {
            reminder: data.reminder || 'Reminder',
            newNotification: data.newNotification || 'New notification',
            noUnread: data.noUnread || 'You have no unread notifications',
            close: data.close || 'Close'
        };
    },

    getLatest: function() {
        fetch(window.location.origin + "/ajax/notification", {
            method: 'get',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        }).then(response => response.json())
          .then(data => {
              if (!data || !Array.isArray(data.notifications)) {
                  return;
              }

              this.renderList(data.notifications);
              this.alertNew(data.notifications);
          })
          .catch(error => console.log(error));
    },

    renderList: function(notifications) {
        const ul = document.getElementById("notification-message-list");
        if (!ul) {
            return;
        }

        while (ul.firstChild) {
            ul.removeChild(ul.firstChild);
        }

        if (notifications.length === 0) {
            this.showEmptyList();
            return;
        }

        this.setBadge(true);

        notifications.forEach(notification => {
            const li = document.createElement('li');
            li.className = 'p-2';
            li.dataset.notificationId = notification.id;

            const alert = document.createElement('div');
            alert.className = 'm-0 alert alert-warning alert-dismissible fade show';
            alert.setAttribute('role', 'alert');

            const link = document.createElement('a');
            link.href = notification.link || '#';
            link.className = 'btn m-0 p-0';
            link.textContent = notification.message;

            const close = document.createElement('button');
            close.type = 'button';
            close.className = 'btn-close';
            close.setAttribute('aria-label', this.labels().close);
            close.addEventListener('click', event => {
                event.stopPropagation();
                this.setRead(notification.id);
            });

            alert.append(link, close);
            li.append(alert);
            ul.append(li);
        });
    },

    showEmptyList: function() {
        const ul = document.getElementById("notification-message-list");
        if (!ul) {
            return;
        }

        const li = document.createElement('li');
        li.className = 'p-2';

        const alert = document.createElement('div');
        alert.className = 'm-0 alert alert-warning fade show';
        alert.setAttribute('role', 'alert');
        alert.textContent = this.labels().noUnread;

        li.append(alert);
        ul.append(li);

        this.setBadge(false);
    },

    setBadge: function(hasUnread) {
        const badge = document.getElementById("notification-badge");
        if (badge) {
            badge.classList.toggle('notification-badge', hasUnread);
            badge.classList.toggle('animation-blink', hasUnread);
        }
    },

    /**
     * Play the sound and show the desktop notification or toast only once per notification.
     */
    alertNew: function(notifications) {
        const notifiedIds = this.getNotifiedIds();
        const unreadIds = notifications.map(notification => notification.id);
        const newNotifications = notifications.filter(notification => !notifiedIds.includes(notification.id));

        this.saveNotifiedIds(unreadIds);

        if (newNotifications.length === 0) {
            return;
        }

        this.playSound();

        const permission = ('Notification' in window) ? Notification.permission : 'denied';
        if (permission === 'default') {
            Notification.requestPermission();
        }

        newNotifications.forEach(notification => {
            if (permission === 'granted') {
                this.showDesktopNotification(notification);
            } else {
                this.showToast(notification);
            }
        });
    },

    showDesktopNotification: function(notification) {
        const desktopNotification = new Notification(this.labels().reminder, {
            body: notification.message,
            icon: "/asset/img/notification.png",
            tag: 'prospectflow-notification-' + notification.id
        });

        desktopNotification.onclick = () => {
            this.setRead(notification.id);
            window.focus();
            if (notification.link) {
                window.location.href = notification.link;
            }
            desktopNotification.close();
        };
    },

    getNotifiedIds: function() {
        try {
            return JSON.parse(localStorage.getItem(this.notifiedStorageKey)) || [];
        } catch (error) {
            return [];
        }
    },

    saveNotifiedIds: function(ids) {
        try {
            localStorage.setItem(this.notifiedStorageKey, JSON.stringify(ids));
        } catch (error) {
            // Storage unavailable: notifications will be alerted again on the next fetch
        }
    },

    setRead: async function(id)
    {
        const li = document.querySelector(`#notification-message-list li[data-notification-id="${id}"]`);
        if (li) {
            li.remove();
        }

        const ul = document.getElementById("notification-message-list");
        if (ul && ul.children.length === 0) {
            this.showEmptyList();
        }

        let url = window.location.origin + '/notification/read/' + id;
        await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-type': 'application/json'
            }
        });
    },

    playSound: function() {
        const audioUrl = `${window.location.origin}/asset/sound/button_tiny.mp3`;
        const audio = new Audio(audioUrl);
        audio.play().catch(() => {});
    },

    showToast: function(notification)
    {
        const container = document.getElementById('notifications-toast-container');
        if (!container) {
            return;
        }

        const toast = document.createElement('div');
        toast.id = `toast-${notification.id}`;
        toast.className = 'toast bg-warning';
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        const header = document.createElement('div');
        header.className = 'toast-header';

        const title = document.createElement('strong');
        title.className = 'me-auto';
        title.textContent = this.labels().newNotification;

        const close = document.createElement('button');
        close.type = 'button';
        close.className = 'btn-close';
        close.setAttribute('data-bs-dismiss', 'toast');
        close.setAttribute('aria-label', this.labels().close);
        close.addEventListener('click', () => this.setRead(notification.id));

        const body = document.createElement('div');
        body.className = 'toast-body';
        if (notification.link) {
            const link = document.createElement('a');
            link.href = notification.link;
            link.className = 'text-reset';
            link.textContent = notification.message;
            body.append(link);
        } else {
            body.textContent = notification.message;
        }

        header.append(title, close);
        toast.append(header, body);
        container.append(toast);

        toast.addEventListener('hidden.bs.toast', () => toast.remove());
        bootstrap.Toast.getOrCreateInstance(toast, {autohide: false}).show();
    }
}
