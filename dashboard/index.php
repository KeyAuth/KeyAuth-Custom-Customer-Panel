<?php
include '../credentials.php';
require '../keyauth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['un'])) {
    die("not logged in");
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: ../");
    exit();
}


$KeyAuthApp = new KeyAuth\api($name, $OwnerId);

$url = "https://keyauth.win/api/seller/?sellerkey={$SellerKey}&type=getsettings";

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$resp = curl_exec($curl);
$json = json_decode($resp);

if (!$json->success) {
    die("Error: {$json->message}");
}

$download = $json->functions->download ?? null;
$webdownload = $json->functions->webdownload ?? null;
$appcooldown = $json->functions->cooldown ?? 0;

$numKeys = $KeyAuthApp->numKeys;
$numUsers = $KeyAuthApp->numUsers;
$numOnlineUsers = $KeyAuthApp->numOnlineUsers;
$customerPanelLink = $KeyAuthApp->customerPanelLink;

$un = $_SESSION['un'];
$url = "https://keyauth.win/api/seller/?sellerkey={$SellerKey}&type=userdata&user={$un}";

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$resp = curl_exec($curl);
$userData = json_decode($resp, true);

if (!$userData['success']) {
    die("Error: {$userData['message']}");
}

$hwid = $userData['hwid'] ?? "Not Set";
$cooldown = $userData['cooldown'] ?? 0;
$token = $userData['token'];
$subscriptions = $userData['subscriptions'] ?? [];
$today = time();

$canReset = is_null($cooldown) || $today >= $cooldown;
?>
<!DOCTYPE html>
<html lang="en" class="bg-custom-back-1 text-white overflow-x-hidden">

<head>
    <base href="">
    <title><?= $name; ?> Panel</title>
    <meta charset="utf-8">
    <link rel="shortcut icon" href="https://cdn.keyauth.cc/global/imgs/Favicon.png">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.keyauth.uk/dashboard/unixtolocal.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="https://cdn.keyauth.cc/v4/css/oput.css">

    <script type="text/javascript">
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</head>

<body class="bg-custom-back-1 text-white antialiased">
    <nav class="bg-custom-back border-b border-gray-800 py-4 px-6 flex justify-between items-center sticky top-0 z-50">
        <div class="flex items-center space-x-4">
            <img src="https://cdn.keyauth.cc/global/imgs/Favicon.png" class="h-8" alt="Logo">
            <h1 class="text-xl font-bold"><?= $name; ?> Dashboard</h1>
        </div>
        <form method="post">
            <button name="logout" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200 flex items-center">
                <i class="lni lni-power-switch mr-2"></i> Log out
            </button>
        </form>
    </nav>

    <main class="max-w-7xl mx-auto p-6">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-white mb-1">Customer Dashboard</h2>
            <p class="text-sm text-gray-400">Manage your account settings and view account information</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-custom-back rounded-xl p-6 border border-gray-800/50 shadow-lg">
                <h2 class="text-xl font-semibold text-white mb-4 flex items-center">
                    <i class="lni lni-user mr-2 text-blue-500"></i> Account Information
                </h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-gray-800/30">
                        <span class="text-gray-400">Account Created</span>
                        <span class="text-white font-medium"><?= isset($userData['createdate']) ? date('M d, Y', is_numeric($userData['createdate']) ? $userData['createdate'] : strtotime($userData['createdate'])) : 'N/A'; ?></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-800/30">
                        <span class="text-gray-400">Username</span>
                        <span class="text-white font-medium"><?= htmlspecialchars($un); ?></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-800/30">
                        <span class="text-gray-400">HWID</span>
                        <span class="text-blue-600"><?= $hwid; ?></span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-400">2FA Status</span>
                        <span class="<?= isset($userData['2fa']) && $userData['2fa'] ? 'text-green-400' : 'text-red-400'; ?> font-medium">
                            <?= isset($userData['2fa']) && $userData['2fa'] ? 'Enabled' : 'Disabled'; ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-custom-back rounded-xl p-6 border border-gray-800/50 shadow-lg">
                <h2 class="text-xl font-semibold text-white mb-4 flex items-center">
                    <i class="lni lni-calendar mr-2 text-blue-500"></i> Subscription Status
                </h2>
                <div class="space-y-4">
                    <?php if (!empty($subscriptions)): ?>
                        <?php foreach ($subscriptions as $sub): ?>
                            <?php
                            $isExpired = $sub['expiry'] < time();
                            $statusColor = $isExpired ? 'text-red-400 bg-red-400/10' : 'text-green-400 bg-green-400/10';
                            $statusText = $isExpired ? 'Expired' : 'Active';
                            ?>
                            <div class="py-3 border-b border-gray-800/30 last:border-0">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-white font-medium"><?= htmlspecialchars($sub['subscription']); ?></span>
                                    <span class="px-2 py-1 text-xs font-bold rounded <?= $statusColor; ?>">
                                        <?= strtoupper($statusText); ?>
                                    </span>
                                </div>
                                <div class="text-xs text-gray-500">
                                    Expires: <span class="text-gray-400"><script>document.write(new Date(<?= $sub['expiry'] * 1000; ?>).toLocaleString());</script></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="lni lni-warning text-yellow-500 text-3xl mb-2"></i>
                            <p class="text-gray-400 text-sm">No active subscriptions found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="bg-custom-back rounded-xl p-6 border border-gray-800/50 shadow-lg mb-6">
            <h2 class="text-xl font-semibold text-white mb-2 flex items-center">
                <i class="lni lni-cog mr-2 text-blue-500"></i> Update Credentials
            </h2>
            <p class="text-sm text-gray-400 mb-6">Secure your account by updating your login information.</p>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label for="username" class="block text-sm font-medium text-gray-400">New Username</label>
                    <input type="text" id="username" name="username" class="w-full bg-custom-back-1 border border-gray-800 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 outline-none" placeholder="Leave blank to keep current">
                </div>
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-gray-400">New Password</label>
                    <input type="password" id="password" name="password" class="w-full bg-custom-back-1 border border-gray-800 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 outline-none" placeholder="Leave blank to keep current">
                </div>
                <div class="md:col-span-2">
                    <button name="saveUser" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition duration-200 shadow-lg shadow-blue-600/20">
                        Save Account Changes
                    </button>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-custom-back rounded-xl p-6 border border-gray-800/50 shadow-lg">
                <h2 class="text-xl font-semibold text-white mb-2 flex items-center">
                    <i class="lni lni-shield mr-2 text-blue-500"></i> HWID Management
                </h2>
                <p class="text-sm text-gray-400 mb-6">Reset your hardware ID if you changed components or moved to a new PC.</p>
                <div>
                    <?php if ($canReset): ?>
                        <form method="post">
                            <button name="resethwid" class="w-full flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl transition duration-200 shadow-lg shadow-blue-600/20">
                                <i class="lni lni-reload mr-2 animate-spin-slow"></i> Reset HWID
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="bg-red-600/10 border border-red-600/20 rounded-xl p-4 text-center">
                            <p class="text-red-400 text-sm font-medium mb-1">Reset on Cooldown</p>
                            <p class="text-xs text-red-400/60">Available: <script>document.write(convertTimestamp(<?= json_encode((int)$cooldown, JSON_NUMERIC_CHECK) ?>));</script></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="bg-custom-back rounded-xl p-6 border border-gray-800/50 shadow-lg">
                <h2 class="text-xl font-semibold text-white mb-2 flex items-center">
                    <i class="lni lni-download mr-2 text-blue-500"></i> Software Access
                </h2>
                <p class="text-sm text-gray-400 mb-6">Get the latest version of our software directly to your computer.</p>
                <div>
                    <?php if (!is_null($download) && !empty($download)): ?>
                        <a href="<?= $download; ?>" target="_blank" class="w-full flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-xl transition duration-200 shadow-lg shadow-green-600/20">
                            <i class="lni lni-cloud-download mr-2"></i> Download Application
                        </a>
                    <?php else: ?>
                        <div class="bg-gray-800/50 border border-gray-700/50 rounded-xl p-4 text-center">
                            <p class="text-gray-400 text-sm font-medium">No download available</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (!is_null($webdownload) && !empty($webdownload)): ?>
            <div class="bg-custom-back rounded-xl p-6 border border-gray-800/50 shadow-lg">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-white flex items-center">
                            <i class="lni lni-control-panel mr-2 text-blue-500"></i> Web Loader
                        </h2>
                        <p class="text-sm text-gray-400">Control your application session from your browser</p>
                    </div>
                </div>
                
                <div id="buttons" style="display: none;" class="mb-6">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Command Center</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        <?php
                        $url = "https://keyauth.win/api/seller/?sellerkey={$SellerKey}&type=fetchallbuttons";
                        $curl = curl_init($url);
                        curl_setopt($curl, CURLOPT_URL, $url);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        $resp = curl_exec($curl);
                        $btnJson = json_decode($resp, true);
                        $arr = $btnJson['buttons'] ?? [];

                        if ($arr !== "not_found" && is_array($arr)) {
                            foreach ($arr as $item) {
                                echo '<button onclick="doButton(this.value)" value="'.htmlspecialchars($item['value']).'" class="bg-gray-800 hover:bg-blue-600 text-gray-300 hover:text-white font-medium py-2.5 px-4 rounded-lg transition duration-200 border border-gray-700 hover:border-blue-500 text-sm">'.htmlspecialchars($item['text']).'</button>';
                            }
                        }
                        ?>
                    </div>
                </div>

                <div id="handshake">
                    <div class="bg-blue-600/5 border border-blue-600/20 rounded-2xl p-8 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-600/20 text-blue-500 mb-4">
                            <i class="lni lni-cloud-sync text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Awaiting Connection</h3>
                        <p class="text-gray-400 text-sm mb-6 max-w-md mx-auto">To use the web loader, ensure you have the loader running on your system and click the connect button below.</p>
                        <button onclick="handshake()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition duration-200 shadow-lg shadow-blue-600/20">
                            Establish Connection
                        </button>
                        <a href="<?= $webdownload; ?>" target="_blank" class="block mt-4 text-xs text-gray-500 hover:text-blue-400 transition-colors underline decoration-gray-700 underline-offset-4">Download Loader Executable</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer class="border-t border-gray-800 mt-12 py-8 text-center">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
            <div class="text-gray-500 text-sm font-medium">
                &copy; 2020 - <?= date('Y'); ?> <span class="text-white">KeyAuth LLC</span>. All rights reserved.
            </div>
            <div class="flex space-x-6 text-gray-500 text-sm">
                <a href="https://keyauth.cc/terms" class="hover:text-white transition-colors">Terms of Service</a>
                <a href="https://keyauth.cc/privacy" class="hover:text-white transition-colors">Privacy Policy</a>   
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

    <script>
        const notyf = new Notyf({
            duration: 3500,
            position: { x: 'right', y: 'top' },
            dismissible: true
        });

        var going = 1;

        function handshake() {
            setTimeout(function() {
                var xmlHttp = new XMLHttpRequest();
                xmlHttp.open("GET", "http://localhost:1337/handshake?user=<?php echo $_SESSION['un']; ?>&token=<?php echo $token; ?>");
                xmlHttp.onload = function() {
                    going = 0;
                    switch (xmlHttp.status) {
                        case 420:
                            notyf.success("Connected to Web Loader!");
                            document.getElementById("handshake").style.display = "none";
                            document.getElementById("buttons").style.display = "block";
                            break;
                        default:
                            notyf.error("Connection failed: " + xmlHttp.statusText);
                            break;
                    }
                };
                xmlHttp.onerror = function() {
                    if (going == 1) handshake();
                };
                xmlHttp.send();
            }, 3000);
        }

        function doButton(value) {
            var xmlHttp = new XMLHttpRequest();
            xmlHttp.open("GET", "http://localhost:1337/" + value);
            xmlHttp.onload = function() {
                notyf.success("Command sent: " + value);
            };
            xmlHttp.onerror = function() {
                notyf.error("Failed to send command. Is loader running?");
            };
            xmlHttp.send();
        }
    </script>

    <?php
    if (isset($_POST['resethwid'])) {
        $today = time();
        $cooldown = $today + $appcooldown;
        $un = $_SESSION['un'];
        
        // Reset User
        $url = "https://keyauth.win/api/seller/?sellerkey={$SellerKey}&type=resetuser&user={$un}";
        file_get_contents($url);

        // Set Cooldown
        $url = "https://keyauth.win/api/seller/?sellerkey={$SellerKey}&type=setcooldown&user={$un}&cooldown={$cooldown}";
        file_get_contents($url);

        echo "<script>notyf.success('HWID Reset Successfully!'); setTimeout(() => { window.location.reload(); }, 2000);</script>";
    }

    if (isset($_POST['saveUser'])) {
        $un = $_SESSION['un'];
        $newUsername = $_POST['username'] ?? '';
        $newPassword = $_POST['password'] ?? '';

        if (!empty($newUsername)) {
            $url = "https://keyauth.win/api/seller/?sellerkey={$SellerKey}&type=editusername&currentUsername={$un}&newUsername={$newUsername}";
            $resp = file_get_contents($url);
            $json = json_decode($resp, true);
            if ($json['success']) {
                $_SESSION['un'] = $newUsername;
                $un = $newUsername;
                echo "<script>notyf.success('Username updated!');</script>";
            } else {
                echo "<script>notyf.error('Failed to update username: {$json['message']}');</script>";
            }
        }

        if (!empty($newPassword)) {
            $url = "https://keyauth.win/api/seller/?sellerkey={$SellerKey}&type=resetpw&user={$un}&passwd={$newPassword}";
            $resp = file_get_contents($url);
            $json = json_decode($resp, true);
            if ($json['success']) {
                echo "<script>notyf.success('Password updated!');</script>";
            } else {
                echo "<script>notyf.error('Failed to update password: {$json['message']}');</script>";
            }
        }
        
        echo "<script>setTimeout(() => { window.location.reload(); }, 2000);</script>";
    }
    ?>
</body>

</html>

<?php
#region Extra Functions
/*
//* Get Public Variable
$var = $KeyAuthApp->var("varName");
echo "Variable Data: " . $var;
//* Get User Variable
$var = $KeyAuthApp->getvar("varName");
echo "Variable Data: " . $var;
//* Set Up User Variable
$KeyAuthApp->setvar("varName", "varData");
//* Log Something to the KeyAuth webhook that you have set up on app settings
$KeyAuthApp->log("message");
//* Basic Webhook with params
$result = $KeyAuthApp->webhook("WebhookID", "&type=add&expiry=1&mask=XXXXXX-XXXXXX-XXXXXX-XXXXXX-XXXXXX-XXXXXX&level=1&amount=1&format=text");
echo "<br> Result from Webhook: " . $result;
//* Webhook with body and content type
$result = $KeyAuthApp->webhook("WebhookID", "", "{\"content\": \"webhook message here\",\"embeds\": null}", "application/json");
echo "<br> Result from Webhook: " . $result;
//* If first sub is what ever then run code
if ($subscription === "Premium") {
	Premium Subscription Code ...
}
*/
#endregion
?>