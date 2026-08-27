<div class="content-card">
    <div style="margin-bottom: 24px;">
        <span class="page-badge" style="margin-bottom: 12px; display: inline-block;">HMVC Architecture</span>
        <h2 style="font-size: 22px; font-weight: 700; color: #fff; margin-bottom: 8px;">Selamat Datang di Modul Home</h2>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6;">
            Aplikasi ini telah menerapkan struktur modular HMVC lengkap dengan <strong>Theme Global</strong> (Header, Sidebar, Footer) yang dikelola melalui <strong>Template Library</strong> dan sistem styling responsif penuh di berbagai perangkat.
        </p>
    </div>

    <!-- Responsive Feature Cards Grid -->
    <div class="stats-grid-3">
        <div class="stat-item">
            <div style="font-size: 20px; color: #38bdf8; margin-bottom: 10px;"><i class="fa-solid fa-route"></i></div>
            <h3 style="font-size: 15px; font-weight: 600; color: #fff; margin-bottom: 6px;">Auto Route Dispatcher</h3>
            <p style="font-size: 13px; color: var(--text-muted);">Tidak perlu mendaftarkan route manual di <code>routes/web.php</code>. Setiap modul langsung aktif.</p>
        </div>

        <div class="stat-item">
            <div style="font-size: 20px; color: #818cf8; margin-bottom: 10px;"><i class="fa-solid fa-palette"></i></div>
            <h3 style="font-size: 15px; font-weight: 600; color: #fff; margin-bottom: 6px;">Template Library</h3>
            <p style="font-size: 13px; color: var(--text-muted);">Gunakan <code>$this->render('module::view', $data)</code> untuk merender view yang terbungkus rapi dalam theme.</p>
        </div>

        <div class="stat-item">
            <div style="font-size: 20px; color: #34d399; margin-bottom: 10px;"><i class="fa-solid fa-mobile-screen-button"></i></div>
            <h3 style="font-size: 15px; font-weight: 600; color: #fff; margin-bottom: 6px;">Multi-Device Ready</h3>
            <p style="font-size: 13px; color: var(--text-muted);">Tampilan menyesuaikan secara otomatis di Smartphone, Tablet, Laptop, dan Desktop.</p>
        </div>
    </div>

    <!-- Demonstrasi Sub-Request HMVC -->
    {!! hmvc('Dashboard@widget', ['widgetTitle' => 'Statistik Sub-Request dari Modul Dashboard']) !!}
</div>