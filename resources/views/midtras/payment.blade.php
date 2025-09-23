<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">

    <script type="text/javascript"
      src="https://app.sandbox.midtrans.com/snap/snap.js"
      data-client-key="{{ $data['midtrans_secret'] }}"></script>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  </head>

  <body>

    <form action="{{ route($data['fallback_url'],$data) }}" id="submit_form" method="POST">
        @csrf
        <input type="hidden" name="json" id="json_callback">
    </form>

    <script type="text/javascript">

        window.snap.pay('{{$data['snap_token']}}', {
          onSuccess: function(result){
            console.log(result);
            send_response_to_form(result);
          },
          onPending: function(result){
            console.log(result);
            send_response_to_form(result);
          },
          onError: function(result){
            console.log(result);
            send_response_to_form(result);
          },
          onClose: function(){
            alert('you closed the popup without finishing the payment');
          }
        })

      function send_response_to_form(result){
        document.getElementById('json_callback').value = JSON.stringify(result);
        $('#submit_form').submit();
      }
    </script>
  </body>
</html>
