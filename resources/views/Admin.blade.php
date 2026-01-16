<table class="table">
    <thead>
        <tr>
            <th>الاسم</th>
            <th>الدور</th>
            <th>الإجراء</th>
        </tr>
    </thead>
    <tbody id="pending-users">
        </tbody>
</table>

<script>
    // عند الضغط على زر موافقة
    function approve(userId) {
        fetch(`/api/admin/approve/${userId}`, { method: 'POST' })
            .then(res => res.json())
            .then(data => alert('تم التفعيل!'));
    }
</script>