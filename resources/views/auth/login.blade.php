<!DOCTYPE html>
<html lang="ar" dir="rtl" id="mainHtml" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="pageTitle">تسجيل الدخول</title>
    <link id="bootstrapStyle" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <style>
        :root {
            --bg-color: #f4f7f6;
            --card-bg: #ffffff;
            --text-color: #212529;
            --input-bg: #ffffff;
            --input-border: #dee2e6;
        }
        [data-theme="dark"] {
            --bg-color: #1a1d20;
            --card-bg: #2b3035;
            --text-color: #f8f9fa;
            --input-bg: #343a40;
            --input-border: #495057;
        }
        body { background: var(--bg-color); color: var(--text-color); display: flex; align-items: center; justify-content: center; height: 100vh; font-family: sans-serif; transition: 0.3s; }
        .login-card { width: 100%; max-width: 400px; padding: 25px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border-radius: 12px; background: var(--card-bg); }
        .form-control { background-color: var(--input-bg); border-color: var(--input-border); color: var(--text-color); }
        .form-control:focus { background-color: var(--input-bg); color: var(--text-color); box-shadow: none; border-color: #0d6efd; }
        .controls-top { position: absolute; top: 20px; right: 20px; display: flex; gap: 10px; z-index: 1000; }
        /* هذا الجزء هو المسؤول عن قلب مكان الأزرار عند تغيير اللغة */
        [dir="ltr"] .controls-top { right: auto; left: 20px; flex-direction: row-reverse; }
        #otpSection { display: none; }
    </style>
</head>
<body>

<div class="controls-top">
    <button id="themeBtn" class="btn btn-outline-secondary btn-sm" onclick="toggleTheme()">🌙</button>
    <button id="langBtn" class="btn btn-outline-primary btn-sm" onclick="toggleLanguage()">English</button>
</div>

<div class="card login-card">
    <h3 id="formTitle" class="text-center mb-4">تسجيل الدخول</h3>
    <form id="loginForm">
        <div class="mb-3">
            <label id="labelEmail" class="form-label">البريد الإلكتروني</label>
            <input type="email" id="email" class="form-control" required placeholder="admin@example.com">
        </div>
        <div class="mb-3">
            <label id="labelPhone" class="form-label">رقم الهاتف</label>
            <input type="tel" id="phone" class="form-control" required placeholder="09xxxxxxxx">
        </div>
        <div class="mb-3">
            <label id="labelPassword" class="form-label">كلمة المرور</label>
            <input type="password" id="password" class="form-control" required>
        </div>
        <div id="otpSection" class="mb-3 border-top pt-3 mt-3">
            <label id="labelOtp" class="form-label text-primary font-weight-bold">رمز الـ OTP</label>
            <input type="text" id="otp_code" class="form-control" placeholder="123456">
        </div>
        <button type="submit" id="submitBtn" class="btn btn-primary w-100">متابعة</button>
    </form>
    <div id="message" class="mt-3 text-center small"></div>
</div>

<script>
    // 1. قاموس اللغات
    const translations = {
        ar: { title: "تسجيل الدخول", email: "البريد الإلكتروني", phone: "رقم الهاتف", password: "كلمة المرور", otp: "رمز الـ OTP (وصلك الآن)", submit: "متابعة", langBtn: "English", dir: "rtl", boot: "https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" },
        en: { title: "Login", email: "Email Address", phone: "Phone Number", password: "Password", otp: "OTP Code (Check your email)", submit: "Continue", langBtn: "العربية", dir: "ltr", boot: "https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" }
    };

    // 2. وظيفة تطبيق اللغة (تقلب الاتجاه وتغير ملف الـ Bootstrap)
    function applyLanguage(lang) {
        const t = translations[lang];
        const htmlTag = document.getElementById('mainHtml');

        htmlTag.dir = t.dir;
        htmlTag.lang = lang;
        document.getElementById('bootstrapStyle').href = t.boot;

        // تحديث النصوص
        document.getElementById('pageTitle').innerText = t.title;
        document.getElementById('formTitle').innerText = t.title;
        document.getElementById('labelEmail').innerText = t.email;
        document.getElementById('labelPhone').innerText = t.phone;
        document.getElementById('labelPassword').innerText = t.password;
        document.getElementById('labelOtp').innerText = t.otp;
        document.getElementById('submitBtn').innerText = t.submit;
        document.getElementById('langBtn').innerText = t.langBtn;

        localStorage.setItem('lang', lang);
    }

    // 3. وظيفة تبديل اللغة عند الضغط
    function toggleLanguage() {
        const current = localStorage.getItem('lang') || 'ar';
        applyLanguage(current === 'ar' ? 'en' : 'ar');
    }

    // 4. وظيفة تطبيق الثيم (فاتح/داكن)
    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        document.getElementById('themeBtn').innerText = theme === 'dark' ? '☀️' : '🌙';
        localStorage.setItem('theme', theme);
    }

    // 5. وظيفة تبديل الثيم عند الضغط
    function toggleTheme() {
        const current = localStorage.getItem('theme') || 'light';
        applyTheme(current === 'dark' ? 'light' : 'dark');
    }

    // 6. تشغيل الإعدادات المحفوظة فور تحميل الصفحة
    window.onload = () => {
        applyLanguage(localStorage.getItem('lang') || 'ar');
        applyTheme(localStorage.getItem('theme') || 'light');
    };

    // 7. منطق إرسال الفورم (Ajax)
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const messageDiv = document.getElementById('message');
        messageDiv.innerText = "جاري المعالجة...";

        const payload = {
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            password: document.getElementById('password').value,
            otp_code: document.getElementById('otp_code').value || null
        };

        try {
            const response = await fetch('/api/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (response.status === 200 && data.requires_otp) {
                document.getElementById('otpSection').style.display = 'block';
                document.getElementById('otp_code').required = true;
                messageDiv.innerHTML = `<span class="text-info">${data.message}</span>`;
            } else if (response.ok) {
                localStorage.setItem('admin_token', data.token);
                messageDiv.innerHTML = `<span class="text-success">تم الدخول بنجاح!</span>`;
                setTimeout(() => window.location.replace('/admin/dashboard'), 1000);
            } else {
                messageDiv.innerHTML = `<span class="text-danger">${data.message}</span>`;
            }
        } catch (error) {
            messageDiv.innerHTML = `<span class="text-danger">فشل الاتصال بالسيرفر</span>`;
        }
    });
</script>
</body>
</html>
