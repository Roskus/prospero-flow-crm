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
                  for (const key in data.notifications) {
                      let li = document.createElement("li");
                      li.classList.add('p-1')
                      let link = document.createElement('a');
                      let linkText = document.createTextNode(data.notifications[key].message);
                      link.appendChild(linkText);
                      link.href = (data.notifications[key].link) ? data.notifications[key].link : '#';
                      link.classList.add("list-group-item","list-group-item-action","rounded","mb-1","p-3","bg-blue");
                      li.appendChild(link);
                      ul.appendChild(li);
                  }
              }
          })
          .catch(error => console.log(error));
    }
}
