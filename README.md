# Prompt Forge

A professional AI prompt engineering workspace. Create, version, test and analyse prompts with typed variables, multi-provider model configuration and run history.

## Features

- **Prompt Library** — Grid/list views with client-side search, category filter, favourites and sorting
- **Prompt Editor** — System instructions, user messages and typed variables inserted via `{{key}}`
- **Playground** — Live variable binding, provider/model switching, token + cost estimation
- **Chat** — Laravel AI agent (`ChatAgent`) with a markdown renderer, token/cost accounting and `Cmd+Enter` to send
- **Versioning** — Version timeline per prompt plus a side-by-side compare view with an LCS line diff
- **Run History** — Every run with tokens, latency, cost and output preview; filterable by status and provider
- **Analytics** — Usage over time, per-model/provider breakdown, top and most expensive prompts
- **Projects & Folders** — Group prompts into projects with roles, folders and tags
- **Command Palette** — Fuzzy search over pages and prompts with `Cmd+K` / `Ctrl+K`
- **Settings** — Profile, appearance (light/dark/system), providers, models, API keys, notifications, account
- **Landing Page** — Public marketing page with hero, features, workflow and pricing

## Tech Stack

- **Backend:** Laravel 13, PHP 8.3
- **Frontend:** Livewire 4 + Volt, Flux UI, Tailwind CSS 4, Vite 8, Alpine (bundled with Livewire)
- **AI:** `laravel/ai` agents (17 provider drivers in `config/ai.php`)
- **Auth:** Laravel Fortify (login, registration, 2FA, passkeys)
- **Database:** PostgreSQL 17 (Docker), cache/queue/session on database

## Requirements

- PHP >= 8.3
- Node.js >= 20 (22 in the Docker images)
- Composer 2
- PostgreSQL 17 — or use the bundled Docker Compose stack

## Setup

```bash
composer setup
```

Runs `composer install`, copies `.env.example` to `.env`, generates the app key, migrates, installs npm packages and builds assets.

`.env.example` points at the Dockerised Postgres on `127.0.0.1:5433`, so bring the database up first:

```bash
docker compose up -d postgres
```

## Development

```bash
composer dev
```

Runs `php artisan dev`, which starts the Laravel server (`serve`), the queue worker (`queue:listen`), the log tail (`pail`) and the Vite watcher together.

### Docker

- `docker compose up -d` — local stack: `app` (Artisan serve), `vite`, `postgres`, `mailpit` UI on `http://localhost:8025`. All host ports are overridable via `APP_HOST_PORT`, `VITE_HOST_PORT`, `DB_HOST_PORT`, `MAILPIT_HOST_PORT`.
- `docker compose -f docker-compose.prod.yml up -d --build` — production stack: `nginx` (the only published port, `HTTP_PORT`, default `80`), `php-fpm`, `queue`, `scheduler`, `postgres` (not published) and a one-shot `assets-init`.

## AI Providers

Provider credentials live in `.env` and `config/ai.php`, not `config/services.php`. The default provider is selected with `AI_PROVIDER`:

```dotenv
AI_PROVIDER=gemini
GEMINI_API_KEY=your-key
```

`config/ai.php` ships drivers for OpenAI, Anthropic, Gemini, Azure, Bedrock, Cohere, DeepSeek, ElevenLabs, Groq, Jina, Mistral, Ollama, OpenRouter, Voyage, xAI and any OpenAI-compatible endpoint. Per-modality defaults are also configurable (`default_for_images`, `default_for_audio`, `default_for_embeddings`, `default_for_reranking`).

The chat agent lives at `app/Ai/Agents/ChatAgent.php`. Generate more with `php artisan make:agent`.

## Tests

```bash
composer test
```

`tests/Feature` covers the public pages, a 22-route authenticated smoke test and the chat component; `tests/Unit` holds unit tests. GitHub Actions runs the suite on PHP 8.3, 8.4 and 8.5.

## Project Structure

```
app/
  Ai/Agents/ChatAgent.php   # Laravel AI agent used by the chat screen
  Livewire/
    Auth/                   # Login, register (Volt single-file components)
    Chat/Chat.php           # Chat screen: prompt the agent, cost accounting
  Models/                   # Prompt, PromptVersion, PromptVariable, PromptRun,
                            # Project, Folder, Tag, Provider, AiModel, Activity
  Support/MockData.php      # Static data provider for the screens
database/
  migrations/               # Domain schema + laravel/ai conversation tables
  factories/                # One factory per model
  seeders/                  # Demo users, providers and models
resources/
  data/                     # Mock dataset (prompts, runs, projects, analytics, …)
  css/app.css               # Tailwind 4 + Flux CSS + brand theme tokens
  js/
    app.js                  # Entry point
    library.js              # pfLibrary, pfRuns, pfDiff Alpine components
    playground.js           # pfPlayground Alpine component
    chat.js                 # Token streaming, markdown rendering, shortcuts
  views/
    welcome.blade.php       # Public landing page
    auth/                   # Fortify auth views
    components/
      app/                  # Layout: sidebar, topbar, command palette, toaster
      prompt/               # Prompt card, prompt row, prompt menu
      shared/               # Avatars, badges, metric card, empty state, …
      layouts/              # app + guest layouts
    livewire/               # Volt / Livewire views (auth, chat)
    screens/                # Page-level views
      dashboard, playground, analytics, settings
      prompts/              # index, create, show, versions, compare
      projects/             # index, create, show
      history/              # index, show
routes/web.php              # All routes
config/ai.php               # Laravel AI provider configuration
```

Routes are declared with `Route::view` (and one Livewire component for chat) so `php artisan optimize` can cache them.

## Status

The UI is feature-complete but **still mock-backed**. This is what exists and what does not:

- **Real:** the database schema, Eloquent models, factories and seeders; auth; the Laravel AI integration for the chat screen; the entire UI layer (library, playground, versions, compare, history, analytics, settings, command palette).
- **Mocked:** `App\Support\MockData` (backed by `resources/data/*.php`) powers every screen except chat — no screen reads Eloquent yet, and prompts/runs/projects are not persisted.
- **Simulated:** playground runs and chat "streaming". Chat does make a real blocking LLM call to `AI_PROVIDER`, then reveals the finished response token-by-token on the client; the provider/model/temperature inputs in the UI are not yet passed to the API. The `agent_conversations` tables are migrated but unused, so chat history is per-request only.
- **Not built:** provider/model/API key CRUD (settings tabs toast instead).

## License

MIT
