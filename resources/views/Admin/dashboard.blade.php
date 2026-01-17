
<!DOCTYPE html>
<html lang="ar" dir="rtl" id="mainHtml" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="pageTitle">لوحة التحكم</title>
    <link id="bootstrapStyle" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">

    <style>
        /* تعريف متغيرات الألوان للوضع الفاتح والداكن */
        :root {
            --bg: #f8f9fa;
            --card: #ffffff;
            --text: #212529;
            --table-border: #dee2e6;
        }
        [data-theme="dark"] {
            --bg: #121212;
            --card: #1e1e1e;
            --text: #e0e0e0;
            --table-border: #373b3e;
        }

        body {
            background-color: var(--bg);
            color: var(--text);
            transition: 0.3s ease;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            background-color: var(--card);
            border: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .table {
            color: var(--text);
            border-color: var(--table-border);
        }

        [data-theme="dark"] .table-hover tbody tr:hover {
            background-color: #2c2c2c;
            color: #ffffff;
        }

        .header-btns {
            display: flex;
            gap: 8px;
            align-items: center;
        }
    </style>
</head>
<body>

<script>
    if (!localStorage.getItem('admin_token')) {
        window.location.href = '/login';
    }
</script>

<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h4 id="headerTitle" class="mb-0">طلبات التسجيل بانتظار الموافقة</h4>
            <div class="header-btns">
                <button id="themeBtn" class="btn btn-outline-light btn-sm" onclick="toggleTheme()">🌙</button>
                <button id="langBtn" class="btn btn-outline-info btn-sm" onclick="toggleLanguage()">English</button>
                <button id="logoutBtn" class="btn btn-danger btn-sm" onclick="handleLogout()">خروج</button>
            </div>
        </div>
        <div class="card-body">
            <div id="loading" class="text-center py-4">جاري تحميل البيانات...</div>

            <table class="table table-hover d-none" id="usersTable">
                <thead>
                    <tr>
                        <th id="thName">الاسم</th>
                        <th id="thEmail">البريد الإلكتروني</th>
                        <th id="thRole">الدور</th>
                        <th id="thAction">الإجراء</th>
                    </tr>
                </thead>
                <tbody id="userRows"></tbody>
            </table>

            <div id="noData" class="text-center d-none py-4">لا توجد طلبات معلقة حالياً.</div>
        </div>
    </div>
</div>

<script>
    // 1. القاموس اللغوي
    const translations = {
        ar: {
            title: "لوحة التحكم",
            header: "طلبات التسجيل بانتظار الموافقة",
            logout: "خروج",
            langBtn: "English",
            name: "الاسم",
            email: "البريد الإلكتروني",
            role: "الدور",
            action: "الإجراء",
            approve: "موافقة",
            loading: "جاري تحميل البيانات...",
            noData: "لا توجد طلبات معلقة حالياً.",
            confirmApprove: "هل أنت متأكد من تفعيل هذا الحساب؟",
            confirmLogout: "هل تريد تسجيل الخروج؟",
            dir: "rtl",
            boot: "https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css"
        },
        en: {
            title: "Dashboard",
            header: "Pending Registration Requests",
            logout: "Logout",
            langBtn: "العربية",
            name: "Name",
            email: "Email Address",
            role: "Role",
            action: "Action",
            approve: "Approve",
            loading: "Loading data...",
            noData: "No pending requests found.",
            confirmApprove: "Are you sure you want to activate this account?",
            confirmLogout: "Do you want to logout?",
            dir: "ltr",
            boot: "https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        }
    };

    const token = localStorage.getItem('admin_token');

    // 2. وظائف اللغة
    function applyLanguage(lang) {
        const t = translations[lang];
        document.getElementById('mainHtml').dir = t.dir;
        document.getElementById('mainHtml').lang = lang;
        document.getElementById('bootstrapStyle').href = t.boot;

        document.getElementById('pageTitle').innerText = t.title;
        document.getElementById('headerTitle').innerText = t.header;
        document.getElementById('logoutBtn').innerText = t.logout;
        document.getElementById('langBtn').innerText = t.langBtn;
        document.getElementById('thName').innerText = t.name;
        document.getElementById('thEmail').innerText = t.email;
        document.getElementById('thRole').innerText = t.role;
        document.getElementById('thAction').innerText = t.action;
        document.getElementById('loading').innerText = t.loading;
        document.getElementById('noData').innerText = t.noData;

        localStorage.setItem('lang', lang);
        if(window.lastData) renderTable(window.lastData);
    }

    function toggleLanguage() {
        const newLang = localStorage.getItem('lang') === 'ar' ? 'en' : 'ar';
        applyLanguage(newLang);
    }

    // 3. وظائف الوضع الداكن
    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        document.getElementById('themeBtn').innerText = theme === 'dark' ? '☀️' : '🌙';
        localStorage.setItem('theme', theme);
    }

    function toggleTheme() {
        const newTheme = localStorage.getItem('theme') === 'dark' ? 'light' : 'dark';
        applyTheme(newTheme);
    }

    // 4. منطق البيانات (API)
    async function loadPendingUsers() {
        try {
            const response = await fetch('/api/admin/pending-users', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (response.status === 401) {
                window.location.href = '/login';
                return;
            }

            const users = await response.json();
            window.lastData = users; // حفظ البيانات للرندرة عند تغيير اللغة
            renderTable(users);
        } catch (error) {
            console.error('Fetch error:', error);
        }
    }

    function renderTable(users) {
        const table = document.getElementById('usersTable');
        const rows = document.getElementById('userRows');
        const loading = document.getElementById('loading');
        const noData = document.getElementById('noData');
        const currentLang = localStorage.getItem('lang') || 'ar';

        loading.classList.add('d-none');

        if (users.length === 0) {
            noData.classList.remove('d-none');
            table.classList.add('d-none');
            return;
        }

        table.classList.remove('d-none');
        noData.classList.add('d-none');
        rows.innerHTML = users.map(user => `
            <tr>
                <td>${user.name}</td>
                <td>${user.email}</td>
                <td><span class="badge bg-info">${user.role}</span></td>
                <td>
                    <button class="btn btn-success btn-sm" onclick="approveUser(${user.id})">
                        ${translations[currentLang].approve}
                    </button>
                </td>
            </tr>
        `).join('');
    }

    async function approveUser(id) {
        const currentLang = localStorage.getItem('lang') || 'ar';
        if (!confirm(translations[currentLang].confirmApprove)) return;

        try {
            await fetch(`/api/admin/approve/${id}`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            loadPendingUsers();
        } catch (error) {
            alert('Error updating user');
        }
    }

    async function handleLogout() {
        const currentLang = localStorage.getItem('lang') || 'ar';
        if (!confirm(translations[currentLang].confirmLogout)) return;
        localStorage.removeItem('admin_token');
        window.location.href = '/login';
    }

    // التشغيل عند التحميل
    window.onload = () => {
        applyLanguage(localStorage.getItem('lang') || 'ar');
        applyTheme(localStorage.getItem('theme') || 'light');
        if (token) loadPendingUsers();
    };
</script>
</body>
</html>
