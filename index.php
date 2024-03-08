<?php
require 'credentials.php';
require 'keyauth.php';

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

<!DOCTYPE html>
<html lang="en" class="bg-[#09090d] text-white overflow-x-hidden">

<head>

    <?php
	echo '
	    <title>KeyAuth - Login to ' . $name . ' Panel</title>
	    <meta name="og:image" content="https://cdn.keyauth.cc/front/assets/img/favicon.png">
        <meta name="description" content="Login to reset your HWID or download ' . $name . '">
        ';
	?>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.keyauth.cc/v3/dist/output.css" />

    <script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    </script>
</head>

<body>
    <!--Navbar-->
    <header>
        <nav class="border-gray-200 px-4 lg:px-6 py-2.5 mb-14">
            <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl">
                <a href="../" class="flex items-center">
                    <img src="https://cdn.keyauth.cc/v2/assets/media/logos/logo-1-dark.png" class="mr-3 h-12 mt-2"
                        alt="KeyAuth Logo" />
                </a>
            </div>
        </nav>
    </header>

    <section>
        <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 grid lg:grid-cols-2 gap-8 lg:gap-16">
            <div class="flex flex-col justify-center">
                <h1
                    class="mb-4 text-4xl font-extrabold tracking-tight leading-none text-white-900 md:text-5xl lg:text-6xl">
                    <?= $name; ?> Customer Panel</h1>
                <p class="mb-6 text-lg font-normal text-gray-500 lg:text-xl">To access <a
                        class="text-blue-600"><?= $name; ?> customer panel</a>,
                    you must login, or register. This information is provided to you by the owner of the application. Do
                    not contact KeyAuth support if
                    you can not access the customer panel.
                </p>
            </div>
            <div>
                <div class="w-full lg:max-w-xl p-6 space-y-8 sm:p-8 bg-[#09090d] rounded-lg shadow-xl">
                    <form class="mt-8 space-y-6" method="post">
                        <div class="relative">
                            <input type="text" name="username"
                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                placeholder=" " autocomplete="on" required />
                            <label for="username"
                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#09090d] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Username</label>
                        </div>
                        <div class="relative">
                            <input type="password" name="password"
                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                placeholder=" " autocomplete="on" required />
                            <label for="password"
                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#09090d] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Password</label>
                        </div>
                        <button name="login"
                            class="w-full px-5 py-3 text-base font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 sm:w-full">Login
                            To <?= $name; ?> Customer Panel</button>
                        <div class="text-sm font-medium text-white-900">
                            New to the panel? <a href="../register/" class="text-blue-600 hover:underline">Register
                                Now</a>.
                        </div>
                        <div class="text-sm font-medium text-white-900">
                            Need to to upgrade your account? <a href="../upgrade/"
                                class="text-blue-600 hover:underline">Upgrade
                                Now</a>.
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script src="https://cdn.keyauth.cc/v3/dist/flowbite.js"></script>

    <?php
	if (isset($_POST['login'])) {
		if ($KeyAuthApp->login($_POST['username'], $_POST['password'])) {
			$_SESSION['un'] = $_POST['username'];
			echo "<meta http-equiv='Refresh' Content='2; url=../dashboard/'>";
			echo '
                        <script type=\'text/javascript\'>
                        
                        const notyf = new Notyf();
                        notyf
                          .success({
                            message: \'You have successfully logged in!\',
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