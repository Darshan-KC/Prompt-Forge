# Prompt Forge

A library for building, managing, and sharing AI prompts. Create reusable prompts with typed variables, system instructions, and model configurations.

## Features

- **Prompt Library** — Browse and search prompts in grid or list view
- **Prompt Editor** — Create prompts with system instructions, user messages, and typed variables
- **Variable Insertion** — Declare variables and insert them into messages with `{{key}}` syntax
- **Playground** — Test prompts with live variable binding
- **Command Palette** — Quick navigation with `Cmd+K`
- **Projects & Folders** — Organize prompts into folders and projects
- **Folders** — Tag and categorize prompts by topic or use case

## Tech Stack

- **Backend:** Laravel 13, PHP 8.3
- **Frontend:** Livewire 4, Flux UI, Tailwind CSS 4, Vite 8
- **Auth:** Laravel Fortify

## Requirements

- PHP >= 8.3
- Node.js >= 20
- npm

## Setup

```bash
composer setup
```

This runs `composer install`, generates an app key, runs migrations, installs npm dependencies, and builds assets.

## Development

```bash
composer dev
```

Runs the Laravel dev server, Vite watcher, and queue worker concurrently.

## Tests

```bash
composer test
```

## Project Structure

```
resources/
  views/
    screens/          # Page-level views
      dashboard.blade.php
      playground.blade.php
      prompts/
        create.blade.php
        index.blade.php
        show.blade.php
        edit.blade.php
      projects/
        index.blade.php
        show.blade.php
      settings.blade.php
      auth/           # Login, register, password reset
    components/
      app/            # Layout: sidebar, command palette, nav
      prompt/         # Prompt cards, rows, menus, detail sections
  data/
    prompts.php       # Mock prompt library (to be replaced with models)
  js/
    playground.js     # Playground Alpine component
app/
  Support/
    MockData.php      # Static data provider (to be replaced with repositories)
routes/
  web.php             # All routes
```

## Status

This is an early-stage UI prototype. Prompts are currently stored as static mock data. Backend persistence (models, migrations, controllers) is not yet implemented.

## License

MIT
