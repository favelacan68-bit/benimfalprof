<?php
session_start();

// =========================================================
// 1. AYARLAR & VERİTABANI BAĞLANTISI
// =========================================================
$DB_HOST = 'localhost';
$DB_USER = 'root';      // Hosting kullanıcı adın
$DB_PASS = '';          // Hosting şifren
$DB_NAME = 'fal_db';    // Veritabanı adın
$API_KEY = "AIzaSyBuIjW4oMkN8NyZhKaNyUyBD5FQu4omoYw"; // Senin Gemini Anahtarın

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Veritabanı yoksa hata basmasın, sadece fonksiyonlar çalışmaz
    $db_err = $e->getMessage();
}

// =========================================================
// 2. PHP BACKEND İŞLEMLERİ (AJAX RESPONSE)
// =========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $res = ['status' => 'error', 'msg' => 'İşlem başarısız'];

    // --- A) KAYIT OL ---
    if ($_POST['action'] == 'register') {
        $user = trim($_POST['username']);
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$user, $pass]);
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $user;
            $_SESSION['avatar'] = 'default.png';
            $res = ['status' => 'success'];
        } catch (Exception $e) {
            $res['msg'] = 'Bu isim zaten kullanılıyor.';
        }
    }

    // --- B) GİRİŞ YAP ---
    elseif ($_POST['action'] == 'login') {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$_POST['username']]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($u && password_verify($_POST['password'], $u['password'])) {
            $_SESSION['user_id'] = $u['id'];
            $_SESSION['username'] = $u['username'];
            $_SESSION['avatar'] = $u['avatar'];
            $res = ['status' => 'success'];
        } else {
            $res['msg'] = 'Hatalı kullanıcı adı veya şifre.';
        }
    }

    // --- C) FOTOĞRAF YÜKLE (AVATAR) ---
    elseif ($_POST['action'] == 'upload_avatar' && isset($_SESSION['user_id'])) {
        if (isset($_FILES['file']['tmp_name']) && !empty($_FILES['file']['tmp_name'])) {
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            if(in_array(strtolower($ext), $allowed)) {
                if (!is_dir('uploads')) mkdir('uploads', 0777, true);
                
                $newName = "u_".$_SESSION['user_id']."_".time().".".$ext;
                if(move_uploaded_file($_FILES['file']['tmp_name'], "uploads/".$newName)) {
                    $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?")->execute([$newName, $_SESSION['user_id']]);
                    $_SESSION['avatar'] = $newName;
                    $res = ['status' => 'success', 'url' => "uploads/".$newName];
                }
            } else { $res['msg'] = 'Sadece resim dosyası yükleyebilirsin.'; }
        }
    }

    // --- D) ŞİFRE DEĞİŞTİR ---
    elseif ($_POST['action'] == 'change_pass' && isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $current = $stmt->fetchColumn();
        if(password_verify($_POST['old'], $current)) {
            $newHash = password_hash($_POST['new'], PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$newHash, $_SESSION['user_id']]);
            $res = ['status' => 'success', 'msg' => 'Şifren başarıyla güncellendi.'];
        } else { $res['msg'] = 'Eski şifren yanlış.'; }
    }

    // --- E) GEMINI AI ANALİZ ---
    elseif ($_POST['action'] == 'analiz') {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $API_KEY;
        $json = json_encode(["contents" => [["parts" => [["text" => $_POST['msg']]]]]]);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        echo curl_exec($ch);
        exit;
    }

    // --- F) ÇIKIŞ ---
    elseif ($_POST['action'] == 'logout') {
        session_destroy();
        $res = ['status' => 'success'];
    }

    echo json_encode($res);
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAL ANALİZ PRO | Ultimate</title>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* =========================================
           ULTRA-MODERN CSS (CYBER GLASS)
           ========================================= */
        :root {
            --bg-dark: #050507;
            --glass-bg: rgba(20, 20, 25, 0.6);
            --glass-border: rgba(255, 255, 255, 0.08);
            --primary: #6366f1; /* Indigo */
            --accent: #ec4899;  /* Pink */
            --text-main: #ffffff;
            --text-muted: #94a3b8;
            --glow: 0 0 20px rgba(99, 102, 241, 0.3);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            margin: 0;
            height: 100vh;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Arka Plan Glow Efektleri */
        body::before {
            content: ''; position: absolute; top: -20%; left: -10%; width: 50%; height: 50%;
            background: radial-gradient(circle, var(--primary) 0%, transparent 70%);
            opacity: 0.15; filter: blur(80px); z-index: -1; animation: float 10s infinite alternate;
        }
        body::after {
            content: ''; position: absolute; bottom: -20%; right: -10%; width: 50%; height: 50%;
            background: radial-gradient(circle, var(--accent) 0%, transparent 70%);
            opacity: 0.15; filter: blur(80px); z-index: -1; animation: float 10s infinite alternate-reverse;
        }

        @keyframes float { from { transform: translate(0,0); } to { transform: translate(20px, 20px); } }

        /* ANA KONTEYNER (GLASS CARD) */
        .app-container {
            width: 95%; max-width: 1400px; height: 90vh;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        /* --- SOL PANEL (SIDEBAR) --- */
        .sidebar {
            width: 300px;
            background: rgba(0,0,0,0.2);
            border-right: 1px solid var(--glass-border);
            padding: 30px 20px;
            display: flex; flex-direction: column;
            gap: 20px;
        }

        .profile-card {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--glass-border);
        }

        .avatar-wrapper {
            width: 90px; height: 90px; margin: 0 auto 15px;
            border-radius: 50%; padding: 3px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            position: relative; cursor: pointer; transition: 0.3s;
        }
        .avatar-wrapper:hover { transform: scale(1.05); box-shadow: var(--glow); }
        
        .avatar-img {
            width: 100%; height: 100%; border-radius: 50%;
            object-fit: cover; border: 3px solid #1a1a1a;
            background: #000;
        }
        
        .user-name { font-size: 1.2rem; font-weight: 700; letter-spacing: -0.5px; }
        .user-role { font-size: 0.8rem; color: var(--accent); font-weight: 600; text-transform: uppercase; }

        .nav-btn {
            background: transparent; border: none; color: var(--text-muted);
            padding: 15px; border-radius: 12px; text-align: left;
            font-size: 0.95rem; cursor: pointer; transition: all 0.2s;
            display: flex; align-items: center; gap: 12px; font-weight: 500;
        }
        .nav-btn:hover { background: rgba(255,255,255,0.05); color: #fff; }
        .nav-btn.active { background: linear-gradient(90deg, rgba(99,102,241,0.2), transparent); color: var(--primary); border-left: 3px solid var(--primary); }

        /* --- SAĞ PANEL (CONTENT) --- */
        .main-content { flex: 1; position: relative; display: flex; flex-direction: column; }
        
        .view-panel {
            display: none; flex: 1; flex-direction: column; height: 100%;
            animation: fadeUI 0.4s ease-out;
        }
        .view-panel.active { display: flex; }

        @keyframes fadeUI { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* CHAT ALANI */
        .chat-area { flex: 1; overflow-y: auto; padding: 40px; scroll-behavior: smooth; }
        .chat-area::-webkit-scrollbar { width: 6px; }
        .chat-area::-webkit-scrollbar-thumb { background: #333; border-radius: 3px; }

        .msg {
            max-width: 80%; padding: 20px; border-radius: 18px; margin-bottom: 25px;
            line-height: 1.6; position: relative; font-size: 0.95rem;
        }
        .msg.ai {
            background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border);
            border-bottom-left-radius: 2px; margin-right: auto;
        }
        .msg.user {
            background: linear-gradient(135deg, var(--primary), #4f46e5);
            box-shadow: 0 4px 15px rgba(99,102,241,0.3);
            border-bottom-right-radius: 2px; margin-left: auto; color: white;
        }
        .msg img { max-width: 100%; border-radius: 10px; margin-top: 10px; }

        /* INPUT ALANI */
        .input-zone {
            padding: 25px; background: rgba(0,0,0,0.3);
            border-top: 1px solid var(--glass-border);
            display: flex; gap: 15px; align-items: center;
        }

        .fancy-input {
            flex: 1; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border);
            padding: 16px; border-radius: 14px; color: white; font-family: inherit;
            outline: none; transition: 0.3s;
        }
        .fancy-input:focus { border-color: var(--primary); box-shadow: var(--glow); background: rgba(0,0,0,0.5); }

        .send-btn {
            background: var(--primary); color: white; border: none; width: 50px; height: 50px;
            border-radius: 14px; cursor: pointer; transition: 0.3s; display: flex; justify-content: center; align-items: center;
        }
        .send-btn:hover { background: var(--accent); transform: scale(1.05); }

        /* --- AYARLAR SAYFASI --- */
        .settings-box {
            max-width: 500px; margin: 50px auto; padding: 40px;
            background: rgba(0,0,0,0.2); border-radius: 20px; border: 1px solid var(--glass-border);
        }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; margin-bottom: 8px; color: var(--text-muted); font-size: 0.9rem; }
        .action-btn {
            width: 100%; padding: 15px; background: var(--primary); color: white; border: none;
            border-radius: 10px; font-weight: bold; cursor: pointer; margin-top: 10px;
        }

        /* --- LOGIN MODAL (FULL SCREEN) --- */
        #auth-overlay {
            position: fixed; inset: 0; z-index: 1000;
            background: #000;
            display: flex; justify-content: center; align-items: center;
            background-image: radial-gradient(circle at center, #1a1a2e 0%, #000 100%);
        }
        .auth-card {
            width: 400px; padding: 50px; background: rgba(255,255,255,0.03);
            backdrop-filter: blur(30px); border: 1px solid var(--glass-border);
            border-radius: 24px; text-align: center; box-shadow: 0 0 50px rgba(0,0,0,0.8);
        }
        .brand-title {
            font-size: 2rem; font-weight: 800; margin-bottom: 10px;
            background: linear-gradient(to right, #fff, var(--text-muted));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .link { color: var(--primary); cursor: pointer; font-size: 0.9rem; margin-top: 20px; display: inline-block; }
    </style>
</head>
<body>

    <?php if(!isset($_SESSION['user_id'])): ?>
    <div id="auth-overlay">
        <div class="auth-card" id="login-box">
            <div class="brand-title">FAL ANALİZ PRO</div>
            <p style="color:#666; margin-bottom:30px;">Kozmik sisteme giriş yap</p>
            
            <div class="form-group">
                <input type="text" id="l_user" class="fancy-input" placeholder="Kullanıcı Adı" style="background:#0a0a0c;">
            </div>
            <div class="form-group">
                <input type="password" id="l_pass" class="fancy-input" placeholder="Şifre" style="background:#0a0a0c;">
            </div>
            <button class="action-btn" onclick="doAuth('login')">GİRİŞ YAP</button>
            <span class="link" onclick="toggleAuth()">Hesap oluştur</span>
        </div>

        <div class="auth-card" id="reg-box" style="display:none;">
            <div class="brand-title">KAYIT OL</div>
            <p style="color:#666; margin-bottom:30px;">Yeni bir başlangıç yap</p>
            
            <div class="form-group">
                <input type="text" id="r_user" class="fancy-input" placeholder="Kullanıcı Adı Seç" style="background:#0a0a0c;">
            </div>
            <div class="form-group">
                <input type="password" id="r_pass" class="fancy-input" placeholder="Şifre Belirle" style="background:#0a0a0c;">
            </div>
            <button class="action-btn" style="background:var(--accent)" onclick="doAuth('register')">KAYIT OL</button>
            <span class="link" onclick="toggleAuth()">Giriş yap</span>
        </div>
    </div>
    <?php endif; ?>

    <div class="app-container">
        
        <div class="sidebar">
            <div class="profile-card">
                <div class="avatar-wrapper" onclick="document.getElementById('file-in').click()">
                    <img id="avatar-display" class="avatar-img" src="<?php echo isset($_SESSION['avatar']) && $_SESSION['avatar']!='default.png' ? 'uploads/'.$_SESSION['avatar'] : 'https://cdn-icons-png.flaticon.com/512/149/149071.png'; ?>">
                    <div style="position:absolute; bottom:0; right:0; background:var(--primary); color:white; width:25px; height:25px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.7rem;"><i class="fas fa-camera"></i></div>
                </div>
                <input type="file" id="file-in" hidden onchange="uploadPhoto()">
                
                <div class="user-name"><?php echo $_SESSION['username'] ?? 'Misafir'; ?></div>
                <div class="user-role">PREMIUM ÜYE</div>
            </div>

            <nav>
                <button class="nav-btn active" onclick="goTab('chat', this)"><i class="fas fa-sparkles"></i> AI Analiz</button>
                <button class="nav-btn" onclick="goTab('settings', this)"><i class="fas fa-user-cog"></i> Ayarlar</button>
            </nav>

            <div style="flex:1"></div>
            <button class="nav-btn" onclick="doAuth('logout')" style="color:#ff4d4d;"><i class="fas fa-power-off"></i> Çıkış</button>
        </div>

        <div class="main-content">
            
            <div id="tab-chat" class="view-panel active">
                <div style="padding: 25px 40px; border-bottom:1px solid var(--glass-border); display:flex; justify-content:space-between; align-items:center;">
                    <h2 style="margin:0; font-size:1.5rem;">Yapay Zeka Kahin</h2>
                    <span style="font-size:0.8rem; color:#0f0;">● SİSTEM AKTİF</span>
                </div>

                <div id="chat-feed" class="chat-area">
                    <div class="msg ai">
                        Merhaba <?php echo $_SESSION['username'] ?? ''; ?>. Ben senin kişisel spiritüel rehberinim. 
                        Burç, tarot, rüya yorumu veya dertleşmek için buradayım.
                    </div>
                </div>

                <div class="input-zone">
                    <input type="text" id="ai-msg" class="fancy-input" placeholder="Bir soru sor... (Örn: Bu hafta aşk hayatım nasıl?)" onkeypress="if(event.key==='Enter') sendMsg()">
                    <button class="send-btn" onclick="sendMsg()"><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>

            <div id="tab-settings" class="view-panel">
                <div style="padding: 25px 40px; border-bottom:1px solid var(--glass-border);">
                    <h2 style="margin:0;">Hesap Güvenliği</h2>
                </div>
                
                <div class="settings-box">
                    <h3 style="margin-top:0; color:var(--primary);">Şifre Değiştir</h3>
                    <div class="form-group">
                        <label class="form-label">Eski Şifreniz</label>
                        <input type="password" id="old-p" class="fancy-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Yeni Şifreniz</label>
                        <input type="password" id="new-p" class="fancy-input">
                    </div>
                    <button class="action-btn" onclick="changePass()">GÜNCELLE</button>
                    <p id="set-res" style="text-align:center; font-size:0.9rem; margin-top:10px;"></p>
                </div>
            </div>

        </div>
    </div>

    <script>
        // --- AUTH ---
        function toggleAuth() {
            const l = document.getElementById('login-box');
            const r = document.getElementById('reg-box');
            if(l.style.display=='none'){ l.style.display='block'; r.style.display='none'; }
            else{ l.style.display='none'; r.style.display='block'; }
        }

        async function doAuth(act) {
            const fd = new FormData();
            fd.append('action', act);
            
            if(act === 'login') {
                fd.append('username', document.getElementById('l_user').value);
                fd.append('password', document.getElementById('l_pass').value);
            } else if(act === 'register') {
                fd.append('username', document.getElementById('r_user').value);
                fd.append('password', document.getElementById('r_pass').value);
            }

            const res = await fetch('', {method:'POST', body:fd});
            const d = await res.json();
            if(d.status === 'success') location.reload();
            else alert(d.msg);
        }

        // --- TABS ---
        function goTab(t, btn) {
            document.querySelectorAll('.view-panel').forEach(x => x.classList.remove('active'));
            document.querySelectorAll('.nav-btn').forEach(x => x.classList.remove('active'));
            document.getElementById('tab-'+t).classList.add('active');
            btn.classList.add('active');
        }

        // --- UPLOAD ---
        async function uploadPhoto() {
            const f = document.getElementById('file-in').files[0];
            if(!f) return;
            const fd = new FormData();
            fd.append('action', 'upload_avatar');
            fd.append('file', f);
            
            const res = await fetch('', {method:'POST', body:fd});
            const d = await res.json();
            if(d.status === 'success') document.getElementById('avatar-display').src = d.url;
            else alert(d.msg);
        }

        // --- PASSWORD ---
        async function changePass() {
            const o = document.getElementById('old-p').value;
            const n = document.getElementById('new-p').value;
            const fd = new FormData();
            fd.append('action', 'change_pass');
            fd.append('old', o);
            fd.append('new', n);

            const res = await fetch('', {method:'POST', body:fd});
            const d = await res.json();
            const inf = document.getElementById('set-res');
            inf.innerText = d.msg;
            inf.style.color = d.status==='success' ? '#4ade80' : '#f87171';
        }

        // --- AI CHAT ---
        async function sendMsg() {
            const i = document.getElementById('ai-msg');
            const txt = i.value.trim();
            if(!txt) return;

            addChat(txt, 'user');
            i.value = '';
            const load = addChat('Analiz ediliyor...', 'ai');

            const fd = new FormData();
            fd.append('action', 'analiz');
            fd.append('msg', txt);

            try {
                const res = await fetch('', {method:'POST', body:fd});
                const d = await res.json();
                if(d.candidates) {
                    load.innerHTML = marked.parse(d.candidates[0].content.parts[0].text);
                } else {
                    load.innerHTML = "Bağlantı hatası.";
                }
            } catch(e) { load.innerHTML = "Sistem hatası."; }
            
            // Auto Scroll
            document.getElementById('chat-feed').scrollTop = 99999;
        }

        function addChat(t, c) {
            const d = document.createElement('div');
            d.className = `msg ${c}`;
            d.innerHTML = t;
            document.getElementById('chat-feed').appendChild(d);
            document.getElementById('chat-feed').scrollTop = 99999;
            return d;
        }
    </script>
</body>
</html>
