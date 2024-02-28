<html>
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <script src="https://accounts.google.com/gsi/client" async></script>
      <link rel="stylesheet" href="css/login.css">
      <title>Google Login API</title>
      <script>
          function decodeJwtResponse (token) {
              var base64Url = token.split('.')[1];
              var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
              var jsonPayload = decodeURIComponent(window.atob(base64).split('').map(function(c) {
              return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
              }).join(''));
              return JSON.parse(jsonPayload);
          }
          function handleCredentialResponse(response) {
              const responsePayload = decodeJwtResponse(response.credential);
        
              fetch('login_process.php', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                  },
                  body: JSON.stringify(responsePayload),
              })
              .then(() => {
                  window.location.href = 'view_recipe.php';
              })
              .catch(error => {
                  console.error('Error:', error);
              });
          }
      </script>
  </head>
  <body>
    <div class="container">
      <div class = "img_container">
        <img src="image/21892101383.png">
      </div>
      <div class = "login_container">
        <h1> GreenGrocer </h1>
        <div id="g_id_onload"
            data-client_id="178800428439-qnrr2117sqsl1ko87og00ldsnk4sgs9j.apps.googleusercontent.com"
            data-context="signin"
            data-ux_mode="popup"
            data-callback="handleCredentialResponse"
            data-auto_prompt="false">
        </div>

        <div class="g_id_signin"
            data-type="standard"
            data-shape="rectangular"
            data-theme="outline"
            data-text="signin_with"
            data-size="large"
            data-logo_alignment="left">
        </div>
      </div>
    </div>
  </body>
</html>