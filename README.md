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