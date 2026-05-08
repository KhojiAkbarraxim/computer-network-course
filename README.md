# Kompyuter Tarmoqlarini O'rganish

Laravel, Blade, Tailwind CSS va Alpine.js asosida qurilgan zamonaviy frontend demo o'quv platformasi. Hozircha loyiha faqat UI/UX qatlamiga qaratilgan, barcha ma'lumotlar statik demo ko'rinishida berilgan.

## Texnologiyalar

- Laravel
- Laravel Blade
- Tailwind CSS
- Alpine.js
- Docker
- Nginx
- MySQL
- Vite

## Tayyor sahifalar

- `/` - Bosh sahifa
- `/course` - Kurs modullari
- `/lesson/sample` - Namuna dars interfeysi
- `/quiz/sample` - Namuna quiz interfeysi
- `/dashboard` - O'quv dashboard
- `/about` - Loyiha haqida

## Docker orqali ishga tushirish

```bash
docker compose up --build -d
docker compose exec app npm run dev -- --host 0.0.0.0
```

Keyin brauzerda:

- Ilova: `http://localhost:8000`
- Vite: `http://localhost:5173`
- MySQL: `127.0.0.1:3307`

## Docker ishlatmasdan lokal ishga tushirish

```bash
composer install
npm install
php artisan serve
npm run dev
```

## Frontend tuzilmasi

- `resources/views/layouts/app.blade.php` - umumiy layout
- `resources/views/components/*` - qayta ishlatiladigan UI komponentlar
- `resources/views/pages/*` - barcha sahifalar
- `config/demo-course.php` - statik demo kontent
- `routes/web.php` - demo route'lar

## Keyingi bosqichlar

- Ma'lumotlarni MySQL bazasiga ulash
- Foydalanuvchi autentifikatsiyasi
- Real progress tracking
- Quiz natijalarini saqlash
- Admin yoki instructor paneli
