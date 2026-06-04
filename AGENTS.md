# Content for the AI Agent Technical Brief v2.0
content = """PROJECT BRIEF: TRACKERIN - AI LEARNING TRACKER

1. INFORMASI UMUM
- Nama Proyek: Trackerin
- Tema: AI-powered Personal Learning Tracker
- Konteks: Tugas akhir gabungan mata kuliah Pemweb, Mobdev, dan RPL.

2. TECH STACK
- Backend & Web Frontend: Laravel Fullstack (Monolith for Web, API for Mobile).
- Mobile App: Native Kotlin (Android Studio).
- Database: PostgreSQL.
- AI Integration: OpenAI/Gemini API.
- Authentication: Laravel Sanctum.
- Infrastructure: Dockerized environment on VPS (2 Core, 4GB RAM).

3. FITUR UTAMA (REVISED)
1. User Authentication: Register/Login via Sanctum (Email & Google Sign-In).
2. Personalized Dashboard: Visualisasi progres dan statistik belajar.
3. AI Curriculum Generator: Roadmap belajar otomatis berdasarkan topik input user.
4. Learning Progress Tracker: Pencatatan status penyelesaian materi (milestones).
5. Smart Search Feature: Pencarian cerdas materi dan topik.
6. Smart Notification & Reminder: Push Notification pengingat via mobile.
7. Knowledge Assessment (Quiz): Kuis otomatis berbasis AI per milestone.
8. Notes: Fitur pencatatan personal per materi.
9. To-dos: Daftar tugas harian pengguna.

4. ARSITEKTUR TEKNIS
- Pendekatan: API-First Design.
- Data Flow: Laravel melayani Web Dashboard (Blade/Inertia) dan memberikan JSON response untuk aplikasi Kotlin via REST API.

5. STYLE GUIDE & VISUAL IDENTITY
- Typography: Font Geist dengan -6% letter-spacing (-0.06em) yang diterapkan secara global.
- Color Style:
  * Main Blue Color: #2F6CF9 (Utility: `main-blue`)
  * Blue Hover/Click: #275ACF (Utility: `hover-blue`)
  * White Background: #ECF0F1 (Utility: `white-bg`)
  * White Pure for Card/Field/Icon: #FFFFFF (Utility: `white-pure`)
  * Dark Text & Icon Color: #091632 (Utility: `dark-text`)
  * Grey Text Color: #6B7280 (Utility: `grey-text`)
  * Semi Dark Color: #102453 (Utility: `semi-dark`)
- Design Direction:
  * Landing Page: Didominasi tipografi tebal dan besar (typography-dominant) layaknya situs creative studio modern dengan navigasi minimalis dan transisi mikro yang mulus.
  * Web Dashboard: Tampilan bento grid yang clean, terorganisasi dengan baik untuk statistik belajar, to-dos, notes, dan data milestone kurikulum.
"""
