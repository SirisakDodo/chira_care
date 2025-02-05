document.addEventListener("DOMContentLoaded", function () {
    const uploadArea = document.getElementById("uploadArea");
    const fileInput = document.getElementById("csvInput");
    const uploadText = document.getElementById("uploadText");

    // เมื่อคลิกพื้นที่อัปโหลด ให้เปิด file dialog
    uploadArea.addEventListener("click", function () {
        fileInput.click();
    });

    // เมื่อมีการเลือกไฟล์ผ่าน input
    fileInput.addEventListener("change", function () {
        if (this.files.length > 0) {
            uploadText.textContent = "📄 ไฟล์ที่เลือก: " + this.files[0].name;
        }
    });

    // ป้องกันการเปิดไฟล์โดยตรงในเบราว์เซอร์
    uploadArea.addEventListener("dragover", function (e) {
        e.preventDefault();
        uploadArea.style.backgroundColor = "#e9ecef"; // เปลี่ยนสีเมื่อกำลังลากไฟล์เข้า
    });

    uploadArea.addEventListener("dragleave", function () {
        uploadArea.style.backgroundColor = "#f8f9fa"; // คืนค่าสีเดิม
    });

    // ดรอปไฟล์ลงในช่องอัปโหลด
    uploadArea.addEventListener("drop", function (e) {
        e.preventDefault();
        uploadArea.style.backgroundColor = "#f8f9fa";

        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files; // อัปเดตไฟล์ที่เลือก
            uploadText.textContent = "📄 ไฟล์ที่เลือก: " + e.dataTransfer.files[0].name;
        }
    });
});