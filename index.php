<?php
// --- AYARLAR ---
$app_title = "FAL ANALİZ PRO";
$app_version = "v2.0 Ultimate Edition";
$main_file = "index.php"; // Yönlendirilecek asıl dosya
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $app_title; ?> | Dokümantasyon</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;700&family=Outfit:wght@300;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg: #030305;
            --primary: #7c3aed;
            --accent: #06b6d4;
            --glass: rgba(255, 255, 255, 0.03);
            --border: rgba(255, 255, 255, 0.08);
            --text: #ffffff;
            --glow: 0 0 40px rgba(124, 58, 237, 0.3);
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Outfit', sans-serif;
            margin: 0; padding: 0;
            overflow-x: hidden;
        }

        /* Arka Plan Efekti */
        .bg-glow {
            position: fixed; top: -20%; left: -10%; width: 60vw; height: 60vw;
            background: radial-gradient(circle, var(--primary) 0%, transparent 70%);
            opacity: 0.15; filter: blur(100px); z-index: -1; animation: pulse 10s infinite alternate;
        }
        @keyframes pulse { from { opacity: 0.1; } to { opacity: 0.2; transform: scale(1.1); } }

        /* Konteyner */
        .container { max-width: 1000px; margin: 0 auto; padding: 50px 20px; }

        /* Başlık Alanı */
        header { text-align: center; margin-bottom: 60px; padding-top: 50px; }
        .badge {
            background: rgba(124, 58, 237, 0.1); color: #a78bfa; border: 1px solid var(--border);
            padding: 6px 16px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; letter-spacing: 2px;
            text-transform: uppercase; display: inline-block; margin-bottom: 20px;
        }
        h1 {
            font-family: 'Space Grotesk', sans-serif; font-size: 3.5rem; margin: 0;
            background: linear-gradient(to right, #fff, #64748b);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            text-shadow: 0 10px 30px rgba(0,0,0,0.5); letter-spacing: -1px;
        }
        p.subtitle { font-size: 1.1rem; color: #94a3b8; max-width: 600px; margin: 20px auto; line-height: 1.6; }

        /* Büyük Başlat Butonu */
        .cta-box { text-align: center; margin-bottom: 80px; }
        .launch-btn {
            background: var(--primary); color: #fff; text-decoration: none;
            padding: 22px 55px; border-radius: 50px; font-size: 1.1rem; font-weight: bold;
            display: inline-flex; align-items: center; gap: 12px; transition: 0.3s;
            box-shadow: 0 15px 40px rgba(124, 58, 237, 0.2); border: 1px solid rgba(255,255,255,0.1);
        }
        .launch-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 60px rgba(124, 58, 237, 0.4); background: #fff; color: var(--primary);
        }

        /* Özellik Kartları Grid */
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
        .card {
            background: var(--glass); border: 1px solid var(--border);
            padding: 35px; border-radius: 24px; backdrop-filter: blur(20px);
            transition: 0.3s; position: relative; overflow: hidden;
        }
        .card:hover { transform: translateY(-7px); border-color: var(--primary); background: rgba(255,255,255,0.06); }
        .card i { font-size: 2rem; color: var(--accent); margin-bottom: 20px; display: block; opacity: 0.8; }
        .card h3 { font-family: 'Space Grotesk', sans-serif; font-size: 1.4rem; margin: 0 0 10px 0; color: #fff; }
        .card p { color: #94a3b8; line-height: 1.6; font-size: 0.95rem; margin: 0; }

        /* Teknik Detaylar */
        .tech-stack {
            margin-top: 100px; border-top: 1px solid var(--border); padding-top: 40px;
            display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;
        }
        .tech-item { display: flex; align-items: center; gap: 10px; color: #64748b; font-weight: 500; font-size: 0.9rem; transition:0.3s; }
        .tech-item:hover { color: #fff; text-shadow: 0 0 10px rgba(255,255,255,0.5); }

        footer { text-align: center; margin-top: 80px; padding: 30px; color: #444; font-size: 0.8rem; border-top: 1px solid var(--border); }
        
        /* Responsive */
        @media (max-width: 768px) {
            h1 { font-size: 2.5rem; }
            .grid { grid-template-columns: 1fr; }
            .tech-stack { justify-content: center; }
        }
    </style>
</head>
<body>

    <div class="bg-glow"></div>

    <div class="container">
        
        <header>
            <span class="badge"><?php echo $app_version; ?></span>
            <h1><?php echo $app_title; ?></h1>
            <p class="subtitle">Yapay zeka destekli, siber güvenlikli ve ultra-modern tasarıma sahip yeni nesil spiritüel analiz platformu.</p>
        </header>

        <div class="cta-box">
            <a href="<?php echo $main_file; ?>" class="launch-btn">
                <i class="fas fa-rocket"></i> SİSTEMİ BAŞLAT
            </a>
            <p style="font-size:0.8rem; color:#555; margin-top:15px;">(Veritabanı bağlantısı gerektirir)</p>
        </div>

        <div class="grid">
            <div class="card">
                <i class="fas fa-brain"></i>
                <h3>Google Gemini AI</h3>
                <p>En son model Gemini-1.5 entegrasyonu ile rüya, tarot ve astroloji analizlerini saniyeler içinde yapar.</p>
            </div>
            <div class="card">
                <i class="fas fa-shield-alt"></i>
                <h3>Siber Güvenlik</h3>
                <p>Kullanıcı şifreleri "Password Hashing" ile kriptolanır. SQL Injection korumalı güvenli oturum yönetimi.</p>
            </div>
            <div class="card">
                <i class="fas fa-layer-group"></i>
                <h3>Glassmorphism UI</h3>
                <p>Arka planda canlı ışık efektleri ve buzlu cam panellerle donatılmış, ödüllük bir kullanıcı arayüzü.</p>
            </div>
            <div class="card">
                <i class="fas fa-user-circle"></i>
                <h3>Profil Yönetimi</h3>
                <p>Kullanıcılar fotoğraf yükleyebilir, şifre değiştirebilir ve kişiselleştirilmiş panellerini yönetebilir.</p>
            </div>
        </div>

        <div class="tech-stack">
            <div class="tech-item"><i class="fab fa-php fa-lg"></i> PHP 8.0+ Backend</div>
            <div class="tech-item"><i class="fas fa-database fa-lg"></i> MySQL / PDO</div>
            <div class="tech-item"><i class="fab fa-css3-alt fa-lg"></i> CSS3 Animations</div>
            <div class="tech-item"><i class="fab fa-google fa-lg"></i> Gemini API</div>
        </div>

        <footer>
            &copy; <?php echo date("Y"); ?> Beyto Corp. Tüm hakları saklıdır. <br> Crafted with Next-Gen Code.
        </footer>

    </div>

</body>
</html>
