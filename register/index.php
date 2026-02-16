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
<html lang="en" class="bg-custom-back-1 text-white overflow-x-hidden">

<head>
    <meta charset="utf-8">
    <link rel="shortcut icon" href="https://cdn.keyauth.cc/global/imgs/Favicon.png" />
    <?php
	echo '
	    <title>KeyAuth - Register to ' . $name . ' Panel</title>
	    <meta name="og:image" content="https://cdn.keyauth.cc/global/imgs/Favicon.png">
        <meta name="description" content="Register to reset your HWID or download ' . $name . '">
        ';
	?>

    <!-- Fonts and Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">

    <!-- Notify Library (toast) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="https://cdn.keyauth.cc/v4/css/oput.css">

    <script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    </script>
</head>

<body>
    <!-- Animated Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-pulse"></div>
        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-gradient-to-r from-blue-500/5 to-blue-500/5 rounded-full blur-3xl animate-spin-slow"></div>
    </div>

    <section class="flex items-center justify-center min-h-screen">
        <div class="bg-custom-back rounded-xl p-4 md:p-10 w-full max-w-lg flex flex-col justify-center mx-2">
            <h1 class="mb-6 text-2xl md:text-3xl text-center font-semibold text-white-900"><?= $name; ?> Customer Panel</h1>
            <h3 class="mb-8 text-sm md:text-lg text-center font-normal text-gray-300">
                Have an account? <a href="../" class="text-blue-500 hover:underline">Sign in</a>
            </h3>
            <form class="mt-8 space-y-6" method="post">
                <div class="relative">
                    <input type="text" name="username"
                        class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " autocomplete="on" required />
                    <label for="username"
                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-custom-back-lbl px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Username</label>
                </div>
                <div class="relative">
                    <input type="password" name="password"
                        class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " autocomplete="on" required />
                    <label for="password"
                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-custom-back-lbl px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Password</label>
                </div>
                <div class="relative">
                    <input type="text" name="license"
                        class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " autocomplete="on" required />
                    <label for="license"
                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-custom-back-lbl px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">License</label>
                </div>
                <button name="register"
                    class="w-full px-5 py-3 text-base font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 sm:w-full">Register
                </button>
            </form>
        </div>
    </section>

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