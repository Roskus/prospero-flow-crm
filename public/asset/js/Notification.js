window.ProspectFlow.Notification = {
    getLatest: function() {
        fetch( window.location.origin + "/notification", {
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
                          let li = document.createElement("li");
                          li.classList.add('p-1', 'rounded', 'mb-1', 'bg-blue', 'p-3', 'm-2');

                          let notificationLink = document.createElement('a');
                          let linkText = document.createTextNode(data.notifications[key].message);
                          notificationLink.appendChild(linkText);
                          notificationLink.href = (data.notifications[key].link) ? data.notifications[key].link : '#';
                          notificationLink.classList.add("list-group-item","list-group-item-action");
                          li.appendChild(notificationLink);

                          let notificationReadLink = document.createElement('a');
                          notificationReadLink.onclick = window.ProspectFlow.Notification.setRead(data.notifications[key].id);

                          let notificationIconReadLink = document.createElement('i');
                          notificationReadLink.appendChild(notificationIconReadLink);
                          notificationIconReadLink.classList.add("fa-solid","fa-xmark",'text-white');
                          li.appendChild(notificationReadLink);
                          ul.appendChild(li);

                          //Play sound
                          this.playSound();

                          // Show toast
                          this.showToast(data.notifications[key].message);
                      }

                  }
              }
          })
          .catch(error => console.log(error));
    },
    setRead: async function(id)
    {
        let url = window.location.origin + '/notification/read/'+ id;
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-type': 'application/json'
            }
        });

        // Awaiting response.json()
        const resData = await response.json();
        console.log(response);
    },
    playSound: function() {
        const audioUrl = `${window.location.origin}/asset/sound/button_tiny.mp3`;
        const audio = new Audio(audioUrl);
        audio.play();
    },
    showToast: function(message)
    {
        const liveToast = $("#liveToast");
        let toastBody = document.querySelector('#liveToast .toast-body');
        toastBody.innerHTML = message;
        liveToast.toast('show');
    }
}
