document.addEventListener('DOMContentLoaded', () => {
    // Initialize Dashboard Controller
    window.trackerinDashboard = new TrackerinDashboard();
});

class TrackerinDashboard {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        this.state = {
            user: {
                name: 'User',
                email: '',
                profile_image: null,
                occupation: localStorage.getItem('user_occupation') || 'Pendidikan belum diatur',
                specialization: localStorage.getItem('user_specialization') || 'Minat belum diatur'
            },
            curriculums: [],
            activeCurriculum: null,
            todos: [],
            notes: [],
            notifications: [],
            activeRoadmapId: null
        };

        this.init();
    }

    async init() {
        // Setup initial UI states, local streak, and active dates
        this.updateStreakAndActiveDays();
        this.checkAndResetWeeklyActivity();
        
        // Setup Event Listeners first (immediate navigation responsiveness)
        this.setupNavigation();
        this.setupTodoListeners();
        this.setupExploreListeners();
        this.setupProfileListeners();
        this.setupNotesListeners();

        // Fetch User and Core Datasets in parallel safely
        try {
            await Promise.allSettled([
                this.fetchUser(),
                this.fetchCurriculums(),
                this.fetchTodos(),
                this.fetchNotes(),
                this.fetchNotifications()
            ]);
        } catch (e) {
            console.error('Error fetching initial data:', e);
        }
        
        // Initial Rendering
        this.renderAll();
    }

    // ==========================================
    // API HELPERS
    // ==========================================
    async apiCall(url, method = 'GET', body = null) {
        const headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };

        if (this.csrfToken) {
            headers['X-CSRF-TOKEN'] = this.csrfToken;
        }

        const options = { method, headers };

        if (body) {
            headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(body);
        }

        try {
            const response = await fetch(url, options);
            if (!response.ok) {
                const errData = await response.json().catch(() => ({}));
                throw new Error(errData.message || `Request failed with status ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error(`API Error on ${url}:`, error);
            throw error;
        }
    }

    // ==========================================
    // STREAK & LOCAL ACTIVITY LOGIC (1-to-1 with Mobile)
    // ==========================================
    updateStreakAndActiveDays() {
        const todayStr = this.getTodayDateString();
        const lastActive = localStorage.getItem('last_active_date');
        let activeDates = JSON.parse(localStorage.getItem('active_dates') || '[]');
        
        // Initialize today's weekday activity to 0f if not set
        const dayOfWeekStr = this.getTodayWeekdayString();
        const key = `activity_${dayOfWeekStr}`;
        if (localStorage.getItem(key) === null) {
            localStorage.setItem(key, '0');
        }

        if (!activeDates.includes(todayStr)) {
            activeDates.push(todayStr);
            localStorage.setItem('active_dates', JSON.stringify(activeDates));
        }

        if (!lastActive) {
            localStorage.setItem('current_streak', '1');
            localStorage.setItem('last_active_date', todayStr);
            return;
        }

        if (lastActive === todayStr) {
            return;
        }

        const yesterdayStr = this.getYesterdayDateString();
        if (lastActive === yesterdayStr) {
            const currentStreak = parseInt(localStorage.getItem('current_streak') || '0', 10);
            localStorage.setItem('current_streak', (currentStreak + 1).toString());
        } else {
            localStorage.setItem('current_streak', '1');
        }
        localStorage.setItem('last_active_date', todayStr);
    }

    checkAndResetWeeklyActivity() {
        const today = new Date();
        const currentYear = today.getFullYear();
        const currentWeek = this.getWeekNumber(today);
        
        const storedWeek = parseInt(localStorage.getItem('stored_week_of_year') || '-1', 10);
        const storedYear = parseInt(localStorage.getItem('stored_year') || '-1', 10);

        if (storedWeek !== currentWeek || storedYear !== currentYear) {
            localStorage.setItem('stored_week_of_year', currentWeek.toString());
            localStorage.setItem('stored_year', currentYear.toString());
            
            // Set all weekdays to -1 (unset/inactive)
            const weekdays = ["MON", "TUE", "WED", "THU", "FRI", "SAT", "SUN"];
            weekdays.forEach(day => {
                localStorage.setItem(`activity_${day}`, '-1');
            });
        }
    }

    incrementDailyActivity(amount = 20) {
        this.checkAndResetWeeklyActivity();
        const dayOfWeekStr = this.getTodayWeekdayString();
        const key = `activity_${dayOfWeekStr}`;
        
        const current = parseFloat(localStorage.getItem(key) || '-1');
        const base = current < 0 ? 0 : current;
        const newValue = Math.min(base + amount, 100);
        
        localStorage.setItem(key, newValue.toString());
        this.renderWeeklyChart();
    }

    getDailyActivity(day) {
        this.checkAndResetWeeklyActivity();
        
        const weekdays = ["MON", "TUE", "WED", "THU", "FRI", "SAT", "SUN"];
        const currentDayStr = this.getTodayWeekdayString();
        const currentDayIndex = weekdays.indexOf(currentDayStr);
        const targetDayIndex = weekdays.indexOf(day);

        // Future day: inactive
        if (targetDayIndex > currentDayIndex) {
            return -1;
        }

        return parseFloat(localStorage.getItem(`activity_${day}`) || '-1');
    }

    // ==========================================
    // CORE FETCHING
    // ==========================================
    async fetchUser() {
        try {
            const res = await this.apiCall('/api/v1/user');
            if (res.data) {
                this.state.user.name = res.data.name;
                this.state.user.email = res.data.email;
                this.state.user.profile_image = res.data.profile_image;
            }
        } catch (e) {
            this.showToast('Gagal memuat profil user', 'error');
        }
    }

    async fetchCurriculums() {
        try {
            const res = await this.apiCall('/api/v1/curriculums');
            this.state.curriculums = res.data || [];
            
            // Active path is the first incomplete roadmap
            this.state.activeCurriculum = this.state.curriculums.find(c => (c.total_progress || 0) < 100) || this.state.curriculums[0] || null;
        } catch (e) {
            this.showToast('Gagal memuat kurikulum', 'error');
        }
    }

    async fetchTodos() {
        try {
            const res = await this.apiCall('/api/v1/todos');
            this.state.todos = res.data || [];
        } catch (e) {
            this.showToast('Gagal memuat tugas harian', 'error');
            this.state.todos = [];
        }
    }

    async fetchNotes() {
        try {
            const res = await this.apiCall('/api/v1/notes');
            this.state.notes = res.data || [];
        } catch (e) {
            this.showToast('Gagal memuat catatan belajar', 'error');
            this.state.notes = [];
        }
    }

    async fetchNotifications() {
        try {
            const res = await this.apiCall('/api/v1/notifications');
            this.state.notifications = res.data || [];
        } catch (e) {
            console.error('Failed to load notifications');
        }
    }

    // ==========================================
    // EVENT LISTENERS & ROUTING
    // ==========================================
    setupNavigation() {
        const navs = document.querySelectorAll('.nav-link');
        const views = document.querySelectorAll('.view-pane');

        navs.forEach(nav => {
            nav.addEventListener('click', (e) => {
                e.preventDefault();
                const targetViewId = nav.getAttribute('data-view');
                
                navs.forEach(n => n.classList.remove('active-nav-link'));
                nav.classList.add('active-nav-link');

                views.forEach(v => v.classList.add('hidden'));
                const targetView = document.getElementById(targetViewId);
                targetView.classList.remove('hidden');

                // Animate entry with GSAP
                if (window.gsap) {
                    window.gsap.fromTo(targetView, { opacity: 0, y: 15 }, { opacity: 1, y: 0, duration: 0.4 });
                }

                // Custom actions on tab enter
                if (targetViewId === 'view-dashboard') {
                    this.renderDashboard();
                } else if (targetViewId === 'view-roadmaps') {
                    this.renderRoadmaps();
                } else if (targetViewId === 'view-todo') {
                    this.renderFullTodos();
                } else if (targetViewId === 'view-notes') {
                    this.renderFullNotes();
                } else if (targetViewId === 'view-profile') {
                    this.renderProfile();
                }
            });
        });
    }

    setupTodoListeners() {
        const form = document.getElementById('quick-add-todo-form');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const titleInput = document.getElementById('todo-title-input');
                const title = titleInput.value.trim();
                
                if (!title) return;

                // Format as JSON payload matching mobile structures
                const taskJson = JSON.stringify({
                    title: title,
                    desc: '',
                    due: ''
                });

                try {
                    await this.apiCall('/api/v1/todos', 'POST', { task: taskJson });
                    titleInput.value = '';
                    await this.fetchTodos();
                    this.renderTodos();
                    this.renderFullTodos();
                    this.renderStats();
                    this.showToast('Tugas ditambahkan!', 'success');
                } catch (err) {
                    this.showToast('Gagal menambahkan tugas', 'error');
                }
            });
        }

        const fullForm = document.getElementById('full-todo-form');
        if (fullForm) {
            fullForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const editId = document.getElementById('edit-todo-id').value;
                const title = document.getElementById('todo-task-title').value.trim();
                const desc = document.getElementById('todo-task-desc').value.trim();
                const due = document.getElementById('todo-task-due').value;

                if (!title) return;

                const taskJson = JSON.stringify({
                    title: title,
                    desc: desc,
                    due: due
                });

                try {
                    const payload = { 
                        task: taskJson,
                        due_date: due || null
                    };

                    if (editId) {
                        await this.apiCall(`/api/v1/todos/${editId}`, 'PUT', payload);
                        this.showToast('Tugas berhasil diperbarui!', 'success');
                    } else {
                        await this.apiCall('/api/v1/todos', 'POST', payload);
                        this.showToast('Tugas berhasil ditambahkan!', 'success');
                    }

                    this.cancelEditTodo();
                    await this.fetchTodos();
                    this.renderTodos();
                    this.renderFullTodos();
                    this.renderStats();
                } catch (err) {
                    this.showToast(err.message || 'Gagal menyimpan tugas', 'error');
                }
            });
        }
    }

    setupExploreListeners() {
        const form = document.getElementById('ai-generate-form');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const input = document.getElementById('explore-search-input');
                const topic = input.value.trim();
                if (!topic) return;

                const loader = document.getElementById('ai-generation-loader');
                loader.classList.remove('hidden');
                
                // Cycle loading messages to look premium
                const messages = [
                    "Menganalisis topik pelajaran Anda...",
                    "Menyusun tahapan kurikulum terstruktur...",
                    "Merancang bahan materi AI Assistant...",
                    "Memformat jalur pembelajaran optimal Anda..."
                ];
                let msgIdx = 0;
                const msgEl = document.getElementById('loader-text-status');
                
                const interval = setInterval(() => {
                    msgIdx = (msgIdx + 1) % messages.length;
                    if (msgEl) msgEl.innerText = messages[msgIdx];
                }, 3000);

                try {
                    const res = await this.apiCall('/api/v1/curriculums/generate', 'POST', { topic });
                    clearInterval(interval);
                    loader.classList.add('hidden');
                    input.value = '';
                    
                    await this.fetchCurriculums();
                    this.showToast('Kurikulum berhasil dibuat!', 'success');
                    
                    // Navigate to roadmaps tab and view the generated curriculum
                    if (res.data) {
                        this.state.activeRoadmapId = res.data.id;
                    }
                    document.querySelector('[data-view="view-roadmaps"]')?.click();
                } catch (err) {
                    clearInterval(interval);
                    loader.classList.add('hidden');
                    this.showToast(err.message || 'Gagal membuat kurikulum via AI', 'error');
                }
            });
        }

        // Live filtering
        const searchBar = document.getElementById('explore-search-filter');
        if (searchBar) {
            searchBar.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase();
                const cards = document.querySelectorAll('.explore-suggested-card');
                cards.forEach(card => {
                    const title = card.querySelector('.suggested-title')?.innerText.toLowerCase() || '';
                    if (title.includes(query)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }
    }

    setupProfileListeners() {
        const form = document.getElementById('profile-edit-form');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const name = document.getElementById('profile-name-input').value.trim();
                const email = document.getElementById('profile-email-input').value.trim();
                const occ = document.getElementById('profile-occ-input').value.trim();
                const spec = document.getElementById('profile-spec-input').value.trim();

                try {
                    const payload = { name, email };

                    await this.apiCall('/api/v1/user', 'PUT', payload);
                    
                    // Save custom occupation and specialization locally (localStorage)
                    localStorage.setItem('user_occupation', occ);
                    localStorage.setItem('user_specialization', spec);
                    this.state.user.occupation = occ;
                    this.state.user.specialization = spec;

                    await this.fetchUser();
                    this.renderProfileHeader();
                    this.showToast('Profil berhasil diperbarui!', 'success');
                } catch (err) {
                    this.showToast(err.message || 'Gagal memperbarui profil', 'error');
                }
            });
        }

        // Contact Us inside Profile
        const contactForm = document.getElementById('dashboard-contact-form');
        if (contactForm) {
            contactForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const name = document.getElementById('contact-name').value.trim();
                const email = document.getElementById('contact-email').value.trim();
                const subject = document.getElementById('contact-subject').value.trim();
                const message = document.getElementById('contact-message').value.trim();

                try {
                    await this.apiCall('/api/v1/contact', 'POST', { name, email, subject, message });
                    this.showToast('Pesan dikirim! Terima kasih.', 'success');
                    contactForm.reset();
                } catch (err) {
                    this.showToast('Gagal mengirimkan pesan.', 'error');
                }
            });
        }

        // Click on profile avatar to choose image upload
        const avatarClickable = document.getElementById('profile-avatar-clickable');
        const avatarInput = document.getElementById('avatar-file-input');
        
        if (avatarClickable && avatarInput) {
            avatarClickable.addEventListener('click', () => {
                avatarInput.click();
            });

            avatarInput.addEventListener('change', async () => {
                if (avatarInput.files.length === 0) return;
                
                const file = avatarInput.files[0];
                const formData = new FormData();
                formData.append('profile_image', file);

                try {
                    this.showToast('Mengunggah foto profil...', 'info');

                    const headers = {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    };
                    if (this.csrfToken) {
                        headers['X-CSRF-TOKEN'] = this.csrfToken;
                    }

                    const response = await fetch('/api/v1/user/avatar', {
                        method: 'POST',
                        headers: headers,
                        body: formData
                    });

                    if (!response.ok) {
                        const errData = await response.json().catch(() => ({}));
                        throw new Error(errData.message || `Upload failed with status ${response.status}`);
                    }

                    const res = await response.json();
                    if (res.profile_image) {
                        this.state.user.profile_image = res.profile_image;
                        this.renderProfileAvatars();
                        this.showToast('Foto profil berhasil diperbarui!', 'success');
                    }
                } catch (err) {
                    this.showToast(err.message || 'Gagal mengunggah foto profil', 'error');
                } finally {
                    avatarInput.value = '';
                }
            });
        }
    }

    setupNotesListeners() {
        const form = document.getElementById('full-note-form');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const editId = document.getElementById('edit-note-id').value;
                const title = document.getElementById('note-title').value.trim();
                const content = document.getElementById('note-content').value.trim();

                if (!title || !content) return;

                try {
                    const payload = { title, content, milestone_id: null };

                    if (editId) {
                        const existingNote = this.state.notes.find(n => n.id === parseInt(editId));
                        if (existingNote && existingNote.milestone_id) {
                            payload.milestone_id = existingNote.milestone_id;
                        }
                        await this.apiCall(`/api/v1/notes/${editId}`, 'PUT', payload);
                        this.showToast('Catatan berhasil diperbarui!', 'success');
                    } else {
                        await this.apiCall('/api/v1/notes', 'POST', payload);
                        this.showToast('Catatan berhasil dibuat!', 'success');
                    }

                    this.cancelEditNote();
                    await this.fetchNotes();
                    this.renderSavedNotes();
                    this.renderFullNotes();
                } catch (err) {
                    this.showToast(err.message || 'Gagal menyimpan catatan', 'error');
                }
            });
        }

        const filterInput = document.getElementById('notes-search-filter');
        if (filterInput) {
            filterInput.addEventListener('input', () => {
                this.renderFullNotes();
            });
        }
    }

    // ==========================================
    // RENDERERS
    // ==========================================
    renderAll() {
        this.renderDashboard();
        this.renderRoadmaps();
        this.renderFullTodos();
        this.renderFullNotes();
        this.renderProfile();
    }

    renderDashboard() {
        this.renderStats();
        this.renderActivePath();
        this.renderTodos();
        this.renderWeeklyChart();
    }

    renderStats() {
        // Calculations
        const completedMilestones = this.state.curriculums.reduce((sum, c) => {
            return sum + Math.floor(((c.total_progress || 0) / 100.0) * 6.0);
        }, 0);

        const completedTasks = this.state.todos.filter(t => t.is_done).length;
        const totalHours = (completedMilestones * 2.0) + (completedTasks * 0.5);

        const currentStreak = localStorage.getItem('current_streak') || '1';
        const activeDates = JSON.parse(localStorage.getItem('active_dates') || '[]');
        const daysActive = activeDates.length || 1;

        // Render Stats values
        const completedMilestonesEl = document.getElementById('stat-completed-count');
        if (completedMilestonesEl) completedMilestonesEl.innerText = completedMilestones.toString();

        const streakEl = document.getElementById('stat-streak-count');
        if (streakEl) streakEl.innerText = `${currentStreak} Hari`;

        const hoursEl = document.getElementById('stat-hours-count');
        if (hoursEl) hoursEl.innerText = `${totalHours.toFixed(1)} Jam`;

        // Render Overview Values
        const ovHours = document.getElementById('overview-total-hours');
        if (ovHours) ovHours.innerText = totalHours.toFixed(1);

        const ovMilestones = document.getElementById('overview-total-milestones');
        if (ovMilestones) ovMilestones.innerText = completedMilestones.toString();

        const ovDays = document.getElementById('overview-days-active');
        if (ovDays) ovDays.innerText = daysActive.toString();

        const dailyAverage = daysActive > 0 ? totalHours / daysActive : totalHours;
        const ovAvg = document.getElementById('overview-daily-average');
        if (ovAvg) ovAvg.innerText = dailyAverage.toFixed(1);
    }

    renderActivePath() {
        const container = document.getElementById('active-path-widget-container');
        if (!container) return;

        if (!this.state.activeCurriculum) {
            container.innerHTML = `
                <div class="text-center py-6 text-grey-text text-sm font-medium">
                    Tidak ada roadmap yang sedang aktif. <br>
                    <a href="#" onclick="document.querySelector('[data-view=\\'view-explore\\']').click(); return false;" class="text-main-blue font-bold hover:underline">Buat Roadmap Baru</a>
                </div>
            `;
            return;
        }

        const c = this.state.activeCurriculum;
        container.innerHTML = `
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                <div>
                    <h3 class="text-lg font-bold text-dark-text">${c.topic}</h3>
                    <p class="text-xs text-grey-text font-medium mt-1 line-clamp-1">${c.description || ''}</p>
                </div>
                <button onclick="window.trackerinDashboard.selectRoadmap(${c.id})" class="px-5 py-2.5 bg-dark-text hover:bg-main-blue active:scale-[0.98] transition-all duration-300 text-xs font-bold text-white-pure rounded-full shrink-0">
                    Lanjutkan Belajar
                </button>
            </div>
            
            <div class="flex justify-between items-center text-xs font-bold text-dark-text mb-2">
                <span>Progress Belajar</span>
                <span class="text-main-blue">${Math.round(c.total_progress || 0)}%</span>
            </div>
            <div class="w-full bg-white-bg rounded-full h-2.5 border border-dark-text/5 overflow-hidden">
                <div class="bg-main-blue h-full rounded-full transition-all duration-500" style="width: ${c.total_progress || 0}%"></div>
            </div>
        `;
    }

    renderTodos() {
        const listContainer = document.getElementById('todos-list-container');
        if (!listContainer) return;

        if (this.state.todos.length === 0) {
            listContainer.innerHTML = `
                <div class="text-center py-8 text-grey-text text-sm font-medium">
                    Belum ada tugas belajar hari ini.
                </div>
            `;
            return;
        }

        listContainer.innerHTML = '';
        this.state.todos.forEach(t => {
            let title = t.task;
            let description = '';
            
            // Attempt to parse structured task JSON from database
            try {
                if (t.task.startsWith('{') && t.task.endsWith('}')) {
                    const parsed = JSON.parse(t.task);
                    title = parsed.title || 'Untitled';
                    description = parsed.desc || '';
                }
            } catch(e) {}

            const li = document.createElement('div');
            li.className = `flex items-center justify-between p-3.5 rounded-xl border border-dark-text/5 bg-white-bg/40 transition-all duration-300 ${t.is_done ? 'opacity-60' : ''}`;
            li.innerHTML = `
                <div class="flex items-center space-x-3.5 min-w-0">
                    <input type="checkbox" ${t.is_done ? 'checked' : ''} 
                           class="w-5 h-5 rounded border-dark-text/10 text-main-blue focus:ring-main-blue cursor-pointer transition-all duration-200"
                           onclick="window.trackerinDashboard.toggleTodo(${t.id}, ${t.is_done})">
                    <div class="min-w-0">
                        <h4 class="text-sm font-bold text-dark-text truncate ${t.is_done ? 'line-through text-grey-text' : ''}">${title}</h4>
                        ${description ? `<p class="text-[11px] text-grey-text mt-0.5 line-clamp-1">${description}</p>` : ''}
                    </div>
                </div>
                <button onclick="window.trackerinDashboard.deleteTodo(${t.id})" class="p-2 text-grey-text hover:text-red-500 hover:bg-white rounded-lg transition-all duration-200 shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            `;
            listContainer.appendChild(li);
        });
    }

    renderWeeklyChart() {
        const days = ["MON", "TUE", "WED", "THU", "FRI", "SAT", "SUN"];
        const todayStr = this.getTodayWeekdayString();
        
        days.forEach(day => {
            const bar = document.getElementById(`chart-bar-${day}`);
            const valEl = document.getElementById(`chart-val-${day}`);
            const textEl = document.getElementById(`chart-text-${day}`);

            if (bar) {
                const activityVal = this.getDailyActivity(day);
                const showVal = activityVal < 0 ? 0 : activityVal;
                
                // Set height (using percent of 120px)
                const heightPx = (showVal / 100) * 120;
                bar.style.height = `${heightPx}px`;
                
                // Set colors: highlight current day
                if (day === todayStr) {
                    bar.style.backgroundColor = '#2F6CF9'; // Main Blue
                    if (textEl) textEl.style.color = '#2F6CF9';
                } else {
                    bar.style.backgroundColor = '#AAC4FF'; // Light blue
                    if (textEl) textEl.style.color = '#6B7280';
                }

                if (valEl) {
                    valEl.innerText = showVal > 0 ? `${Math.round(showVal)}%` : '';
                }
            }
        });
    }

    // ==========================================
    // ROADMAPS VIEW
    // ==========================================
    async renderRoadmaps() {
        const container = document.getElementById('roadmaps-list-container');
        if (!container) return;

        if (this.state.curriculums.length === 0) {
            container.innerHTML = `
                <div class="text-center py-12 bg-white-pure rounded-3xl border border-dark-text/5 p-8">
                    <p class="text-grey-text font-medium mb-4 text-sm">Belum ada kurikulum belajar yang dihasilkan.</p>
                    <button onclick="document.querySelector('[data-view=\\'view-explore\\']').click();" class="px-6 py-3 bg-main-blue hover:bg-hover-blue active:scale-[0.98] transition-all duration-300 text-sm font-bold text-white-pure rounded-full">
                        Buat Roadmap Pertama Anda
                    </button>
                </div>
            `;
            return;
        }

        if (this.state.activeRoadmapId) {
            // Load and view milestone details for the active selection
            await this.viewRoadmapDetails(this.state.activeRoadmapId);
            return;
        }

        // Render general roadmaps list grid
        container.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                ${this.state.curriculums.map(c => `
                    <div class="gradient-border-card p-6 shadow-sm bg-white-pure flex flex-col justify-between hover:translate-y-[-2px] transition-all duration-300 border border-dark-text/5">
                        <div>
                            <div class="flex justify-between items-start gap-4 mb-3">
                                <h3 class="text-base font-bold text-dark-text line-clamp-1">${c.topic}</h3>
                                <span class="text-xs font-bold text-main-blue shrink-0">${Math.round(c.total_progress || 0)}%</span>
                            </div>
                            <p class="text-grey-text text-xs font-medium leading-relaxed line-clamp-3 mb-6">${c.description || 'Tidak ada deskripsi.'}</p>
                        </div>
                        
                        <div>
                            <div class="w-full bg-white-bg rounded-full h-1.5 mb-5 overflow-hidden">
                                <div class="bg-main-blue h-full rounded-full" style="width: ${c.total_progress || 0}%"></div>
                            </div>
                            <div class="flex justify-between items-center">
                                <button onclick="window.trackerinDashboard.deleteRoadmap(${c.id})" class="text-xs text-red-500 hover:text-red-700 font-bold">
                                    Hapus
                                </button>
                                <button onclick="window.trackerinDashboard.selectRoadmap(${c.id})" class="px-4 py-2 bg-dark-text hover:bg-main-blue active:scale-[0.98] transition-all duration-300 text-xs font-bold text-white-pure rounded-full">
                                    Lihat Detail
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
    }

    async viewRoadmapDetails(id) {
        const container = document.getElementById('roadmaps-list-container');
        if (!container) return;

        container.innerHTML = `<div class="text-center py-12"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-main-blue mx-auto"></div></div>`;

        try {
            const res = await this.apiCall(`/api/v1/curriculums/${id}`);
            const c = res.data;
            if (!c) return;

            // Render Roadmap details header and milestone list
            container.innerHTML = `
                <div class="flex items-center gap-3 mb-6">
                    <button onclick="window.trackerinDashboard.backToRoadmapsList()" class="p-2 text-grey-text hover:text-dark-text hover:bg-white-bg rounded-full transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    </button>
                    <h2 class="text-xl font-bold text-dark-text">Detail Roadmap</h2>
                </div>

                <div class="gradient-border-card p-6 shadow-sm bg-white-pure border border-dark-text/5 mb-8">
                    <h3 class="text-lg font-bold text-dark-text mb-2">${c.topic}</h3>
                    <p class="text-grey-text text-sm font-medium leading-relaxed mb-6">${c.description || ''}</p>
                    
                    <div class="flex justify-between items-center text-xs font-bold text-dark-text mb-2">
                        <span>Penyelesaian Roadmap</span>
                        <span class="text-main-blue">${Math.round(c.total_progress || 0)}%</span>
                    </div>
                    <div class="w-full bg-white-bg rounded-full h-2 overflow-hidden">
                        <div class="bg-main-blue h-full rounded-full transition-all duration-500" style="width: ${c.total_progress || 0}%"></div>
                    </div>
                </div>

                <h3 class="text-sm font-bold uppercase tracking-wider text-grey-text mb-4">Milestone Pembelajaran</h3>
                <div class="space-y-4">
                    ${c.milestones.map((m, index) => {
                        const canExpand = index === 0 || c.milestones[index - 1].is_completed;
                        return `
                            <div class="gradient-border-card bg-white-pure border border-dark-text/5 overflow-hidden transition-all duration-300">
                                <!-- Milestone Header Row -->
                                <div class="p-5 flex items-center justify-between gap-4 cursor-pointer hover:bg-white-bg/20 transition-all duration-200" 
                                     onclick="${canExpand ? `window.trackerinDashboard.toggleMilestonePanel(${m.id})` : `window.trackerinDashboard.showLockedWarning()`}">
                                    <div class="flex items-center space-x-4 min-w-0">
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white-pure ${m.is_completed ? 'bg-green-500' : 'bg-main-blue/70'} shrink-0">
                                            ${index + 1}
                                        </div>
                                        <span class="text-sm font-bold text-dark-text truncate ${m.is_completed ? 'line-through text-grey-text' : ''}">${m.title}</span>
                                    </div>
                                    <div class="flex items-center space-x-3 shrink-0">
                                        ${m.is_completed ? `
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-800">Selesai</span>
                                        ` : `
                                            ${canExpand ? '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">Aktif</span>' : '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-800">Terkunci</span>'}
                                        `}
                                        ${canExpand ? `
                                            <svg id="chevron-icon-${m.id}" class="w-4 h-4 text-grey-text transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                                        ` : ''}
                                    </div>
                                </div>

                                <!-- Expanded Content -->
                                ${canExpand ? `
                                    <div id="milestone-panel-${m.id}" class="hidden border-t border-dark-text/5 bg-white-bg/10 p-5 space-y-5">
                                        <!-- Actions Row -->
                                        <div class="flex flex-wrap gap-4 items-center justify-between">
                                            <div class="flex gap-2">
                                                <button onclick="window.trackerinDashboard.openQuiz(${m.id})" class="px-4 py-2 rounded-full border border-main-blue hover:bg-main-blue hover:text-white-pure active:scale-[0.98] transition-all duration-300 text-xs font-bold text-main-blue">
                                                    Evaluasi Kuis AI
                                                </button>
                                                <button onclick="window.trackerinDashboard.toggleNotesForm(${m.id})" class="px-4 py-2 rounded-full border border-dark-text/10 hover:border-dark-text/80 active:scale-[0.98] transition-all duration-300 text-xs font-bold text-dark-text bg-white-pure">
                                                    Catatan Belajar
                                                </button>
                                            </div>
                                            
                                            ${!m.is_completed ? `
                                                <button onclick="window.trackerinDashboard.completeMilestoneManual(${m.id})" class="px-5 py-2.5 bg-green-500 hover:bg-green-600 active:scale-[0.98] transition-all duration-300 text-xs font-bold text-white-pure rounded-full">
                                                    Tandai Selesai
                                                </button>
                                            ` : ''}
                                        </div>

                                        <!-- Milestone Study Notes Form -->
                                        <div id="notes-form-container-${m.id}" class="hidden bg-white-pure p-4 rounded-xl border border-dark-text/5 space-y-3">
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="font-bold text-dark-text uppercase tracking-wider">Catatan Saya</span>
                                                <span id="notes-save-status-${m.id}" class="text-[10px] text-grey-text font-medium">Semua catatan tersimpan secara otomatis</span>
                                            </div>
                                            <textarea id="milestone-notes-textarea-${m.id}" rows="4" 
                                                      class="w-full bg-white-bg/40 border border-dark-text/10 focus:border-main-blue text-dark-text rounded-xl p-3 outline-none text-xs font-medium resize-none placeholder:text-grey-text transition-all duration-300"
                                                      placeholder="Ketik catatan belajar Anda di sini..."
                                                      oninput="window.trackerinDashboard.autoSaveNote(${m.id})"></textarea>
                                        </div>
                                    </div>
                                ` : ''}
                            </div>
                        `;
                    }).join('')}
                </div>
            `;
            
            // Load and pre-fill note contents for expanded states
            c.milestones.forEach(async m => {
                const notesTextarea = document.getElementById(`milestone-notes-textarea-${m.id}`);
                if (notesTextarea) {
                    try {
                        const notesData = await this.apiCall(`/api/v1/notes?milestone_id=${m.id}`);
                        if (notesData.data && notesData.data.length > 0) {
                            notesTextarea.value = notesData.data[0].content || '';
                        }
                    } catch (e) {}
                }
            });

        } catch (e) {
            this.showToast('Gagal memuat detail kurikulum', 'error');
            this.backToRoadmapsList();
        }
    }

    selectRoadmap(id) {
        this.state.activeRoadmapId = id;
        this.renderRoadmaps();
    }

    backToRoadmapsList() {
        this.state.activeRoadmapId = null;
        this.renderRoadmaps();
    }

    toggleMilestonePanel(id) {
        const panel = document.getElementById(`milestone-panel-${id}`);
        const chevron = document.getElementById(`chevron-icon-${id}`);

        if (panel.classList.contains('hidden')) {
            panel.classList.remove('hidden');
            chevron.style.transform = 'rotate(180deg)';
        } else {
            panel.classList.add('hidden');
            chevron.style.transform = 'rotate(0deg)';
        }
    }

    showLockedWarning() {
        this.showToast('Selesaikan milestone sebelumnya terlebih dahulu!', 'warning');
    }

    async completeMilestoneManual(id) {
        try {
            await this.apiCall(`/api/v1/milestones/${id}/complete`, 'PUT');
            this.showToast('Milestone berhasil diselesaikan!', 'success');
            
            // Increment progress tracker activity
            this.incrementDailyActivity(20);
            
            await this.fetchCurriculums();
            this.renderStats();
            this.renderActivePath();
            await this.viewRoadmapDetails(this.state.activeRoadmapId);
        } catch(e) {
            this.showToast('Gagal menyelesaikan milestone', 'error');
        }
    }

    toggleNotesForm(id) {
        const container = document.getElementById(`notes-form-container-${id}`);
        if (container.classList.contains('hidden')) {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }

    autoSaveNote(milestoneId) {
        const statusEl = document.getElementById(`notes-save-status-${milestoneId}`);
        const textarea = document.getElementById(`milestone-notes-textarea-${milestoneId}`);
        const content = textarea.value;

        if (statusEl) statusEl.innerText = "Menyimpan...";

        // Debounce saving
        clearTimeout(this[`noteTimeout_${milestoneId}`]);
        this[`noteTimeout_${milestoneId}`] = setTimeout(async () => {
            try {
                // Determine whether to create or update notes
                const notesData = await this.apiCall(`/api/v1/notes?milestone_id=${milestoneId}`);
                if (notesData.data && notesData.data.length > 0) {
                    const noteId = notesData.data[0].id;
                    await this.apiCall(`/api/v1/notes/${noteId}`, 'PUT', { title: 'Study Note', content });
                } else {
                    await this.apiCall('/api/v1/notes', 'POST', { milestone_id: milestoneId, title: 'Study Note', content });
                }
                if (statusEl) statusEl.innerText = "Semua catatan tersimpan secara otomatis";
            } catch (err) {
                if (statusEl) statusEl.innerText = "Gagal menyimpan catatan";
            }
        }, 1000);
    }

    // ==========================================
    // AI QUIZ SYSTEM
    // ==========================================
    async openQuiz(milestoneId) {
        const modal = document.getElementById('quiz-modal');
        const container = document.getElementById('quiz-modal-content');
        if (!modal || !container) return;

        modal.classList.remove('hidden');
        container.innerHTML = `<div class="text-center py-12"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-main-blue mx-auto mb-4"></div><p class="text-xs text-grey-text font-medium">Asisten AI sedang menyiapkan pertanyaan kuis...</p></div>`;

        try {
            const res = await this.apiCall(`/api/v1/milestones/${milestoneId}/generate-quiz`, 'POST');
            const data = res.data;

            if (!data || !data.questions) {
                throw new Error("No questions available");
            }

            container.innerHTML = `
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-dark-text mb-1">${data.title || 'Evaluasi Milestone'}</h3>
                    <p class="text-xs text-grey-text font-medium">Jawab ketiga pertanyaan kuis di bawah untuk menguji pemahaman konsep Anda.</p>
                </div>
                
                <form id="quiz-submission-form" onsubmit="window.trackerinDashboard.submitQuiz(event, ${milestoneId})">
                    <div class="space-y-6 max-h-[60vh] overflow-y-auto pr-2">
                        ${data.questions.map((q, qIndex) => `
                            <div class="p-4 bg-white-bg/40 border border-dark-text/5 rounded-2xl">
                                <h4 class="text-sm font-bold text-dark-text mb-3 leading-relaxed">${qIndex + 1}. ${q.question}</h4>
                                <div class="space-y-2.5">
                                    ${q.options.map((option, oIndex) => `
                                        <label class="flex items-center space-x-3 p-3 rounded-xl border border-dark-text/5 bg-white-pure hover:bg-main-blue/5 cursor-pointer transition-all duration-200">
                                            <input type="radio" required name="question-${qIndex}" value="${option}"
                                                   class="w-4 h-4 text-main-blue border-dark-text/10 focus:ring-main-blue">
                                            <span class="text-xs font-semibold text-dark-text">${option}</span>
                                        </label>
                                    `).join('')}
                                </div>
                            </div>
                        `).join('')}
                    </div>

                    <div class="mt-8 flex justify-end gap-3 border-t border-dark-text/5 pt-4">
                        <button type="button" onclick="window.trackerinDashboard.closeQuiz()" class="px-6 py-3 border border-dark-text/10 hover:bg-white-bg text-xs font-bold text-dark-text rounded-full transition-all duration-200">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-3 bg-main-blue hover:bg-hover-blue active:scale-[0.98] transition-all duration-300 text-xs font-bold text-white-pure rounded-full">
                            Kirim Jawaban
                        </button>
                    </div>
                </form>
            `;

            // Keep correct answers stored in state to calculate score
            this.state.currentQuizAnswers = data.questions.map(q => q.correct_answer);

        } catch (e) {
            this.showToast('Gagal memuat kuis dari AI', 'error');
            this.closeQuiz();
        }
    }

    async submitQuiz(e, milestoneId) {
        e.preventDefault();
        
        const form = document.getElementById('quiz-submission-form');
        const correctAnswers = this.state.currentQuizAnswers;
        if (!form || !correctAnswers) return;

        let scoreCount = 0;
        correctAnswers.forEach((ans, qIndex) => {
            const selected = form.querySelector(`input[name="question-${qIndex}"]:checked`)?.value;
            if (selected === ans) {
                scoreCount++;
            }
        });

        const score = Math.round((scoreCount / correctAnswers.length) * 100);
        
        // Render score overlay popup
        const container = document.getElementById('quiz-modal-content');
        container.innerHTML = `
            <div class="text-center py-8 flex flex-col items-center">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mb-6 ${scoreCount >= 2 ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'}">
                    ${scoreCount >= 2 ? `
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    ` : `
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    `}
                </div>
                <h3 class="text-lg font-bold text-dark-text mb-2">${scoreCount >= 2 ? 'Selamat! Evaluasi Lulus 🎉' : 'Sayang Sekali, Evaluasi Gagal'}</h3>
                <p class="text-xs text-grey-text font-medium mb-1">Skor Kuis Anda: <span class="font-bold text-dark-text">${score}%</span></p>
                <p class="text-[11px] text-grey-text font-medium max-w-sm mb-6">${scoreCount >= 2 ? 'Anda memahami topik ini dengan sangat baik. Milestone telah dicentang otomatis.' : 'Anda butuh setidaknya 2 jawaban benar untuk lulus. Silakan pelajari ulang catatan dan ulangi kuis.'}</p>
                
                <button onclick="window.trackerinDashboard.finishQuizResult(${scoreCount}, ${milestoneId})" class="px-8 py-3 bg-dark-text hover:bg-main-blue active:scale-[0.98] transition-all duration-300 text-xs font-bold text-white-pure rounded-full">
                    Selesai
                </button>
            </div>
        `;
    }

    async finishQuizResult(score, milestoneId) {
        this.closeQuiz();
        
        try {
            await this.apiCall(`/api/v1/milestones/${milestoneId}/quiz/submit`, 'POST', { score });
            
            // If quiz is successfully completed (>= 2), complete milestone
            if (score >= 2) {
                await this.completeMilestoneManual(milestoneId);
            }
        } catch (e) {
            console.error('Failed to submit score to server');
        }
    }

    closeQuiz() {
        const modal = document.getElementById('quiz-modal');
        if (modal) modal.classList.add('hidden');
        this.state.currentQuizAnswers = null;
    }

    // ==========================================
    // PROFILE TAB
    // ==========================================
    renderProfile() {
        this.renderProfileHeader();
        this.renderSavedNotes();
    }

    renderProfileHeader() {
        const nameInputs = document.getElementById('profile-name-input');
        if (nameInputs) nameInputs.value = this.state.user.name;

        const emailInputs = document.getElementById('profile-email-input');
        if (emailInputs) emailInputs.value = this.state.user.email;

        const occInputs = document.getElementById('profile-occ-input');
        if (occInputs) occInputs.value = this.state.user.occupation;

        const specInputs = document.getElementById('profile-spec-input');
        if (specInputs) specInputs.value = this.state.user.specialization;

        const titleEl = document.getElementById('profile-header-name');
        if (titleEl) titleEl.innerText = this.state.user.name;

        const occEl = document.getElementById('profile-header-occupation');
        if (occEl) occEl.innerText = this.state.user.occupation;

        const specEl = document.getElementById('profile-header-specialization');
        if (specEl) specEl.innerText = this.state.user.specialization;

        // Render avatars dynamically
        this.renderProfileAvatars();
    }

    renderProfileAvatars() {
        const profileImage = this.state.user.profile_image;
        
        // Sidebar Avatar
        const sidebarImg = document.getElementById('sidebar-avatar-img');
        const sidebarInitial = document.getElementById('sidebar-avatar-initial');
        
        // Profile Tab Avatar
        const profileImg = document.getElementById('profile-avatar-img');
        const profileInitial = document.getElementById('profile-avatar-initial');

        if (profileImage) {
            if (sidebarImg) {
                sidebarImg.src = profileImage;
                sidebarImg.classList.remove('hidden');
            }
            if (sidebarInitial) {
                sidebarInitial.classList.add('hidden');
            }

            if (profileImg) {
                profileImg.src = profileImage;
                profileImg.classList.remove('hidden');
            }
            if (profileInitial) {
                profileInitial.classList.add('hidden');
            }
        } else {
            const name = this.state.user.name || 'User';
            const initial = name.trim().charAt(0).toUpperCase();

            if (sidebarImg) {
                sidebarImg.classList.add('hidden');
            }
            if (sidebarInitial) {
                sidebarInitial.innerText = initial;
                sidebarInitial.classList.remove('hidden');
            }

            if (profileImg) {
                profileImg.classList.add('hidden');
            }
            if (profileInitial) {
                profileInitial.innerText = initial;
                profileInitial.classList.remove('hidden');
            }
        }
    }

    renderSavedNotes() {
        const container = document.getElementById('saved-notes-list-container');
        if (!container) return;

        const notes = this.state.notes || [];
        if (notes.length === 0) {
            container.innerHTML = `<p class="text-xs text-grey-text font-medium py-4 text-center">Belum ada catatan belajar yang dibuat.</p>`;
            return;
        }

        container.innerHTML = `
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                ${notes.map(n => `
                    <div class="p-4 bg-white-bg/40 border border-dark-text/5 rounded-2xl flex flex-col justify-between">
                        <div>
                            <h4 class="text-xs font-bold text-dark-text uppercase tracking-wider mb-2">${n.milestone_title || 'Catatan Umum'}</h4>
                            <p class="text-xs text-grey-text leading-relaxed font-medium whitespace-pre-wrap line-clamp-4">${n.content}</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-dark-text/5 flex justify-between items-center text-[10px] text-grey-text font-bold">
                            <span>${new Date(n.updated_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'})}</span>
                            <div class="flex space-x-2">
                                <button onclick="window.trackerinDashboard.editNoteFromProfile(${n.id})" class="text-main-blue hover:underline">Edit</button>
                                <button onclick="window.trackerinDashboard.deleteNote(${n.id})" class="text-red-500 hover:underline">Hapus</button>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
    }

    async deleteNote(noteId) {
        if (!confirm('Apakah Anda yakin ingin menghapus catatan ini?')) return;
        try {
            await this.apiCall(`/api/v1/notes/${noteId}`, 'DELETE');
            this.showToast('Catatan berhasil dihapus', 'success');
            await this.fetchNotes();
            this.renderSavedNotes();
            this.renderFullNotes();
        } catch(e) {
            this.showToast('Gagal menghapus catatan', 'error');
        }
    }

    // ==========================================
    // TODO HELPER ACTIONS
    // ==========================================
    async toggleTodo(id, currentDone) {
        try {
            await this.apiCall(`/api/v1/todos/${id}`, 'PUT', { is_done: !currentDone });
            await this.fetchTodos();
            this.renderTodos();
            this.renderFullTodos();
            this.renderStats();
        } catch (e) {
            this.showToast('Gagal memperbarui status tugas', 'error');
        }
    }

    async deleteTodo(id) {
        try {
            await this.apiCall(`/api/v1/todos/${id}`, 'DELETE');
            await this.fetchTodos();
            this.renderTodos();
            this.renderFullTodos();
            this.renderStats();
            this.showToast('Tugas berhasil dihapus', 'success');
        } catch (e) {
            this.showToast('Gagal menghapus tugas', 'error');
        }
    }

    renderFullTodos() {
        const listContainer = document.getElementById('full-todos-list-container');
        if (!listContainer) return;

        const filter = this.state.todoFilter || 'all';
        let filteredTodos = this.state.todos || [];

        if (filter === 'active') {
            filteredTodos = filteredTodos.filter(t => !t.is_done);
        } else if (filter === 'completed') {
            filteredTodos = filteredTodos.filter(t => t.is_done);
        }

        if (filteredTodos.length === 0) {
            listContainer.innerHTML = `
                <div class="text-center py-12 text-grey-text text-sm font-medium">
                    Tidak ada tugas belajar yang cocok.
                </div>
            `;
            return;
        }

        listContainer.innerHTML = '';
        filteredTodos.forEach(t => {
            let title = t.task;
            let description = '';
            let dueStr = '';
            
            try {
                if (t.task.startsWith('{') && t.task.endsWith('}')) {
                    const parsed = JSON.parse(t.task);
                    title = parsed.title || 'Untitled';
                    description = parsed.desc || '';
                    dueStr = parsed.due || '';
                }
            } catch(e) {}
            
            if (!dueStr && t.due_date) {
                dueStr = t.due_date.substring(0, 10);
            }

            const item = document.createElement('div');
            item.className = `flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-2xl border border-dark-text/5 bg-white-bg/40 hover:bg-white-bg/85 transition-all duration-300 gap-4 ${t.is_done ? 'opacity-65' : ''}`;
            
            let badgeHtml = '';
            if (dueStr) {
                const today = this.getTodayDateString();
                const isOverdue = !t.is_done && dueStr < today;
                const isToday = dueStr === today;
                
                let badgeClass = 'bg-grey-text/10 text-grey-text';
                if (isOverdue) {
                    badgeClass = 'bg-red-500/10 text-red-500';
                } else if (isToday) {
                    badgeClass = 'bg-orange-500/10 text-orange-500';
                } else if (t.is_done) {
                    badgeClass = 'bg-green-500/10 text-green-500';
                }
                
                badgeHtml = `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold ${badgeClass}">${dueStr}</span>`;
            }
            
            item.innerHTML = `
                <div class="flex items-start space-x-3.5 min-w-0 flex-1">
                    <input type="checkbox" ${t.is_done ? 'checked' : ''} 
                           class="w-5 h-5 mt-0.5 rounded border-dark-text/10 text-main-blue focus:ring-main-blue cursor-pointer transition-all duration-200 shrink-0"
                           onclick="window.trackerinDashboard.toggleTodo(${t.id}, ${t.is_done})">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h4 class="text-sm font-bold text-dark-text truncate ${t.is_done ? 'line-through text-grey-text' : ''}">${title}</h4>
                            ${badgeHtml}
                        </div>
                        ${description ? `<p class="text-xs text-grey-text mt-1 whitespace-pre-wrap ${t.is_done ? 'line-through text-grey-text' : ''}">${description}</p>` : ''}
                    </div>
                </div>
                <div class="flex items-center space-x-1 shrink-0 self-end sm:self-center">
                    <button onclick="window.trackerinDashboard.editTodo(${t.id})" class="p-2 text-grey-text hover:text-main-blue hover:bg-white rounded-lg transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button onclick="window.trackerinDashboard.deleteTodo(${t.id})" class="p-2 text-grey-text hover:text-red-500 hover:bg-white rounded-lg transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            `;
            listContainer.appendChild(item);
        });
    }

    editTodo(id) {
        const t = this.state.todos.find(item => item.id === id);
        if (!t) return;

        let title = t.task;
        let description = '';
        let dueStr = '';
        
        try {
            if (t.task.startsWith('{') && t.task.endsWith('}')) {
                const parsed = JSON.parse(t.task);
                title = parsed.title || 'Untitled';
                description = parsed.desc || '';
                dueStr = parsed.due || '';
            }
        } catch(e) {}
        
        if (!dueStr && t.due_date) {
            dueStr = t.due_date.substring(0, 10);
        }

        document.getElementById('edit-todo-id').value = t.id;
        document.getElementById('todo-task-title').value = title;
        document.getElementById('todo-task-desc').value = description;
        document.getElementById('todo-task-due').value = dueStr;

        const cancelBtn = document.getElementById('todo-cancel-edit-btn');
        if (cancelBtn) cancelBtn.classList.remove('hidden');

        const submitBtn = document.getElementById('todo-submit-btn');
        if (submitBtn) {
            submitBtn.querySelector('span').innerText = "Simpan Perubahan";
        }
        
        const titleHeader = document.getElementById('todo-manager-title');
        if (titleHeader) titleHeader.innerText = "Edit Tugas Belajar";
    }

    cancelEditTodo() {
        const form = document.getElementById('full-todo-form');
        if (form) form.reset();
        
        const editIdInput = document.getElementById('edit-todo-id');
        if (editIdInput) editIdInput.value = '';

        const cancelBtn = document.getElementById('todo-cancel-edit-btn');
        if (cancelBtn) cancelBtn.classList.add('hidden');

        const submitBtn = document.getElementById('todo-submit-btn');
        if (submitBtn) {
            submitBtn.querySelector('span').innerText = "Tambah Tugas";
        }

        const titleHeader = document.getElementById('todo-manager-title');
        if (titleHeader) titleHeader.innerText = "Tambah Tugas Baru";
    }

    filterTodos(filter) {
        this.state.todoFilter = filter;
        
        const filters = ['all', 'active', 'completed'];
        filters.forEach(f => {
            const btn = document.getElementById(`todo-filter-${f}`);
            if (btn) {
                if (f === filter) {
                    btn.className = "px-3 py-1.5 rounded-lg text-[10px] font-bold bg-dark-text text-white-pure transition-all duration-200";
                } else {
                    btn.className = "px-3 py-1.5 rounded-lg text-[10px] font-bold text-grey-text hover:text-dark-text transition-all duration-200";
                }
            }
        });
        
        this.renderFullTodos();
    }

    renderFullNotes() {
        const listContainer = document.getElementById('full-notes-list-container');
        if (!listContainer) return;

        const query = document.getElementById('notes-search-filter')?.value.toLowerCase() || '';
        let filteredNotes = this.state.notes || [];

        if (query) {
            filteredNotes = filteredNotes.filter(n => {
                return (n.title || '').toLowerCase().includes(query) || 
                       (n.content || '').toLowerCase().includes(query) ||
                       (n.milestone_title || '').toLowerCase().includes(query);
            });
        }

        if (filteredNotes.length === 0) {
            listContainer.innerHTML = `
                <div class="text-center py-12 text-grey-text text-sm font-medium">
                    Tidak ada catatan yang ditemukan.
                </div>
            `;
            return;
        }

        listContainer.innerHTML = '';
        filteredNotes.forEach(n => {
            const item = document.createElement('div');
            item.className = "gradient-border-card p-5 bg-white-pure hover:shadow-md border border-dark-text/5 rounded-2xl flex flex-col justify-between hover:translate-y-[-2px] transition-all duration-300";
            item.innerHTML = `
                <div>
                    <div class="flex justify-between items-start gap-4 mb-2">
                        <h4 class="text-sm font-bold text-dark-text truncate">${n.title || 'Catatan Belajar'}</h4>
                        ${n.milestone_title ? `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-main-blue/10 text-main-blue max-w-[150px] truncate" title="${n.milestone_title}">${n.milestone_title}</span>` : ''}
                    </div>
                    <p class="text-xs text-grey-text leading-relaxed font-medium whitespace-pre-wrap mb-4">${n.content}</p>
                </div>
                <div class="pt-3 border-t border-dark-text/5 flex justify-between items-center text-[10px] font-bold text-grey-text">
                    <span>${new Date(n.updated_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'})}</span>
                    <div class="flex items-center space-x-2">
                        <button onclick="window.trackerinDashboard.editNote(${n.id})" class="text-main-blue hover:underline">Edit</button>
                        <button onclick="window.trackerinDashboard.deleteNote(${n.id})" class="text-red-500 hover:underline">Hapus</button>
                    </div>
                </div>
            `;
            listContainer.appendChild(item);
        });
    }

    editNote(id) {
        const n = this.state.notes.find(item => item.id === id);
        if (!n) return;

        document.getElementById('edit-note-id').value = n.id;
        document.getElementById('note-title').value = n.title;
        document.getElementById('note-content').value = n.content;

        const cancelBtn = document.getElementById('note-cancel-edit-btn');
        if (cancelBtn) cancelBtn.classList.remove('hidden');

        const submitBtn = document.getElementById('note-submit-btn');
        if (submitBtn) {
            submitBtn.innerText = "Simpan Catatan";
        }
        
        const titleHeader = document.getElementById('notes-manager-title');
        if (titleHeader) titleHeader.innerText = "Edit Catatan Belajar";
    }

    editNoteFromProfile(id) {
        document.querySelector('[data-view="view-notes"]')?.click();
        this.editNote(id);
    }

    cancelEditNote() {
        const form = document.getElementById('full-note-form');
        if (form) form.reset();
        
        const editIdInput = document.getElementById('edit-note-id');
        if (editIdInput) editIdInput.value = '';

        const cancelBtn = document.getElementById('note-cancel-edit-btn');
        if (cancelBtn) cancelBtn.classList.add('hidden');

        const submitBtn = document.getElementById('note-submit-btn');
        if (submitBtn) {
            submitBtn.innerText = "Simpan Catatan";
        }

        const titleHeader = document.getElementById('notes-manager-title');
        if (titleHeader) titleHeader.innerText = "Buat Catatan Baru";
    }

    async deleteRoadmap(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus roadmap ini?')) return;
        try {
            await this.apiCall(`/api/v1/curriculums/${id}`, 'DELETE');
            this.showToast('Roadmap berhasil dihapus', 'success');
            await this.fetchCurriculums();
            this.renderRoadmaps();
            this.renderActivePath();
            this.renderStats();
        } catch (e) {
            this.showToast('Gagal menghapus roadmap', 'error');
        }
    }

    // ==========================================
    // GENERAL HELPERS
    // ==========================================
    showToast(message, type = 'info') {
        const alertBox = document.getElementById('dashboard-global-alert');
        if (!alertBox) return;

        alertBox.innerText = message;
        alertBox.className = "fixed top-6 right-6 z-[100] px-6 py-3.5 rounded-full text-xs font-bold text-white-pure shadow-lg transition-all duration-300 transform scale-100";
        
        if (type === 'success') {
            alertBox.style.backgroundColor = '#10B981'; // Green
        } else if (type === 'error') {
            alertBox.style.backgroundColor = '#EF4444'; // Red
        } else if (type === 'warning') {
            alertBox.style.backgroundColor = '#F59E0B'; // Orange
        } else {
            alertBox.style.backgroundColor = '#2F6CF9'; // Blue
        }

        setTimeout(() => {
            alertBox.className = "fixed top-6 right-6 z-[100] px-6 py-3.5 rounded-full text-xs font-bold text-white-pure shadow-lg transition-all duration-300 transform scale-0";
        }, 3000);
    }

    getTodayDateString() {
        const today = new Date();
        const yyyy = today.getFullYear();
        let mm = today.getMonth() + 1;
        let dd = today.getDate();
        
        if (dd < 10) dd = '0' + dd;
        if (mm < 10) mm = '0' + mm;
        
        return `${yyyy}-${mm}-${dd}`;
    }

    getYesterdayDateString() {
        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        const yyyy = yesterday.getFullYear();
        let mm = yesterday.getMonth() + 1;
        let dd = yesterday.getDate();
        
        if (dd < 10) dd = '0' + dd;
        if (mm < 10) mm = '0' + mm;
        
        return `${yyyy}-${mm}-${dd}`;
    }

    getTodayWeekdayString() {
        const weekdays = ["SUN", "MON", "TUE", "WED", "THU", "FRI", "SAT"];
        const today = new Date();
        const index = today.getDay();
        
        // Map: in TokenManager, weeks start on MON
        const map = ["SUN", "MON", "TUE", "WED", "THU", "FRI", "SAT"];
        return map[index];
    }

    getWeekNumber(d) {
        d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
        d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay() || 7));
        const yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
        return Math.ceil(( ( (d - yearStart) / 86400000) + 1) / 7);
    }
}
