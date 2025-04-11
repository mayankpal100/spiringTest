# Leaderboard App - Laravel + Vue.js

A full-stack leaderboard application using Laravel (API/backend) and Vue.js (frontend).

## Features

- Add, update, delete users
- Increment/decrement points
- View user details
- Sorting by name and points
- Search/filter by name
- Grouped stats by points via API
- QR code generation on user creation
- Scheduled winner tracking every 5 minutes

## Setup Instructions

### Backend (Laravel)

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

### Frontend (Vue)

```bash
npm install
npm run dev
```

To start:
```bash
php artisan serve
```

To generate QR: ensure queue is running:
```bash
php artisan queue:work
```

To run scheduled task use direct command to test:
```bash
*/5 * * * * cd /path-to-project && php artisan declare:winner >> /dev/null 2>&1
          OR 
php artisan declare:winner
```

Reset user scores:
```bash
php artisan users:reset-scores
```
Declare Winner command:
```bash
php artisan declare:winner
```


### API Endpoints

- `GET {APP_URL}/api/users` - list users (supports `search`, `sortBy`, `sortOrder`)
- `POST {APP_URL}/api/users` - add user (name, age, address)
- `DELETE {APP_URL}/api/users/{id}` - delete user
- `PATCH {APP_URL}/api/users/{id}/increment` - increase score
- `PATCH {APP_URL}/api/users/{id}/decrement` - decrease score
- `GET {APP_URL}/api/users/{id}` - user details
- `GET {APP_URL}/api/grouped-by-score` - grouped stats

