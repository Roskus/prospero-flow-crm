window.ProspectFlow.Notification = {
    getLatest: function() {
        fetch( window.location.origin + "/ajax/notification", {
            method: 'get',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        }).then(response => response.json())
          .then(data => {
              console.log(data)
              if(data) {
                  let ul = document.getElementById("notification-message-list");
                  //Remove previous messages
                  while(ul.firstChild) {ul.removeChild(ul.firstChild);}

                  if(data.notifications.length > 0)
                  {
                      //Add blinking notification badge
                      document.getElementById("notification-badge").classList.add('notification-badge', 'animation-blink');

                      for (const key in data.notifications) {

                          const template = `
                            <li class="p-2">
                              <div class="m-0 alert alert-warning alert-dismissible fade show" role="alert">
                                <a href="${(data.notifications[key].link) ? data.notifications[key].link : '#'}" class="btn m-0 p-0">
                                    ${data.notifications[key].message}
                                </a>
                                <button onclick="window.ProspectFlow.Notification.setRead(${data.notifications[key].id})" class="btn-close" type="button" aria-label="Close" data-bs-dismiss="alert"></button>
                              </div>
                            </li>
                          `;

                          ul.insertAdjacentHTML('beforeend', template);

                          //Play sound
                          this.playSound();

                          // Show toast
                          let permission = Notification.permission;
                          if (permission === "granted") {
                              let notification = new Notification("Reminder", {
                                  body: data.notifications[key].message,
                                  icon: "ruta-a-icono.png"
                              });
                          }

                          if (permission === "default") {
                              // Request permission
                              Notification.requestPermission().then(function(permission) {});
                          }

                          if (permission !== "granted" ) {
                            this.showToast(data.notifications[key].message);
                          }
                      }

                  }
              }
          })
          .catch(error => console.log(error));
    },
    setRead: async function(id)
    {
        let url = window.location.origin + '/notification/read/'+ id;
        await fetch(url, {
            method: 'GET',
            headers: {
                'Content-type': 'application/json'
            }
        });
    },
    playSound: function() {
        const audioUrl = `${window.location.origin}/asset/sound/button_tiny.mp3`;
        const audio = new Audio(audioUrl);
        audio.play();
    },
    showToast: function(id, message)
    {
        const toastId = `toast-${id}`;

        const template = `
          <div id="${toastId}" class="toast bg-warning" role="alert" aria-live="assertive" aria-atomic="true">
              <div class="toast-header">
                  <strong class="me-auto">New notification</strong>
                  <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
              </div>
              <div class="toast-body">
                  ${message}
              </div>
          </div>
        `;

        const toast = $(`#${toastId}`);
        const notificationsToastContainer = document.getElementById('notifications-toast-container');

        notificationsToastContainer.insertAdjacentHTML('beforeend', template);
        toast.toast('show');
    }
}
