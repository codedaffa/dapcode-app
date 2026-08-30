# 🛡️ DapCode License — Threat Model & Trust Boundary

## 1. Introduction

Dokumen ini mendefinisikan model ancaman (*Threat Model*), batasan kepercayaan (*Trust Boundaries*), dan mitigasi keamanan untuk sistem perizinan dan aktivasi modul **DapCode License & Module Activation Engine**.

---

## 2. Trust Boundaries

Pemisahan tanggung jawab dan batasan kepercayaan dalam arsitektur DapCode didefinisikan secara tegas sebagai berikut:

```text
┌────────────────────────────────────────────────────────────────────────┐
│                   TRUSTED ZONE: DAPCODE LICENSE AUTHORITY              │
│                                                                        │
│  - Private Signing Key (RSA 2048-bit / Ed25519)                        │
│  - License Issuance Engine                                             │
│  - Revocation Authority                                                │
│  - Official Authority Database & Infrastructure                        │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
                         Signed License Payload
                         Signed Revocation Token
                                    │
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│               UNTRUSTED ZONE: CLIENT APPLICATION HOST                  │
│                                                                        │
│  - Application Source Code (PHP Interpreter)                           │
│  - Application Database & Local Storage                                │
│  - Local Environment Variables (.env)                                  │
│  - Public Verification Key                                             │
│  - Local System Clock                                                  │
│  - Server Administrator / Client Developer                             │
└────────────────────────────────────────────────────────────────────────┘
```

### 2.1. Trusted Components
- **DapCode License Authority Server:** Memegang *Private Signing Key*. Bertanggung jawab penuh untuk menandatangani lisensi (*Sign License*) dan menandatangani perintah pencabutan (*Sign Revocation*).
- **Cryptographic Trapdoor Mathematics:** Keamanan matematis algoritma asimetris (RSA 2048-bit / SHA-256) menjamin bahwa kepemilikan Public Key tidak memungkinkan pembuatan tanda tangan digital yang valid.

### 2.2. Untrusted Components
- **Client Application Code & Server:** Lingkungan tempat aplikasi Laravel dijalankan berada di bawah kontrol penuh pemilik server/klien.
- **Client Storage & Cache:** File runtime lokal (`storage/app/dapcode/.license`) dapat dibaca dan dimodifikasi oleh sistem administrator host.

---

## 3. Cryptographic Security Matrix

Tabel berikut merangkum cakupan perlindungan kriptografis terhadap berbagai vektor ancaman:

| Threat / Attack Vector | Protected? | Mitigation Mechanism | Classification |
|---|:---:|---|---|
| **Fake / Unsigned License** | **YES** | RSA-2048 Digital Signature Verification | Cryptographic Security |
| **Forged Signature** | **YES** | Public Key Verification (`openssl_verify`) | Cryptographic Security |
| **Expiration Date Tampering** | **YES** | Timestamp is part of Canonical Signed Payload | Cryptographic Security |
| **Module Privilege Escalation** | **YES** | Module whitelist array is Cryptographically Signed | Cryptographic Security |
| **Installation ID Tampering** | **YES** | Unique machine identifier is Cryptographically Signed | Cryptographic Security |
| **Cross-Installation Copying** | **YES** | Local `.installation` mismatch triggers Fail-Closed | Cryptographic Security |
| **Forged Revocation Token** | **YES** | Signed Revocation Token verified with Public Key | Cryptographic Security |
| **Status Manipulation (`REVOKED` &rarr; `ACTIVE`)** | **YES** | Status string is bound into Cryptographic Signature | Cryptographic Security |
| **Missing / Deleted License File** | **YES** | Fail-Closed Architecture (Default Deny 403) | System Hardening |
| **Corrupted License Payload** | **YES** | Integrity Check & Signature Verification Fail-Closed | System Hardening |
| **`.env` Flag Bypass** | **YES** | Zero bypass flags in codebase or config | Code Hardening |
| **Local Environment Bypass (`APP_ENV=local`)** | **YES** | No environment-based exemption logic | Code Hardening |
| **Debug Mode Bypass (`APP_DEBUG=true`)** | **YES** | Middleware enforcement active regardless of debug | Code Hardening |
| **Source Code Modification** | **NO** | Client has white-box access to PHP source files | **Trust Boundary Limitation** |
| **Middleware Removal** | **NO** | Client controls local framework files | **Trust Boundary Limitation** |
| **PHP Runtime Modification** | **NO** | Client owns and operates server runtime | **Trust Boundary Limitation** |

---

## 4. Inherent Trust Boundary Limitations

### 4.1. White-Box PHP Execution Reality
Dalam arsitektur perangkat lunak berbasis skrip interpreter (seperti PHP/Laravel) yang di-hosting sendiri (*self-hosted*):
- Seorang programmer atau sysadmin yang memiliki akses root ke file `.php` dapat secara teknis menghapus baris kode middleware atau menambahkan `return true;` pada service.
- **Kondisi ini BUKAN merupakan celah kriptografis (*Cryptographic Vulnerability*)**, melainkan batasan fundamental kepemilikan kode sumber (*Client Code Ownership / Trust Boundary Limitation*).
- Upaya mengaburkan kode (*obfuscation*, *string splitting*, *base64 hiding*) tidak memberikan keamanan sejati (*Security by Obscurity*). Keamanan DapCode bertumpu pada **Integritas Otentikasi Lisensi Kriptografis** yang kokoh dan dapat diaudit secara transparan (*Auditable & Testable*).

---

## 5. Security Principles & Terminology Guidelines

Untuk menjaga integritas dokumentasi dan audit:
- ❌ **Dilarang Mengklaim:** "100% Unbreakable", "Tamper-Proof Application", "Unbypassable".
- ✅ **Gunakan Terminologi Standar:**
  - *Cryptographically Forgery-Resistant*
  - *Asymmetric Digital Signature Protected*
  - *Fail-Closed Access Control*
  - *Granular Module Authorization*
