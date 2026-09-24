# Task Manager API

## Short Description

Task Manager is a full-featured task management application built with a Laravel REST API backend and an AI-generated modern frontend. Users can register, log in (including via Google OAuth), and manage their personal tasks through a secure, token-based authentication system powered by Laravel Sanctum. The API supports full CRUD operations on tasks, along with pagination, search, and status filtering, ensuring each user only accesses their own data. The project follows Laravel best practices, including Form Request validation, Eloquent relationships, and clean separation of concerns between authentication and task logic. It is designed to be lightweight, secure, and easy to extend, making it a solid foundation for real-world task tracking applications.

---

## Tech Stack

- **Backend:** Laravel 12 (PHP 8.2)
- **Authentication:** Laravel Sanctum (Token-based) + Google OAuth (Laravel Socialite)
- **Database:** MySQL (TiDB Cloud in production)
- **Frontend:** Built with AI (modern UI, animations, bilingual English/Arabic support)
- **Hosting:** Render (Backend), TiDB Cloud (Database)

---

## Features

- User registration and login with hashed passwords
- Google OAuth login (auto-creates an account on first login)
- Token-based authentication via Laravel Sanctum (with token expiration)
- Logout that revokes only the current session's token
- Full CRUD for tasks (create, read, update, delete)
- Each user can only see and manage their own tasks
- Pagination on task listing (15 per page)
- Optional search by task title
- Optional filtering by task status (completed / not completed)
- Centralized validation via Form Requests
- Clean RESTful API structure under `/api`

---

## Database Structure

### `users`
| Column | Type | Notes |
|---|---|---|
| id | bigint | Primary key |
| name | string | |
| email | string | Unique |
| password | string | Hashed |
| timestamps | | created_at, updated_at |

### `tasks`
| Column | Type | Notes |
|---|---|---|
| id | bigint | Primary key |
| title | string | Required |
| description | text | Nullable |
| status | boolean | Default: false |
| user_id | foreign key | References `users.id`, cascades on delete |
| timestamps | | created_at, updated_at |

### `personal_access_tokens`
Managed automatically by Laravel Sanctum to store API tokens.

---

## Authentication

All protected routes require a Bearer token in the request header:

```
Authorization: Bearer {token}
```

### Public Endpoints

| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/register` | Register a new user. Requires `name`, `email`, `password`, `password_confirmation`. Returns a token automatically (auto-login). |
| POST | `/api/login` | Log in with `email` and `password`. Returns a token on success, or 401 on invalid credentials. |
| GET | `/api/auth/google` | Redirects the user to Google's login page. |
| GET | `/api/auth/google/callback` | Handles Google's response, creates/finds the user, and redirects to the frontend with a token. |

### Protected Endpoints

| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/logout` | Revokes the current access token only. |

---

## Task Endpoints (Protected)

All endpoints below require a valid Bearer token and only return/affect the authenticated user's own tasks.

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/tasks` | List tasks (paginated, 15 per page). Supports `?status=0\|1` and `?search=keyword`. |
| POST | `/api/tasks` | Create a new task. Body: `title` (required), `description` (optional), `status` (optional). |
| GET | `/api/tasks/{id}` | Show a single task's details. |
| PUT/PATCH | `/api/tasks/{id}` | Update a task. All fields optional. |
| DELETE | `/api/tasks/{id}` | Delete a task (returns 204 No Content). |

---

## Validation

Validation is handled through dedicated Form Request classes:

- `StoreTaskRequest` — validates task creation input.
- `UpdateTaskRequest` — validates task update input (fields optional via `sometimes`).
- `StoreRegisterRequest` — validates registration input, including a strong password policy (min 8 characters, mixed case, numbers, symbols).
- `LoginRequest` — validates login input (basic presence checks only, since credential correctness is verified separately for security reasons).

---

## Security Notes

- Passwords are always hashed using `Hash::make()` before being stored, including randomly generated passwords for Google OAuth accounts.
- Login failures (wrong email or wrong password) return the same generic error message and status code (401) to prevent user enumeration attacks.
- Tokens expire automatically after 1 month.
- Logout only revokes the token used in the current request, allowing multiple devices/sessions to remain active independently.
- Mass assignment is protected via the `$fillable` property on models.

---

## Frontend

The frontend is a modern, AI-generated single-page application featuring:

- A landing page with a welcoming hero animation
- Login and registration pages (including a "Sign in with Google" option)
- A protected dashboard for managing tasks with search, filtering, and pagination
- Toast notifications for successful actions (login, registration, task creation/update)
- Full bilingual support (English as default, Arabic with RTL support)
- Smooth transitions and modern animation effects throughout

---

## Environment Variables

Key variables required in `.env`:

```
DB_CONNECTION=mysql
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URL=

FRONTEND_URL=
```

---

## Deployment

- **Backend:** Hosted on Render.
- **Database:** Hosted on TiDB Cloud (MySQL-compatible).
- **Frontend:** Hosted separately, connected to the deployed backend via the API base URL.

---

## Author

Built by Ali as a hands-on Laravel learning project, progressing from a basic CRUD API to a fully authenticated, deployable task management system.
