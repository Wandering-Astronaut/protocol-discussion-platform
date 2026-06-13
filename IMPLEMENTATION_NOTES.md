# Implementation Notes

## Architecture Decisions

### Backend (Laravel 11)

**Polymorphic Votes Table**  
Votes use `morphs('voteable')` so a single `votes` table handles upvotes/downvotes for both `Thread` and `Comment` models. A unique constraint on `(user_id, voteable_type, voteable_id)` enforces one vote per user per item. The vote endpoint is a toggle: casting the same vote removes it; casting the opposite changes it.

**Vote Score Denormalization**  
`vote_score` is stored on `protocols`, `threads`, and `comments` tables and recalculated on every vote change via `recalculateVoteScore()`. This avoids a `SUM` query on every list render and makes Typesense indexing straightforward.

**Typesense Auto-Sync**  
Every create/update/delete on Protocol and Thread calls `TypesenseService::upsertDocument()` or `deleteDocument()`. The `TypesenseReindex` Artisan command (`php artisan typesense:reindex --fresh`) handles bulk reindexing using chunked `bulkImport()`. The same command is exposed as `POST /api/admin/reindex`.

**Review Rating Recalculation**  
`Review::booted()` fires `protocol->recalculateRating()` on every save/delete. This keeps `avg_rating` and `review_count` always accurate and syncs to Typesense so faceted sorting by rating reflects reality.

**Soft Deletes**  
Protocols, Threads, and Reviews use `SoftDeletes`. Comments use an `is_deleted` flag instead — this preserves thread structure (replies remain visible) while hiding the deleted body, consistent with how Reddit/HN handle deleted comments.

**Nested Comments**  
Comments have a `parent_id` (self-referential) and a `depth` field (0, 1, 2, …). The frontend renders up to depth 3 before disabling further nesting. Eager loading loads up to 3 levels: `replies.replies.user`.

---

### Frontend (Next.js 14 App Router)

**Search Strategy**  
The `/search` page uses Typesense directly via the REST API (`/api/protocols/search`, `/api/threads/search`). The backend proxies Typesense with custom `query_by_weights` (title 4×, tags 3×, content 2×, author 1×) and facet support. A 300ms debounce prevents excessive API calls during typing.

**State Management**  
No external state library. Each page manages its own local state with `useState`/`useCallback`/`useEffect`. This is appropriate for the scope — a larger app would benefit from React Query or Zustand.

**Demo User**  
Authentication is mocked via `DEMO_USER_ID = 1` (the seeded `demo@justholistics.com` user). All create/vote/review/comment actions are attributed to this user. A real auth system (Sanctum + login page) would replace this constant.

**Docker Support**
The project runs fully containerized via Docker Compose. The backend Dockerfile runs `migrate:fresh --seed` on every startup ensuring a clean database with realistic wellness content. Environment variables in `docker-compose.yml` take priority over `.env` files inside the container, making local development consistent across any machine.

**Responsive Design**  
- Mobile-first Tailwind classes throughout
- Grid layouts switch from 1 → 2 → 3 columns at `sm`/`lg` breakpoints
- Navbar collapses to hamburger on mobile
- Cards and forms are full-width on mobile

---

## Typesense Collections Schema

**protocols**  
`id (string), title, content, tags (string[]), author, avg_rating (float), vote_score (int32), review_count (int32), created_at (int64)`  
Default sort: `vote_score:desc`

**threads**  
`id, title, body, tags (string[]), author, protocol_id, protocol_title, vote_score (int32), comment_count (int32), created_at (int64)`  
Default sort: `vote_score:desc`

---

## What Would Be Added in Production

1. **Real authentication** — Sanctum tokens, login/register pages
2. **Image uploads** — Protocol cover images, user avatars
3. **Notifications** — Real-time replies via Laravel Echo + Pusher
4. **OpenAI summaries** — Summarize protocol content on creation (bonus feature)
5. **Analytics heatmap** — Track protocol views, engagement over time
6. **Rate limiting** — Throttle vote and comment endpoints
7. **Moderation** — Flag system, admin dashboard
