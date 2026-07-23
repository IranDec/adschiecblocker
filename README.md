# Adschi External Connections Blocker (AdschiEcBlocker)

A PrestaShop module to block external HTTP requests and remove Google Fonts to improve website speed, especially for servers located in restricted networks (such as intranets in Iran).

## Features
- Blocks unnecessary external requests (by overriding `file_get_contents`).
- Removes Google Fonts from the Front-end and Back-end to improve loading times.
- Whitelist support: By default, essential SEO and Google tools (like Google Analytics, Google Tag Manager, etc.) bypass the blocker.
- Blacklist support: Force block specific domains.
- Fully compatible with PrestaShop 1.7 and above.

## Installation
1. Zip the module folder.
2. Go to your PrestaShop admin panel.
3. Navigate to **Modules > Module Manager** and click on **Upload a module**.
4. Upload the zip file and wait for the installation to finish.

## Configuration
After installation, you can go to the module configuration page to manage:
- Enable/Disable blocking of external requests.
- Enable/Disable removing Google Fonts.
- Add custom domains to the Whitelist (one domain per line).
- Add custom domains to the Blacklist.

---

# ماژول AdschiEcBlocker

ماژول پرستاشاپ برای مسدودسازی درخواست‌های خارجی و حذف فونت‌های گوگل جهت افزایش سرعت سایت، به‌ویژه برای سرورهایی که در شبکه‌های محدود و اینترانت (مانند ایران) قرار دارند.

## ویژگی‌ها (Features)
- مسدودسازی درخواست‌های خارجی غیرضروری (با بازنویسی تابع `file_get_contents`)
- حذف فونت‌های گوگل (Google Fonts) از ظاهر سایت (Front-end) و بخش مدیریت (Back-end)
- پشتیبانی از لیست مجاز (Whitelist) با امکان عبور دادن ابزارهای حیاتی سئو به صورت پیش‌فرض (مثل Google Analytics و Google Tag Manager)
- پشتیبانی از لیست سیاه (Blacklist) برای مسدودسازی اجباری دامنه‌های خاص
- سازگاری کامل با پرستاشاپ ۱.۷ به بالا

## نحوه نصب (Installation)
۱. پوشه ماژول را به صورت فایل zip درآورید.
۲. وارد پنل مدیریت پرستاشاپ خود شوید.
۳. از منوی ماژول‌ها، روی گزینه **بارگذاری یک ماژول** کلیک کنید.
۴. فایل زیپ را آپلود کرده و منتظر بمانید تا ماژول نصب شود.

## پیکربندی (Configuration)
پس از نصب، می‌توانید به بخش تنظیمات ماژول مراجعه کرده و موارد زیر را مدیریت کنید:
- فعال یا غیرفعال کردن مسدودسازی درخواست‌های خارجی.
- فعال یا غیرفعال کردن حذف فونت‌های گوگل.
- افزودن دامنه‌های دلخواه به لیست مجاز (هر دامنه در یک خط).
- افزودن دامنه‌های دلخواه به لیست سیاه.

---

**Author / توسعه‌دهنده:** Mohammad Babaei
**Website / وب‌سایت:** [adschi.com](https://adschi.com)
## Changelog
### v1.2.5
- Fixed an issue causing a blank white screen on large pages (like permissions and stats) due to regex limits by implementing a safe replace method.
- Skipped JSON content from output buffering to prevent data corruption on AJAX endpoints.

---
### v1.2.4
- Improved `check_version.php` block to filter the iframe from the Dashboard HTML output.
- Improved Font Awesome regex block to support modern Kit JS scripts and CDN links ending in `all.min.css`.
- Fixed local Font Awesome compatibility to prevent blocking self-hosted files.

---
### v1.2.2
- Fixed Dashboard timeout issues by preventing problematic modules (like \`gamification\` and \`ps_mbo\`) from making external API calls when PrestaShop API blocking is enabled.

---
### v1.2.1
- Added `module_key` to bypass "untrusted module" warning during installation on PrestaShop 1.7.

---
## لیست تغییرات
### نسخه 1.2.5
- رفع مشکل صفحه سفید در صفحات طولانی و حجیم (مانند آمار و دسترسی‌ها) به دلیل محدودیت‌های جایگزینی عبارات باقاعده (regex limits).
- نادیده گرفتن کدهای JSON از فیلترهای بافر خروجی جهت جلوگیری از خراب شدن دیتا در درخواست‌های ایجکس.

### نسخه 1.2.4
- بهبود مسدودسازی درخواست `check_version.php` در پیشخوان با حذف iframe مربوطه از کدهای HTML.
- بهبود مسدودسازی فونت آوسام (Font Awesome) جهت پشتیبانی از اسکریپت‌های Kit و لینک‌های CDN جدید.
- رفع تداخل مسدودسازی فونت آوسام با فایل‌های محلی (Local) و جلوگیری از مسدود شدن فونت‌های خود سایت.

### نسخه 1.2.2
- رفع مشکل تایم‌اوت و باز نشدن صفحه پیشخوان (Dashboard) با جلوگیری از اجرای ماژول‌های دردسرساز (مانند \`gamification\` و \`ps_mbo\`) هنگام فعال بودن مسدودسازی API پرستاشاپ.

### نسخه 1.2.1
- اضافه شدن `module_key` برای رفع خطای "ماژول تایید نشده" هنگام نصب در پرستاشاپ 1.7.
