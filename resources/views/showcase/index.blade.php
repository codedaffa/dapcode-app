<div class="content-card" style="padding: 28px;"><div style="margin-bottom: 20px;"><x-ui.breadcrumb :items="['Developer' => '#', 'UI Component Showcase' => '']" /></div><div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 20px;"><div><div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;"><h2 style="font-size: 24px; font-weight: 800; color: #fff; margin: 0;">{{ $title }}</h2><x-ui.badge variant="primary" style="solid" pill> v1.0 Ready </x-ui.badge><x-ui.badge variant="info" style="outline" pill><i class="fa-solid fa-code"></i> Live Code & Docs </x-ui.badge></div><p style="color: var(--text-muted); font-size: 14px; margin: 0;">{{ $subtitle }}</p></div><div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;"><button type="button" id="btn-toggle-all-code" class="ui-btn ui-btn-outline ui-btn-sm" onclick="toggleAllShowcaseCode(this)" title="Buka atau tutup seluruh contoh kode implementasi pada halaman" ><i class="fa-solid fa-code"></i><span> Tampilkan Semua Kode </span></button><x-ui.button variant="primary" size="sm" icon="fa-solid fa-bell" onclick="DapToast.success('DapToast notification berhasil dipicu secara real-time!', 'Notifikasi Sukses');" > Test Toast </x-ui.button><x-ui.button variant="danger" size="sm" icon="fa-solid fa-triangle-exclamation" onclick="DapConfirm({title: 'Hapus Data Contoh?', message: 'Apakah Anda yakin ingin menghapus data contoh ini?', type: 'danger'}).then(ok => { if(ok) DapToast.error('Data contoh berhasil dihapus!'); });" > Test Confirm </x-ui.button><x-ui.button variant="secondary" size="sm" icon="fa-solid fa-window-maximize" data-ui-modal-target="demo-modal" > Buka Modal </x-ui.button></div></div><x-ui.tabs :items="[ ['id' => 'tab-primitives', 'label' => 'Primitives (Button, Badge, Alert)', 'icon' => 'fa-solid fa-shapes'], ['id' => 'tab-cards', 'label' => 'Cards & Feedback', 'icon' => 'fa-solid fa-id-card'], ['id' => 'tab-forms', 'label' => 'Form Controls', 'icon' => 'fa-solid fa-pen-to-square'], ['id' => 'tab-tables', 'label' => 'Table & Navigation', 'icon' => 'fa-solid fa-table-list'], ['id' => 'tab-interactive', 'label' => 'Interactive & Dialogs', 'icon' => 'fa-solid fa-bolt'], ['id' => 'tab-docs', 'label' => 'Panduan & Integrasi', 'icon' => 'fa-solid fa-book-bookmark'], ]" active="tab-primitives" variant="pills" ><div id="tab-primitives" class="ui-tab-pane is-active"><x-ui.card title="Button Variants & Sizes" subtitle="Komponen <x-ui.button> dengan berbagai varian tema, ukuran, dan status" icon="fa-solid fa-hand-pointer" style="margin-bottom: 24px;"><x-slot name="actions"><button type="button" class="ui-code-toggle-pill" onclick="toggleShowcaseCode('code-buttons', this)"><i class="fa-solid fa-code"></i><span> Lihat Kode </span></button></x-slot><div style="margin-bottom: 20px;"><div style="font-size: 13px; font-weight: 700; color: var(--text-muted); margin-bottom: 10px; text-transform: uppercase;"> 1. Varian Warna </div><div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;"><x-ui.button variant="primary"> Primary </x-ui.button><x-ui.button variant="secondary"> Secondary </x-ui.button><x-ui.button variant="success" icon="fa-solid fa-check"> Success </x-ui.button><x-ui.button variant="danger" icon="fa-solid fa-trash"> Danger </x-ui.button><x-ui.button variant="warning" icon="fa-solid fa-triangle-exclamation"> Warning </x-ui.button><x-ui.button variant="info" icon="fa-solid fa-circle-info"> Info </x-ui.button><x-ui.button variant="outline"> Outline </x-ui.button><x-ui.button variant="ghost"> Ghost </x-ui.button></div></div><div style="margin-bottom: 20px;"><div style="font-size: 13px; font-weight: 700; color: var(--text-muted); margin-bottom: 10px; text-transform: uppercase;"> 2. Ukuran & Bentuk </div><div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;"><x-ui.button size="sm" variant="primary"> Small (sm) </x-ui.button><x-ui.button size="md" variant="primary"> Medium (md) </x-ui.button><x-ui.button size="lg" variant="primary"> Large (lg) </x-ui.button><x-ui.button variant="primary" pill> Pill Shape </x-ui.button><x-ui.button variant="primary" disabled> Disabled State </x-ui.button><x-ui.button variant="primary" loading> Loading... </x-ui.button></div></div><div><div style="font-size: 13px; font-weight: 700; color: var(--text-muted); margin-bottom: 10px; text-transform: uppercase;"> 3. Dropdown Menu Trigger </div><div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;"><x-ui.dropdown label="Menu Tindakan" icon="fa-solid fa-ellipsis-vertical" variant="secondary"><x-ui.dropdown-item icon="fa-solid fa-eye" href="#view"> Lihat Detail </x-ui.dropdown-item><x-ui.dropdown-item icon="fa-solid fa-pen" href="#edit"> Edit Data </x-ui.dropdown-item><x-ui.dropdown-item divider /><x-ui.dropdown-item icon="fa-solid fa-trash" danger href="#delete"> Hapus Data </x-ui.dropdown-item></x-ui.dropdown></div></div><div id="code-buttons" class="ui-code-drawer" style="display: none; margin-top: 20px;"><div class="ui-code-box"><div class="ui-code-header"><div class="ui-code-lang"><i class="fa-brands fa-laravel" style="color: #f43f5e;"></i> Blade Component &bull; <code>&lt;x-ui.button&gt;</code> & <code>&lt;x-ui.dropdown&gt;</code> </div><button type="button" class="ui-copy-btn" onclick="copyShowcaseSnippet('raw-code-buttons', this)"><i class="fa-regular fa-copy"></i><span> Salin Kode </span></button></div> <pre class="ui-code-pre"><code>{{-- Varian Warna --}}
&lt;x-ui.button variant="primary"&gt;Primary&lt;/x-ui.button&gt;
&lt;x-ui.button variant="success" icon="fa-solid fa-check"&gt;Success&lt;/x-ui.button&gt;
&lt;x-ui.button variant="danger" icon="fa-solid fa-trash"&gt;Danger&lt;/x-ui.button&gt;
&lt;x-ui.button variant="outline"&gt;Outline&lt;/x-ui.button&gt;

{{-- Ukuran, Pill, Loading & Link Mode --}}
&lt;x-ui.button size="sm" variant="primary"&gt;Small&lt;/x-ui.button&gt;
&lt;x-ui.button size="lg" variant="primary" pill&gt;Pill Shape&lt;/x-ui.button&gt;
&lt;x-ui.button variant="primary" loading&gt;Loading...&lt;/x-ui.button&gt;
&lt;x-ui.button href="{{ url('/profile') }}" variant="secondary" icon="fa-solid fa-arrow-right"&gt;Sebagai Link (a tag)&lt;/x-ui.button&gt;

{{-- Dropdown Menu --}}
&lt;x-ui.dropdown label="Menu Tindakan" icon="fa-solid fa-ellipsis-vertical" variant="secondary"&gt;
    &lt;x-ui.dropdown-item icon="fa-solid fa-eye" href="#view"&gt;Lihat Detail&lt;/x-ui.dropdown-item&gt;
    &lt;x-ui.dropdown-item icon="fa-solid fa-pen" href="#edit"&gt;Edit Data&lt;/x-ui.dropdown-item&gt;
    &lt;x-ui.dropdown-item divider /&gt;
    &lt;x-ui.dropdown-item icon="fa-solid fa-trash" danger href="#delete"&gt;Hapus Data&lt;/x-ui.dropdown-item&gt;
&lt;/x-ui.dropdown&gt;</code></pre> </div><div class="ui-props-table-wrap"><table class="ui-props-table"><thead><tr><th> Prop / Atribut </th><th> Tipe Data </th><th> Default </th><th> Deskripsi & Opsi Nilai </th></tr></thead><tbody><tr><td> <code>variant</code> </td><td> string </td><td> <code>'primary'</code> </td><td> <code>primary</code>, <code>secondary</code>, <code>success</code>, <code>danger</code>, <code>warning</code>, <code>info</code>, <code>outline</code>, <code>ghost</code> </td></tr><tr><td> <code>size</code> </td><td> string </td><td> <code>'md'</code> </td><td> <code>sm</code> (kecil), <code>md</code> (standar), <code>lg</code> (besar) </td></tr><tr><td> <code>icon</code> / <code>iconRight</code> </td><td> string </td><td> <code>null</code> </td><td> Kelas icon FontAwesome (contoh: <code>fa-solid fa-plus</code>) </td></tr><tr><td> <code>href</code> </td><td> string|null </td><td> <code>null</code> </td><td> Jika diisi, komponen otomatis di-render sebagai tag <code>&lt;a&gt;</code> bukan <code>&lt;button&gt;</code> </td></tr><tr><td> <code>pill</code> / <code>block</code> / <code>loading</code> / <code>disabled</code> </td><td> boolean </td><td> <code>false</code> </td><td> Modifier bentuk membulat (pill), lebar penuh 100% (block), status animasi loading, atau disabled </td></tr></tbody></table></div> <textarea id="raw-code-buttons" style="display:none;">@verbatim{{-- Varian Warna --}}
<x-ui.button variant="primary">Primary</x-ui.button>
<x-ui.button variant="success" icon="fa-solid fa-check">Success</x-ui.button>
<x-ui.button variant="danger" icon="fa-solid fa-trash">Danger</x-ui.button>
<x-ui.button variant="outline">Outline</x-ui.button>

{{-- Ukuran, Pill, Loading & Link Mode --}}
<x-ui.button size="sm" variant="primary">Small</x-ui.button>
<x-ui.button size="lg" variant="primary" pill>Pill Shape</x-ui.button>
<x-ui.button variant="primary" loading>Loading...</x-ui.button>
<x-ui.button href="{{ url('/profile') }}" variant="secondary" icon="fa-solid fa-arrow-right">Sebagai Link (a tag)</x-ui.button>

{{-- Dropdown Menu --}}
<x-ui.dropdown label="Menu Tindakan" icon="fa-solid fa-ellipsis-vertical" variant="secondary">
    <x-ui.dropdown-item icon="fa-solid fa-eye" href="#view">Lihat Detail</x-ui.dropdown-item>
    <x-ui.dropdown-item icon="fa-solid fa-pen" href="#edit">Edit Data</x-ui.dropdown-item>
    <x-ui.dropdown-item divider />
    <x-ui.dropdown-item icon="fa-solid fa-trash" danger href="#delete">Hapus Data</x-ui.dropdown-item>
</x-ui.dropdown>@endverbatim</textarea> </div></x-ui.card><x-ui.card title="Badge & Status Dots" subtitle="Komponen <x-ui.badge> dan <x-ui.status-indicator> " icon="fa-solid fa-tag" style="margin-bottom: 24px;"><x-slot name="actions"><button type="button" class="ui-code-toggle-pill" onclick="toggleShowcaseCode('code-badges', this)"><i class="fa-solid fa-code"></i><span> Lihat Kode </span></button></x-slot><div style="margin-bottom: 20px;"><div style="font-size: 13px; font-weight: 700; color: var(--text-muted); margin-bottom: 10px; text-transform: uppercase;"> 1. Subtle Badges (Default) </div><div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;"><x-ui.badge variant="primary"> Primary </x-ui.badge><x-ui.badge variant="success" dot> Active </x-ui.badge><x-ui.badge variant="warning" dot> Pending </x-ui.badge><x-ui.badge variant="danger" dot> Inactive </x-ui.badge><x-ui.badge variant="info"> Information </x-ui.badge><x-ui.badge variant="secondary"> Neutral </x-ui.badge></div></div><div style="margin-bottom: 20px;"><div style="font-size: 13px; font-weight: 700; color: var(--text-muted); margin-bottom: 10px; text-transform: uppercase;"> 2. Solid & Outline Badges </div><div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;"><x-ui.badge variant="primary" style="solid" pill> Solid Pill </x-ui.badge><x-ui.badge variant="success" style="solid" pill> Verified </x-ui.badge><x-ui.badge variant="danger" style="solid" pill> Suspended </x-ui.badge><x-ui.badge variant="primary" style="outline"> Outline </x-ui.badge><x-ui.badge variant="success" style="outline"> Approved </x-ui.badge><x-ui.badge variant="warning" style="outline"> Warning </x-ui.badge></div></div><div><div style="font-size: 13px; font-weight: 700; color: var(--text-muted); margin-bottom: 10px; text-transform: uppercase;"> 3. Real-time Status Indicators </div><div style="display: flex; gap: 20px; flex-wrap: wrap; align-items: center;"><x-ui.status-indicator status="active" pulse label="System Online (Pulsing)" /><x-ui.status-indicator status="warning" label="Sync Pending" /><x-ui.status-indicator status="danger" pulse label="Server Down (Critical)" /><x-ui.status-indicator status="inactive" label="Offline" /></div></div><div id="code-badges" class="ui-code-drawer" style="display: none; margin-top: 20px;"><div class="ui-code-box"><div class="ui-code-header"><div class="ui-code-lang"><i class="fa-brands fa-laravel" style="color: #f43f5e;"></i> Blade Component &bull; <code>&lt;x-ui.badge&gt;</code> & <code>&lt;x-ui.status-indicator&gt;</code> </div><button type="button" class="ui-copy-btn" onclick="copyShowcaseSnippet('raw-code-badges', this)"><i class="fa-regular fa-copy"></i><span> Salin Kode </span></button></div> <pre class="ui-code-pre"><code>{{-- Subtle Badge dengan Dot --}}
&lt;x-ui.badge variant="success" dot&gt;Active&lt;/x-ui.badge&gt;
&lt;x-ui.badge variant="warning" dot&gt;Pending&lt;/x-ui.badge&gt;
&lt;x-ui.badge variant="danger" dot&gt;Inactive&lt;/x-ui.badge&gt;

{{-- Solid & Outline Pill Badges --}}
&lt;x-ui.badge variant="primary" style="solid" pill&gt;Solid Pill&lt;/x-ui.badge&gt;
&lt;x-ui.badge variant="success" style="outline"&gt;Approved&lt;/x-ui.badge&gt;

{{-- Status Indicator (Dot dengan Pulse Animasi) --}}
&lt;x-ui.status-indicator status="active" pulse label="System Online (Pulsing)" /&gt;
&lt;x-ui.status-indicator status="danger" pulse label="Server Down (Critical)" /&gt;
&lt;x-ui.status-indicator status="warning" label="Sync Pending" /&gt;
&lt;x-ui.status-indicator status="inactive" label="Offline" /&gt;</code></pre> </div><div class="ui-props-table-wrap"><table class="ui-props-table"><thead><tr><th> Komponen </th><th> Prop Utama </th><th> Default </th><th> Keterangan </th></tr></thead><tbody><tr><td> <code>&lt;x-ui.badge&gt;</code> </td><td> <code>variant</code>, <code>style</code>, <code>pill</code>, <code>dot</code>, <code>size</code> </td><td> <code>variant="primary"</code>, <code>style="subtle"</code> </td><td> Style mendukung: <code>subtle</code>, <code>solid</code>, <code>outline</code>. Boolean <code>dot</code> memunculkan titik status. </td></tr><tr><td> <code>&lt;x-ui.status-indicator&gt;</code> </td><td> <code>status</code>, <code>pulse</code>, <code>label</code> </td><td> <code>status="active"</code>, <code>pulse=false</code> </td><td> Status mendukung: <code>active</code>, <code>warning</code>, <code>danger</code>, <code>inactive</code>. Prop <code>pulse</code> memberi efek radar animasi. </td></tr></tbody></table></div> <textarea id="raw-code-badges" style="display:none;">{{-- Subtle Badge dengan Dot --}}
<x-ui.badge variant="success" dot>Active</x-ui.badge>
<x-ui.badge variant="warning" dot>Pending</x-ui.badge>
<x-ui.badge variant="danger" dot>Inactive</x-ui.badge>

{{-- Solid & Outline Pill Badges --}}
<x-ui.badge variant="primary" style="solid" pill>Solid Pill</x-ui.badge>
<x-ui.badge variant="success" style="outline">Approved</x-ui.badge>

{{-- Status Indicator (Dot dengan Pulse Animasi) --}}
<x-ui.status-indicator status="active" pulse label="System Online (Pulsing)" />
<x-ui.status-indicator status="danger" pulse label="Server Down (Critical)" />
<x-ui.status-indicator status="warning" label="Sync Pending" />
<x-ui.status-indicator status="inactive" label="Offline" /></textarea> </div></x-ui.card><x-ui.card title="Alerts & Callouts" subtitle="Komponen <x-ui.alert> dengan pesan kontekstual dan tombol tutup" icon="fa-solid fa-triangle-exclamation"><x-slot name="actions"><button type="button" class="ui-code-toggle-pill" onclick="toggleShowcaseCode('code-alerts', this)"><i class="fa-solid fa-code"></i><span> Lihat Kode </span></button></x-slot><div style="display: flex; flex-direction: column; gap: 12px;"><x-ui.alert variant="info" title="Informasi Sistem" dismissible accent> Komponen sistem UI dirancang menggunakan Vanilla CSS dan Native Blade anonymous component tanpa dependensi eksternal. </x-ui.alert><x-ui.alert variant="success" title="Pembaruan Berhasil" dismissible accent> Seluruh 13 modul HMVC kini dapat menggunakan komponen UI terstandarisasi dengan mudah. </x-ui.alert><x-ui.alert variant="warning" title="Perhatian" dismissible accent> Pastikan untuk selalu memvalidasi input formulir sebelum melakukan commit data ke database. </x-ui.alert><x-ui.alert variant="danger" title="Akses Ditolak" dismissible accent> Modul ini memerlukan lisensi aktif atau otoritas level Developer untuk mengakses terminal. </x-ui.alert></div><div id="code-alerts" class="ui-code-drawer" style="display: none; margin-top: 20px;"><div class="ui-code-box"><div class="ui-code-header"><div class="ui-code-lang"><i class="fa-brands fa-laravel" style="color: #f43f5e;"></i> Blade Component &bull; <code>&lt;x-ui.alert&gt;</code> </div><button type="button" class="ui-copy-btn" onclick="copyShowcaseSnippet('raw-code-alerts', this)"><i class="fa-regular fa-copy"></i><span> Salin Kode </span></button></div> <pre class="ui-code-pre"><code>&lt;x-ui.alert variant="info" title="Informasi Sistem" dismissible accent&gt;
    Komponen sistem UI dirancang menggunakan Vanilla CSS tanpa dependensi eksternal.
&lt;/x-ui.alert&gt;

&lt;x-ui.alert variant="success" title="Pembaruan Berhasil" dismissible accent&gt;
    Seluruh 13 modul HMVC kini terintegrasi dengan baik.
&lt;/x-ui.alert&gt;

&lt;x-ui.alert variant="warning" title="Perhatian" dismissible accent&gt;
    Pastikan memvalidasi input sebelum menyimpan data.
&lt;/x-ui.alert&gt;

&lt;x-ui.alert variant="danger" title="Akses Ditolak" dismissible accent&gt;
    Modul ini memerlukan lisensi aktif.
&lt;/x-ui.alert&gt;</code></pre> </div><div class="ui-props-table-wrap"><table class="ui-props-table"><thead><tr><th> Prop </th><th> Tipe </th><th> Default </th><th> Deskripsi </th></tr></thead><tbody><tr><td> <code>variant</code> </td><td> string </td><td> <code>'info'</code> </td><td> Varian status: <code>info</code>, <code>success</code>, <code>warning</code>, <code>danger</code> </td></tr><tr><td> <code>title</code> </td><td> string|null </td><td> <code>null</code> </td><td> Judul tebal di atas teks deskripsi pesan </td></tr><tr><td> <code>dismissible</code> </td><td> boolean </td><td> <code>false</code> </td><td> Jika <code>true</code>, menampilkan tombol (X) untuk menutup alert secara interaktif </td></tr><tr><td> <code>accent</code> </td><td> boolean </td><td> <code>false</code> </td><td> Menambahkan border tebal di sisi kiri sesuai warna varian </td></tr></tbody></table></div> <textarea id="raw-code-alerts" style="display:none;"><x-ui.alert variant="info" title="Informasi Sistem" dismissible accent>
    Komponen sistem UI dirancang menggunakan Vanilla CSS tanpa dependensi eksternal.
</x-ui.alert>

<x-ui.alert variant="success" title="Pembaruan Berhasil" dismissible accent>
    Seluruh 13 modul HMVC kini terintegrasi dengan baik.
</x-ui.alert>

<x-ui.alert variant="warning" title="Perhatian" dismissible accent>
    Pastikan memvalidasi input sebelum menyimpan data.
</x-ui.alert>

<x-ui.alert variant="danger" title="Akses Ditolak" dismissible accent>
    Modul ini memerlukan lisensi aktif.
</x-ui.alert></textarea> </div></x-ui.card></div><div id="tab-cards" class="ui-tab-pane"><div style="margin-bottom: 24px;"><div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;"><div style="font-size: 15px; font-weight: 700; color: #fff;"> Stat Cards Pattern </div><button type="button" class="ui-code-toggle-pill" onclick="toggleShowcaseCode('code-stat-cards', this)"><i class="fa-solid fa-code"></i><span> Lihat Kode </span></button></div><div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;"><div class="ui-stat-card"><div><div class="ui-stat-label"> Total Modul HMVC </div><div class="ui-stat-value"> 13 Modul </div><div class="ui-stat-trend is-up"><i class="fa-solid fa-arrow-trend-up"></i> 100% Terintegrasi </div></div><div class="ui-card-icon" style="background: rgba(99, 102, 241, 0.15); color: var(--primary);"><i class="fa-solid fa-cubes"></i></div></div><div class="ui-stat-card"><div><div class="ui-stat-label"> UI Components </div><div class="ui-stat-value"> 27 Komponen </div><div class="ui-stat-trend is-up"><i class="fa-solid fa-check-double"></i> Siap Pakai </div></div><div class="ui-card-icon" style="background: rgba(16, 185, 129, 0.15); color: var(--success);"><i class="fa-solid fa-layer-group"></i></div></div><div class="ui-stat-card"><div><div class="ui-stat-label"> Kecepatan Render </div><div class="ui-stat-value"> ~1.2 ms </div><div class="ui-stat-trend is-up"><i class="fa-solid fa-bolt"></i> Ultra Fast </div></div><div class="ui-card-icon" style="background: rgba(56, 189, 248, 0.15); color: var(--info);"><i class="fa-solid fa-gauge-high"></i></div></div></div><div id="code-stat-cards" class="ui-code-drawer" style="display: none; margin-top: 16px;"><div class="ui-code-box"><div class="ui-code-header"><div class="ui-code-lang"><i class="fa-solid fa-code" style="color: #38bdf8;"></i> HTML & CSS Pattern &bull; <code>.ui-stat-card</code> </div><button type="button" class="ui-copy-btn" onclick="copyShowcaseSnippet('raw-code-stat-cards', this)"><i class="fa-regular fa-copy"></i><span> Salin Kode </span></button></div> <pre class="ui-code-pre"><code>&lt;div class="ui-stat-card"&gt;
    &lt;div&gt;
        &lt;div class="ui-stat-label"&gt;Total Pengguna Aktif&lt;/div&gt;
        &lt;div class="ui-stat-value"&gt;1,420&lt;/div&gt;
        &lt;div class="ui-stat-trend is-up"&gt;&lt;i class="fa-solid fa-arrow-trend-up"&gt;&lt;/i&gt; +12% bulan ini&lt;/div&gt;
    &lt;/div&gt;
    &lt;div class="ui-card-icon" style="background: rgba(99, 102, 241, 0.15); color: var(--primary);"&gt;
        &lt;i class="fa-solid fa-users"&gt;&lt;/i&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre> </div> <textarea id="raw-code-stat-cards" style="display:none;"><div class="ui-stat-card">
    <div>
        <div class="ui-stat-label">Total Pengguna Aktif</div>
        <div class="ui-stat-value">1,420</div>
        <div class="ui-stat-trend is-up"><i class="fa-solid fa-arrow-trend-up"></i> +12% bulan ini</div>
    </div>
    <div class="ui-card-icon" style="background: rgba(99, 102, 241, 0.15); color: var(--primary);">
        <i class="fa-solid fa-users"></i>
    </div>
</div></textarea> </div></div><div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;"><x-ui.card title="Loading & Skeleton" subtitle="Indikator pemuatan data" icon="fa-solid fa-spinner"><x-slot name="actions"><button type="button" class="ui-code-toggle-pill" onclick="toggleShowcaseCode('code-loading', this)"><i class="fa-solid fa-code"></i><span> Lihat Kode </span></button></x-slot><div style="display: flex; flex-direction: column; gap: 16px;"><x-ui.loading type="spinner" size="md" text="Memuat data modul..." /><x-ui.loading type="skeleton" height="24px" width="70%" /><x-ui.loading type="skeleton" height="16px" width="100%" /><x-ui.loading type="skeleton" height="16px" width="40%" /></div><div id="code-loading" class="ui-code-drawer" style="display: none; margin-top: 20px;"><div class="ui-code-box"><div class="ui-code-header"><div class="ui-code-lang"><i class="fa-brands fa-laravel" style="color: #f43f5e;"></i> Blade Component &bull; <code>&lt;x-ui.loading&gt;</code> </div><button type="button" class="ui-copy-btn" onclick="copyShowcaseSnippet('raw-code-loading', this)"><i class="fa-regular fa-copy"></i><span> Salin Kode </span></button></div> <pre class="ui-code-pre"><code>{{-- Spinner Loader --}}
&lt;x-ui.loading type="spinner" size="md" text="Memuat data modul..." /&gt;

{{-- Skeleton Loader Placeholder --}}
&lt;x-ui.loading type="skeleton" height="24px" width="70%" /&gt;
&lt;x-ui.loading type="skeleton" height="16px" width="100%" /&gt;
&lt;x-ui.loading type="skeleton" height="16px" width="40%" /&gt;</code></pre> </div> <textarea id="raw-code-loading" style="display:none;">{{-- Spinner Loader --}}
<x-ui.loading type="spinner" size="md" text="Memuat data modul..." />

{{-- Skeleton Loader Placeholder --}}
<x-ui.loading type="skeleton" height="24px" width="70%" />
<x-ui.loading type="skeleton" height="16px" width="100%" />
<x-ui.loading type="skeleton" height="16px" width="40%" /></textarea> </div></x-ui.card><x-ui.card title="Empty State Container" subtitle="Tampilan saat data kosong" icon="fa-solid fa-inbox"><x-slot name="actions"><button type="button" class="ui-code-toggle-pill" onclick="toggleShowcaseCode('code-empty', this)"><i class="fa-solid fa-code"></i><span> Lihat Kode </span></button></x-slot><x-ui.empty-state icon="fa-solid fa-folder-open" title="Belum Ada Item" description="Mulai tambahkan entitas pertama Anda pada formulir penambahan modul." ><x-slot name="action"><x-ui.button variant="primary" size="sm" icon="fa-solid fa-plus"> Buat Baru </x-ui.button></x-slot></x-ui.empty-state><div id="code-empty" class="ui-code-drawer" style="display: none; margin-top: 20px;"><div class="ui-code-box"><div class="ui-code-header"><div class="ui-code-lang"><i class="fa-brands fa-laravel" style="color: #f43f5e;"></i> Blade Component &bull; <code>&lt;x-ui.empty-state&gt;</code> </div><button type="button" class="ui-copy-btn" onclick="copyShowcaseSnippet('raw-code-empty', this)"><i class="fa-regular fa-copy"></i><span> Salin Kode </span></button></div> <pre class="ui-code-pre"><code>&lt;x-ui.empty-state 
    icon="fa-solid fa-folder-open" 
    title="Belum Ada Item" 
    description="Mulai tambahkan entitas pertama Anda pada formulir penambahan modul."
&gt;
    &lt;x-slot name="action"&gt;
        &lt;x-ui.button variant="primary" size="sm" icon="fa-solid fa-plus"&gt;
            Buat Baru
        &lt;/x-ui.button&gt;
    &lt;/x-slot&gt;
&lt;/x-ui.empty-state&gt;</code></pre> </div> <textarea id="raw-code-empty" style="display:none;"><x-ui.empty-state 
    icon="fa-solid fa-folder-open" 
    title="Belum Ada Item" 
    description="Mulai tambahkan entitas pertama Anda pada formulir penambahan modul."
>
    <x-slot name="action">
        <x-ui.button variant="primary" size="sm" icon="fa-solid fa-plus">
            Buat Baru
        </x-ui.button>
    </x-slot>
</x-ui.empty-state></textarea> </div></x-ui.card></div></div><div id="tab-forms" class="ui-tab-pane"><x-ui.card title="Form Controls & Inputs" subtitle="Standardisasi elemen formulir lengkap dengan validasi dan styling modern" icon="fa-solid fa-pen-nib"><x-slot name="actions"><button type="button" class="ui-code-toggle-pill" onclick="toggleShowcaseCode('code-forms', this)"><i class="fa-solid fa-code"></i><span> Lihat Kode </span></button></x-slot><form onsubmit="event.preventDefault(); DapToast.success('Formulir berhasil divalidasi dan disimulasikan terkirim!');"><div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;"><x-ui.input name="full_name" label="Nama Lengkap" placeholder="Contoh: Mochammad Daffa" value="Mochammad Daffa" required icon="fa-regular fa-user" help="Masukkan nama sesuai kartu identitas Anda." /><x-ui.input type="email" name="email" label="Alamat Email" placeholder="nama@domain.com" value="developer@dapcode.com" required icon="fa-regular fa-envelope" /><x-ui.select name="role" label="Hak Akses / Role" :options="[ 'admin' => 'Administrator', 'developer' => 'Developer & Maintainer', 'author' => 'Content Author', 'viewer' => 'Read Only' ]" selected="developer" /><x-ui.date-picker name="birthdate" label="Tanggal Efektif" value="2026-09-13" /></div><x-ui.textarea name="notes" label="Deskripsi / Catatan Tambahan" placeholder="Tuliskan keterangan detail konfigurasi di sini..." rows="3" help="Mendukung teks hingga 500 karakter." /><div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; margin-top: 10px; margin-bottom: 20px;"><div><div class="ui-label" style="margin-bottom: 8px;"> Pengaturan Notifikasi (Switch) </div><x-ui.checkbox name="notify_email" label="Kirim notifikasi via Email" switch checked /><x-ui.checkbox name="notify_sms" label="Kirim ringkasan mingguan" switch /></div><div><div class="ui-label" style="margin-bottom: 8px;"> Pilihan Opsi (Radio) </div><div style="display: flex; flex-direction: column; gap: 8px;"><x-ui.radio name="visibility" value="public" label="Publik (Terlihat semua orang)" checked /><x-ui.radio name="visibility" value="private" label="Privat (Hanya akun terdaftar)" /></div></div><div><div class="ui-label" style="margin-bottom: 8px;"> Persetujuan (Checkbox Standar) </div><x-ui.checkbox name="agree_terms" label="Saya menyetujui kebijakan privasi" checked required /></div></div><x-ui.file-upload name="attachment" label="Unggah Berkas Pendukung" accept=".pdf,.png,.jpg"> Format yang didukung: PDF, PNG, JPG (Maks 5MB) </x-ui.file-upload><div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;"><x-ui.button type="reset" variant="ghost"> Batal </x-ui.button><x-ui.button type="submit" variant="primary" icon="fa-solid fa-floppy-disk"> Simpan Data </x-ui.button></div></form><div id="code-forms" class="ui-code-drawer" style="display: none; margin-top: 24px;"><div class="ui-code-box"><div class="ui-code-header"><div class="ui-code-lang"><i class="fa-brands fa-laravel" style="color: #f43f5e;"></i> Blade Components &bull; Form Controls </div><button type="button" class="ui-copy-btn" onclick="copyShowcaseSnippet('raw-code-forms', this)"><i class="fa-regular fa-copy"></i><span> Salin Kode </span></button></div> <pre class="ui-code-pre"><code>{{-- Input Teks dengan Icon & Bantuan Validasi --}}
&lt;x-ui.input 
    name="full_name" 
    label="Nama Lengkap" 
    placeholder="Contoh: Mochammad Daffa" 
    value="&#123;&#123; old('full_name', $user-&gt;name ?? '') &#125;&#125;" 
    required 
    icon="fa-regular fa-user" 
    help="Masukkan nama sesuai kartu identitas Anda." 
/&gt;

{{-- Select Dropdown dengan Key-Value Array --}}
&lt;x-ui.select 
    name="role" 
    label="Hak Akses / Role" 
    :options="[
        'admin' =&gt; 'Administrator',
        'developer' =&gt; 'Developer &amp; Maintainer',
        'author' =&gt; 'Content Author'
    ]"
    selected="developer"
    required
/&gt;

{{-- Date Picker --}}
&lt;x-ui.date-picker name="birthdate" label="Tanggal Efektif" value="2026-09-13" /&gt;

{{-- Textarea Multi-line --}}
&lt;x-ui.textarea name="notes" label="Deskripsi" rows="3" placeholder="Tuliskan keterangan..." /&gt;

{{-- Toggle Switch & Checkbox --}}
&lt;x-ui.checkbox name="notify_email" label="Kirim notifikasi via Email" switch checked /&gt;
&lt;x-ui.checkbox name="agree_terms" label="Saya menyetujui kebijakan privasi" required /&gt;

{{-- Radio Group --}}
&lt;x-ui.radio name="visibility" value="public" label="Publik" checked /&gt;
&lt;x-ui.radio name="visibility" value="private" label="Privat" /&gt;

{{-- File Upload Drag & Drop --}}
&lt;x-ui.file-upload name="attachment" label="Unggah Berkas" accept=".pdf,.png,.jpg"&gt;
    Format didukung: PDF, PNG, JPG (Maks 5MB)
&lt;/x-ui.file-upload&gt;</code></pre> </div><div class="ui-props-table-wrap"><table class="ui-props-table"><thead><tr><th> Komponen </th><th> Prop Utama </th><th> Fungsi & Perilaku </th></tr></thead><tbody><tr><td> <code>&lt;x-ui.input&gt;</code> </td><td> <code>type</code>, <code>name</code>, <code>label</code>, <code>value</code>, <code>icon</code>, <code>required</code>, <code>help</code>, <code>error</code> </td><td> Mendukung icon awalan (prefix), penanda bintang (*), pesan helper, serta error validasi otomatis dari Laravel session <code>$errors</code>. </td></tr><tr><td> <code>&lt;x-ui.select&gt;</code> </td><td> <code>name</code>, <code>label</code>, <code>:options</code>, <code>selected</code>, <code>placeholder</code> </td><td> Menerima array key-value asosiatif atau list biasa. Otomatis memberi styling custom dropdown panah. </td></tr><tr><td> <code>&lt;x-ui.checkbox&gt;</code> </td><td> <code>name</code>, <code>label</code>, <code>switch</code>, <code>checked</code> </td><td> Jika diberi prop <code>switch</code> (boolean), komponen beralih visual menjadi toggle switch animasi modern. </td></tr></tbody></table></div> <textarea id="raw-code-forms" style="display:none;">@verbatim<x-ui.input 
    name="full_name" 
    label="Nama Lengkap" 
    placeholder="Contoh: Mochammad Daffa" 
    value="{{ old('full_name', $user->name ?? '') }}" 
    required 
    icon="fa-regular fa-user" 
    help="Masukkan nama sesuai kartu identitas Anda." 
/>

<x-ui.select 
    name="role" 
    label="Hak Akses / Role" 
    :options="[
        'admin' => 'Administrator',
        'developer' => 'Developer & Maintainer',
        'author' => 'Content Author'
    ]"
    selected="developer"
    required
/>

<x-ui.date-picker name="birthdate" label="Tanggal Efektif" value="2026-09-13" />

<x-ui.textarea name="notes" label="Deskripsi" rows="3" placeholder="Tuliskan keterangan..." />

<x-ui.checkbox name="notify_email" label="Kirim notifikasi via Email" switch checked />
<x-ui.checkbox name="agree_terms" label="Saya menyetujui kebijakan privasi" required />

<x-ui.radio name="visibility" value="public" label="Publik" checked />
<x-ui.radio name="visibility" value="private" label="Privat" />

<x-ui.file-upload name="attachment" label="Unggah Berkas" accept=".pdf,.png,.jpg">
    Format didukung: PDF, PNG, JPG (Maks 5MB)
</x-ui.file-upload>@endverbatim</textarea> </div></x-ui.card></div><div id="tab-tables" class="ui-tab-pane"><div style="display: flex; justify-content: flex-end; margin-bottom: 12px;"><button type="button" class="ui-code-toggle-pill" onclick="toggleShowcaseCode('code-tables', this)"><i class="fa-solid fa-code"></i><span> Lihat Kode Tabel & Navigasi </span></button></div><x-ui.filter resetUrl="#reset"><div style="flex: 1; min-width: 200px;"><x-ui.search placeholder="Cari data pengguna..." name="user_search" /></div><div style="min-width: 160px;"><x-ui.select name="status_filter" placeholder="Semua Status" :options="['active' => 'Aktif', 'pending' => 'Menunggu', 'suspended' => 'Ditangguhkan']" /></div></x-ui.filter><x-ui.datatable id="demo-datatable" searchPlaceholder="Cari dalam tabel..." refreshable auto-refresh-toggle ><x-slot name="actions"><x-ui.button variant="secondary" size="sm" icon="fa-solid fa-file-export"> Export </x-ui.button><x-ui.button variant="primary" size="sm" icon="fa-solid fa-plus"> Tambah Data </x-ui.button></x-slot><x-ui.table striped hover :headers="['#', 'Nama Lengkap', 'Email', 'Role', 'Status', 'Terakhir Aktif', 'Aksi']"><tr><td><strong> 1 </strong></td><td><div style="display: flex; align-items: center; gap: 10px;"><div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px;"> MD </div><div><div style="font-weight: 600;"> Mochammad Daffa </div><div style="font-size: 11.5px; color: var(--text-muted);"> ID: #USR-001 </div></div></div></td><td> developer @dapcode .com </td><td><x-ui.badge variant="primary"> Lead Architect </x-ui.badge></td><td><x-ui.badge variant="success" dot> Aktif </x-ui.badge></td><td> Baru saja </td><td><div style="display: flex; gap: 6px;"><x-ui.tooltip text="Lihat rincian pengguna"><x-ui.button size="sm" variant="secondary" icon="fa-solid fa-eye" /></x-ui.tooltip><x-ui.tooltip text="Edit data"><x-ui.button size="sm" variant="outline" icon="fa-solid fa-pen" /></x-ui.tooltip></div></td></tr><tr><td><strong> 2 </strong></td><td><div style="display: flex; align-items: center; gap: 10px;"><div style="width: 32px; height: 32px; border-radius: 50%; background: var(--success); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px;"> AS </div><div><div style="font-weight: 600;"> Aegis Security Bot </div><div style="font-size: 11.5px; color: var(--text-muted);"> ID: #BOT-002 </div></div></div></td><td> bot @dapcode .internal </td><td><x-ui.badge variant="info"> Automated </x-ui.badge></td><td><x-ui.badge variant="success" dot> Aktif </x-ui.badge></td><td> 5 menit lalu </td><td><div style="display: flex; gap: 6px;"><x-ui.tooltip text="Lihat rincian bot"><x-ui.button size="sm" variant="secondary" icon="fa-solid fa-eye" /></x-ui.tooltip></div></td></tr><tr><td><strong> 3 </strong></td><td><div style="display: flex; align-items: center; gap: 10px;"><div style="width: 32px; height: 32px; border-radius: 50%; background: #64748b; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px;"> TU </div><div><div style="font-weight: 600;"> Test User Offline </div><div style="font-size: 11.5px; color: var(--text-muted);"> ID: #USR-003 </div></div></div></td><td> test @example .com </td><td><x-ui.badge variant="secondary"> Viewer </x-ui.badge></td><td><x-ui.badge variant="danger" dot> Ditangguhkan </x-ui.badge></td><td> 2 minggu lalu </td><td><div style="display: flex; gap: 6px;"><x-ui.tooltip text="Aktifkan kembali"><x-ui.button size="sm" variant="secondary" icon="fa-solid fa-rotate-left" /></x-ui.tooltip></div></td></tr></x-ui.table><x-slot name="pagination"><div class="ui-pagination"><div> Menampilkan <strong> 1 - 3 </strong> dari <strong> 3 </strong> data &bull; <span style="font-size: 11.5px; color: var(--text-muted);"><i class="fa-regular fa-clock"></i> Update: <span class="ui-table-last-updated" style="color: #38bdf8; font-weight: 600;">{{ date('H:i:s') }}</span></span></div><div class="ui-pagination-pages"><span class="ui-page-item is-disabled"><i class="fa-solid fa-chevron-left"></i></span><span class="ui-page-item is-active"> 1 </span><span class="ui-page-item is-disabled"><i class="fa-solid fa-chevron-right"></i></span></div></div></x-slot></x-ui.datatable><div id="code-tables" class="ui-code-drawer" style="display: none; margin-top: 20px;"><div class="ui-code-box"><div class="ui-code-header"><div class="ui-code-lang"><i class="fa-brands fa-laravel" style="color: #f43f5e;"></i> Blade & JavaScript API &bull; DataTable & DapTable Engine </div><button type="button" class="ui-copy-btn" onclick="copyShowcaseSnippet('raw-code-tables', this)"><i class="fa-regular fa-copy"></i><span> Salin Kode </span></button></div> <pre class="ui-code-pre"><code>{{-- 1. DataTable dengan Auto-Refresh & Tombol Refresh Bawaan --}}
&lt;x-ui.datatable 
    id="users-table" 
    endpoint="{{ url('/api/users/rows') }}" 
    refreshable 
    auto-refresh-toggle
    auto-refresh="10000"
&gt;
    &lt;x-slot name="actions"&gt;
        &lt;x-ui.button variant="primary" size="sm" icon="fa-solid fa-plus"&gt;Tambah Data&lt;/x-ui.button&gt;
    &lt;/x-slot&gt;

    &lt;x-ui.table striped hover :headers="['#', 'Nama', 'Email', 'Status', 'Aksi']"&gt;
        &lt;tr&gt;
            &lt;td&gt;1&lt;/td&gt;
            &lt;td&gt;Mochammad Daffa&lt;/td&gt;
            &lt;td&gt;developer@dapcode.com&lt;/td&gt;
            &lt;td&gt;&lt;x-ui.badge variant="success" dot&gt;Aktif&lt;/x-ui.badge&gt;&lt;/td&gt;
            &lt;td&gt;
                &lt;x-ui.tooltip text="Lihat detail data"&gt;
                    &lt;x-ui.button size="sm" variant="secondary" icon="fa-solid fa-eye" /&gt;
                &lt;/x-ui.tooltip&gt;
            &lt;/td&gt;
        &lt;/tr&gt;
    &lt;/x-ui.table&gt;

    &lt;x-slot name="pagination"&gt;
        &lt;div class="ui-pagination"&gt;
            &lt;div&gt;Terakhir update: &lt;span class="ui-table-last-updated"&gt;Sekarang&lt;/span&gt;&lt;/div&gt;
        &lt;/div&gt;
    &lt;/x-slot&gt;
&lt;/x-ui.datatable&gt;

{{-- 2. JavaScript Helper API (DapTable) --}}
&lt;script&gt;
// Segarkan tabel secara manual/terprogram via AJAX
await DapTable.refresh('users-table');

// Aktifkan auto-refresh berkala setiap 5 detik
DapTable.startAutoRefresh('users-table', 5000);

// Hentikan auto-refresh
DapTable.stopAutoRefresh('users-table');

// Toggle on/off auto-refresh
const isRunning = DapTable.toggleAutoRefresh('users-table', 5000);

// Event listener saat tabel selesai diperbarui
document.getElementById('users-table').addEventListener('dap:table:refreshed', (e) => {
    console.log('Tabel berhasil diperbarui:', e.detail.timestamp);
});
&lt;/script&gt;</code></pre> <div style="background: rgba(15, 23, 42, 0.6); padding: 12px 16px; border-top: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;"><span style="font-size: 12.5px; color: var(--text-muted); font-weight: 600;"><i class="fa-solid fa-bolt" style="color: #f59e0b;"></i> Uji Coba JavaScript API Langsung: </span><div style="display: flex; gap: 8px; flex-wrap: wrap;"><x-ui.button size="sm" variant="secondary" icon="fa-solid fa-rotate" onclick="DapTable.refresh('demo-datatable').then(() => DapToast.success('DapTable.refresh() berhasil dieksekusi!'));" > DapTable.refresh() </x-ui.button><x-ui.button size="sm" variant="primary" icon="fa-solid fa-clock-rotate-left" onclick="const active = DapTable.toggleAutoRefresh('demo-datatable', 4000); if(active) DapToast.info('Auto-refresh aktif setiap 4 detik'); else DapToast.warning('Auto-refresh dihentikan');" > DapTable.toggleAutoRefresh(4s) </x-ui.button></div></div></div><div class="ui-props-table-wrap"><table class="ui-props-table"><thead><tr><th> Prop / Atribut </th><th> Tipe Data </th><th> Default </th><th> Deskripsi Fitur Auto-Refresh & Data </th></tr></thead><tbody><tr><td> <code>id</code> </td><td> string </td><td> <code>'ui-datatable-...'</code> </td><td> ID elemen kontainer datatable untuk target pemanggilan API <code>DapTable.refresh(id)</code>. </td></tr><tr><td> <code>endpoint</code> </td><td> string|null </td><td> <code>null</code> </td><td> URL endpoint AJAX yang mengembalikan potongan HTML <code>&lt;tr&gt;</code> atau JSON data baris tabel. </td></tr><tr><td> <code>refreshable</code> </td><td> boolean </td><td> <code>false</code> </td><td> Jika <code>true</code>, memunculkan tombol <strong> Refresh </strong> bawaan pada toolbar dengan animasi putar. </td></tr><tr><td> <code>auto-refresh</code> </td><td> number|boolean </td><td> <code>null</code> </td><td> Interval polling otomatis dalam milidetik (contoh: <code>auto-refresh="5000"</code> untuk 5 detik). </td></tr><tr><td> <code>auto-refresh-toggle</code> </td><td> boolean </td><td> <code>false</code> </td><td> Menambahkan switch toggle interaktif <strong> "Auto Refresh" </strong> langsung pada toolbar tabel. </td></tr></tbody></table></div> <textarea id="raw-code-tables" style="display:none;">@verbatim{{-- 1. DataTable dengan Auto-Refresh & Tombol Refresh Bawaan --}}
<x-ui.datatable 
    id="users-table" 
    endpoint="{{ url('/api/users/rows') }}" 
    refreshable 
    auto-refresh-toggle
    auto-refresh="10000"
>
    <x-slot name="actions">
        <x-ui.button variant="primary" size="sm" icon="fa-solid fa-plus">Tambah Data</x-ui.button>
    </x-slot>

    <x-ui.table striped hover :headers="['#', 'Nama', 'Email', 'Status', 'Aksi']">
        <tr>
            <td>1</td>
            <td>Mochammad Daffa</td>
            <td>developer@dapcode.com</td>
            <td><x-ui.badge variant="success" dot>Aktif</x-ui.badge></td>
            <td>
                <x-ui.tooltip text="Lihat detail data">
                    <x-ui.button size="sm" variant="secondary" icon="fa-solid fa-eye" />
                </x-ui.tooltip>
            </td>
        </tr>
    </x-ui.table>

    <x-slot name="pagination">
        <div class="ui-pagination">
            <div>Terakhir update: <span class="ui-table-last-updated">Sekarang</span></div>
        </div>
    </x-slot>
</x-ui.datatable>

{{-- 2. JavaScript Helper API (DapTable) --}}
<script>await DapTable.refresh('users-table'); DapTable.startAutoRefresh('users-table', 5000); DapTable.stopAutoRefresh('users-table'); const isRunning = DapTable.toggleAutoRefresh('users-table', 5000); document.getElementById('users-table').addEventListener('dap:table:refreshed', (e) => { console.log('Tabel berhasil diperbarui:', e.detail.timestamp); });</script>@endverbatim</textarea> </div></div><div id="tab-interactive" class="ui-tab-pane"><x-ui.card title="Interactive Dialogs & Toast API" subtitle="Kemudahan memanggil dialog dan notifikasi langsung dari kode JavaScript" icon="fa-solid fa-code"><x-slot name="actions"><button type="button" class="ui-code-toggle-pill" onclick="toggleShowcaseCode('code-interactive', this)"><i class="fa-solid fa-code"></i><span> Lihat Kode JS API </span></button></x-slot><div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;"><div style="background: rgba(15, 23, 42, 0.7); border: 1px solid var(--border-color); border-radius: 12px; padding: 18px;"><h4 style="font-size: 15px; font-weight: 700; color: #fff; margin-bottom: 8px;"> DapToast Notification API </h4><p style="font-size: 13px; color: var(--text-muted); margin-bottom: 14px;"> Trigger notifikasi mengambang dengan satu baris kode JavaScript. </p><div style="display: flex; gap: 8px; flex-wrap: wrap;"><x-ui.button size="sm" variant="success" onclick="DapToast.success('Operasi penyimpanan berhasil!');"> Success </x-ui.button><x-ui.button size="sm" variant="danger" onclick="DapToast.error('Gagal menghubungi endpoint API.');"> Error </x-ui.button><x-ui.button size="sm" variant="warning" onclick="DapToast.warning('Sesi Anda akan berakhir dalam 5 menit.');"> Warning </x-ui.button><x-ui.button size="sm" variant="info" onclick="DapToast.info('Versi baru telah tersedia.');"> Info </x-ui.button></div></div><div style="background: rgba(15, 23, 42, 0.7); border: 1px solid var(--border-color); border-radius: 12px; padding: 18px;"><h4 style="font-size: 15px; font-weight: 700; color: #fff; margin-bottom: 8px;"> DapConfirm Promise API </h4><p style="font-size: 13px; color: var(--text-muted); margin-bottom: 14px;"> Dialog konfirmasi modern berbasis Promise (async/await) tanpa browser alert kuno. </p><div style="display: flex; gap: 8px; flex-wrap: wrap;"><x-ui.button size="sm" variant="danger" icon="fa-solid fa-trash" onclick="DapConfirm({title: 'Hapus Database?', message: 'Tindakan ini tidak dapat dibatalkan.', type: 'danger'}).then(ok => { if(ok) DapToast.success('Terkonfirmasi: Dihapus'); else DapToast.info('Dibatalkan'); });" > Konfirmasi Bahaya </x-ui.button><x-ui.button size="sm" variant="primary" icon="fa-solid fa-floppy-disk" onclick="DapConfirm({title: 'Publikasikan Perubahan?', message: 'Data akan langsung tayang di portal.', type: 'info', confirmText: 'Ya, Publikasikan'}).then(ok => { if(ok) DapToast.success('Perubahan dipublikasikan!'); });" > Konfirmasi Biasa </x-ui.button></div></div><div style="background: rgba(15, 23, 42, 0.7); border: 1px solid var(--border-color); border-radius: 12px; padding: 18px;"><h4 style="font-size: 15px; font-weight: 700; color: #fff; margin-bottom: 8px;"> Declarative Modal Component </h4><p style="font-size: 13px; color: var(--text-muted); margin-bottom: 14px;"> Buka modal menggunakan atribut <code>data-ui-modal-target="id"</code> atau <code>DapModal.open('id')</code>. </p><x-ui.button size="sm" variant="secondary" icon="fa-solid fa-up-right-from-square" data-ui-modal-target="demo-modal"> Pratinjau Modal Lengkap </x-ui.button></div></div><div id="code-interactive" class="ui-code-drawer" style="display: none; margin-top: 20px;"><div class="ui-code-box"><div class="ui-code-header"><div class="ui-code-lang"><i class="fa-brands fa-js" style="color: #f7df1e;"></i> JavaScript API &bull; DapToast, DapConfirm & DapModal </div><button type="button" class="ui-copy-btn" onclick="copyShowcaseSnippet('raw-code-interactive', this)"><i class="fa-regular fa-copy"></i><span> Salin Kode </span></button></div> <pre class="ui-code-pre"><code>// 1. DapToast API (Notifikasi Mengambang)
DapToast.success('Data berhasil diperbarui!', 'Sukses Disimpan');
DapToast.error('Gagal terhubung ke database server.', 'Koneksi Terputus');
DapToast.warning('Pastikan lisensi modul Anda tetap aktif.');
DapToast.info('Modul versi 1.2 siap diunduh.');

// 2. DapConfirm API (Dialog Konfirmasi Asynchronous)
DapConfirm({
    title: 'Hapus Data Pengguna?',
    message: 'Data yang terhapus tidak dapat dipulihkan kembali.',
    type: 'danger',           // 'danger' | 'info' | 'warning'
    confirmText: 'Ya, Hapus Data',
    cancelText: 'Batal'
}).then((confirmed) => {
    if (confirmed) {
        // Lakukan request AJAX hapus data
        DapToast.success('Data berhasil dihapus dari sistem.');
    } else {
        DapToast.info('Operasi penghapusan dibatalkan.');
    }
});

// 3. Modal Component & Triggering
// Cara A: Menggunakan HTML Data-Attribute (Otomatis & Tanpa JS tambahan)
// &lt;button data-ui-modal-target="modal-id"&gt;Buka Modal&lt;/button&gt;

// Cara B: Memanggil lewat JavaScript
DapModal.open('demo-modal');
DapModal.close('demo-modal');</code></pre> </div> <textarea id="raw-code-interactive" style="display:none;">// 1. DapToast API (Notifikasi Mengambang)
DapToast.success('Data berhasil diperbarui!', 'Sukses Disimpan');
DapToast.error('Gagal terhubung ke database server.', 'Koneksi Terputus');
DapToast.warning('Pastikan lisensi modul Anda tetap aktif.');
DapToast.info('Modul versi 1.2 siap diunduh.');

// 2. DapConfirm API (Dialog Konfirmasi Asynchronous)
DapConfirm({
    title: 'Hapus Data Pengguna?',
    message: 'Data yang terhapus tidak dapat dipulihkan kembali.',
    type: 'danger',           // 'danger' | 'info' | 'warning'
    confirmText: 'Ya, Hapus Data',
    cancelText: 'Batal'
}).then((confirmed) => {
    if (confirmed) {
        // Lakukan request AJAX hapus data
        DapToast.success('Data berhasil dihapus dari sistem.');
    } else {
        DapToast.info('Operasi penghapusan dibatalkan.');
    }
});

// 3. Modal Component & Triggering
// Cara A: Menggunakan HTML Data-Attribute
// <button data-ui-modal-target="modal-id">Buka Modal</button>

// Cara B: Memanggil lewat JavaScript
DapModal.open('demo-modal');
DapModal.close('demo-modal');</textarea> </div></x-ui.card></div><div id="tab-docs" class="ui-tab-pane"><x-ui.card title="Panduan Arsitektur & Penggunaan UI System" subtitle="Petunjuk implementasi praktis di seluruh 13 modul HMVC DapCode Framework" icon="fa-solid fa-book-open" style="margin-bottom: 24px;"><div style="line-height: 1.7; color: var(--text-main); font-size: 14px;"><div style="background: rgba(99, 102, 241, 0.08); border-left: 4px solid var(--primary); padding: 14px 18px; border-radius: 8px; margin-bottom: 20px;"><h4 style="margin: 0 0 6px 0; color: #fff; font-size: 15px; font-weight: 700;"> Zero-Config Anonymous Blade Components </h4><p style="margin: 0; color: var(--text-muted);"> Seluruh komponen UI tersimpan dalam direktori <code>resources/views/components/ui/</code>. Laravel Blade secara otomatis mengenali prefix <code>&lt;x-ui.*&gt;</code> di setiap view (termasuk template modul HMVC di <code>app/Modules/*/Views/</code>) tanpa memerlukan deklarasi Component Class atau import manual! </p></div><h4 style="font-size: 16px; font-weight: 700; color: #fff; margin-top: 24px; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;"><i class="fa-solid fa-laptop-code" style="color: #38bdf8;"></i> Contoh Penggunaan Nyata Pada File View Modul </h4><div class="ui-code-box" style="margin-bottom: 20px;"><div class="ui-code-header"><div class="ui-code-lang"><i class="fa-brands fa-laravel" style="color: #f43f5e;"></i> Contoh View: <code>app/Modules/Profile/Views/index.blade.php</code> </div><button type="button" class="ui-copy-btn" onclick="copyShowcaseSnippet('raw-code-sample-module', this)"><i class="fa-regular fa-copy"></i><span> Salin Kode </span></button></div> <pre class="ui-code-pre"><code>&lt;x-ui.card title="Sunting Biodata Pengembang" subtitle="Perbarui profil publik Anda" icon="fa-solid fa-user-pen"&gt;
    &lt;x-slot name="actions"&gt;
        &lt;x-ui.badge variant="success" dot&gt;Terverifikasi&lt;/x-ui.badge&gt;
    &lt;/x-slot&gt;

    &lt;form action="&#123;&#123; route('profile.update') &#125;&#125;" method="POST"&gt;
        &#123;&#123; csrf_field() &#125;&#125;
        
        &lt;div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;"&gt;
            &lt;x-ui.input name="name" label="Nama Lengkap" value="&#123;&#123; $user-&gt;name &#125;&#125;" required icon="fa-regular fa-user" /&gt;
            &lt;x-ui.input type="email" name="email" label="Email" value="&#123;&#123; $user-&gt;email &#125;&#125;" required icon="fa-regular fa-envelope" /&gt;
        &lt;/div&gt;

        &lt;x-ui.textarea name="bio" label="Biografi Singkat" rows="3"&gt;&#123;&#123; $user-&gt;bio &#125;&#125;&lt;/x-ui.textarea&gt;
        &lt;x-ui.checkbox name="is_public" label="Tampilkan profil di beranda portofolio" switch checked /&gt;

        &lt;div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;"&gt;
            &lt;x-ui.button variant="ghost" href="&#123;&#123; route('dashboard') &#125;&#125;"&gt;Batal&lt;/x-ui.button&gt;
            &lt;x-ui.button type="submit" variant="primary" icon="fa-solid fa-floppy-disk"&gt;Simpan Profil&lt;/x-ui.button&gt;
        &lt;/div&gt;
    &lt;/form&gt;
&lt;/x-ui.card&gt;</code></pre> </div> <textarea id="raw-code-sample-module" style="display:none;">@verbatim<x-ui.card title="Sunting Biodata Pengembang" subtitle="Perbarui profil publik Anda" icon="fa-solid fa-user-pen">
    <x-slot name="actions">
        <x-ui.badge variant="success" dot>Terverifikasi</x-ui.badge>
    </x-slot>

    <form action="{{ route('profile.update') }}" method="POST">
        {{ csrf_field() }}
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <x-ui.input name="name" label="Nama Lengkap" value="{{ $user->name }}" required icon="fa-regular fa-user" />
            <x-ui.input type="email" name="email" label="Email" value="{{ $user->email }}" required icon="fa-regular fa-envelope" />
        </div>

        <x-ui.textarea name="bio" label="Biografi Singkat" rows="3">{{ $user->bio }}</x-ui.textarea>
        <x-ui.checkbox name="is_public" label="Tampilkan profil di beranda portofolio" switch checked />

        <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
            <x-ui.button variant="ghost" href="{{ route('dashboard') }}">Batal</x-ui.button>
            <x-ui.button type="submit" variant="primary" icon="fa-solid fa-floppy-disk">Simpan Profil</x-ui.button>
        </div>
    </form>
</x-ui.card>@endverbatim</textarea> <h4 style="font-size: 16px; font-weight: 700; color: #fff; margin-top: 28px; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;"><i class="fa-solid fa-list-check" style="color: #34d399;"></i> Katalog Ringkasan Seluruh 27 Komponen UI Internal </h4><div class="ui-props-table-wrap"><table class="ui-props-table"><thead><tr><th style="width: 200px;"> Nama Komponen </th><th style="width: 280px;"> Tag Sintaks Blade </th><th> Kegunaan & Keunggulan </th></tr></thead><tbody><tr><td><strong> Button </strong></td><td> <code>&lt;x-ui.button&gt;</code> </td><td> Tombol aksi serbaguna dengan 8 varian tema, ukuran <code>sm/md/lg</code>, mode link (a), status loading, dan ikon otomatis. </td></tr><tr><td><strong> Badge </strong></td><td> <code>&lt;x-ui.badge&gt;</code> </td><td> Label badge dengan 3 gaya (<code>subtle</code>, <code>solid</code>, <code>outline</code>), bentuk pill, dan dot indikator. </td></tr><tr><td><strong> Status Indicator </strong></td><td> <code>&lt;x-ui.status-indicator&gt;</code> </td><td> Indikator status sistem dengan animasi denyut gelombang (pulse radar) untuk pemantauan real-time. </td></tr><tr><td><strong> Alert </strong></td><td> <code>&lt;x-ui.alert&gt;</code> </td><td> Kotak notifikasi halaman kontekstual dengan aksen warna, judul tebal, serta tombol dismiss (close). </td></tr><tr><td><strong> Card </strong></td><td> <code>&lt;x-ui.card&gt;</code> </td><td> Kontainer kartu modular dengan header, title, subtitle, slot <code>actions</code>, body, dan slot <code>footer</code>. </td></tr><tr><td><strong> Input </strong></td><td> <code>&lt;x-ui.input&gt;</code> </td><td> Input teks, password, email, dan angka dengan ikon FontAwesome, helper text, dan integrasi error Laravel. </td></tr><tr><td><strong> Select </strong></td><td> <code>&lt;x-ui.select&gt;</code> </td><td> Dropdown pilihan dengan styling terpadu yang menerima array key-value secara langsung. </td></tr><tr><td><strong> Date Picker </strong></td><td> <code>&lt;x-ui.date-picker&gt;</code> </td><td> Input pemilih tanggal terstandarisasi dengan ikon kalender. </td></tr><tr><td><strong> Textarea </strong></td><td> <code>&lt;x-ui.textarea&gt;</code> </td><td> Area input teks panjang multi-baris dengan konfigurasi <code>rows</code> dan teks panduan. </td></tr><tr><td><strong> Checkbox & Switch </strong></td><td> <code>&lt;x-ui.checkbox&gt;</code> </td><td> Kotak centang standar atau toggle switch halus dengan menambahkan prop <code>switch</code>. </td></tr><tr><td><strong> Radio </strong></td><td> <code>&lt;x-ui.radio&gt;</code> </td><td> Pilihan tunggal berbentuk lingkaran berdesain modern dan kontras. </td></tr><tr><td><strong> File Upload </strong></td><td> <code>&lt;x-ui.file-upload&gt;</code> </td><td> Area unggah berkas drag & drop dengan preview nama berkas terpilih secara interaktif. </td></tr><tr><td><strong> Table </strong></td><td> <code>&lt;x-ui.table&gt;</code> </td><td> Tabel responsif dengan dukungan striping zebra (<code>striped</code>) dan efek sorot kursor (<code>hover</code>). </td></tr><tr><td><strong> DataTable </strong></td><td> <code>&lt;x-ui.datatable&gt;</code> </td><td> Wrapper tabel komprehensif dengan bilah pencarian data, slot tombol aksi ekspor, dan slot navigasi halaman. </td></tr><tr><td><strong> Filter & Search </strong></td><td> <code>&lt;x-ui.filter&gt;</code>, <code>&lt;x-ui.search&gt;</code> </td><td> Bilah penyaring data dengan reset URL dan input pencarian instan. </td></tr><tr><td><strong> Tabs </strong></td><td> <code>&lt;x-ui.tabs&gt;</code> </td><td> Sistem navigasi tab dinamis dengan varian <code>underline</code> atau <code>pills</code> tanpa reload halaman. </td></tr><tr><td><strong> Modal </strong></td><td> <code>&lt;x-ui.modal&gt;</code> </td><td> Jendela dialog popup terpadu yang dapat dipicu secara deklaratif (<code>data-ui-modal-target</code>) atau JS (<code>DapModal.open</code>). </td></tr><tr><td><strong> Toast </strong></td><td> <code>DapToast.*</code> </td><td> Engine notifikasi mengambang dengan durasi otomatis dan tipe status (success, error, warning, info). </td></tr><tr><td><strong> Confirm Dialog </strong></td><td> <code>DapConfirm(...)</code> </td><td> Dialog konfirmasi berbasis Promise (async/await) untuk aksi sensitif seperti penghapusan data. </td></tr><tr><td><strong> DapTable API </strong></td><td> <code>DapTable.*</code> </td><td> Engine JavaScript untuk auto-refresh polling berkala (<code>DapTable.startAutoRefresh</code>) dan penyegaran data AJAX terprogram (<code>DapTable.refresh</code>). </td></tr><tr><td><strong> Tooltip </strong></td><td> <code>&lt;x-ui.tooltip&gt;</code> </td><td> Tooltip melayang ringan yang muncul saat kursor diarahkan ke elemen. </td></tr><tr><td><strong> Loading & Skeleton </strong></td><td> <code>&lt;x-ui.loading&gt;</code> </td><td> Indikator pemuatan berupa spinner putar atau shimmer skeleton loader tiruan konten. </td></tr><tr><td><strong> Empty State </strong></td><td> <code>&lt;x-ui.empty-state&gt;</code> </td><td> Tampilan representatif saat tabel atau list belum memiliki rekaman data apapun. </td></tr><tr><td><strong> Breadcrumb </strong></td><td> <code>&lt;x-ui.breadcrumb&gt;</code> </td><td> Jejak navigasi hirarkis dengan ikon beranda dan pemisah otomatis. </td></tr><tr><td><strong> Dropdown </strong></td><td> <code>&lt;x-ui.dropdown&gt;</code> </td><td> Menu dropdown aksi yang memuat elemen <code>&lt;x-ui.dropdown-item&gt;</code>. </td></tr></tbody></table></div></div></x-ui.card></div></x-ui.tabs><x-ui.modal id="demo-modal" title="Demo Modal Component" size="md"><div style="font-size: 14px; color: var(--text-main); line-height: 1.6;"><p> Ini adalah contoh modal interaktif yang dibuat dengan komponen <code>&lt;x-ui.modal&gt;</code>. </p><x-ui.alert variant="info" title="Keunggulan Modal Ini"> Mendukung penutupan via tombol Escape, klik di luar (backdrop), autofocus otomatis, dan transisi smooth. </x-ui.alert><x-ui.input label="Nama Komponen Uji" placeholder="Masukkan teks..." value="Dapcode Framework Component" /></div><x-slot name="footer"><x-ui.button variant="ghost" data-ui-modal-close> Tutup </x-ui.button><x-ui.button variant="primary" onclick="DapToast.success('Data dalam modal tersimpan!'); DapModal.close('demo-modal');"> Simpan Perubahan </x-ui.button></x-slot></x-ui.modal></div><style>.ui-tabs-pills{display:flex !important;flex-wrap:wrap !important;gap:6px !important;max-width:100% !important;width:fit-content !important;box-sizing:border-box !important;}.ui-tabs-pills .ui-tab-btn{flex-shrink:0 !important;}@media (max-width:640px){.ui-tabs-pills{display:flex !important;flex-wrap:nowrap !important;overflow-x:auto !important;-webkit-overflow-scrolling:touch !important;scrollbar-width:none !important;width:100% !important;}.ui-tabs-pills::-webkit-scrollbar{display:none !important;}}#ui-confirm-dialog-singleton .ui-modal-footer .ui-btn,.ui-confirm-dialog .ui-modal-footer .ui-btn{padding:10px 22px !important;font-size:13.5px !important;min-width:110px !important;display:inline-flex !important;align-items:center !important;justify-content:center !important;gap:8px !important;}.ui-code-toggle-pill{display:inline-flex;align-items:center;gap:6px;background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.3);color:#818cf8;padding:5px 12px;border-radius:9999px;font-size:12px;font-weight:600;cursor:pointer;transition:all 0.2s ease;}.ui-code-toggle-pill:hover,.ui-code-toggle-pill.is-active{background:var(--primary);border-color:var(--primary);color:#fff;box-shadow:0 0 12px rgba(99,102,241,0.4);}.ui-code-drawer{border-top:1px dashed var(--border-color);padding-top:16px;animation:fadeInDrawer 0.25s ease forwards;}@keyframes fadeInDrawer{from{opacity:0;transform:translateY(-6px);}to{opacity:1;transform:translateY(0);}}.ui-code-box{background:#090d16;border:1px solid #1e293b;border-radius:10px;overflow:hidden;margin-bottom:14px;box-shadow:0 4px 16px rgba(0,0,0,0.4);}.ui-code-header{background:#0f172a;border-bottom:1px solid #1e293b;padding:8px 14px;display:flex;justify-content:space-between;align-items:center;}.ui-code-lang{font-size:12px;font-weight:700;color:#94a3b8;display:flex;align-items:center;gap:8px;}.ui-code-lang code{color:#38bdf8;background:rgba(56,189,248,0.1);padding:1px 6px;border-radius:4px;font-size:11.5px;}.ui-copy-btn{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#cbd5e1;padding:4px 10px;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all 0.15s ease;}.ui-copy-btn:hover{background:rgba(255,255,255,0.15);color:#fff;}.ui-copy-btn.is-copied{background:rgba(16,185,129,0.2);border-color:rgba(16,185,129,0.4);color:#34d399;}.ui-code-pre{margin:0;padding:14px 18px;overflow-x:auto;font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono",monospace;font-size:12.5px;line-height:1.6;color:#e2e8f0;}.ui-props-table-wrap{overflow-x:auto;border:1px solid var(--border-color);border-radius:8px;background:rgba(15,23,42,0.4);}.ui-props-table{width:100%;border-collapse:collapse;font-size:12.5px;text-align:left;}.ui-props-table th{background:rgba(255,255,255,0.04);padding:9px 12px;color:#94a3b8;font-weight:700;border-bottom:1px solid var(--border-color);}.ui-props-table td{padding:9px 12px;border-bottom:1px solid rgba(255,255,255,0.05);color:var(--text-main);}.ui-props-table tr:last-child td{border-bottom:none;}.ui-props-table code{background:rgba(255,255,255,0.07);padding:2px 6px;border-radius:4px;color:#38bdf8;font-size:11.5px;font-family:monospace;}</style><script>function toggleShowcaseCode(targetId, btn) { const el = document.getElementById(targetId); if (!el) return; const isHidden = (el.style.display === 'none' || el.style.display === ''); if (isHidden) { el.style.display = 'block'; if (btn) { btn.classList.add('is-active'); const span = btn.querySelector('span'); if (span) span.textContent = 'Sembunyikan Kode'; } } else { el.style.display = 'none'; if (btn) { btn.classList.remove('is-active'); const span = btn.querySelector('span'); if (span) span.textContent = 'Lihat Kode'; } } } function toggleAllShowcaseCode(btn) { const drawers = document.querySelectorAll('.ui-code-drawer'); const toggleButtons = document.querySelectorAll('.ui-code-toggle-pill'); let anyHidden = false; drawers.forEach(d => { if (d.style.display === 'none' || d.style.display === '') anyHidden = true; }); const shouldOpen = anyHidden; drawers.forEach(d => d.style.display = shouldOpen ? 'block' : 'none'); toggleButtons.forEach(b => { const span = b.querySelector('span'); if (shouldOpen) { b.classList.add('is-active'); if (span) span.textContent = 'Sembunyikan Kode'; } else { b.classList.remove('is-active'); if (span) span.textContent = 'Lihat Kode'; } }); if (btn) { const btnSpan = btn.querySelector('span'); if (btnSpan) { btnSpan.textContent = shouldOpen ? 'Sembunyikan Semua Kode' : 'Tampilkan Semua Kode'; } } if (typeof DapToast !== 'undefined') { DapToast.info(shouldOpen ? 'Seluruh contoh kode implementasi ditampilkan' : 'Semua contoh kode disembunyikan'); } } function copyShowcaseSnippet(rawTextareaId, btn) { const textarea = document.getElementById(rawTextareaId); if (!textarea) return; const textToCopy = textarea.value; const copySuccess = () => { if (btn) { btn.classList.add('is-copied'); const originalHtml = btn.innerHTML; btn.innerHTML = '<i class="fa-solid fa-check"></i><span>Tersalin!</span>'; setTimeout(() => { btn.classList.remove('is-copied'); btn.innerHTML = originalHtml; }, 2000); } if (typeof DapToast !== 'undefined') { DapToast.success('Kode komponen berhasil disalin ke clipboard!', 'Tersalin'); } }; if (navigator.clipboard && window.isSecureContext) { navigator.clipboard.writeText(textToCopy).then(copySuccess).catch(() => { fallbackCopy(textarea, copySuccess); }); } else { fallbackCopy(textarea, copySuccess); } } function fallbackCopy(textarea, callback) { textarea.style.display = 'block'; textarea.select(); try { document.execCommand('copy'); callback(); } catch (e) { console.error('Copy failed', e); } textarea.style.display = 'none'; }</script>