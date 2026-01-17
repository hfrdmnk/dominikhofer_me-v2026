---
name: kirby-theme-expert
description: Create and develop Kirby CMS themes (v5). Use when building Kirby templates, snippets, blueprints, or working with Kirby's file-based content system. Covers PHP templates, YAML blueprints for the Panel, and Kirby's PHP API.
---

# Kirby CMS Theme Development (v5)

This skill provides guidance for creating themes and sites with Kirby CMS version 5.

## Core Concepts

Kirby is a **file-based CMS** — no database required. Content is stored in text files, and the folder structure determines the site hierarchy.

### Key Architecture

```
your-site/
├── content/           # All content lives here (text files + media)
├── site/
│   ├── blueprints/    # Panel configuration (YAML)
│   │   ├── site.yml
│   │   ├── pages/
│   │   ├── files/
│   │   └── users/
│   ├── templates/     # Frontend templates (PHP)
│   ├── snippets/      # Reusable template parts (PHP)
│   ├── controllers/   # Template logic (PHP)
│   ├── models/        # Custom page models (PHP)
│   ├── collections/   # Reusable content collections
│   ├── plugins/       # Kirby plugins
│   └── config/
│       └── config.php # Site configuration
├── assets/            # CSS, JS, images (public)
└── kirby/             # Kirby core (don't edit)
```

## Content Structure

Content is stored in `/content/` as folders with text files:

```
content/
├── site.txt                    # Global site data
├── home/
│   └── home.txt                # Home page content
├── blog/
│   ├── blog.txt                # Blog listing page
│   ├── 1_first-post/
│   │   ├── post.txt            # Blog post content
│   │   └── cover.jpg           # Page files
│   └── 2_second-post/
│       └── post.txt
└── about/
    └── about.txt
```

### Content Files Format

Content files use a simple field format with `----` separators:

```
Title: My Page Title

----

Text: This is the main content.
You can write multiple lines.

----

Date: 2025-01-15

----

Tags: design, kirby, cms
```

### Folder Naming

-   **Numbered folders** (`1_`, `2_`) = sorted/listed pages
-   **Unnumbered folders** = unlisted pages
-   **Drafts** use `_drafts` prefix or are managed via Panel

## Templates

Templates are PHP files in `/site/templates/` that render pages. The template name must match the content file name.

| Content File | Template             |
| ------------ | -------------------- |
| `home.txt`   | `templates/home.php` |
| `blog.txt`   | `templates/blog.php` |
| `post.txt`   | `templates/post.php` |

### Basic Template

```php
<?php snippet('header') ?>

<main>
  <h1><?= $page->title()->html() ?></h1>
  <?= $page->text()->kirbytext() ?>
</main>

<?php snippet('footer') ?>
```

### Template Variables

Every template has access to:

-   `$page` — Current page object
-   `$site` — Site object (global data from `site.txt`)
-   `$pages` — All top-level pages
-   `$kirby` — Kirby instance

### Common Page Methods

```php
// Basic info
$page->title()          // Page title
$page->url()            // Full URL
$page->slug()           // URL slug
$page->template()       // Template name
$page->isHomePage()     // Is this the home page?

// Content fields (from .txt file)
$page->text()           // Get "Text:" field
$page->date()           // Get "Date:" field
$page->myfield()        // Any custom field

// Field methods for output
$page->title()->html()          // HTML escaped
$page->text()->kirbytext()      // Parse Markdown/Kirbytext
$page->date()->toDate('Y-m-d')  // Format date

// Navigation
$page->children()       // Child pages
$page->siblings()       // Sibling pages
$page->parent()         // Parent page
$page->hasChildren()    // Has children?

// Files
$page->files()          // All files
$page->images()         // Only images
$page->image('cover.jpg')  // Specific image
```

### Site Methods

```php
$site->title()          // Site title
$site->url()            // Site URL
$site->children()       // Top-level pages
$site->find('blog')     // Find page by path
```

## Snippets

Reusable template parts in `/site/snippets/`:

### Basic Snippet

```php
// site/snippets/header.php
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title><?= $page->title() ?> | <?= $site->title() ?></title>
  <?= css('assets/css/main.css') ?>
</head>
<body>
```

### Using Snippets

```php
// Basic usage
<?php snippet('header') ?>

// With data
<?php snippet('card', ['item' => $page]) ?>

// With slots (Kirby 4+)
<?php snippet('layout', slots: true) ?>
  <?php slot('sidebar') ?>
    <nav>...</nav>
  <?php endslot() ?>

  <?php slot() ?>
    <main>Main content here</main>
  <?php endslot() ?>
<?php endsnippet() ?>
```

### Snippet with Data

```php
// site/snippets/article-card.php
<article>
  <h2><?= $article->title()->html() ?></h2>
  <?= $article->intro()->kirbytext() ?>
  <a href="<?= $article->url() ?>">Read more</a>
</article>
```

```php
// In template
<?php foreach ($page->children()->listed() as $article): ?>
  <?php snippet('article-card', ['article' => $article]) ?>
<?php endforeach ?>
```

## Blueprints (Panel Configuration)

YAML files in `/site/blueprints/` that define the Panel interface.

### Page Blueprint

```yaml
# site/blueprints/pages/post.yml
title: Blog Post
icon: 📝

# Page status options
status:
    draft: Draft
    listed: Published

# Form fields
fields:
    text:
        type: textarea
        label: Content

    date:
        type: date
        label: Published
        default: today

    cover:
        type: files
        label: Cover Image
        max: 1

    tags:
        type: tags
        label: Tags
```

### Site Blueprint

```yaml
# site/blueprints/site.yml
title: Site Settings

fields:
    title:
        type: text
        label: Site Title

    description:
        type: textarea
        label: Site Description

    copyright:
        type: text
        label: Copyright Text
```

### Common Field Types

```yaml
# Text inputs
title:
    type: text

description:
    type: textarea

content:
    type: writer # Rich text editor

body:
    type: blocks # Block editor (page builder)

# Selection
category:
    type: select
    options:
        - Design
        - Development
        - Marketing

featured:
    type: toggle
    text: Featured post?

tags:
    type: tags

# Media
cover:
    type: files
    max: 1

gallery:
    type: files
    multiple: true

# Dates & Numbers
date:
    type: date
    default: today

time:
    type: time

price:
    type: number
    step: 0.01

# Structure (repeatable groups)
team:
    type: structure
    fields:
        name:
            type: text
        role:
            type: text
        photo:
            type: files
            max: 1
```

### Blueprint Layout with Columns

```yaml
# site/blueprints/pages/article.yml
title: Article

columns:
    - width: 2/3
      fields:
          title:
              type: text
          text:
              type: blocks

    - width: 1/3
      fields:
          date:
              type: date
          author:
              type: users
              max: 1
          cover:
              type: files
              max: 1
```

### Tabs

```yaml
title: Article

tabs:
    content:
        label: Content
        icon: text
        fields:
            title:
                type: text
            text:
                type: blocks

    meta:
        label: Meta
        icon: cog
        fields:
            date:
                type: date
            tags:
                type: tags
```

## Controllers

Separate logic from templates in `/site/controllers/`:

```php
// site/controllers/blog.php
<?php
return function ($page, $pages, $site, $kirby) {

    $articles = $page->children()
        ->listed()
        ->sortBy('date', 'desc');

    // Pagination
    $articles = $articles->paginate(10);
    $pagination = $articles->pagination();

    return [
        'articles' => $articles,
        'pagination' => $pagination
    ];
};
```

Then use in template:

```php
// site/templates/blog.php
<?php snippet('header') ?>

<?php foreach ($articles as $article): ?>
  <?php snippet('article-card', ['article' => $article]) ?>
<?php endforeach ?>

<?php snippet('pagination', ['pagination' => $pagination]) ?>

<?php snippet('footer') ?>
```

## Configuration

```php
// site/config/config.php
<?php
return [
    'debug' => true,  // Disable in production!

    'panel' => [
        'install' => true,  // Allow panel install
    ],

    'thumbs' => [
        'quality' => 80,
        'srcsets' => [
            'default' => [300, 600, 900, 1200]
        ]
    ],

    'cache' => [
        'pages' => [
            'active' => true
        ]
    ],

    'routes' => [
        // Custom routes here
    ]
];
```

## Image Handling

```php
// Get image
$image = $page->image('cover.jpg');

// Thumbnail with options
$image->thumb(['width' => 800, 'quality' => 80]);

// Srcset for responsive images
$image->srcset([300, 600, 900, 1200]);

// In template
<img
  src="<?= $image->resize(800)->url() ?>"
  srcset="<?= $image->srcset([400, 800, 1200]) ?>"
  alt="<?= $image->alt() ?>"
>
```

## Helpers

```php
// Assets
<?= css('assets/css/main.css') ?>
<?= js('assets/js/app.js') ?>

// URLs
<?= url('blog/my-post') ?>

// Dump for debugging
<?= dump($page->content()->toArray()) ?>

// Escaping
<?= $page->title()->html() ?>    // HTML entities
<?= $page->title()->escape() ?>  // Same as above

// Kirbytext (Markdown + extras)
<?= $page->text()->kirbytext() ?>
```

## Common Patterns

### Navigation Menu

```php
// site/snippets/menu.php
<nav>
  <ul>
    <?php foreach ($site->children()->listed() as $item): ?>
    <li>
      <a href="<?= $item->url() ?>" <?= e($item->isActive(), 'aria-current="page"') ?>>
        <?= $item->title()->html() ?>
      </a>
    </li>
    <?php endforeach ?>
  </ul>
</nav>
```

### Conditional Content

```php
<?php if ($page->cover()->isNotEmpty()): ?>
  <img src="<?= $page->cover()->toFile()->url() ?>" alt="">
<?php endif ?>

<?php if ($image = $page->image()): ?>
  <img src="<?= $image->url() ?>" alt="">
<?php endif ?>
```

### Filtering & Sorting

```php
// Get listed children
$page->children()->listed()

// Filter by template
$site->index()->filterBy('template', 'post')

// Filter by field value
$page->children()->filterBy('featured', true)

// Sort
$page->children()->sortBy('date', 'desc')

// Limit
$page->children()->limit(5)

// Pagination
$page->children()->paginate(10)
```

## Getting Help

### Official Documentation

-   **Guide**: https://getkirby.com/docs/guide/quickstart
-   **Reference** (API): https://getkirby.com/docs/reference
-   **Cookbook** (recipes): https://getkirby.com/docs/cookbook

### Community

-   **Forum**: https://forum.getkirby.com — Very active, search first!
-   **Discord**: https://chat.getkirby.com
-   **Plugins**: https://plugins.getkirby.com

### Key Reference Pages

-   [Page Object](https://getkirby.com/docs/reference/objects/cms/page)
-   [Field Methods](https://getkirby.com/docs/reference/templates/field-methods)
-   [Blueprint Fields](https://getkirby.com/docs/reference/panel/fields)
-   [Helpers](https://getkirby.com/docs/reference/templates/helpers)

## Checklist for New Theme

1. [ ] Set up folder structure (`site/templates`, `site/snippets`, `site/blueprints`)
2. [ ] Create `site.yml` blueprint for global settings
3. [ ] Create `default.php` template (required fallback)
4. [ ] Create header/footer snippets
5. [ ] Define page blueprints for each content type
6. [ ] Create matching templates
7. [ ] Add assets (CSS/JS) to `/assets/`
8. [ ] Configure in `site/config/config.php`
9. [ ] Test Panel at `/panel`
