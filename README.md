# JustHolistics — Community Protocol & Discussion Platform

> A full-stack community platform for sharing structured wellness protocols, discussion threads, reviews, and voting — powered by **Laravel 11**, **Next.js 14**, **TailwindCSS**, and **Typesense**.

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Tech Stack](#2-tech-stack)
3. [Setup Option A — Manual (No Docker)](#3-setup-option-a--manual-no-docker)
4. [Setup Option B — Docker (One Command)](#4-setup-option-b--docker-one-command)
5. [Environment Variables](#5-environment-variables)
6. [API Reference](#6-api-reference)
7. [Typesense Search](#7-typesense-search)
8. [Seeded Demo Data](#8-seeded-demo-data)
9. [Project Structure](#9-project-structure)
10. [GitHub Setup Guide](#10-github-setup-guide)
11. [Walkthrough Video Guide](#11-walkthrough-video-guide)
12. [Troubleshooting](#12-troubleshooting)

---

## 1. Project Overview

JustHolistics is a Reddit-style discussion platform centred around **structured healing and wellness protocols**. Community members can:

- 📋 Browse, create, and search **protocols** (e.g. "30-Day Gut Reset", "Dopamine Detox", "Circadian Rhythm Optimization")
- 💬 Start and reply to **discussion threads** — standalone or linked to a protocol
- ⭐ Leave **star-rated reviews** with written feedback on any protocol
- 👍 **Upvote / downvote** threads and comments (one vote per user, toggle to remove)
- 🔍 **Search-as-you-type** powered by Typesense with sort and filter controls

---

## 2. Tech Stack

| Layer      | Technology                                     |
|------------|------------------------------------------------|
| Backend    | Laravel 11, PHP 8.3, SQLite (or MySQL)         |
| Frontend   | Next.js 14 (App Router), React 18, TailwindCSS |
| Search     | Typesense Cloud (hosted)                       |
| API Style  | RESTful JSON                                   |
| Dev Tools  | Docker Compose (optional), Artisan CLI         |

---

## 3. Setup Option A — Manual (No Docker)

**Use this for a standard local development setup without Docker.**

### Prerequisites — install once

| Tool     | Min Version | Download |
|----------|-------------|----------|
| PHP      | 8.2+        | https://windows.php.net/download (Windows) · `brew install php` (Mac) |
| Composer | 2.x         | https://getcomposer.org/download |
| Node.js  | 18+         | https://nodejs.org (LTS version) |

> **Windows only — enable PHP extensions:**
> Open `C:\php\php.ini` in Notepad and remove the `;` from the start of each of these lines:
> ```
> extension=curl
> extension=mbstring
> extension=openssl
> extension=pdo_sqlite
> extension=sqlite3
> extension=zip
> extension=fileinfo
> ```

---

### A1 — Backend

```bash
# Navigate into the backend folder
cd backend

# Copy the environment file
cp .env.example .env          # Mac / Linux
copy .env.example .env        # Windows

# Install all PHP packages (~2 min first time)
composer install

# Generate the Laravel application key
php artisan key:generate

# Create the SQLite database file
touch database/database.sqlite          # Mac / Linux
echo. > database\database.sqlite        # Windows

# Run all database migrations (creates every table)
php artisan migrate

# Seed demo data — protocols, threads, comments, votes, reviews
# Also indexes everything into Typesense automatically
php artisan db:seed

# Start the API server
php artisan serve
```

✅ **Backend is running at http://localhost:8000**
Test it: open http://localhost:8000/api/protocols in your browser — you should see JSON.

---

### A2 — Frontend

Open a **second terminal** and keep the backend running in the first one:

```bash
# Navigate into the frontend folder
cd frontend

# Copy the environment file
cp .env.local.example .env.local          # Mac / Linux
copy .env.local.example .env.local        # Windows

# Install all Node packages (~1-2 min first time)
npm install

# Start the development server
npm run dev
```

✅ **App is running at http://localhost:3000**

---

### A3 — Using MySQL instead of SQLite (optional)

Edit `backend/.env` and replace the DB section:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=justholistics
DB_USERNAME=root
DB_PASSWORD=your_password
```

Then re-run:
```bash
php artisan migrate:fresh --seed
```

---

## 4. Setup Option B — Docker (One Command)

**Use this to skip installing PHP, Composer, and Node.js entirely.**
Docker handles all dependencies automatically inside containers.

### Prerequisite — Install Docker Desktop (once)

| OS      | Download |
|---------|----------|
| Windows | https://www.docker.com/products/docker-desktop |
| Mac     | https://www.docker.com/products/docker-desktop |
| Linux   | https://docs.docker.com/engine/install |

After installing, open Docker Desktop and wait for the green **"Engine running"** indicator in the bottom-left corner.

---

### B1 — Start everything

Open a terminal in the project root (where `docker-compose.yml` is) and run:

```bash
docker compose up --build
```

Docker will automatically:
- Install PHP 8.3 + all extensions
- Run `composer install`
- Install Node.js + run `npm install`
- Create the SQLite database
- Run `php artisan migrate`
- Run `php artisan db:seed` (seeds data + indexes Typesense)
- Start both servers

> **First build takes ~5 minutes** (downloads base images once). After that it starts in ~15 seconds.

✅ **Frontend: http://localhost:3000**
✅ **Backend API: http://localhost:8000/api**

---

### B2 — Daily Docker commands

```bash
docker compose up              # Start (after first build)
docker compose up --build      # Rebuild after code changes
docker compose down            # Stop everything
docker compose down -v         # Stop + wipe database volume (fresh start)
docker compose logs -f         # Watch live logs from both containers
docker compose logs backend    # Backend logs only
docker compose logs frontend   # Frontend logs only
```

---

### B3 — Run Artisan commands inside Docker

```bash
# Reindex Typesense
docker compose exec backend php artisan typesense:reindex --fresh

# Wipe and reseed everything
docker compose exec backend php artisan migrate:fresh --seed

# Open Laravel Tinker (interactive REPL)
docker compose exec backend php artisan tinker
```

---

## 5. Environment Variables

### Backend — `backend/.env`

| Variable | Example Value | Description |
|----------|--------------|-------------|
| `APP_KEY` | *(generated)* | Run `php artisan key:generate` |
| `APP_ENV` | `local` | `local` or `production` |
| `APP_DEBUG` | `true` | Show detailed errors |
| `APP_URL` | `http://localhost:8000` | Backend base URL |
| `DB_CONNECTION` | `sqlite` | `sqlite` or `mysql` |
| `DB_DATABASE` | `database/database.sqlite` | SQLite path or MySQL DB name |
| `TYPESENSE_HOST` | `x32yfd6mhrs4nvb5p-1.a1.typesense.net` | Typesense cloud host |
| `TYPESENSE_PORT` | `443` | Typesense port |
| `TYPESENSE_PROTOCOL` | `https` | `https` for cloud |
| `TYPESENSE_API_KEY` | *(provided in brief)* | Admin API key |
| `TYPESENSE_SEARCH_ONLY_KEY` | *(provided in brief)* | Frontend search-only key |
| `FRONTEND_URL` | `http://localhost:3000` | Allowed CORS origin |

### Frontend — `frontend/.env.local`

| Variable | Example Value | Description |
|----------|--------------|-------------|
| `NEXT_PUBLIC_API_URL` | `http://localhost:8000/api` | Backend API base URL |
| `NEXT_PUBLIC_TYPESENSE_HOST` | `f8g1svtrc4xbjdnwp-1.a1.typesense.net` | Typesense cloud host |
| `NEXT_PUBLIC_TYPESENSE_PORT` | `443` | Typesense port |
| `NEXT_PUBLIC_TYPESENSE_PROTOCOL` | `https` | Protocol |
| `NEXT_PUBLIC_TYPESENSE_SEARCH_KEY` | *(provided in brief)* | Search-only key (safe for browser) |

> **Note:** The Typesense credentials in the original assessment brief are outdated.
> The correct credentials for the active cluster are already configured in `docker-compose.yml` and `backend/.env.example`. No changes needed to run the project.

---

## 6. API Reference

**Base URL:** `http://localhost:8000/api`

### Protocols

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/protocols` | List all protocols (paginated) |
| POST | `/protocols` | Create a new protocol |
| GET | `/protocols/{id}` | Get protocol detail with threads and reviews |
| PUT | `/protocols/{id}` | Update a protocol |
| DELETE | `/protocols/{id}` | Delete a protocol |
| GET | `/protocols/search?q=...` | Typesense full-text search |
| GET | `/protocols/{id}/reviews` | List reviews for a protocol |
| POST | `/protocols/{id}/reviews` | Create or update a review |

**List query params:** `search`, `sort`, `category`, `tag`, `page`, `per_page`
**Sort values:** `recent` · `most_upvoted` · `highest_rated` · `most_reviewed`

---

### Threads

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/threads` | List all threads (paginated) |
| POST | `/threads` | Create a new thread |
| GET | `/threads/{id}` | Get thread detail with nested comments |
| PUT | `/threads/{id}` | Update a thread |
| DELETE | `/threads/{id}` | Delete a thread |
| GET | `/threads/search?q=...` | Typesense full-text search |
| GET | `/threads/{id}/comments` | List comments (nested tree) |
| POST | `/threads/{id}/comments` | Post a comment or reply |

**List query params:** `search`, `sort`, `protocol_id`, `tag`, `page`, `per_page`
**Sort values:** `recent` · `most_upvoted` · `most_commented`

---

### Comments

| Method | Endpoint | Description |
|--------|----------|-------------|
| PUT | `/comments/{id}` | Edit a comment |
| DELETE | `/comments/{id}` | Soft-delete (shows [deleted], keeps thread structure) |

**Post a reply** — include `parent_id` in the POST body to `/threads/{id}/comments`:
```json
{ "body": "Great point!", "parent_id": 42, "user_id": 1 }
```

---

### Votes

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/vote` | Cast, change, or remove a vote |
| GET | `/votes/user/{userId}` | Get all current votes by a user |

**Vote payload:**
```json
{
  "user_id": 1,
  "voteable_type": "thread",
  "voteable_id": 5,
  "value": 1
}
```
- `value: 1` = upvote · `value: -1` = downvote
- Posting the **same value again removes the vote** (toggle behaviour)
- `voteable_type` accepts: `thread` or `comment`

---

### Reviews

| Method | Endpoint | Description |
|--------|----------|-------------|
| DELETE | `/reviews/{id}` | Delete a review |

**Post review payload:**
```json
{ "rating": 5, "title": "Life-changing", "body": "Full details...", "user_id": 1 }
```
One review per user per protocol — posting again **updates** the existing review.

---

### Admin / Utility

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/health` | Health check |
| POST | `/admin/reindex` | Trigger full Typesense reindex |

---

## 7. Typesense Search

### Collections

**`protocols`** — synced on every create/update/delete
```
id, title, content, tags[], author, avg_rating, vote_score, review_count, created_at
```

**`threads`** — synced on every create/update/delete
```
id, title, body, tags[], author, protocol_id, protocol_title, vote_score, comment_count, created_at
```

### Search query weights
`title (4×) · tags (3×) · content/body (2×) · author (1×)`

### Manual reindex CLI
```bash
php artisan typesense:reindex                        # all collections
php artisan typesense:reindex --collection=protocols # protocols only
php artisan typesense:reindex --collection=threads   # threads only
php artisan typesense:reindex --fresh                # drop + recreate + reindex
```

---

## 8. Seeded Demo Data

`php artisan db:seed` creates realistic wellness content:

| Resource  | Count    | Notes |
|-----------|----------|-------|
| Users     | 20       | Demo: `demo@justholistics.com` / `password` |
| Protocols | 13       | Full wellness protocols (gut reset, dopamine detox, circadian rhythm, longevity, etc.) |
| Threads   | 20       | Community-style discussions, 15 linked to protocols |
| Comments  | 80–160   | Nested replies up to 3 levels deep |
| Reviews   | 26–78    | Star ratings + written feedback |
| Votes     | varies   | Upvotes/downvotes on threads and comments |

---

## 9. Project Structure

```
justholistics/
├── docker-compose.yml                  ← Run both services with one command
├── README.md
├── IMPLEMENTATION_NOTES.md
│
├── backend/                            ← Laravel 11 REST API
│   ├── Dockerfile
│   ├── .env.example
│   ├── artisan
│   ├── composer.json
│   ├── app/
│   │   ├── Console/Commands/
│   │   │   └── TypesenseReindex.php    ← php artisan typesense:reindex
│   │   ├── Http/Controllers/
│   │   │   ├── ProtocolController.php  ← CRUD + Typesense search
│   │   │   ├── ThreadController.php    ← CRUD + Typesense search
│   │   │   ├── CommentController.php   ← Nested comments
│   │   │   ├── ReviewController.php    ← Protocol reviews + ratings
│   │   │   └── VoteController.php      ← Polymorphic upvote/downvote
│   │   ├── Models/
│   │   │   ├── Protocol.php            ← tags (JSON), avg_rating, vote_score
│   │   │   ├── Thread.php              ← belongs to Protocol
│   │   │   ├── Comment.php             ← self-referential (parent_id, depth)
│   │   │   ├── Review.php              ← unique per user+protocol
│   │   │   ├── Vote.php                ← polymorphic morphTo
│   │   │   └── User.php
│   │   └── Services/
│   │       └── TypesenseService.php    ← upsert, delete, search, bulk import
│   ├── config/
│   │   ├── typesense.php               ← collection schemas
│   │   └── cors.php                    ← CORS allowed origins
│   ├── database/
│   │   ├── migrations/                 ← 6 migrations (users→protocols→threads→comments→reviews→votes)
│   │   ├── factories/                  ← realistic wellness content
│   │   └── seeders/DatabaseSeeder.php  ← seeds + Typesense bulk index
│   └── routes/
│       └── api.php                     ← all API routes
│
└── frontend/                           ← Next.js 14 (App Router)
    ├── Dockerfile
    ├── .env.local.example
    ├── tailwind.config.ts
    └── src/
        ├── app/
        │   ├── layout.tsx              ← Root layout with Navbar
        │   ├── globals.css             ← Tailwind + custom component classes
        │   ├── page.tsx                ← Home page
        │   ├── protocols/
        │   │   ├── page.tsx            ← Browse + filter protocols
        │   │   ├── new/page.tsx        ← Create protocol form
        │   │   └── [id]/page.tsx       ← Detail: content, threads, reviews
        │   ├── threads/
        │   │   ├── page.tsx            ← Browse + filter threads
        │   │   ├── new/page.tsx        ← Create thread form
        │   │   └── [id]/page.tsx       ← Detail: body + nested comments
        │   └── search/page.tsx         ← Typesense search (protocols + threads tabs)
        ├── components/
        │   ├── layout/Navbar.tsx       ← Responsive nav, mobile hamburger
        │   ├── protocols/ProtocolCard.tsx
        │   ├── threads/ThreadCard.tsx
        │   ├── threads/CommentItem.tsx ← Recursive nested replies
        │   ├── search/SearchBar.tsx    ← Debounced input with clear button
        │   ├── voting/VoteButton.tsx   ← Live upvote/downvote with toggle
        │   └── ui/StarRating.tsx       ← Interactive or display star rating
        └── lib/
            ├── api.ts                  ← Typed axios wrappers for every endpoint
            ├── typesense.ts            ← Typesense InstantSearch adapter config
            └── utils.ts               ← formatDate, truncate, DEMO_USER_ID, helpers
```

---

## 10. GitHub Setup Guide

Follow these steps to push the project to a GitHub repository for submission.

### Step 1 — Create a GitHub account
If you don't have one: https://github.com/signup

### Step 2 — Create a new repository
1. Go to https://github.com/new
2. Repository name: `justholistics` (or any name)
3. Set to **Public** (required for assessment review)
4. **Do NOT** tick "Add a README" — we already have one
5. Click **Create repository**

### Step 3 — Install Git (if not installed)
- Windows: https://git-scm.com/download/win (click through installer, all defaults)
- Mac: run `xcode-select --install` in Terminal
- Verify: `git --version`

### Step 4 — Push the project

Open terminal in the `justholistics` project root folder and run:

```bash
# Initialise git
git init

# Create a .gitignore to exclude large/sensitive files
cat > .gitignore << 'EOF'
backend/vendor/
backend/.env
backend/database/database.sqlite
frontend/node_modules/
frontend/.next/
frontend/.env.local
EOF

# Stage everything
git add .

# First commit
git commit -m "Initial commit: JustHolistics full-stack platform

- Laravel 11 REST API (protocols, threads, comments, reviews, votes)
- Next.js 14 frontend with TailwindCSS
- Typesense search integration with auto-sync
- Polymorphic voting system (upvote/downvote)
- Nested threaded comments
- Docker Compose support
- Full seeder with realistic wellness content"

# Connect to GitHub (replace YOUR_USERNAME and YOUR_REPO_NAME)
git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPO_NAME.git

# Push
git branch -M main
git push -u origin main
```

### Step 5 — Verify
Open `https://github.com/YOUR_USERNAME/YOUR_REPO_NAME` — all files should be visible.

> **Note:** `vendor/` and `node_modules/` are excluded by `.gitignore` — that is correct and expected. Reviewers run `composer install` and `npm install` themselves.

---

## 11. Walkthrough Video Guide

The assessment asks for a walkthrough video. Here is exactly what to record:

### Recommended tool
- **Windows:** Xbox Game Bar (`Win + G`) → Capture → Record, or [OBS Studio](https://obsproject.com) (free)
- **Mac:** QuickTime Player → File → New Screen Recording
- **Any platform:** [Loom](https://loom.com) (free, records + uploads automatically, gives you a shareable link)

### Suggested video structure (~5–8 minutes)

| # | Section | What to show | Time |
|---|---------|--------------|------|
| 1 | **Intro** | Say your name, explain the project | 30 sec |
| 2 | **Setup** | Show terminal running `composer install`, `php artisan migrate`, `php artisan db:seed` successfully | 1 min |
| 3 | **Backend API** | Open Postman or browser — show `GET /api/protocols`, `GET /api/threads`, `GET /api/protocols/search?q=gut` | 1 min |
| 4 | **Home page** | Show the live app at localhost:3000 — home page, categories | 30 sec |
| 5 | **Protocols** | Browse protocols list, use search bar, change sort filter, click a protocol card | 1 min |
| 6 | **Protocol detail** | Show full content, embedded threads, reviews with star ratings, write a review | 1 min |
| 7 | **Voting** | Click upvote on a thread, show score change. Click again to toggle off | 30 sec |
| 8 | **Threads** | Browse threads, open a thread, show nested comments, post a comment and a reply | 1 min |
| 9 | **Search page** | Go to /search, type a query, show instant results, switch protocols/threads tab, change sort | 1 min |
| 10 | **Code walkthrough** | Briefly show: `ProtocolController.php`, `Vote.php` (polymorphic), `TypesenseService.php`, `VoteButton.tsx` | 1 min |

### Where to upload
- **Loom** (recommended): https://loom.com — gives a link instantly
- **YouTube** (unlisted): upload → set to Unlisted → copy link
- **Google Drive**: upload `.mp4` → share → "Anyone with the link can view"

Include the video link in your submission email and in the GitHub repo `README.md`.

---

## 12. Troubleshooting

| Error | Solution |
|-------|----------|
| `APP_KEY` not set | Run `php artisan key:generate` |
| `No such table` | Run `php artisan migrate` |
| `Class not found` | Run `composer dump-autoload` |
| `composer: command not found` | Install from https://getcomposer.org |
| `npm: command not found` | Install Node.js from https://nodejs.org |
| CORS error in browser | Confirm `FRONTEND_URL=http://localhost:3000` in `backend/.env` |
| Typesense connection refused | Check `TYPESENSE_API_KEY` and `TYPESENSE_HOST` in `.env` |
| Port 8000 already in use | `php artisan serve --port=8001` — also update `NEXT_PUBLIC_API_URL` |
| Port 3000 already in use | `npm run dev -- --port 3000` |
| Docker: port conflict | `docker compose down` → `docker compose up --build` |
| Docker: want fresh database | `docker compose down -v` → `docker compose up --build` |
| Seeder Typesense error | Run `php artisan typesense:reindex --fresh` after seeding |

---


---

## 12. Hosting Guide — 100% Free (Render + Vercel)

Deploy your live demo for free using:
- **Render** — hosts the Laravel backend (free tier, Docker-based)
- **Vercel** — hosts the Next.js frontend (free forever for personal projects)

> ⚠️ **Render free tier note:** The backend "sleeps" after 15 minutes of no traffic and takes ~30 seconds to wake up on the first request. This is normal for the free tier — your app still works fine, it just has a cold-start delay. Perfect for an assessment demo.

---

### Overview

| Service | Hosts | Cost | URL format |
|---------|-------|------|------------|
| [Render](https://render.com) | Laravel API | Free | `https://justholistics-api.onrender.com` |
| [Vercel](https://vercel.com) | Next.js frontend | Free | `https://justholistics.vercel.app` |

**Total time to deploy: ~15 minutes**

---

### Before you start — push to GitHub

Both Render and Vercel deploy directly from your GitHub repo.
Follow **Section 10 (GitHub Setup Guide)** above first if you haven't already.

Your GitHub repo must have this structure:
```
github.com/YOUR_USERNAME/justholistics/
├── backend/         ← Laravel API
├── frontend/        ← Next.js app
├── docker-compose.yml
└── README.md
```

---

### Step 1 — Deploy Backend to Render (Free)

**1.1 — Create a Render account**
1. Go to **https://render.com**
2. Click **"Get Started for Free"**
3. Sign up with your **GitHub account** (easiest — gives Render access to your repos)

**1.2 — Create a new Web Service**
1. In your Render dashboard, click **"New +"** → **"Web Service"**
2. Click **"Connect a repository"** → find and select your `justholistics` repo
3. Click **"Connect"**

**1.3 — Configure the service**

Fill in these settings:

| Field | Value |
|-------|-------|
| **Name** | `justholistics-api` |
| **Root Directory** | `backend` |
| **Runtime** | `Docker` |
| **Branch** | `main` |
| **Instance Type** | `Free` |

**1.4 — Add environment variables**

Scroll down to **"Environment Variables"** and add these one by one (click "Add Environment Variable" for each):

| Key | Value |
|-----|-------|
| `APP_NAME` | `JustHolistics` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | *(leave blank — auto-generated by the app)* |
| `DB_CONNECTION` | `sqlite` |
| `DB_DATABASE` | `/var/www/html/database/database.sqlite` |
| `TYPESENSE_HOST` | `x32yfd6mhrs4nvb5p-1.a1.typesense.net` |
| `TYPESENSE_PORT` | `443` |
| `TYPESENSE_PROTOCOL` | `https` |
| `TYPESENSE_API_KEY` | `XE7Wrc3WU8JNehnUH3QpcEqpG4xp6wvg` |
| `TYPESENSE_SEARCH_ONLY_KEY` | `I2KxztOF7xCHq9IuhvEwEurIhwqH0Yco` |
| `FRONTEND_URL` | `https://justholistics.vercel.app` *(update after Step 2)* |

**1.5 — Deploy**

Click **"Create Web Service"**. Render will:
- Pull your code from GitHub
- Build the Docker image (~3–5 minutes first time)
- Run migrations and seed the database automatically
- Start the server

Watch the build logs in real time on the Render dashboard.

**1.6 — Get your backend URL**

Once the deploy shows **"Live"** (green), your backend URL will be shown at the top:
```
https://justholistics-api.onrender.com
```

**1.7 — Verify it works**

Open this in your browser:
```
https://justholistics-api.onrender.com/api/protocols
```
You should see a JSON response with 13 protocols. ✅

> If you see a loading spinner for ~30 seconds, that's just the free tier waking up. Refresh once and it will be fast.

---

### Step 2 — Deploy Frontend to Vercel (Free)

**2.1 — Create a Vercel account**
1. Go to **https://vercel.com**
2. Click **"Sign Up"** → **"Continue with GitHub"**

**2.2 — Import your project**
1. Click **"Add New..."** → **"Project"**
2. Find your `justholistics` repo and click **"Import"**

**2.3 — Configure the project**

| Field | Value |
|-------|-------|
| **Framework Preset** | `Next.js` (auto-detected) |
| **Root Directory** | Click "Edit" → type `frontend` → click "Continue" |

**2.4 — Add environment variables**

Expand **"Environment Variables"** and add:

| Key | Value |
|-----|-------|
| `NEXT_PUBLIC_API_URL` | `https://justholistics-api.onrender.com/api` |
| `NEXT_PUBLIC_TYPESENSE_HOST` | `x32yfd6mhrs4nvb5p-1.a1.typesense.net` |
| `NEXT_PUBLIC_TYPESENSE_PORT` | `443` |
| `NEXT_PUBLIC_TYPESENSE_PROTOCOL` | `https` |
| `NEXT_PUBLIC_TYPESENSE_SEARCH_KEY` | `I2KxztOF7xCHq9IuhvEwEurIhwqH0Yco` |

> Replace `justholistics-api` in the API URL with your actual Render service name from Step 1.6.

**2.5 — Deploy**

Click **"Deploy"**. Vercel builds and deploys in ~2 minutes.

You'll get a URL like:
```
https://justholistics.vercel.app
```

✅ **Your app is now live!**

---

### Step 3 — Update FRONTEND_URL on Render

Now that you have your real Vercel URL:

1. Go to your Render dashboard → **justholistics-api** service
2. Click **"Environment"** tab
3. Find `FRONTEND_URL` → click the edit icon
4. Change the value to your actual Vercel URL (e.g. `https://justholistics-xxxx.vercel.app`)
5. Click **"Save Changes"** — Render will auto-redeploy

This makes sure CORS allows requests from your frontend.

---

### Step 4 — Verify everything end-to-end

Open your Vercel URL and test:

- [ ] Home page loads with categories
- [ ] Protocols page loads with 13 seeded protocols
- [ ] Search bar returns results as you type
- [ ] Click a protocol → detail page shows content, threads, reviews
- [ ] Click upvote on a thread → score updates live
- [ ] Open a thread → post a comment → it appears
- [ ] Create a new protocol via the form → it saves and appears in the list

---

### Redeploying after code changes

**Both services auto-redeploy every time you push to GitHub:**

```bash
git add .
git commit -m "describe your change"
git push
```

Render and Vercel both watch your `main` branch and trigger a new build automatically.

---

### Running Artisan commands on Render

In your Render dashboard → **justholistics-api** → **"Shell"** tab (available on free tier):

```bash
php artisan typesense:reindex --fresh
php artisan migrate:fresh --seed
php artisan tinker
```

---

### Your live URLs (fill in after deploying)

| | URL |
|-|-----|
| 🌐 **Live App** | `https://_________________________________.vercel.app` |
| 🔧 **API** | `https://_________________________________.onrender.com/api` |
| 📁 **GitHub Repo** | `https://github.com/______________/justholistics` |

Include these three links in your assessment submission email.

---

### Troubleshooting

| Problem | Fix |
|---------|-----|
| Render build fails | Check **Logs** tab in Render dashboard — look for the red error line |
| App loads but API returns errors | Check Render logs — likely a missing env variable |
| CORS error in browser console | Update `FRONTEND_URL` on Render to match your exact Vercel URL |
| Vercel build fails | Make sure Root Directory is set to `frontend` not the repo root |
| Backend returns 500 | Open `https://YOUR-RENDER-URL/api/health` — check Render logs for details |
| Data missing / empty | Use Render Shell: `php artisan migrate:fresh --seed` |
| Typesense search returns nothing | Use Render Shell: `php artisan typesense:reindex --fresh` |
| Render free tier sleeping | Normal — first request after idle takes ~30 sec. Just refresh once. |
