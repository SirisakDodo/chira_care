function confirmDelete(id, name) {
    Swal.fire({
        title: `ต้องการลบ ${name} ใช่ไหม?`,
        text: "กดตกลงเพื่อลบ หรือ ยกเลิก",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "ตกลง",
        cancelButtonText: "ยกเลิก"
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: "กำลังลบ...", allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ id: id })
            })
                .then(response => response.json())
                .then(data => {
                    Swal.fire({ icon: data.success ? 'success' : 'error', title: data.message, confirmButtonText: 'ตกลง' })
                        .then(() => { if (data.success) location.reload(); });
                });
        }
    })
}

function editTraining(id, name, status) {
    Swal.fire({
        title: "แก้ไขการฝึกอบรม",
        html: `
            <div style="display: flex; flex-direction: column; gap: 15px; text-align: left; width: 100%; max-width: 500px; margin: auto; align-items: center;">
                <div style="display: flex; flex-direction: column; width: 100%; margin: 0;">
                    <label for="swal-training-name" style="font-weight: bold; margin-bottom: 5px;">ชื่อหน่วยฝึกอบรม</label>
                    <input id="swal-training-name" class="swal2-input" placeholder="ชื่อหน่วยฝึกอบรม" value="${name}"
                        style="width: 100%; box-sizing: border-box; padding: 12px; border-radius: 8px; border: 1px solid #ccc; height: 45px; font-size: 14px; margin-left: 0;">
                </div>
                <div style="display: flex; flex-direction: column; width: 100%; margin: 0;">
                    <label for="swal-training-status" style="font-weight: bold; margin-bottom: 5px;">สถานะ</label>
                    <select id="swal-training-status" class="swal2-select"
                        style="width: 100%; box-sizing: border-box; padding: 12px; border-radius: 8px; border: 1px solid #ccc; height: 45px; font-size: 14px; margin-left: 0;">
                        <option value="active" ${status === 'active' ? 'selected' : ''}>Active</option>
                        <option value="inactive" ${status === 'inactive' ? 'selected' : ''}>Inactive</option>
                    </select>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: "บันทึก",
        cancelButtonText: "ยกเลิก",
        preConfirm: () => ({
            id: id,
            training_unit: document.getElementById("swal-training-name").value.trim(),
            training_status: document.getElementById("swal-training-status").value
        })
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ update_training: true, id: result.value.id, training_unit: result.value.training_unit, training_status: result.value.training_status })
            })
                .then(response => response.json())
                .then(data => {
                    Swal.fire({ icon: data.success ? 'success' : 'error', title: data.message, confirmButtonText: 'ตกลง' })
                        .then(() => { if (data.success) location.reload(); });
                })
                .catch(error => Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: error.message }));
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById('statusFilter')?.addEventListener('change', function () {
        window.location.href = `?status=${this.value}`;
    })
});