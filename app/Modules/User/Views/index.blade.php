<div class="content-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <div style="min-width: 0; flex: 1;">
            <h2 style="font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 4px;">Daftar Pengguna</h2>
            <p style="color: var(--text-muted); font-size: 13.5px;">Manajemen user dari modul HMVC <code>app/Modules/User</code>.</p>
        </div>
        <div>
            <button type="button" style="background: var(--primary); color: #fff; border: none; padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; white-space: nowrap;">
                <i class="fa-solid fa-plus"></i> Tambah User
            </button>
        </div>
    </div>

    <!-- Responsive Table with Smooth Horizontal Scroll -->
    <div class="table-responsive">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px; min-width: 580px;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); color: var(--text-muted);">
                    <th style="padding: 12px 16px; font-weight: 600;">ID</th>
                    <th style="padding: 12px 16px; font-weight: 600;">NAMA</th>
                    <th style="padding: 12px 16px; font-weight: 600;">EMAIL</th>
                    <th style="padding: 12px 16px; font-weight: 600;">ROLE</th>
                    <th style="padding: 12px 16px; font-weight: 600;">STATUS</th>
                    <th style="padding: 12px 16px; font-weight: 600; text-align: right;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                    <td style="padding: 14px 16px; color: var(--text-muted);">#{{ $user['id'] }}</td>
                    <td style="padding: 14px 16px; font-weight: 600; color: #fff;">{{ $user['name'] }}</td>
                    <td style="padding: 14px 16px; color: var(--text-muted);">{{ $user['email'] }}</td>
                    <td style="padding: 14px 16px;">
                        <span style="background: rgba(99, 102, 241, 0.15); color: #818cf8; padding: 3px 10px; border-radius: 6px; font-size: 12px; font-weight: 500; white-space: nowrap;">
                            {{ $user['role'] }}
                        </span>
                    </td>
                    <td style="padding: 14px 16px;">
                        <span style="background: {{ $user['status'] == 'Active' ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' }}; color: {{ $user['status'] == 'Active' ? '#34d399' : '#f87171' }}; padding: 3px 10px; border-radius: 6px; font-size: 12px; font-weight: 500; white-space: nowrap;">
                            {{ $user['status'] }}
                        </span>
                    </td>
                    <td style="padding: 14px 16px; text-align: right; white-space: nowrap;">
                        <a href="javascript:void(0)" style="color: #38bdf8; margin-right: 12px; text-decoration: none;"><i class="fa-solid fa-pen-to-square"></i></a>
                        <a href="javascript:void(0)" style="color: #f87171; text-decoration: none;"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>