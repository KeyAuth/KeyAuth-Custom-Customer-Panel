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
	<title>Register</title>
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
						Register
					</span>


					<div class="wrap-input100 validate-input m-b-16" data-validate="Username is required">
						<input class="input100" type="text" name="username" placeholder="Username">
						<span class="focus-input100"></span>
					</div>


					<div class="wrap-input100 validate-input m-b-16" data-validate="Password is required">
						<input class="input100" type="password" name="password" placeholder="Password">
						<span class="focus-input100"></span>
					</div>

					<div class="wrap-input100 validate-input m-b-16" data-validate="License is required">
						<input class="input100" type="text" name="license" placeholder="License">
						<span class="focus-input100"></span>
					</div>

					<div class="container-login100-form-btn m-t-17">
						<button name="register" class="login100-form-btn">
							Register
						</button>
					</div>

				</form>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

	<?php
	if (isset($_POST['register'])) {
		if ($KeyAuthApp->register($_POST['username'], $_POST['password'], $_POST['license'])) {
			$_SESSION['un'] = $_POST['username'];
			echo "<meta http-equiv='Refresh' Content='2; url=../dashboard/'>";
			echo '
                        <script type=\'text/javascript\'>
                        
                        const notyf = new Notyf();
                        notyf
                          .success({
                            message: \'You have successfully registered!\',
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