<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم الإدارة</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
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
        <div class="card-header bg-dark text-white">
            <h4 class="mb-0">طلبات التسجيل بانتظار الموافقة</h4>
            <button class="btn btn-danger btn-sm" onclick="handleLogout()">تسجيل الخروج</button>
</div>

<script>
    async function handleLogout() {
        if (!confirm('هل تريد تسجيل الخروج؟')) return;

        try {
            const response = await fetch('/api/logout', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
                    'Accept': 'application/json'
                }
            });

            localStorage.removeItem('admin_token');
            
            alert('تم تسجيل الخروج بنجاح');
            window.location.href = '/login'; 
        } catch (error) {
            console.error('Logout failed:', error);
            localStorage.removeItem('admin_token');
            window.location.href = '/login';
        }
    }
</script>
        </div>
        <div class="card-body">
            <div id="loading" class="text-center">جاري تحميل البيانات...</div>
            
            <table class="table table-hover d-none" id="usersTable">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الدور</th>
                        <th>الإجراء</th>
                    </tr>
                </thead>
                <tbody id="userRows"></tbody>
            </table>
            
            <div id="noData" class="text-center d-none">لا توجد طلبات معلقة حالياً.</div>
        </div>
    </div>
</div>

<script>
    const token = localStorage.getItem('admin_token'); 

    async function loadPendingUsers() {
        try {
            const response = await fetch('/api/admin/pending-users', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (response.status === 401 || response.status === 403) {
                alert('غير مصرح لك بالدخول أو انتهت جلستك');
                return;
            }

            const users = await response.json();
            renderTable(users);
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function renderTable(users) {
        const table = document.getElementById('usersTable');
        const rows = document.getElementById('userRows');
        const loading = document.getElementById('loading');
        const noData = document.getElementById('noData');

        loading.classList.add('d-none');
        
        if (users.length === 0) {
            noData.classList.remove('d-none');
            table.classList.add('d-none');
            return;
        }

        table.classList.remove('d-none');
        rows.innerHTML = users.map(user => `
            <tr>
                <td>${user.name}</td>
                <td>${user.email}</td>
                <td><span class="badge bg-info">${user.role}</span></td>
                <td>
                    <button class="btn btn-success btn-sm" onclick="approveUser(${user.id})">موافقة</button>
                </td>
            </tr>
        `).join('');
    }

    async function approveUser(id) {
        if (!confirm('هل أنت متأكد من تفعيل هذا الحساب؟')) return;

        try {
            const response = await fetch(`/api/admin/approve/${id}`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();
            alert(result.message);
            loadPendingUsers();
        } catch (error) {
            alert('حدث خطأ أثناء العملية');
        }
    }

    if (token) {
        loadPendingUsers();
    } else {
        alert('الرجاء تسجيل الدخول كأدمن أولاً');
    }
</script>

</body>
</html>