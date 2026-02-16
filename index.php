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
<html lang="en" class="bg-custom-back-1 text-white overflow-x-hidden">

<head>
    <link rel="shortcut icon" href="https://cdn.keyauth.cc/global/imgs/Favicon.png" />
    <?php
    echo '
	    <title>KeyAuth - Login to ' . $name . ' Panel</title>
	    <meta name="og:image" content="https://cdn.keyauth.cc/global/imgs/Favicon.png">
        <meta name="description" content="Login to reset your HWID or download ' . $name . '">
        ';
    ?>

    <!-- Fonts and icons -->
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
                New to the panel? <a href="register/" class="text-blue-500 hover:underline">Sign up</a>
            </h3>

            <!-- TAB BUTTONS: Segmented pill control -->
            <div class="mb-4 flex justify-center">
                <div id="tabs-segmented" class="relative inline-flex rounded-2xl p-1 border border-white/10 bg-custom-back/90 gap-1 shadow-[inset_0_1px_0_rgba(255,255,255,0.06),0_2px_10px_rgba(0,0,0,0.25)]">
                    <span id="tabs-slider" class="absolute top-1 bottom-1 left-0 rounded-xl bg-gray-600/30 ring-1 ring-black/40 shadow-[0_1px_1px_rgba(0,0,0,0.35)] pointer-events-none"></span>
                    <button id="tab-user" data-tab="user"
                        class="relative z-10 px-7 py-2 rounded-xl font-semibold text-white transition-colors"
                        type="button" aria-selected="true">
                        User/Pass
                    </button>
                    <button id="tab-license" data-tab="license"
                        class="relative z-10 px-7 py-2 rounded-xl font-semibold text-white/70 hover:text-white transition-colors"
                        type="button" aria-selected="false">
                        License
                    </button>
                </div>
            </div>
            <form class="mt-8 space-y-6" method="post">
                <div id="user-content">
                    <div class="relative">
                        <input type="text" name="username"
                            class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                            placeholder=" " autocomplete="on" />
                        <label for="username"
                            class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-custom-back-lbl px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Username</label>
                    </div>
                    <div class="relative mt-6">
                        <input type="password" name="password"
                            class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                            placeholder=" " autocomplete="on" />
                        <label for="password"
                            class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-custom-back-lbl px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Password</label>
                    </div>

                    <div class="relative mt-6">
                        <input type="text" name="tfa"
                            class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                            placeholder=" " autocomplete="on" />
                        <label for="tfa"
                            class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-custom-back-lbl px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">2FA (Two Factor Authentication)</label>
                    </div>

                    <button name="login"
                        class="cursor-pointer mt-6 w-full px-5 py-3 text-base font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 sm:w-full">Log in
                        to <?= $name; ?> Customer Panel</button>
                </div>

                <div id="license-content" class="hidden">
                    <p class="mb-6 text-sm text-gray-500">
                        Licenses must be <b>registered</b> on an application before they can be used to log in.
                    </p>

                    <div class="relative">
                        <input type="text" name="license"
                            class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                            placeholder=" " autocomplete="on" />
                        <label for="license"
                            class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-custom-back-lbl px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">License (if using license only)</label>
                    </div>

                    <div class="relative mt-6">
                        <input type="text" name="tfa"
                            class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                            placeholder=" " autocomplete="on" />
                        <label for="tfa"
                            class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-custom-back-lbl px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">2FA (Two Factor Authentication)</label>
                    </div>

                    <button name="licenseLogin"
                        class="mt-6 w-full px-5 py-3 text-base font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 sm:w-full">Log in
                        To <?= $name; ?> Customer Panel</button>
                </div>


                <div class="text-sm font-medium text-white-900">
                    Need to to upgrade your account? <a href="../upgrade/" class="text-blue-600 hover:underline">Upgrade
                        Now</a>.
                </div>
            </form>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

    <script>
        (function() {
            let initialized = false;

            function initTabs() {
                if (initialized) return;

                const container = document.getElementById('tabs-segmented');
                if (!container) return;

                const slider = document.getElementById('tabs-slider');
                if (!slider) return;

                const tabs = Array.from(container.querySelectorAll('button[data-tab]'));
                if (tabs.length === 0) return;

                function layout(activeIdx = 0) {
                    const target = tabs[activeIdx] || tabs[0];
                    if (!target) return;
                    slider.style.width = target.offsetWidth + 'px';
                    slider.style.transform = 'translateX(' + target.offsetLeft + 'px)';
                }

                function setActive(name) {
                    const idx = tabs.findIndex(b => b.dataset.tab === name);
                    tabs.forEach((b, i) => {
                        const active = i === idx;
                        b.setAttribute('aria-selected', active ? 'true' : 'false');
                        b.classList.toggle('text-white', active);
                        b.classList.toggle('text-white/70', !active);
                    });
                    layout(idx);
                }

                function showContent(tabName) {
                    if (tabName === 'user') {
                        document.getElementById('user-content').classList.remove('hidden');
                        document.getElementById('license-content').classList.add('hidden');
                    } else if (tabName === 'license') {
                        document.getElementById('license-content').classList.remove('hidden');
                        document.getElementById('user-content').classList.add('hidden');
                    }
                }

                tabs.forEach(btn => {
                    btn.addEventListener('click', () => {
                        const tabName = btn.dataset.tab;
                        setActive(tabName);
                        showContent(tabName);
                    });
                });

                layout(0);
                initialized = true;
                window.addEventListener('resize', () => layout(tabs.findIndex(b => b.getAttribute('aria-selected') === 'true')));
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initTabs);
            } else {
                initTabs();
            }
        })();
    </script>

    <?php
    if (isset($_POST['login'])) {
        if ($KeyAuthApp->login($_POST['username'], $_POST['password'], $_POST['tfa'])) {
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

    if (isset($_POST['licenseLogin'])) {
        if ($KeyAuthApp->license($_POST['license'], $_POST['tfa'])) {
            $_SESSION['un'] = $_POST['license'];
            echo "<meta http-equiv='Refresh' Content='2; url=../dashboard/'>";
            echo '
                        <script type=\'text/javascript\'>
                        
                        const notyf = new Notyf();
                        notyf
                          .success({
                            message: \'You have successfully logged in with your license!\',
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