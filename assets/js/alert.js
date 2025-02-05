document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("soldierForm").addEventListener("submit", function (event) {
        event.preventDefault(); // ป้องกันการส่งฟอร์มทันที
        let form = this;

        Swal.fire({
            title: "ยืนยันการเพิ่มข้อมูล",
            text: "คุณต้องการเพิ่มข้อมูลทหารใช่หรือไม่?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                // แสดงสถานะโหลด
                Swal.fire({
                    title: "กำลังนำทหารเข้าสู่ฐานข้อมูล...",
                    text: "กรุณารอสักครู่",
                    icon: "info",
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading(); // แสดงแอนิเมชันโหลด
                    }
                });

                // ส่งฟอร์มโดยใช้ AJAX
                fetch(form.action, {
                    method: form.method,
                    body: new FormData(form)
                })
                    .then(response => response.text())
                    .then(data => {
                        console.log(data); // Debugging response

                        if (data.includes("success")) {
                            Swal.fire({
                                title: "สำเร็จ!",
                                text: "ทหารถูกเพิ่มเข้าสู่ฐานข้อมูลเรียบร้อย",
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then(() => {
                                window.location.href = "add_soldiers_form.php"; // รีเฟรชหน้า
                            });
                        } else {
                            Swal.fire({
                                title: "ไม่สำเร็จ!",
                                text: "มีข้อผิดพลาด กรุณาลองใหม่อีกครั้ง",
                                icon: "error",
                                confirmButtonText: "OK"
                            });
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        Swal.fire({
                            title: "ไม่สำเร็จ!",
                            text: "การส่งข้อมูลล้มเหลว",
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    });
            }
        });
    });
});