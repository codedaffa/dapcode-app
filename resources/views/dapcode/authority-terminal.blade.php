<div class="content-wrapper" style="max-width: 1150px; margin: 0 auto; padding: 10px 0;">
    <!-- Breadcrumb & Header -->
    <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                <a href="{{ url('/') }}" style="color: var(--text-muted); text-decoration: none;">Home</a>
                <i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i>
                <a href="{{ route('dapcode.activate') }}" style="color: var(--text-muted); text-decoration: none;">DapCode Security</a>
                <i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i>
                <span style="color: #6366f1;">Developer Web Terminal</span>
            </div>
            <h1 style="font-size: 22px; font-weight: 700; color: #fff; margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-terminal" style="color: #6366f1;"></i> DapCode Developer Web Terminal
            </h1>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <button type="button" onclick="openMakeModuleModal()" style="padding: 7px 18px; font-size: 13px; font-weight: 700; border-radius: 8px; display: inline-flex; align-items: center; gap: 8px; border: 1px solid rgba(16, 185, 129, 0.5); color: #fff; background: #059669; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); transition: all 0.2s;">
                <i class="fa-solid fa-folder-plus"></i> + Make Module
            </button>
            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 9999px; background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.4); color: #818cf8; font-size: 12.5px; font-weight: 600; font-family: monospace;">
                <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; box-shadow: 0 0 8px #10b981;"></span>
                ARTISAN CLI ACTIVE
            </span>
            <a href="{{ route('dapcode.activate') }}" class="btn-secondary" style="padding: 7px 16px; font-size: 13px; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px; border: 1px solid var(--border-color); color: #e2e8f0; background: rgba(255,255,255,0.05);">
                <i class="fa-solid fa-shield-halved"></i> Form Aktivasi
            </a>
        </div>
    </div>

    <!-- Main Navigation Tabs -->
    <div style="display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
        <button type="button" id="mainTabArtisan" onclick="switchMainTab('ARTISAN')" style="padding: 10px 20px; border-radius: 8px; border: none; background: #6366f1; color: #fff; font-weight: 700; font-size: 13.5px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s;">
            <i class="fa-solid fa-terminal"></i> Laravel Artisan Console
        </button>
        <button type="button" id="mainTabSigner" onclick="switchMainTab('SIGNER')" style="padding: 10px 20px; border-radius: 8px; border: 1px solid var(--border-color); background: transparent; color: var(--text-muted); font-weight: 700; font-size: 13.5px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s;">
            <i class="fa-solid fa-key"></i> RSA-2048 License Signer
        </button>
    </div>

    <!-- SECTION 1: ARTISAN WEB TERMINAL -->
    <div id="sectionArtisan">
        <!-- Quick Preset Actions -->
        <div style="background: rgba(15, 23, 42, 0.8); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px 20px; margin-bottom: 20px;">
            <div style="font-size: 12.5px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-bolt" style="color: #f59e0b;"></i> Quick Command Presets:
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                <button type="button" onclick="openMakeModuleModal()" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); color: #34d399; padding: 6px 14px; border-radius: 6px; font-size: 12.5px; font-family: monospace; cursor: pointer;">
                    <i class="fa-solid fa-plus-circle"></i> make:module
                </button>
                <button type="button" onclick="openRemoveModuleModal()" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #f87171; padding: 6px 14px; border-radius: 6px; font-size: 12.5px; font-family: monospace; cursor: pointer;">
                    <i class="fa-solid fa-trash-can"></i> remove:module
                </button>
                <button type="button" onclick="runPreset('dapcode:pack all')" style="background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.4); color: #fbbf24; padding: 6px 14px; border-radius: 6px; font-size: 12.5px; font-family: monospace; cursor: pointer;">
                    <i class="fa-solid fa-box-archive"></i> dapcode:pack all
                </button>
                <button type="button" onclick="runPreset('dapcode:module status')" style="background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.4); color: #818cf8; padding: 6px 14px; border-radius: 6px; font-size: 12.5px; font-family: monospace; cursor: pointer;">
                    <i class="fa-solid fa-list-check"></i> dapcode:module status
                </button>
                <button type="button" onclick="runPreset('optimize:clear')" style="background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border-color); color: #e2e8f0; padding: 6px 14px; border-radius: 6px; font-size: 12.5px; font-family: monospace; cursor: pointer;">
                    <i class="fa-solid fa-broom"></i> optimize:clear
                </button>
                <button type="button" onclick="runPreset('route:list')" style="background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border-color); color: #e2e8f0; padding: 6px 14px; border-radius: 6px; font-size: 12.5px; font-family: monospace; cursor: pointer;">
                    <i class="fa-solid fa-network-wired"></i> route:list
                </button>
                <button type="button" onclick="runPreset('migrate:status')" style="background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border-color); color: #e2e8f0; padding: 6px 14px; border-radius: 6px; font-size: 12.5px; font-family: monospace; cursor: pointer;">
                    <i class="fa-solid fa-database"></i> migrate:status
                </button>
                <button type="button" onclick="runPreset('list')" style="background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border-color); color: #e2e8f0; padding: 6px 14px; border-radius: 6px; font-size: 12.5px; font-family: monospace; cursor: pointer;">
                    <i class="fa-solid fa-terminal"></i> list
                </button>
            </div>
        </div>

        <!-- Terminal Window Card -->
        <div style="background: #030712; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
            <!-- Terminal Header -->
            <div style="background: #0b0f19; padding: 10px 16px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 11px; height: 11px; border-radius: 50%; background: #ef4444;"></div>
                    <div style="width: 11px; height: 11px; border-radius: 50%; background: #f59e0b;"></div>
                    <div style="width: 11px; height: 11px; border-radius: 50%; background: #10b981;"></div>
                    <span style="font-family: monospace; font-size: 12px; color: #94a3b8; margin-left: 10px;">
                        bash - laravel-artisan-cli
                    </span>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="button" onclick="clearTerminal()" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #94a3b8; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; cursor: pointer;">
                        <i class="fa-solid fa-trash-can"></i> Clear
                    </button>
                    <button type="button" onclick="copyTerminalOutput()" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #94a3b8; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; cursor: pointer;">
                        <i class="fa-solid fa-copy"></i> Copy
                    </button>
                </div>
            </div>

            <!-- Terminal Output Area -->
            <div id="terminalScreen" style="background: #030712; color: #38bdf8; font-family: 'Courier New', Courier, monospace; font-size: 13px; line-height: 1.5; padding: 20px; min-height: 380px; max-height: 520px; overflow-y: auto; white-space: pre-wrap; word-break: break-all;">
<span style="color: #6366f1; font-weight: bold;">======================================================================</span>
<span style="color: #fff; font-weight: bold;">       DAPCODE AEGISGUARD — INTERACTIVE LARAVEL ARTISAN TERMINAL       </span>
<span style="color: #6366f1; font-weight: bold;">======================================================================</span>
<span style="color: #94a3b8;">Type any artisan command below (e.g. </span><span style="color: #34d399;">dapcode:module status</span><span style="color: #94a3b8;"> or </span><span style="color: #34d399;">route:list</span><span style="color: #94a3b8;">) and press Enter.</span>

<span style="color: #10b981;">developer@dapcode:~$ </span><span style="color: #e2e8f0;">php artisan dapcode:module status</span>
            </div>

            <!-- Terminal Command Input Form -->
            <form id="artisanForm" onsubmit="event.preventDefault(); submitArtisanCommand();" style="display: flex; background: #090d16; border-top: 1px solid rgba(255, 255, 255, 0.08); padding: 12px 16px; gap: 10px; align-items: center;">
                <span style="color: #10b981; font-family: monospace; font-weight: bold; font-size: 13.5px; white-space: nowrap;">
                    php artisan
                </span>
                <input type="text" id="artisanInput" placeholder="Masukkan perintah command (e.g. dapcode:module status, optimize:clear, route:list)..." autocomplete="off" style="flex: 1; background: transparent; border: none; outline: none; color: #fff; font-family: monospace; font-size: 13.5px;">
                <button type="submit" id="artisanBtn" style="background: #6366f1; color: #fff; border: none; padding: 8px 18px; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-play"></i> Run
                </button>
            </form>
        </div>
    </div>

    <!-- SECTION 2: RSA LICENSE SIGNER -->
    <div id="sectionSigner" style="display: none;">
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
                            <input type="password" id="passcode" required placeholder="Masukkan Passcode Otorisasi Authority..." style="width: 100%; box-sizing: border-box; background: #090d16; border: 1px solid var(--border-color); color: #fff; font-family: monospace; font-size: 13px; padding: 10px 40px 10px 12px; border-radius: 8px; outline: none;">
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
                            <option value="2" selected>2 Tahun (Standard Enterprise)</option>
                            <option value="5">5 Tahun (Long-term Support)</option>
                            <option value="10">10 Tahun (Lifetime)</option>
                        </select>
                    </div>

                    <!-- Granular Modules Selection -->
                    <div id="modulesContainer" style="margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <label style="font-size: 13px; font-weight: 600; color: #e2e8f0;">
                                <i class="fa-solid fa-cubes" id="modulesIcon" style="color: #38bdf8;"></i> <span id="modulesTitleText">Otorisasi Modul (Aktivasi)</span>
                            </label>
                            <div style="font-size: 11.5px; display: flex; gap: 8px;">
                                <a href="javascript:void(0)" onclick="selectAllModules(true)" style="color: #818cf8; text-decoration: none;">Pilih Semua</a>
                                <span style="color: var(--text-muted);">|</span>
                                <a href="javascript:void(0)" onclick="selectAllModules(false)" style="color: var(--text-muted); text-decoration: none;">Kosongkan</a>
                            </div>
                        </div>

                        <div style="max-height: 180px; overflow-y: auto; background: #090d16; border: 1px solid var(--border-color); border-radius: 8px; padding: 10px;">
                            <label id="wildcardRow" style="display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: #fbbf24; font-weight: 600; padding: 4px 0; cursor: pointer; border-bottom: 1px solid rgba(255,255,255,0.05); margin-bottom: 4px;">
                                <input type="checkbox" id="mod_wildcard" value="*" checked onchange="toggleWildcard(this.checked)">
                                <span id="wildcardLabelText">* (Full Wildcard — Izinkan Semua Modul)</span>
                            </label>
                            <div id="noLicensedModulesNotice" style="display: none; color: #94a3b8; font-size: 12px; padding: 8px 4px; font-style: italic;">
                                <i class="fa-solid fa-circle-info" style="color: #f59e0b; margin-right: 6px;"></i> Belum ada modul aktif yang terdaftar dalam lisensi saat ini.
                            </div>
                            @php
                                $hasActiveLicense = !empty($currentLicense) && ($currentLicense['status'] ?? '') === 'ACTIVE';
                                $licensedList = array_map('strtolower', (array) ($activeLicensedModules ?? []));
                                $isWildcardLicense = in_array('*', $licensedList, true);
                            @endphp
                            @foreach($modules as $modKey => $modLabel)
                            @php
                                $isModLicensed = $hasActiveLicense && ($isWildcardLicense || in_array(strtolower($modKey), $licensedList, true));
                            @endphp
                            <label class="mod-row" data-licensed="{{ $isModLicensed ? '1' : '0' }}" style="display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: #e2e8f0; padding: 3px 0; cursor: pointer;">
                                <input type="checkbox" class="mod-checkbox" value="{{ $modKey }}" checked onchange="onIndividualCheckboxChange()">
                                <span>{{ $modLabel }} (<code style="color: #818cf8; font-size: 11px;">{{ $modKey }}</code>)</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Revocation Reason (for Revocation) -->
                    <div id="reasonContainer" style="display: none; margin-bottom: 16px;">
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #e2e8f0; margin-bottom: 6px;">
                            <i class="fa-solid fa-circle-exclamation" style="color: #f59e0b;"></i> Alasan Pencabutan (Revocation Reason)
                        </label>
                        <input type="text" id="revokeReason" value="Contract Terminated / License Cancelled" placeholder="e.g. Contract Terminated" style="width: 100%; box-sizing: border-box; background: #090d16; border: 1px solid var(--border-color); color: #fff; font-size: 13px; padding: 10px 12px; border-radius: 8px; outline: none;">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="btnSign" class="btn-primary" style="width: 100%; padding: 12px; border-radius: 8px; font-weight: 700; font-size: 14px; display: flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer;">
                        <i class="fa-solid fa-signature"></i> Tanda Tangani Digital (Generate Token)
                    </button>
                </form>
            </div>

            <!-- Right Column: Output & Terminal Log -->
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <!-- Generated Token Box -->
                <div class="stat-item" style="padding: 24px; border-radius: 12px; background: rgba(15, 23, 42, 0.8); border: 1px solid var(--border-color); flex: 1;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <label style="font-size: 13px; font-weight: 700; color: #e2e8f0; display: flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-file-code" style="color: #10b981;"></i> Output JSON Token
                        </label>
                        <button type="button" id="btnCopy" onclick="copyGeneratedPayload()" style="display: none; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); color: #34d399; font-size: 12px; padding: 4px 10px; border-radius: 6px; cursor: pointer;">
                            <i class="fa-solid fa-copy"></i> Salin Token
                        </button>
                    </div>

                    <textarea id="outputPayload" readonly placeholder="Payload JSON bertanda tangan digital RSA-2048 akan muncul di sini setelah Anda mengklik tombol Tanda Tangani Digital..." style="width: 100%; height: 180px; box-sizing: border-box; background: #090d16; border: 1px solid var(--border-color); color: #34d399; font-family: monospace; font-size: 12px; padding: 12px; border-radius: 8px; outline: none; resize: none;"></textarea>

                    <div id="quickActionContainer" style="display: none; margin-top: 12px;">
                        <button type="button" onclick="injectToActivationForm()" style="width: 100%; padding: 9px; border-radius: 8px; background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.4); color: #818cf8; font-size: 12.5px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                            <i class="fa-solid fa-arrow-right-to-bracket"></i> Terapkan ke Form Aktivasi Sistem
                        </button>
                    </div>
                </div>

                <!-- Signer Log Box -->
                <div class="stat-item" style="padding: 16px 20px; border-radius: 12px; background: #060911; border: 1px solid rgba(255, 255, 255, 0.08);">
                    <div style="font-size: 12px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-terminal" style="color: #6366f1;"></i> Authority Execution Log
                    </div>
                    <div id="terminalLog" style="font-family: monospace; font-size: 11.5px; color: #94a3b8; line-height: 1.6; max-height: 120px; overflow-y: auto;">
                        [HSM] Ready. Menunggu input otorisasi Authority...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Tab Switching: ARTISAN vs SIGNER
    function switchMainTab(tab) {
        const artisanTab = document.getElementById('mainTabArtisan');
        const signerTab = document.getElementById('mainTabSigner');
        const sectionArtisan = document.getElementById('sectionArtisan');
        const sectionSigner = document.getElementById('sectionSigner');

        if (tab === 'ARTISAN') {
            artisanTab.style.background = '#6366f1';
            artisanTab.style.color = '#fff';
            artisanTab.style.border = 'none';

            signerTab.style.background = 'transparent';
            signerTab.style.color = 'var(--text-muted)';
            signerTab.style.border = '1px solid var(--border-color)';

            sectionArtisan.style.display = 'block';
            sectionSigner.style.display = 'none';

            document.getElementById('artisanInput').focus();
        } else {
            signerTab.style.background = '#6366f1';
            signerTab.style.color = '#fff';
            signerTab.style.border = 'none';

            artisanTab.style.background = 'transparent';
            artisanTab.style.color = 'var(--text-muted)';
            artisanTab.style.border = '1px solid var(--border-color)';

            sectionArtisan.style.display = 'none';
            sectionSigner.style.display = 'block';
        }
    }

    // ARTISAN CLI WEB CONSOLE ENGINE
    let commandHistory = [];
    let historyIndex = -1;

    function runPreset(cmd) {
        document.getElementById('artisanInput').value = cmd;
        submitArtisanCommand();
    }

    function clearTerminal() {
        document.getElementById('terminalScreen').innerHTML = '<span style="color: #94a3b8;">Terminal cleared. Ready for commands.</span>\n\n';
    }

    function copyTerminalOutput() {
        const screen = document.getElementById('terminalScreen');
        navigator.clipboard.writeText(screen.innerText).then(() => {
            showDapcodeToast('Output terminal berhasil disalin ke clipboard!', 'success');
        });
    }

    // Keyboard History (Up / Down arrow)
    document.getElementById('artisanInput').addEventListener('keydown', function(e) {
        if (e.key === 'ArrowUp') {
            if (commandHistory.length > 0 && historyIndex > 0) {
                historyIndex--;
                this.value = commandHistory[historyIndex];
            } else if (historyIndex === 0) {
                this.value = commandHistory[0];
            }
            e.preventDefault();
        } else if (e.key === 'ArrowDown') {
            if (historyIndex < commandHistory.length - 1) {
                historyIndex++;
                this.value = commandHistory[historyIndex];
            } else {
                historyIndex = commandHistory.length;
                this.value = '';
            }
            e.preventDefault();
        }
    });

    async function submitArtisanCommand() {
        const input = document.getElementById('artisanInput');
        const btn = document.getElementById('artisanBtn');
        const screen = document.getElementById('terminalScreen');
        const rawCmd = input.value.trim();

        if (!rawCmd) return;

        commandHistory.push(rawCmd);
        historyIndex = commandHistory.length;

        // Print user input to terminal screen
        screen.innerHTML += `\n<span style="color: #10b981;">developer@dapcode:~$ </span><span style="color: #fff; font-weight: bold;">php artisan ${escapeHtml(rawCmd)}</span>\n`;
        screen.innerHTML += `<span style="color: #64748b;">[Executing command...]</span>\n`;
        screen.scrollTop = screen.scrollHeight;

        input.value = '';
        input.disabled = true;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

        try {
            const res = await fetch('{{ route("dapcode.terminal.artisan") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ command: rawCmd })
            });

            const text = await res.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (jsonErr) {
                const jsonMatch = text.match(/\{[\s\S]*\}/);
                if (jsonMatch) {
                    try {
                        data = JSON.parse(jsonMatch[0]);
                    } catch (e2) {
                        data = { success: false, exitCode: 1, output: text };
                    }
                } else {
                    data = { success: false, exitCode: 1, output: text };
                }
            }

            // Remove "[Executing command...]" line
            screen.innerHTML = screen.innerHTML.replace('<span style="color: #64748b;">[Executing command...]</span>\n', '');

            if (data.success) {
                screen.innerHTML += `<span style="color: #38bdf8;">${escapeHtml(data.output)}</span>\n`;
                screen.innerHTML += `<span style="color: #10b981; font-size: 11.5px;">✓ Process exited with code 0</span>\n`;
            } else {
                screen.innerHTML += `<span style="color: #f87171;">${escapeHtml(data.output)}</span>\n`;
                screen.innerHTML += `<span style="color: #ef4444; font-size: 11.5px;">✗ Process exited with code ${data.exitCode}</span>\n`;
            }
        } catch (err) {
            screen.innerHTML = screen.innerHTML.replace('<span style="color: #64748b;">[Executing command...]</span>\n', '');
            screen.innerHTML += `<span style="color: #ef4444;">[FATAL] Network or execution error: ${escapeHtml(err.message)}</span>\n`;
        } finally {
            input.disabled = false;
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-play"></i> Run';
            input.focus();
            screen.scrollTop = screen.scrollHeight;
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    function promptMakeModule() {
        openMakeModuleModal();
    }

    // SIGNER SCRIPTS
    function switchAction(action) {
        document.getElementById('actionInput').value = action;
        const tabAct = document.getElementById('tabActivate');
        const tabRev = document.getElementById('tabRevoke');
        const durCont = document.getElementById('durationContainer');
        const modCont = document.getElementById('modulesContainer');
        const licCont = document.getElementById('licenseIdContainer');
        const reasCont = document.getElementById('reasonContainer');
        const modTitle = document.getElementById('modulesTitleText');
        const modIcon = document.getElementById('modulesIcon');
        const wildcardLabel = document.getElementById('wildcardLabelText');
        const modRows = document.querySelectorAll('.mod-row');
        const wildcardRow = document.getElementById('wildcardRow');
        const noLicensedNotice = document.getElementById('noLicensedModulesNotice');

        if (action === 'ACTIVATE') {
            tabAct.style.background = 'var(--primary)';
            tabAct.style.color = '#fff';
            tabAct.style.border = 'none';

            tabRev.style.background = 'transparent';
            tabRev.style.color = 'var(--text-muted)';
            tabRev.style.border = '1px solid var(--border-color)';

            durCont.style.display = 'block';
            modCont.style.display = 'block';
            licCont.style.display = 'none';
            reasCont.style.display = 'none';

            if (modTitle) modTitle.innerText = 'Otorisasi Modul (Aktivasi)';
            if (modIcon) modIcon.style.color = '#38bdf8';
            if (wildcardLabel) wildcardLabel.innerText = '* (Full Wildcard — Izinkan Semua Modul)';

            if (wildcardRow) wildcardRow.style.display = 'flex';
            if (noLicensedNotice) noLicensedNotice.style.display = 'none';
            modRows.forEach(row => {
                row.style.display = 'flex';
            });

            btnSign.innerHTML = '<i class="fa-solid fa-signature"></i> Tanda Tangani Digital (Generate License)';
            btnSign.className = 'btn-primary';
            btnSign.style.background = 'var(--primary)';
        } else {
            tabRev.style.background = '#ef4444';
            tabRev.style.color = '#fff';
            tabRev.style.border = 'none';

            tabAct.style.background = 'transparent';
            tabAct.style.color = 'var(--text-muted)';
            tabAct.style.border = '1px solid var(--border-color)';

            durCont.style.display = 'none';
            modCont.style.display = 'block';
            licCont.style.display = 'block';
            reasCont.style.display = 'block';

            if (modTitle) modTitle.innerText = 'Pilih Modul yang Ingin Dicabut (Revoke)';
            if (modIcon) modIcon.style.color = '#ef4444';
            if (wildcardLabel) wildcardLabel.innerText = '* (Full Wildcard — Cabut Seluruh Lisensi & Semua Modul)';

            let licensedCount = 0;
            modRows.forEach(row => {
                const isLicensed = row.getAttribute('data-licensed') === '1';
                if (isLicensed) {
                    row.style.display = 'flex';
                    licensedCount++;
                } else {
                    row.style.display = 'none';
                    const cb = row.querySelector('.mod-checkbox');
                    if (cb) {
                        cb.checked = false;
                        cb.disabled = false;
                    }
                }
            });

            if (licensedCount === 0) {
                if (noLicensedNotice) noLicensedNotice.style.display = 'block';
            } else {
                if (noLicensedNotice) noLicensedNotice.style.display = 'none';
            }

            btnSign.innerHTML = '<i class="fa-solid fa-ban"></i> Tanda Tangani Digital (Generate Revocation Token)';
            btnSign.className = '';
            btnSign.style.background = '#ef4444';
        }

        const wildcard = document.getElementById('mod_wildcard');
        if (wildcard) {
            toggleWildcard(wildcard.checked);
        }
    }

    function togglePasscodeVisibility() {
        const inp = document.getElementById('passcode');
        const icon = document.getElementById('eyeIcon');
        if (inp.type === 'password') {
            inp.type = 'text';
            icon.className = 'fa-solid fa-eye-slash';
        } else {
            inp.type = 'password';
            icon.className = 'fa-solid fa-eye';
        }
    }

    function useCurrentInstallationId() {
        document.getElementById('installationId').value = "{{ $currentInstId }}";
    }

    function getVisibleCheckboxes() {
        return Array.from(document.querySelectorAll('.mod-row'))
            .filter(row => row.style.display !== 'none')
            .map(row => row.querySelector('.mod-checkbox'))
            .filter(Boolean);
    }

    function toggleWildcard(isChecked) {
        const visibleCheckboxes = getVisibleCheckboxes();
        visibleCheckboxes.forEach(cb => {
            cb.disabled = isChecked;
            if (isChecked) {
                cb.checked = true;
            }
        });
    }

    function onIndividualCheckboxChange() {
        const wildcard = document.getElementById('mod_wildcard');
        const visibleCheckboxes = getVisibleCheckboxes();
        const allChecked = visibleCheckboxes.length > 0 && visibleCheckboxes.every(cb => cb.checked);
        if (!allChecked && wildcard && wildcard.checked) {
            wildcard.checked = false;
        }
    }

    function selectAllModules(checkAll) {
        const wildcard = document.getElementById('mod_wildcard');
        if (wildcard) {
            wildcard.checked = checkAll;
        }
        const visibleCheckboxes = getVisibleCheckboxes();
        visibleCheckboxes.forEach(cb => {
            cb.disabled = checkAll;
            cb.checked = checkAll;
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const wildcard = document.getElementById('mod_wildcard');
        if (wildcard) {
            toggleWildcard(wildcard.checked);
        }
    });

    async function executeSigning() {
        const action = document.getElementById('actionInput').value;
        const passcode = document.getElementById('passcode').value;
        const instId = document.getElementById('installationId').value;
        const btn = document.getElementById('btnSign');
        const logBox = document.getElementById('terminalLog');
        const outBox = document.getElementById('outputPayload');
        const copyBtn = document.getElementById('btnCopy');
        const actionBtn = document.getElementById('quickActionContainer');

        let selectedModules = [];
        if (document.getElementById('mod_wildcard').checked) {
            selectedModules = ['*'];
        } else {
            getVisibleCheckboxes().forEach(cb => {
                if (cb.checked) {
                    selectedModules.push(cb.value);
                }
            });
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses Tanda Tangan Kriptografi RSA-2048...';
        logBox.innerHTML = `[HSM] Menginisialisasi request penandatanganan kriptografis (${action})...`;

        const payload = {
            action: action,
            passcode: passcode,
            authority_passcode: passcode,
            installation_id: instId,
            license_id: document.getElementById('targetLicenseId').value,
            years: document.getElementById('years').value,
            modules: selectedModules,
            reason: document.getElementById('revokeReason').value,
        };

        try {
            const res = await fetch('{{ route("dapcode.terminal.sign") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();

            if (data.success) {
                outBox.value = JSON.stringify(data.payload, null, 4);
                logBox.innerHTML = escapeHtml(data.log).replace(/\n/g, '<br>');
                copyBtn.style.display = 'inline-block';
                actionBtn.style.display = 'block';
                showDapcodeToast('Tanda tangan digital berhasil di-generate!', 'success');
            } else {
                outBox.value = '';
                logBox.innerHTML = `<span style="color: #ef4444;">[ERROR] Otorisasi Ditolak: ${escapeHtml(data.message)}</span>`;
                showDapcodeToast('Gagal: ' + data.message, 'error');
            }
        } catch (e) {
            logBox.innerHTML = `<span style="color: #ef4444;">[FATAL] Gagal menghubungi server penandatanganan: ${escapeHtml(e.message)}</span>`;
            showDapcodeToast('Gagal menghubungi server: ' + e.message, 'error');
        } finally {
            btn.disabled = false;
            switchAction(action);
        }
    }

    function copyGeneratedPayload() {
        const text = document.getElementById('outputPayload').value;
        navigator.clipboard.writeText(text).then(() => {
            showDapcodeToast('Payload token bertanda tangan berhasil disalin ke clipboard!', 'success');
        });
    }

    function injectToActivationForm() {
        const text = document.getElementById('outputPayload').value;
        if (!text) return;
        localStorage.setItem('dapcode_injected_payload', text);
        showDapcodeToast('Menerapkan payload ke Form Aktivasi...', 'info');
        setTimeout(() => {
            window.location.href = "{{ route('dapcode.activate') }}?injected=1";
        }, 300);
    }

    function openMakeModuleModal() {
        document.getElementById('makeModuleModal').style.display = 'flex';
        document.getElementById('modalModuleName').value = '';
        document.getElementById('modalModuleSlug').innerText = '/...';
        setTimeout(() => document.getElementById('modalModuleName').focus(), 100);
    }

    function closeMakeModuleModal() {
        document.getElementById('makeModuleModal').style.display = 'none';
    }

    function updateModuleSlugPreview(val) {
        const clean = val.toLowerCase().replace(/[^a-z0-9]/g, '');
        document.getElementById('modalModuleSlug').innerText = clean ? '/' + clean : '/...';
    }

    function submitMakeModuleModal(e) {
        if (e) e.preventDefault();
        const raw = document.getElementById('modalModuleName').value.trim();
        if (!raw) {
            showDapcodeToast('Masukkan nama modul terlebih dahulu!', 'error');
            return;
        }
        closeMakeModuleModal();
        switchMainTab('ARTISAN');
        const input = document.getElementById('artisanInput');
        input.value = 'make:module ' + raw;
        runArtisan();
    }

    function openRemoveModuleModal() {
        document.getElementById('removeModuleModal').style.display = 'flex';
    }

    function closeRemoveModuleModal() {
        document.getElementById('removeModuleModal').style.display = 'none';
    }

    function submitRemoveModuleModal(e) {
        if (e) e.preventDefault();
        const mod = document.getElementById('modalRemoveModuleName').value;
        if (!mod) {
            showDapcodeToast('Pilih modul yang ingin dihapus!', 'error');
            return;
        }
        closeRemoveModuleModal();
        switchMainTab('ARTISAN');
        const input = document.getElementById('artisanInput');
        input.value = 'remove:module ' + mod + ' --force';
        runArtisan();
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

<!-- MODAL: MAKE MODULE -->
<div id="makeModuleModal" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #0b0f19; border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 14px; max-width: 480px; width: 100%; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8); overflow: hidden; animation: fadeIn 0.2s ease-out;">
        <!-- Modal Header -->
        <div style="padding: 16px 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); display: flex; justify-content: space-between; align-items: center; background: rgba(16, 185, 129, 0.08);">
            <div style="font-size: 15px; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-folder-plus" style="color: #10b981;"></i> Buat Modul Baru (Make Module)
            </div>
            <button type="button" onclick="closeMakeModuleModal()" style="background: none; border: none; color: #94a3b8; font-size: 16px; cursor: pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form onsubmit="submitMakeModuleModal(event)" style="padding: 20px;">
            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px; line-height: 1.5;">
                Sistem akan membuat struktur lengkap: <strong>Controller</strong>, <strong>Model</strong>, <strong>Blade View</strong>, <strong>Core Base Controller</strong>, dan otomatis mengamankannya dengan enkripsi <strong>AES-256-GCM Layer 6</strong>.
            </p>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #e2e8f0; margin-bottom: 6px;">
                    Nama Modul (PascalCase) <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" id="modalModuleName" required placeholder="Contoh: Blog, Analytics, Store, Finance" oninput="updateModuleSlugPreview(this.value)" style="width: 100%; box-sizing: border-box; background: #090d16; border: 1px solid var(--border-color); color: #fff; font-size: 13.5px; padding: 10px 14px; border-radius: 8px; outline: none;">
            </div>

            <!-- Route Preview -->
            <div style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 8px; padding: 10px 14px; margin-bottom: 20px; font-size: 12.5px; display: flex; justify-content: space-between; align-items: center;">
                <span style="color: var(--text-muted);">Akses URL Route:</span>
                <code id="modalModuleSlug" style="color: #38bdf8; font-weight: 600; font-family: monospace; font-size: 13px;">/...</code>
            </div>

            <!-- Modal Footer -->
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeMakeModuleModal()" style="padding: 9px 16px; font-size: 13px; font-weight: 600; border-radius: 8px; border: 1px solid var(--border-color); color: #e2e8f0; background: transparent; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 9px 20px; font-size: 13px; font-weight: 700; border-radius: 8px; border: none; background: #059669; color: #fff; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);">
                    <i class="fa-solid fa-plus"></i> Buat Modul Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: REMOVE MODULE -->
<div id="removeModuleModal" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #0b0f19; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 14px; max-width: 480px; width: 100%; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8); overflow: hidden; animation: fadeIn 0.2s ease-out;">
        <!-- Modal Header -->
        <div style="padding: 16px 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); display: flex; justify-content: space-between; align-items: center; background: rgba(239, 68, 68, 0.08);">
            <div style="font-size: 15px; font-weight: 700; color: #ef4444; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-trash-can"></i> Hapus Modul (Remove Module)
            </div>
            <button type="button" onclick="closeRemoveModuleModal()" style="background: none; border: none; color: #94a3b8; font-size: 16px; cursor: pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form onsubmit="submitRemoveModuleModal(event)" style="padding: 20px;">
            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px; line-height: 1.5;">
                Pilih modul yang ingin dihapus. Sistem akan menghapus seluruh folder modul di <code>app/Modules/</code>, Core Base Controller, dan memperbarui integritas Layer 5.
            </p>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #e2e8f0; margin-bottom: 6px;">
                    Pilih Modul Target <span style="color: #ef4444;">*</span>
                </label>
                <select id="modalRemoveModuleName" required style="width: 100%; box-sizing: border-box; background: #090d16; border: 1px solid var(--border-color); color: #fff; font-size: 13.5px; padding: 10px 14px; border-radius: 8px; outline: none; cursor: pointer;">
                    <option value="" disabled selected>-- Pilih Modul untuk Dihapus --</option>
                    @foreach($modules as $m)
                        <option value="{{ $m }}">{{ $m }} (app/Modules/{{ $m }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Modal Footer -->
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeRemoveModuleModal()" style="padding: 9px 16px; font-size: 13px; font-weight: 600; border-radius: 8px; border: 1px solid var(--border-color); color: #e2e8f0; background: transparent; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 9px 20px; font-size: 13px; font-weight: 700; border-radius: 8px; border: none; background: #ef4444; color: #fff; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);">
                    <i class="fa-solid fa-trash-can"></i> Hapus Modul Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
