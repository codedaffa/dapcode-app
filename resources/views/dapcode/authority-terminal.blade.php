<div class="content-wrapper" style="max-width: 1100px; margin: 0 auto; padding: 10px 0;">
    <!-- Breadcrumb & Header -->
    <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                <a href="{{ url('/') }}" style="color: var(--text-muted); text-decoration: none;">Home</a>
                <i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i>
                <a href="{{ route('dapcode.activate') }}" style="color: var(--text-muted); text-decoration: none;">DapCode Security</a>
                <i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i>
                <span style="color: #6366f1;">Authority Signer Terminal</span>
            </div>
            <h1 style="font-size: 22px; font-weight: 700; color: #fff; margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-terminal" style="color: #6366f1;"></i> Authority Signer Terminal
            </h1>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 9999px; background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.4); color: #818cf8; font-size: 12.5px; font-weight: 600; font-family: monospace;">
                <span style="width: 8px; height: 8px; border-radius: 50%; background: #6366f1; box-shadow: 0 0 8px #6366f1;"></span>
                RSA-2048 HSM ENGINE
            </span>
            <a href="{{ route('dapcode.activate') }}" class="btn-secondary" style="padding: 7px 16px; font-size: 13px; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px; border: 1px solid var(--border-color); color: #e2e8f0; background: rgba(255,255,255,0.05);">
                <i class="fa-solid fa-shield-halved"></i> Form Aktivasi
            </a>
        </div>
    </div>

    <!-- Terminal Banner -->
    <div style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 27, 75, 0.6) 100%); border: 1px solid rgba(99, 102, 241, 0.3); border-radius: 12px; padding: 18px 22px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(99, 102, 241, 0.2); border: 1px solid rgba(99, 102, 241, 0.4); display: flex; align-items: center; justify-content: center; font-size: 20px; color: #818cf8;">
                <i class="fa-solid fa-server"></i>
            </div>
            <div>
                <div style="font-weight: 700; color: #fff; font-size: 15px; margin-bottom: 2px;">DapCode Authority Cryptographic Server</div>
                <div style="font-size: 12.5px; color: var(--text-muted);">
                    Konsol resmi Pemilik Aplikasi untuk menandatangani digital Signed License & Revocation Token dengan RSA-2048.
                </div>
            </div>
        </div>
        <div style="font-size: 12px; color: #94a3b8; background: rgba(0,0,0,0.3); padding: 6px 12px; border-radius: 6px; font-family: monospace;">
            STATUS: HSM_READY
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Left Column: Generator Form -->
        <div class="stat-item" style="padding: 24px; border-radius: 12px; background: rgba(15, 23, 42, 0.8); border: 1px solid var(--border-color);">
            <div style="display: flex; gap: 8px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                <button type="button" id="tabActivate" onclick="switchAction('ACTIVATE')" style="flex: 1; padding: 9px 12px; border-radius: 8px; border: none; background: var(--primary); color: #fff; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                    <i class="fa-solid fa-key"></i> Sign License
                </button>
                <button type="button" id="tabRevoke" onclick="switchAction('REVOKE')" style="flex: 1; padding: 9px 12px; border-radius: 8px; border: 1px solid var(--border-color); background: transparent; color: var(--text-muted); font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                    <i class="fa-solid fa-ban"></i> Sign Revocation
                </button>
            </div>

            <form id="signerForm" onsubmit="event.preventDefault(); executeSigning();">
                <input type="hidden" id="actionInput" value="ACTIVATE">

                <!-- Authority Passcode -->
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #e2e8f0; margin-bottom: 6px;">
                        <i class="fa-solid fa-lock" style="color: #f59e0b;"></i> Authority Secret Passcode <span style="color: #ef4444;">*</span>
                    </label>
                    <div style="position: relative;">
                        <input type="password" id="passcode" required placeholder="Masukkan passcode otorisasi Authority" style="width: 100%; box-sizing: border-box; background: #090d16; border: 1px solid var(--border-color); color: #fff; font-family: monospace; font-size: 13px; padding: 10px 40px 10px 12px; border-radius: 8px; outline: none;">
                        <button type="button" onclick="togglePasscodeVisibility()" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 14px;">
                            <i class="fa-solid fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Target Installation ID -->
                <div style="margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <label style="font-size: 13px; font-weight: 600; color: #e2e8f0;">
                            <i class="fa-solid fa-fingerprint" style="color: #6366f1;"></i> Target Installation ID <span style="color: #ef4444;">*</span>
                        </label>
                        <button type="button" onclick="useCurrentInstallationId()" style="background: none; border: none; color: #818cf8; font-size: 11.5px; cursor: pointer; text-decoration: underline;">
                            Gunakan ID Mesin Ini
                        </button>
                    </div>
                    <input type="text" id="installationId" required value="{{ $currentInstId }}" placeholder="DAP-XXXXXX-..." style="width: 100%; box-sizing: border-box; background: #090d16; border: 1px solid var(--border-color); color: #818cf8; font-family: monospace; font-size: 13px; padding: 10px 12px; border-radius: 8px; outline: none;">
                </div>

                <!-- License ID (for Revocation) -->
                <div id="licenseIdContainer" style="display: none; margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #e2e8f0; margin-bottom: 6px;">
                        <i class="fa-solid fa-id-card" style="color: #ef4444;"></i> Target License ID <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" id="targetLicenseId" value="{{ $currentLicenseId }}" placeholder="LIC-2026-PRO-XXXXXX" style="width: 100%; box-sizing: border-box; background: #090d16; border: 1px solid var(--border-color); color: #fff; font-family: monospace; font-size: 13px; padding: 10px 12px; border-radius: 8px; outline: none;">
                </div>

                <!-- Validity Duration (for Activation) -->
                <div id="durationContainer" style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #e2e8f0; margin-bottom: 6px;">
                        <i class="fa-solid fa-clock" style="color: #10b981;"></i> Masa Berlaku Lisensi
                    </label>
                    <select id="years" style="width: 100%; box-sizing: border-box; background: #090d16; border: 1px solid var(--border-color); color: #e2e8f0; font-size: 13px; padding: 10px 12px; border-radius: 8px; outline: none;">
                        <option value="1">1 Tahun</option>
                        <option value="2" selected>2 Tahun (Standar)</option>
                        <option value="5">5 Tahun</option>
                        <option value="10">10 Tahun (Enterprise / Lifetime)</option>
                    </select>
                </div>

                <!-- Revocation Reason (for Revocation) -->
                <div id="reasonContainer" style="display: none; margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #e2e8f0; margin-bottom: 6px;">
                        <i class="fa-solid fa-circle-info" style="color: #ef4444;"></i> Alasan Pencabutan
                    </label>
                    <input type="text" id="revokeReason" value="Pencabutan Resmi via Authority Terminal" style="width: 100%; box-sizing: border-box; background: #090d16; border: 1px solid var(--border-color); color: #fff; font-size: 13px; padding: 10px 12px; border-radius: 8px; outline: none;">
                </div>

                <!-- Module Permission / Revocation Selector -->
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <label id="moduleSectionLabel" style="font-size: 13px; font-weight: 600; color: #e2e8f0;">
                            <i class="fa-solid fa-cubes" style="color: #6366f1;"></i> Otorisasi Modul yang Diizinkan
                        </label>
                        <label style="font-size: 12px; color: #818cf8; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;">
                            <input type="checkbox" id="selectAllModules" checked onchange="toggleAllModules(this.checked)">
                            <span id="selectAllLabel">Semua Modul (*)</span>
                        </label>
                    </div>

                    <div id="moduleGrid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; max-height: 180px; overflow-y: auto; padding: 8px; background: #090d16; border: 1px solid var(--border-color); border-radius: 8px;">
                        @foreach($modules as $key => $name)
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: #cbd5e1; cursor: pointer; padding: 4px; border-radius: 4px;">
                                <input type="checkbox" class="module-chk" value="{{ $key }}" checked onchange="onModuleChange()">
                                <span>{{ $key }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <button type="submit" id="btnExecute" style="width: 100%; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #fff; border: none; padding: 12px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4); transition: all 0.2s;">
                    <i class="fa-solid fa-bolt"></i> Generate & Sign License (RSA-2048)
                </button>
            </form>
        </div>

        <!-- Right Column: Live Terminal Log & JSON Output -->
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <!-- Terminal Output Console -->
            <div style="background: #090d16; border: 1px solid #1e293b; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
                <div style="background: #0f172a; padding: 10px 14px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #1e293b;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #f59e0b; display: inline-block;"></span>
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                        <span style="font-size: 12px; font-family: monospace; color: #94a3b8; margin-left: 6px;">authority-console.sh</span>
                    </div>
                    <span id="terminalStatus" style="font-size: 11px; font-family: monospace; color: #64748b;">IDLE</span>
                </div>
                <div id="terminalLog" style="padding: 14px; font-family: 'Courier New', Courier, monospace; font-size: 12px; color: #38bdf8; line-height: 1.5; min-height: 140px; max-height: 180px; overflow-y: auto; white-space: pre-wrap;">
[SYSTEM_INIT] DapCode Authority Signer Console ready.
[SECURITY_CHECK] RSA-2048 Private Signing Engine loaded.
[AWAITING_INPUT] Masukkan passcode dan parameter lisensi lalu klik execute...<span class="blinking-cursor">_</span>
                </div>
            </div>

            <!-- Signed JSON Payload Result -->
            <div class="stat-item" style="padding: 18px; border-radius: 12px; background: rgba(15, 23, 42, 0.8); border: 1px solid var(--border-color); flex: 1; display: flex; flex-direction: column;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <div style="font-size: 13.5px; font-weight: 600; color: #fff; display: flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-file-code" style="color: #10b981;"></i> Generated Signed Payload (JSON)
                    </div>
                    <button type="button" id="btnCopyJson" onclick="copySignedPayload()" disabled style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); color: #10b981; padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; opacity: 0.5;">
                        <i class="fa-regular fa-copy" id="copyIcon"></i> Salin JSON
                    </button>
                </div>

                <textarea id="jsonOutput" readonly placeholder="Payload bertanda-tangan digital akan muncul di sini setelah proses generate berhasil..." style="width: 100%; flex: 1; min-height: 160px; box-sizing: border-box; background: #090d16; border: 1px solid var(--border-color); color: #4ade80; font-family: monospace; font-size: 12.5px; padding: 12px; border-radius: 8px; outline: none; line-height: 1.4; resize: none;"></textarea>

                <div id="activateShortcut" style="display: none; margin-top: 12px; padding: 10px 14px; background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.3); border-radius: 8px; justify-content: space-between; align-items: center;">
                    <span style="font-size: 12px; color: #cbd5e1;">Payload siap digunakan! Langsung proses:</span>
                    <a href="{{ route('dapcode.activate') }}" target="_blank" style="color: #818cf8; font-weight: 600; font-size: 12.5px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                        Buka /dapcode/activate <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0; }
}
.blinking-cursor {
    animation: blink 1s infinite;
    color: #38bdf8;
    font-weight: bold;
}
</style>

<script>
const currentMachineId = "{{ $currentInstId }}";

function switchAction(action) {
    document.getElementById('actionInput').value = action;
    const tabAct = document.getElementById('tabActivate');
    const tabRev = document.getElementById('tabRevoke');
    const licIdBox = document.getElementById('licenseIdContainer');
    const durBox = document.getElementById('durationContainer');
    const reasonBox = document.getElementById('reasonContainer');
    const moduleLabel = document.getElementById('moduleSectionLabel');
    const selectAllLabel = document.getElementById('selectAllLabel');
    const selectAllChk = document.getElementById('selectAllModules');
    const btn = document.getElementById('btnExecute');

    if (action === 'ACTIVATE') {
        tabAct.style.background = 'var(--primary)';
        tabAct.style.color = '#fff';
        tabAct.style.border = 'none';

        tabRev.style.background = 'transparent';
        tabRev.style.color = 'var(--text-muted)';
        tabRev.style.border = '1px solid var(--border-color)';

        licIdBox.style.display = 'none';
        durBox.style.display = 'block';
        reasonBox.style.display = 'none';

        moduleLabel.innerHTML = '<i class="fa-solid fa-cubes" style="color: #6366f1;"></i> Otorisasi Modul yang Diizinkan';
        selectAllLabel.innerText = 'Semua Modul (*)';
        selectAllChk.checked = true;
        toggleAllModules(true);

        btn.innerHTML = '<i class="fa-solid fa-bolt"></i> Generate & Sign License (RSA-2048)';
        btn.style.background = 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)';
    } else {
        tabRev.style.background = '#ef4444';
        tabRev.style.color = '#fff';
        tabRev.style.border = 'none';

        tabAct.style.background = 'transparent';
        tabAct.style.color = 'var(--text-muted)';
        tabAct.style.border = '1px solid var(--border-color)';

        licIdBox.style.display = 'block';
        durBox.style.display = 'none';
        reasonBox.style.display = 'block';

        moduleLabel.innerHTML = '<i class="fa-solid fa-ban" style="color: #ef4444;"></i> Pilih Modul yang Ingin Dicabut';
        selectAllLabel.innerText = 'Cabut Seluruh Lisensi (*)';
        selectAllChk.checked = false;
        toggleAllModules(false);

        btn.innerHTML = '<i class="fa-solid fa-ban"></i> Generate Signed Revocation Token';
        btn.style.background = 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
    }
}

function togglePasscodeVisibility() {
    const input = document.getElementById('passcode');
    const icon = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fa-solid fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fa-solid fa-eye';
    }
}

function useCurrentInstallationId() {
    document.getElementById('installationId').value = currentMachineId;
}

function toggleAllModules(checked) {
    const checkboxes = document.querySelectorAll('.module-chk');
    checkboxes.forEach(cb => cb.checked = checked);
}

function onModuleChange() {
    const checkboxes = document.querySelectorAll('.module-chk');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    document.getElementById('selectAllModules').checked = allChecked;
}

function executeSigning() {
    const passcode = document.getElementById('passcode').value;
    const installationId = document.getElementById('installationId').value;
    const action = document.getElementById('actionInput').value;
    const years = document.getElementById('years').value;
    const licenseId = document.getElementById('targetLicenseId').value;
    const reason = document.getElementById('revokeReason').value;

    const selectAll = document.getElementById('selectAllModules').checked;
    let modules = ['*'];
    if (!selectAll) {
        modules = Array.from(document.querySelectorAll('.module-chk:checked')).map(cb => cb.value);
        if (modules.length === 0) {
            alert(action === 'REVOKE' 
                ? 'Pilih minimal satu modul yang ingin dicabut, atau centang "Cabut Seluruh Lisensi (*)"!' 
                : 'Pilih minimal satu modul atau centang "Semua Modul (*)"!');
            return;
        }
    }

    if (action === 'REVOKE' && !licenseId) {
        alert('Target License ID tidak boleh kosong untuk mencabut lisensi!');
        return;
    }

    const termLog = document.getElementById('terminalLog');
    const termStatus = document.getElementById('terminalStatus');
    const jsonOutput = document.getElementById('jsonOutput');
    const copyBtn = document.getElementById('btnCopyJson');
    const activateLink = document.getElementById('activateShortcut');

    termStatus.innerText = 'EXECUTING...';
    termStatus.style.color = '#f59e0b';
    termLog.innerHTML = `[CONNECT] Requesting Authority Signer Engine...\n[ACTION] ${action}\n[TARGET_ID] ${installationId}\n[MODULES] ${JSON.stringify(modules)}\n[CRYPT] Engaging OpenSSL RSA-2048 with SHA-256...`;

    fetch("{{ route('dapcode.terminal.sign') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        },
        body: JSON.stringify({
            passcode: passcode,
            installation_id: installationId,
            action: action,
            years: years,
            license_id: licenseId,
            reason: reason,
            modules: modules
        })
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) {
            throw new Error(data.message || 'Gagal menandatangani payload.');
        }
        return data;
    })
    .then(data => {
        termStatus.innerText = 'SUCCESS (200 OK)';
        termStatus.style.color = '#10b981';
        termLog.innerHTML = `[STATUS] SUCCESS\n${data.log}\n[PAYLOAD] Canonical JSON Asymmetric Signature Generated Successfully!\n[READY] Salin payload di bawah untuk aktivasi / pencabutan.`;

        const prettyJson = JSON.stringify(data.payload, null, 4);
        jsonOutput.value = prettyJson;

        copyBtn.disabled = false;
        copyBtn.style.opacity = '1';
        activateLink.style.display = 'flex';
    })
    .catch(err => {
        termStatus.innerText = 'ERROR (403/500)';
        termStatus.style.color = '#ef4444';
        termLog.innerHTML = `[SECURITY_ALERT] SIGNING REJECTED!\n[ERROR_DETAILS] ${err.message}\n[AUDIT] Operation blocked by Authority Gate.`;
        jsonOutput.value = `// ERROR: ${err.message}`;
        copyBtn.disabled = true;
        copyBtn.style.opacity = '0.5';
        activateLink.style.display = 'none';
    });
}

function copySignedPayload() {
    const jsonOutput = document.getElementById('jsonOutput');
    if (!jsonOutput.value) return;

    navigator.clipboard.writeText(jsonOutput.value).then(() => {
        const btn = document.getElementById('btnCopyJson');
        const icon = document.getElementById('copyIcon');
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Tersalin!';
        btn.style.background = '#10b981';
        btn.style.color = '#fff';

        setTimeout(() => {
            btn.innerHTML = '<i class="fa-regular fa-copy" id="copyIcon"></i> Salin JSON';
            btn.style.background = 'rgba(16, 185, 129, 0.15)';
            btn.style.color = '#10b981';
        }, 2000);
    });
}
</script>
