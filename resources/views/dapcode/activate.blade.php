<div class="content-card" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
        <div>
            <h2 style="font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 4px;">Aktivasi Lisensi DapCode</h2>
            <p style="font-size: 13.5px; color: var(--text-muted);">Kelola aktivasi lisensi dan perizinan modul ekosistem DapCode.</p>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('dapcode.terminal') }}" style="background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.4); color: #818cf8; padding: 6px 14px; border-radius: 8px; font-size: 12.5px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-terminal"></i> Authority Terminal
            </a>
            @if($licenseStatus === 'ACTIVE')
                <span style="background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid #10b981; padding: 6px 14px; border-radius: 9999px; font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-circle-check"></i> AKTIF (LICENSED)
                </span>
            @elseif($licenseStatus === 'REVOKED')
                <span style="background: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid #ef4444; padding: 6px 14px; border-radius: 9999px; font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-ban"></i> DICABUT (REVOKED)
                </span>
            @elseif($licenseStatus === 'EXPIRED')
                <span style="background: rgba(245, 158, 11, 0.2); color: #f59e0b; border: 1px solid #f59e0b; padding: 6px 14px; border-radius: 9999px; font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-clock"></i> KADALUWARSA (EXPIRED)
                </span>
            @else
                <span style="background: rgba(245, 158, 11, 0.2); color: #f59e0b; border: 1px solid #f59e0b; padding: 6px 14px; border-radius: 9999px; font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-hourglass-half"></i> BELUM DIAKTIVASI (PENDING)
                </span>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #10b981; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #ef4444; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Installation ID Card -->
    <div class="stat-item" style="margin-bottom: 20px;">
        <div style="font-size: 14.5px; font-weight: 600; color: #fff; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-fingerprint" style="color: var(--primary);"></i> Unique Installation ID
        </div>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px;">
            Gunakan Installation ID ini untuk membuat atau meminta signed license key dari DapCode License Authority.
        </p>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <input type="text" id="instIdInput" readonly value="{{ $installationId }}" style="flex: 1; min-width: 260px; background: #090d16; border: 1px solid var(--border-color); color: #38bdf8; font-family: monospace; font-size: 13px; padding: 10px 14px; border-radius: 8px; outline: none;">
            <button type="button" onclick="navigator.clipboard.writeText('{{ $installationId }}'); showDapcodeToast('Installation ID berhasil disalin ke clipboard!', 'success');" style="background: rgba(255,255,255,0.08); border: 1px solid var(--border-color); color: #fff; padding: 10px 16px; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-regular fa-copy"></i> Salin ID
            </button>
        </div>
    </div>

    <!-- Active License Details (if active) -->
    @if($license && $licenseStatus === 'ACTIVE')
        <div class="stat-item" style="margin-bottom: 20px; border-color: rgba(16, 185, 129, 0.4);">
            <div style="font-size: 14.5px; font-weight: 600; color: #10b981; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-certificate"></i> Informasi Lisensi Aktif
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; font-size: 13px;">
                <div>
                    <span style="color: var(--text-sub); display: block; font-size: 11px; text-transform: uppercase; font-weight: 700;">License ID</span>
                    <div style="display: flex; align-items: center; gap: 8px; margin-top: 2px;">
                        <strong style="color: #fff; font-family: monospace;">{{ $license['license_id'] ?? '-' }}</strong>
                        @if(!empty($license['license_id']))
                            <button type="button" onclick="navigator.clipboard.writeText('{{ $license['license_id'] }}'); showDapcodeToast('License ID berhasil disalin ke clipboard!', 'success');" title="Salin License ID" style="background: rgba(255,255,255,0.08); border: 1px solid var(--border-color); color: #e2e8f0; padding: 2px 8px; border-radius: 4px; cursor: pointer; font-size: 11px; display: inline-flex; align-items: center; gap: 4px;">
                                <i class="fa-regular fa-copy"></i> Salin
                            </button>
                        @endif
                    </div>
                </div>
                <div>
                    <span style="color: var(--text-sub); display: block; font-size: 11px; text-transform: uppercase; font-weight: 700;">Masa Berlaku</span>
                    <strong style="color: #fff;">{{ $license['expires_at'] ?? 'Lifetime / Permanent' }}</strong>
                </div>
                <div>
                    <span style="color: var(--text-sub); display: block; font-size: 11px; text-transform: uppercase; font-weight: 700;">Aktivasi Tanggal</span>
                    <strong style="color: #fff;">{{ $license['activated_at'] ?? '-' }}</strong>
                </div>
                <div style="grid-column: 1 / -1;">
                    <span style="color: var(--text-sub); display: block; font-size: 11px; text-transform: uppercase; font-weight: 700; margin-bottom: 6px;">Modul Berlisensi</span>
                    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                        @foreach($license['modules'] ?? [] as $mod)
                            @if(!in_array(strtolower($mod), array_map('strtolower', $license['revoked_modules'] ?? [])))
                                <span style="background: rgba(99, 102, 241, 0.2); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.4); padding: 3px 10px; border-radius: 4px; font-size: 12px; font-family: monospace;">
                                    {{ $mod }}
                                </span>
                            @endif
                        @endforeach
                        @if(!empty($license['revoked_modules']))
                            @foreach($license['revoked_modules'] as $revMod)
                                <span style="background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); padding: 3px 10px; border-radius: 4px; font-size: 12px; font-family: monospace; text-decoration: line-through;" title="Modul Dicabut">
                                    {{ $revMod }} (Dicabut)
                                </span>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Deactivation section -->
            <div style="border-top: 1px solid var(--border-color); padding-top: 20px; margin-top: 20px;">
                <div style="font-size: 14.5px; font-weight: 600; color: #ef4444; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-ban"></i> Masukkan Signed Revocation Token
                </div>
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 14px;">
                    Tempelkan Signed Revocation Payload (JSON) dari Authority untuk mencabut seluruh lisensi atau modul tertentu.
                </p>
                <form id="revokeForm" action="{{ route('dapcode.deactivate') }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 16px;">
                        <textarea id="revocation_payload_input" name="revocation_payload" rows="8" required placeholder='{
  "action": "REVOKE",
  "license_id": "{{ $license['license_id'] ?? 'LIC-2026-XXXX' }}",
  "installation_id": "{{ $installationId }}",
  "revoked_modules": ["*"],
  "revoked_at": "2026-08-29T00:00:00Z",
  "reason": "Pencabutan Modul Tertentu",
  "auth_token": "...",
  "signature": "..."
}' style="width: 100%; box-sizing: border-box; background: #090d16; border: 1px solid rgba(239, 68, 68, 0.4); color: #e2e8f0; font-family: monospace; font-size: 13px; padding: 12px; border-radius: 8px; outline: none; line-height: 1.4; transition: all 0.3s;"></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-start; align-items: center; gap: 12px;">
                        <button type="button" onclick="openRevokeConfirmModal()" style="background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #ef4444; padding: 10px 24px; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-ban"></i> Cabut & Deaktivasi Lisensi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Activation Form Card -->
    <div class="stat-item">
        <div style="font-size: 14.5px; font-weight: 600; color: #fff; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-key" style="color: var(--primary);"></i> Masukkan Signed License Key
        </div>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 14px;">
            Tempelkan JSON payload lisensi atau signed token yang telah di-generate oleh DapCode License Authority untuk Installation ID Anda.
        </p>

        <form action="{{ route('dapcode.activate.post') }}" method="POST">
            @csrf
            <div style="margin-bottom: 16px;">
                <textarea id="license_payload_input" name="license_payload" rows="8" required placeholder='{
  "license_id": "LIC-2026-XXXX",
  "installation_id": "{{ $installationId }}",
  "status": "ACTIVE",
  "issued_at": "2026-08-29T00:00:00Z",
  "expires_at": "2028-12-31T23:59:59Z",
  "modules": ["*"],
  "auth_token": "...",
  "signature": "..."
}' style="width: 100%; box-sizing: border-box; background: #090d16; border: 1px solid var(--border-color); color: #e2e8f0; font-family: monospace; font-size: 13px; padding: 12px; border-radius: 8px; outline: none; line-height: 1.4; transition: all 0.3s;"></textarea>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <button type="submit" style="background: var(--primary); color: #fff; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);">
                    <i class="fa-solid fa-shield-halved"></i> Verifikasi & Aktivasi
                </button>
                <a href="{{ url('/') }}" style="color: var(--text-muted); text-decoration: none; font-size: 13.5px;">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Portfolio
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const injected = localStorage.getItem('dapcode_injected_payload');
    if (injected) {
        try {
            const data = JSON.parse(injected);
            const isRevoke = (data.action === 'REVOKE' || data.action === 'REVOKE_MODULES' || Boolean(data.revoked_at));
            if (isRevoke) {
                const revInput = document.getElementById('revocation_payload_input');
                if (revInput) {
                    revInput.value = JSON.stringify(data, null, 2);
                    revInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    revInput.style.borderColor = '#ef4444';
                    revInput.style.boxShadow = '0 0 15px rgba(239, 68, 68, 0.5)';
                    showDapcodeToast('Payload token pencabutan berhasil disematkan!', 'info');
                }
            } else {
                const licInput = document.getElementById('license_payload_input');
                if (licInput) {
                    licInput.value = JSON.stringify(data, null, 2);
                    licInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    licInput.style.borderColor = '#10b981';
                    licInput.style.boxShadow = '0 0 15px rgba(16, 185, 129, 0.5)';
                    showDapcodeToast('Payload lisensi baru berhasil disematkan!', 'info');
                }
            }
            localStorage.removeItem('dapcode_injected_payload');
        } catch (e) {
            console.error('Error parsing injected payload:', e);
        }
    }
});

function openRevokeConfirmModal() {
    const input = document.getElementById('revocation_payload_input');
    if (!input || !input.value.trim()) {
        showDapcodeToast('Masukkan signed revocation payload JSON terlebih dahulu!', 'error');
        if (input) input.focus();
        return;
    }
    document.getElementById('revokeConfirmModal').style.display = 'flex';
}

function closeRevokeConfirmModal() {
    document.getElementById('revokeConfirmModal').style.display = 'none';
}

function confirmRevokeExecution() {
    closeRevokeConfirmModal();
    document.getElementById('revokeForm').submit();
}

// DAPCODE TOAST SYSTEM
function showDapcodeToast(message, type = 'success', duration = 3500) {
    let container = document.getElementById('dapcodeToastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'dapcodeToastContainer';
        container.style.cssText = 'position: fixed; bottom: 24px; right: 24px; z-index: 999999; display: flex; flex-direction: column; gap: 10px; pointer-events: none;';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.style.cssText = 'pointer-events: auto; min-width: 280px; max-width: 420px; padding: 12px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; font-family: system-ui, -apple-system, sans-serif; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5); display: flex; align-items: center; gap: 10px; opacity: 0; transform: translateY(15px); transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); backdrop-filter: blur(10px);';

    let icon = 'fa-circle-check';
    if (type === 'error') {
        toast.style.background = 'rgba(239, 68, 68, 0.95)';
        toast.style.color = '#fff';
        toast.style.border = '1px solid rgba(255, 255, 255, 0.2)';
        icon = 'fa-triangle-exclamation';
    } else if (type === 'info') {
        toast.style.background = 'rgba(30, 41, 59, 0.95)';
        toast.style.color = '#38bdf8';
        toast.style.border = '1px solid rgba(56, 189, 248, 0.4)';
        icon = 'fa-circle-info';
    } else {
        toast.style.background = 'rgba(16, 185, 129, 0.95)';
        toast.style.color = '#fff';
        toast.style.border = '1px solid rgba(255, 255, 255, 0.2)';
        icon = 'fa-circle-check';
    }

    toast.innerHTML = `<i class="fa-solid ${icon}" style="font-size: 16px;"></i> <span style="flex: 1; line-height: 1.4;">${message}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
    }, 10);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(15px)';
        setTimeout(() => toast.remove(), 300);
    }, duration);
}
</script>

<div id="dapcodeToastContainer" style="position: fixed; bottom: 24px; right: 24px; z-index: 999999; display: flex; flex-direction: column; gap: 10px; pointer-events: none;"></div>

<!-- MODAL: CONFIRM REVOKE -->
<div id="revokeConfirmModal" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #0b0f19; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 14px; max-width: 440px; width: 100%; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8); overflow: hidden; animation: fadeIn 0.2s ease-out;">
        <div style="padding: 16px 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); display: flex; justify-content: space-between; align-items: center; background: rgba(239, 68, 68, 0.08);">
            <div style="font-size: 15px; font-weight: 700; color: #ef4444; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-triangle-exclamation"></i> Konfirmasi Pencabutan Lisensi
            </div>
            <button type="button" onclick="closeRevokeConfirmModal()" style="background: none; border: none; color: #94a3b8; font-size: 16px; cursor: pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div style="padding: 20px;">
            <p style="font-size: 13.5px; color: #e2e8f0; margin-bottom: 12px; line-height: 1.5;">
                Apakah Anda yakin ingin memproses <strong>pencabutan lisensi / modul</strong> pada instalasi ini?
            </p>
            <p style="font-size: 12.5px; color: #94a3b8; line-height: 1.4; margin-bottom: 20px; background: rgba(239, 68, 68, 0.08); border-left: 3px solid #ef4444; padding: 8px 12px; border-radius: 4px;">
                File controller/model dari modul yang dicabut akan otomatis dihapus dari disk dan dikunci secara fail-closed.
            </p>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeRevokeConfirmModal()" style="padding: 9px 16px; font-size: 13px; font-weight: 600; border-radius: 8px; border: 1px solid var(--border-color); color: #e2e8f0; background: transparent; cursor: pointer;">
                    Batal
                </button>
                <button type="button" onclick="confirmRevokeExecution()" style="padding: 9px 20px; font-size: 13px; font-weight: 700; border-radius: 8px; border: none; background: #ef4444; color: #fff; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);">
                    <i class="fa-solid fa-ban"></i> Ya, Proses Pencabutan
                </button>
            </div>
        </div>
    </div>
</div>
