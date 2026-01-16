<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <style>
        body { background: #f4f7f6; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: sans-serif; }
        .login-card { width: 100%; max-width: 400px; padding: 25px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border-radius: 12px; background: #fff; }
        #otpSection { display: none; } 
    </style>
</head>
<body>  

<div class="card login-card">
    <h3 class="text-center mb-4">تسجيل الدخول</h3>
    
    <form id="loginForm">
        <div id="credentialsSection">
            <div class="mb-3">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" id="email" class="form-control" required placeholder="admin@example.com">
            </div>
            <div class="mb-3">
                <label class="form-label">رقم الهاتف</label>
                <input type="tel" id="phone" class="form-control" required placeholder="09xxxxxxxx">
            </div>
            <div class="mb-3">
                <label class="form-label">كلمة المرور</label>
                <input type="password" id="password" class="form-control" required>
            </div>
        </div>

        <div id="otpSection" class="mb-3 border-top pt-3 mt-3">
            <label class="form-label text-primary font-weight-bold">رمز الـ OTP (وصلك الآن على إيميلك)</label>
            <input type="text" id="otp_code" class="form-control" placeholder="123456">
        </div>

        <button type="submit" id="submitBtn" class="btn btn-primary w-100">متابعة</button>
    </form>
    
    <div id="message" class="mt-3 text-center small"></div>
</div>

<script>
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const messageDiv = document.getElementById('message');
        const otpSection = document.getElementById('otpSection');
        const submitBtn = document.getElementById('submitBtn');
        
        messageDiv.innerText = 'جاري المعالجة...';

        const payload = {
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            password: document.getElementById('password').value,
            otp_code: document.getElementById('otp_code').value || null
        };

        try {
            const response = await fetch('/api/login', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'Accept': 'application/json' 
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (response.status === 200 && data.requires_otp) {
                otpSection.style.display = 'block';
                document.getElementById('otp_code').setAttribute('required', 'true');
                submitBtn.innerText = 'تأكيد الدخول';
                messageDiv.innerHTML = `<span class="text-info">${data.message}</span>`;
            } 
            else if (response.ok) {
                localStorage.setItem('admin_token', data.token);
                messageDiv.innerHTML = '<span class="text-success">تم تسجيل الدخول بنجاح! جاري التحويل...</span>';
                
                if (data.user.role === 'admin') {
                    window.location.replace('/admin/dashboard');
                } else {
                    messageDiv.innerHTML = '<span class="text-success">أهلاً بك، تم تسجيل الدخول.</span>';
                }
            } 
            else {
                messageDiv.innerHTML = `<span class="text-danger">${data.message}</span>`;
            }
        } catch (error) {
            messageDiv.innerHTML = '<span class="text-danger">حدث خطأ في الاتصال بالسيرفر</span>';
        }
    });
</script>

</body>
</html>
