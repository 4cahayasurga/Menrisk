<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= htmlspecialchars($title ?? 'Login — IFPRO Risk Command Center') ?></title>
  <style>
    :root {
      --navy: #081e38;
      --navy-light: #0f2d52;
      --navy-card: #13345d;
      --teal: #00a89d;
      --teal-light: #29b6af;
      --red: #d93b45;
      --line: rgba(255, 255, 255, 0.12);
      --text: #ffffff;
      --muted: #9fb4c9;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      background: radial-gradient(circle at 50% 20%, #10335e 0%, #081e38 70%);
      color: var(--text);
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .login-container {
      width: 100%;
      max-width: 440px;
    }
    .brand-header {
      text-align: center;
      margin-bottom: 28px;
    }
    .brandmark {
      display: inline-grid;
      place-items: center;
      width: 54px;
      height: 54px;
      background: linear-gradient(145deg, var(--teal-light), #11706d);
      border-radius: 14px;
      font-weight: 900;
      font-size: 22px;
      letter-spacing: -1px;
      color: #fff;
      box-shadow: 0 8px 24px rgba(0, 168, 157, 0.35);
      margin-bottom: 12px;
    }
    .brand-header h1 {
      font-size: 24px;
      font-weight: 800;
      letter-spacing: .02em;
    }
    .brand-header p {
      font-size: 13px;
      color: var(--muted);
      margin-top: 4px;
    }
    .login-card {
      background: rgba(16, 47, 85, 0.7);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid var(--line);
      border-radius: 16px;
      padding: 32px 28px;
      box-shadow: 0 16px 40px rgba(0, 0, 0, 0.4);
    }
    .card-title {
      font-size: 16px;
      font-weight: 700;
      margin-bottom: 18px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .card-title span {
      display: inline-block;
      width: 6px;
      height: 16px;
      background: var(--teal);
      border-radius: 3px;
    }
    .alert-error {
      background: rgba(217, 59, 69, 0.15);
      border: 1px solid rgba(217, 59, 69, 0.4);
      border-left: 4px solid var(--red);
      color: #ffb4b9;
      padding: 12px 14px;
      border-radius: 8px;
      font-size: 13px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .form-group {
      margin-bottom: 18px;
    }
    .form-group label {
      display: block;
      font-size: 12px;
      font-weight: 700;
      color: var(--muted);
      margin-bottom: 7px;
      letter-spacing: .04em;
    }
    .input-wrap {
      position: relative;
    }
    .form-group input {
      width: 100%;
      height: 44px;
      background: rgba(7, 24, 46, 0.8);
      border: 1px solid var(--line);
      border-radius: 9px;
      padding: 0 14px;
      color: #fff;
      font-size: 14px;
      transition: all 0.2s;
    }
    .form-group input:focus {
      outline: none;
      border-color: var(--teal);
      box-shadow: 0 0 0 3px rgba(0, 168, 157, 0.25);
    }
    .btn-login {
      width: 100%;
      height: 46px;
      background: linear-gradient(135deg, #00a89d, #00877e);
      border: none;
      border-radius: 9px;
      color: #fff;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      margin-top: 10px;
      transition: all 0.2s;
      box-shadow: 0 4px 14px rgba(0, 168, 157, 0.3);
    }
    .btn-login:hover {
      background: linear-gradient(135deg, #02b8ac, #00998f);
      transform: translateY(-1px);
    }
    .credentials-demo {
      margin-top: 24px;
      background: rgba(7, 25, 48, 0.5);
      border: 1px dashed var(--line);
      border-radius: 10px;
      padding: 14px 16px;
      font-size: 12px;
      color: var(--muted);
    }
    .credentials-demo strong {
      color: #fff;
      display: block;
      margin-bottom: 6px;
      font-size: 11px;
      letter-spacing: .06em;
      text-transform: uppercase;
    }
    .demo-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
    }
    .demo-box {
      background: rgba(255, 255, 255, 0.04);
      padding: 8px;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.2s;
    }
    .demo-box:hover {
      background: rgba(255, 255, 255, 0.09);
    }
    .demo-box b {
      color: var(--teal-light);
      display: block;
    }
    .demo-box code {
      font-size: 11px;
      color: #d1dfed;
    }
    .footer-note {
      text-align: center;
      margin-top: 24px;
      font-size: 11px;
      color: #657e9b;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="brand-header">
      <div class="brandmark">IF</div>
      <h1>IFPRO Risk Command Center</h1>
      <p>Enterprise Risk Management & Executive Oversight</p>
    </div>

    <div class="login-card">
      <div class="card-title">
        <span></span>
        Masuk ke Sistem
      </div>

      <?php if (!empty($error)): ?>
        <div class="alert-error">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          <div><?= htmlspecialchars($error) ?></div>
        </div>
      <?php endif; ?>

      <form action="<?= site_url('/auth/process_login') ?>" method="POST" id="loginForm">
        <div class="form-group">
          <label for="username">USERNAME</label>
          <div class="input-wrap">
            <input type="text" id="username" name="username" placeholder="Masukkan username" required autocomplete="username" autofocus />
          </div>
        </div>

        <div class="form-group">
          <label for="password">PASSWORD</label>
          <div class="input-wrap">
            <input type="password" id="password" name="password" placeholder="Masukkan password" required autocomplete="current-password" />
          </div>
        </div>

        <button type="submit" class="btn-login" id="submitBtn">Masuk Dashboard →</button>
      </form>

      <!-- Demo Accounts Helper -->
      <!-- <div class="credentials-demo">
        <strong>Demo Akun Tersedia (Klik untuk isi):</strong>
        <div class="demo-grid">
          <div class="demo-box" onclick="fillDemo('admin', 'admin123')">
            <b>Admin (Full Access)</b>
            <code>user: admin</code><br>
            <code>pass: admin123</code>
          </div>
          <div class="demo-box" onclick="fillDemo('user', 'user123')">
            <b>User (Risk Officer)</b>
            <code>user: user</code><br>
            <code>pass: user123</code>
          </div>
        </div>
      </div> -->
    </div>

    <div class="footer-note">
      IFPRO Enterprise Risk Command Center · Connected to SQL Server
    </div>
  </div>

  <script>
    function fillDemo(u, p) {
      document.getElementById('username').value = u;
      document.getElementById('password').value = p;
    }
  </script>
</body>
</html>
