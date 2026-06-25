# 🤖 Bot Reminder Telegram

> **ID:** Bot Telegram personal untuk menyimpan pesan dan mendapat pengingat pada waktu yang ditentukan, dilengkapi dashboard web untuk manajemen reminder.
>
> **EN:** A personal Telegram bot to save messages and get reminders at a scheduled time, with a web dashboard for reminder management.

---

## Tech Stack

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-3-4FC08D?style=flat-square&logo=vue.js&logoColor=white)
![Inertia.js](https://img.shields.io/badge/Inertia.js-2-9553E9?style=flat-square&logo=inertia&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-6-646CFF?style=flat-square&logo=vite&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Telegram](https://img.shields.io/badge/Telegram-Bot_API-26A5E4?style=flat-square&logo=telegram&logoColor=white)

---

## Fitur / Features

**ID:**
- Kirim pesan (teks/foto/file) ke bot → bot tanya kapan diingatkan
- Reminder sekali & recurring (contoh: setiap Senin, 9AM)
- Dashboard web: lihat, edit, hapus, toggle reminder
- Edit profil (email & password)
- Pengaturan Bot Token & Chat ID langsung dari dashboard

**EN:**
- Send any message (text/photo/file) to bot → bot asks when to remind
- One-time & recurring reminders (e.g. every Monday, 9AM)
- Web dashboard: view, edit, delete, toggle reminders
- Profile editor (email & password)
- Set Bot Token & Chat ID directly from the dashboard

---

## Instalasi / Installation

### Prasyarat / Prerequisites

- PHP 8.2 (XAMPP)
- Composer
- Node.js & npm
- MySQL (XAMPP)
- Telegram Bot Token (via [@BotFather](https://t.me/BotFather))

### Langkah / Steps

```bash
# 1. Clone repository
git clone <repo-url> bot-reminder-web
cd bot-reminder-web

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
/opt/lampp/bin/php artisan key:generate

# 4. Edit .env
# Isi DB_DATABASE, DB_USERNAME, DB_PASSWORD
# Isi TELEGRAM_BOT_TOKEN dan TELEGRAM_CHAT_ID

# 5. Jalankan migrasi dan seed
/opt/lampp/bin/php artisan migrate --seed

# 6. Jalankan aplikasi
/opt/lampp/bin/php artisan serve        # Terminal 1 — Laravel
npm run dev                              # Terminal 2 — Vite
/opt/lampp/bin/php artisan queue:work   # Terminal 3 — Queue
/opt/lampp/bin/php artisan bot:poll     # Terminal 4 — Bot polling
```

### Login Default / Default Credentials

| Field    | Value                     |
|----------|---------------------------|
| Email    | `admin@botreminder.local` |
| Password | `password`                |

> **ID:** Ganti password segera setelah login pertama via halaman Profil.
>
> **EN:** Change the password immediately after first login via the Profile page.

---

## Lisensi / License

[MIT](LICENSE)
