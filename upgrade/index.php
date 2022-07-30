<?php
require '../credentials.php';
require '../keyauth.php';

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

if (isset($_SESSION['un'])) {
	header("Location: ../dashboard/");
	exit();
}

$KeyAuthApp = new KeyAuth\api($name, $OwnerId);

if (!isset($_SESSION['sessionid'])) {
	$KeyAuthApp->init();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>Upgrade</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" href="https://cdn.keyauth.uk/assets/img/favicon.png" type="image/x-icon">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.keyauth.uk/auth/css/util.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.keyauth.uk/auth/css/main.css">
	<meta name="robots" content="nosnippet, nofollow, noindex" />
</head>

<body>
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100 p-t-50 p-b-90">
				<form class="login100-form validate-form flex-sb flex-w" method="post">
					<span class="login100-form-title p-b-51">
						Upgrade
					</span>


					<div class="wrap-input100 validate-input m-b-16" data-validate="Username is required">
						<input class="input100" type="text" name="username" placeholder="Username">
						<span class="focus-input100"></span>
					</div>

					<div class="wrap-input100 validate-input m-b-16" data-validate="License is required">
						<input class="input100" type="text" name="license" placeholder="License">
						<span class="focus-input100"></span>
					</div>

					<div class="container-login100-form-btn m-t-17">
						<button name="upgrade" class="login100-form-btn">
							Upgrade
						</button>
					</div>

				</form>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

	<?php
	if (isset($_POST['upgrade'])) {
		if ($KeyAuthApp->upgrade($_POST['username'], $_POST['license'])) {
			// don't login, upgrade function is not for authentication, it's simply for redeeming keys
			echo '
                        <script type=\'text/javascript\'>
                        
                        const notyf = new Notyf();
                        notyf
                          .success({
                            message: \'Upgraded Successfully! Now login please.\',
                            duration: 3500,
                            dismissible: true
                          });                
                        
                        </script>
                        ';
		}
	}
	?>
</body>

</html>