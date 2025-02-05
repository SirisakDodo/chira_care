$(document).on("click", ".delete-soldier", function () {
    var soldierId = $(this).data("id");

    Swal.fire({
        title: "คุณแน่ใจหรือไม่?",
        text: "การลบข้อมูลนี้จะไม่สามารถกู้คืนได้!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "ใช่, ลบเลย!",
        cancelButtonText: "ยกเลิก",
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "กำลังลบข้อมูลทหาร...",
                text: "โปรดรอสักครู่",
                icon: "info",
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "../../function/delete_soldier.php",
                type: "POST",
                data: { soldier_id_card: soldierId },
                dataType: "json",
                success: function (response) {
                    if (response.status === "success") {
                        Swal.fire("ลบสำเร็จ!", response.message, "success").then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire("เกิดข้อผิดพลาด!", response.message, "error");
                    }
                },
                error: function () {
                    Swal.fire("เกิดข้อผิดพลาด!", "ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้", "error");
                }
            });
        }
    });
});
