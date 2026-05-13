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

## Backend tayanch tuzilmasi

Loyiha hali ham frontend-first holatda ishlaydi, lekin endi asosiy ma'lumotlar qatlamining skeleti qo'shildi.

### Yaratilgan migratsiyalar

- `courses` - kursning umumiy ma'lumoti
- `modules` - kurs ichidagi modullar
- `lessons` - har bir modulga tegishli darslar
- `quizzes` - darsga bog'langan bitta nazorat
- `quiz_questions` - nazorat savollari
- `quiz_answers` - savol variantlari

### Yaratilgan modellar

- `App\Models\Course`
- `App\Models\Module`
- `App\Models\Lesson`
- `App\Models\Quiz`
- `App\Models\QuizQuestion`
- `App\Models\QuizAnswer`

### Yaratilgan seeder'lar

- `Database\Seeders\DemoCourseSeeder`
- `Database\Seeders\DatabaseSeeder`

### Migratsiya va seed ishga tushirish

Docker ichida:

```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
```

Hammasini qayta tozalab boshlash kerak bo'lsa:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

## Keyingi bosqichlar

- Ma'lumotlarni MySQL bazasiga ulash
- Foydalanuvchi autentifikatsiyasi
- Real progress tracking
- Quiz natijalarini saqlash
- Admin yoki instructor paneli
