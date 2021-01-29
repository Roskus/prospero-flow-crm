window.Hammer = {

    exit: function(exit_text) {
        response = confirm(exit_text);
        debugger;
        if(response) document.getElementById('logout-form').submit();
    }
}

