# Content for the AI Agent Technical Brief
content = """PROJECT BRIEF: TRACKERIN - AI LEARNING TRACKER

1. INFORMASI UMUM
- Nama Proyek: Trackerin
- Tema: AI-powered Personal Learning Tracker
- Tujuan: Membantu pengguna (pelajar/mahasiswa) merencanakan, melacak, dan mengoptimalkan proses belajar mereka menggunakan teknologi AI.
- Konteks: Tugas akhir gabungan mata kuliah Pemrograman Web, Pengembangan Aplikasi Bergerak, dan Rekayasa Perangkat Lunak (RPL).

2. TECH STACK (FINAL DECISION)
- Backend & Web Frontend: Laravel Fullstack (Monolith for Web Dashboard, but providing API for Mobile).
- Mobile App: Native Kotlin (Android Studio).
- Database: PostgreSQL (Relational Database).
- AI Integration: API pihak ketiga (OpenAI/Gemini) untuk pemrosesan cerdas.
- Authentication: Laravel Sanctum (Session-based untuk Web, Token-based untuk Kotlin).
- Infrastructure: Dockerized environment (Laravel & PostgreSQL) deployed on VPS (2 Core, 4GB RAM).

3. FITUR UTAMA (8 REQUIREMENTS)
1. User Authentication: Register/Login (Email & Google Sign-In) via Firebase/Sanctum.
2. Personalized Dashboard: Visualisasi progres belajar dan statistik dalam bentuk grafik.
3. AI Curriculum Generator: Menghasilkan roadmap/silabus belajar otomatis berdasarkan topik inputan user.
4. Learning Progress Tracker: Pencatatan status penyelesaian materi secara dinamis (persentase).
5. Smart Search Feature: Pencarian cerdas untuk materi, catatan, dan topik terkait.
6. AI-Driven Recommendation: Saran materi tambahan (artikel/video) berdasarkan data aktivitas belajar.
7. Smart Notification & Reminder: Push Notification pengingat jadwal belajar via mobile.
8. Knowledge Assessment (Quiz): Kuis otomatis berbasis AI untuk validasi pemahaman materi.
9. CRUD TODO
10. CRUD NOTES

4. ARSITEKTUR TEKNIS
- Pendekatan: API-First Design dalam lingkungan Laravel.
- Web: Laravel menangani view dashboard secara langsung (Blade/Inertia).
- Mobile: Aplikasi Kotlin melakukan HTTP Request ke endpoint API yang disediakan oleh Laravel yang sama.
- Data Flow: User Input -> Laravel Controller -> Service Layer (AI/Logic) -> PostgreSQL -> Response (JSON/View).
"""
