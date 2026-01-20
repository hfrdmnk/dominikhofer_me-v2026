# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a personal website built with **Kirby 5** - a file-based CMS for PHP. Content is stored in flat files (no database), making it easy to version control and deploy.
Use the docs (https://getkirby.com/docs/reference) or the forum (https://forum.getkirby.com/) for more information when you're stuck.

More info about the project in @docs/SPEC.md. Also take a look at the designs in docs/img if needed.

## Local Development

The project is served locally using **Laravel Herd**. The local URL is:
- `https://dominikhofer_me-v2026.test`

When using the Chrome extension, it should open this URL.

The user runs `pnpm watch` to compile assets during development (Claude does not run this).

## Common Commands

```bash
# Install dependencies
composer install
```

## Architecture

### Directory Structure

-   **`content/`** - Content files (txt files + media). Each folder = a page. The folder name determines the URL slug.
-   **`site/blueprints/`** - Panel schemas defining editable fields for pages (`pages/`) and site-wide settings (`site.yml`)
-   **`site/templates/`** - PHP templates that render pages. Template name matches content file type (e.g., `default.txt` uses `default.php`)
-   **`site/snippets/`** - Reusable PHP template partials
-   **`site/config/`** - Configuration files (create `config.php` here for settings)
-   **`kirby/`** - Core CMS (don't edit, managed by composer)
-   **`media/`** - Auto-generated cache for processed images (gitignored)

### Kirby Conventions

-   **Blueprints** use YAML to define the Panel UI for editing content
-   **Templates** have access to `$page`, `$site`, and `$kirby` objects
-   **Content files** use a simple `Key: Value` format with `----` separators between fields
-   Page URLs are derived from folder names (e.g., `content/1_projects/` = `/projects`)
-   Number prefixes in folder names (like `1_`) control sort order and are stripped from URLs

## Code Style

-   2 spaces for indentation in templates, snippets, CSS, JS, YAML
-   UTF-8 encoding, LF line endings

## Bluesky Sync

The Bluesky plugin provides a sync endpoint for importing posts:

- **Endpoint:** `POST /api/bluesky/sync`
- **Authentication:** Uses Kirby's API authentication
- **Response:** `{ "status": "success", "message": "..." }`

For automated syncing via external cron on a production server:
```bash
# Cron example (every hour)
0 * * * * curl -X POST https://yoursite.com/api/bluesky/sync -H "Authorization: Bearer YOUR_API_TOKEN"
```

The 1-hour cache TTL also provides passive refresh when pages are visited.

## Skills

-   **`/rams`** - Run accessibility and visual design review on component files
