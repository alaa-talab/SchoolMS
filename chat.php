<!DOCTYPE html>
<html>
<head>
  <title>Chat</title>
  <style>
    /* Your CSS styles for the chat interface */
  </style>
</head>
<body>
  <h2>Chat</h2>

  <div id="messages"></div>
  <form id="message-form">
    <input type="text" id="message-input" placeholder="Type a message...">
    <button type="submit">Send</button>
  </form>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      // Initialize the AjaxPush object
      var push = new AjaxPush('listener.php', 'sender.php');

      // Connect to the chat and start receiving messages
      push.connect(function(data) {
        // Handle received data
        $('#messages').append('<p>' + data.message + '</p>');
      });

      // Handle the form submission
      $('#message-form').submit(function(e) {
        e.preventDefault();
        var message = $('#message-input').val();

        // Send the message via AjaxPush
        push.doRequest({ message: message }, function() {
          // Handle success
          console.log('Message sent successfully');
        });

        $('#message-input').val('');
      });
    });

    // AJAX Push mechanism
    var AjaxPush = function(listener, sender) {
      this.listener = listener || '';
      this.sender = sender || '';
      this.state = false;
      this.timestamp = 0;
    };

    AjaxPush.prototype = {
      connect: function(callback) {
        var that = this;
        var status = false;

        $.ajax({
          url: this.listener,
          dataType: 'json',
          data: { timestamp: this.timestamp },
          success: function(data) {
            if (!that.state)
              console.info('Connected!');

            status = true;
            that.state = true;
            that.timestamp = data.timestamp;
            callback(data);
          },
          complete: function(data) {
            if (!status) {
              console.info('The connection has been lost! Trying to reconnect...');
              setTimeout(function() {
                that.connect(callback);
              }, 1000);
            } else {
              that.connect(callback);
            }

            that.state = data.status == 200 ? true : false;
          }
        });
      },

      doRequest: function(data, callback) {
        $.ajax({
          url: this.sender,
          data: data,
          success: function() {
            callback();
          }
        });
      }
    };
  </script>
</body>
</html>
