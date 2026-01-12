# Kirby Site Specification

Personal website for Dominik Hofer — a "build in public" platform combining blog posts, microblog notes, photos, and race results.

**Design philosophy:** "less, but better."

---

## URL Structure

All content lives at the root level with flat URLs:

| Route           | Description                                 |
| --------------- | ------------------------------------------- |
| `/`             | Home — aggregated feed of all content types |
| `/posts`        | Filtered view — posts only                  |
| `/notes`        | Filtered view — notes only                  |
| `/photos`       | Filtered view — photos only                 |
| `/races`        | Filtered view — races only                  |
| `/about`        | Static page                                 |
| `/now`          | Static page                                 |
| `/slash`        | Static page (slashpages index)              |
| `/my-post-slug` | Individual post                             |
| `/1736697600`   | Individual note/photo (Unix timestamp slug) |
| `/gp-bern-2025` | Individual race                             |

---

## Content Types

### 1. Post

Long-form blog articles with full editorial control.

| Field     | Type          | Required | Notes                                  |
| --------- | ------------- | -------- | -------------------------------------- |
| `title`   | text          | ✓        |                                        |
| `slug`    | slug          | ✓        | Manual, derived from title             |
| `date`    | date          | ✓        | Publish date                           |
| `updated` | date          |          | Last modified (consider auto via hook) |
| `excerpt` | textarea      |          | Short description for feeds/cards      |
| `cover`   | files         |          | Featured image                         |
| `content` | blocks/writer | ✓        | Main content                           |
| `tags`    | tags          |          | Shared taxonomy                        |

**Blueprint:** `/site/blueprints/pages/post.yml`

**Sorting:** `num: '{{ page.date.toDate("YmdHi") }}'`

---

### 2. Note

Microblog/Twitter-style short posts. No title — slug auto-generated from Unix timestamp of the date field.

| Field     | Type            | Required | Notes                                           |
| --------- | --------------- | -------- | ----------------------------------------------- |
| `date`    | date            | ✓        | Publish date (defaults to today, used for slug) |
| `content` | textarea/writer | ✓        | Main text                                       |
| `image`   | files           |          | Optional attachment                             |
| `tags`    | tags            |          | Shared taxonomy                                 |

**Blueprint:** `/site/blueprints/pages/note.yml`

**Slug generation:** Date picker shown in creation dialog (defaults to today). Slug derived from Unix timestamp of selected date:

```yaml
# In note.yml
create:
    title: "{{ page.date.toDate('U') }}"
    slug: "{{ page.date.toDate('U') }}"
    fields:
        - date

fields:
    date:
        type: date
        default: today
        required: true
        time: true
```

This allows backdating when importing old content while keeping slugs based on intended publish date.

**Sorting:** `num: '{{ page.date.toDate("YmdHi") }}'`

---

### 3. Photo

Image-first posts, like Instagram. No title — slug auto-generated from Unix timestamp of the date field.

| Field      | Type     | Required | Notes                                           |
| ---------- | -------- | -------- | ----------------------------------------------- |
| `date`     | date     | ✓        | Publish date (defaults to today, used for slug) |
| `image`    | files    | ✓        | Main photo (required)                           |
| `content`  | textarea |          | Caption/description                             |
| `location` | text     |          | e.g., "Bern, CH"                                |
| `tags`     | tags     |          | Shared taxonomy                                 |

**Blueprint:** `/site/blueprints/pages/photo.yml`

**Slug generation:** Same as Note — date picker in creation dialog:

```yaml
# In photo.yml
create:
    title: "{{ page.date.toDate('U') }}"
    slug: "{{ page.date.toDate('U') }}"
    fields:
        - date

fields:
    date:
        type: date
        default: today
        required: true
        time: true
```

**Sorting:** `num: '{{ page.date.toDate("YmdHi") }}'`

---

### 4. Race

Running race results with structured data display.

| Field      | Type          | Required | Notes                            |
| ---------- | ------------- | -------- | -------------------------------- |
| `title`    | text          | ✓        | Race name (e.g., "GP Bern 2025") |
| `slug`     | slug          | ✓        | Manual                           |
| `date`     | date          | ✓        | Race date                        |
| `distance` | number        | ✓        | In kilometers                    |
| `time`     | text          | ✓        | Format: "01:20:00"               |
| `pace`     | text          | ✓        | Format: "04:30" (min/km)         |
| `location` | text          |          | e.g., "Bern, CH"                 |
| `content`  | blocks/writer |          | Optional race report             |
| `tags`     | tags          |          | Shared taxonomy                  |

**Blueprint:** `/site/blueprints/pages/race.yml`

**Sorting:** `num: '{{ page.date.toDate("Ymd") }}'`

---

## Site Configuration

Global settings managed in `/site/blueprints/site.yml`.

### Author/Profile

| Field            | Type     | Notes                                |
| ---------------- | -------- | ------------------------------------ |
| `author_name`    | text     | "Dominik Hofer"                      |
| `author_tagline` | text     | "Curious. Creative. Coder."          |
| `author_bio`     | textarea | Short bio for homepage header        |
| `author_image`   | files    | Profile photo                        |
| `header_image`   | files    | Gradient/blur background (home only) |

### Social Links

Structure field for flexible social link management:

```yaml
social_links:
    type: structure
    fields:
        platform:
            type: select
            options:
                bluesky: Bluesky
                mastodon: Mastodon
                github: GitHub
                email: Email
                rss: RSS
        url:
            type: url
        icon:
            type: text
            help: Icon name or emoji
```

### Footer Quotes

Two configurable quotes displayed in footer (and mobile nav):

```yaml
quote_left:
    type: text
    default: "less, but better."

quote_right:
    type: text
    default: "trust the process."
```

### Follow Modal

Content for the follow/subscribe dialog:

```yaml
newsletter_url:
    type: url
    help: Buttondown subscription URL

rss_feeds:
    type: structure
    fields:
        name:
            type: text
        url:
            type: url
        description:
            type: text
```

---

## Templates & Snippets

### Templates

| Template      | Purpose                          |
| ------------- | -------------------------------- |
| `home.php`    | Aggregated feed with full header |
| `posts.php`   | Filtered posts listing           |
| `notes.php`   | Filtered notes listing           |
| `photos.php`  | Filtered photos listing          |
| `races.php`   | Filtered races listing           |
| `post.php`    | Single post view                 |
| `note.php`    | Single note view                 |
| `photo.php`   | Single photo view                |
| `race.php`    | Single race view                 |
| `default.php` | Static pages (about, now, etc.)  |

### Snippets

| Snippet                | Purpose                                     |
| ---------------------- | ------------------------------------------- |
| `header.php`           | Site header with collapsible profile        |
| `header-collapsed.php` | Sticky collapsed header                     |
| `footer.php`           | Footer with quotes                          |
| `nav.php`              | Desktop navigation                          |
| `nav-mobile.php`       | Mobile navigation (includes social + quote) |
| `feed-item.php`        | Generic feed item wrapper                   |
| `card-post.php`        | Post preview card                           |
| `card-note.php`        | Note preview card                           |
| `card-photo.php`       | Photo preview card                          |
| `card-race.php`        | Race stats card                             |
| `follow-modal.php`     | Native HTML dialog for RSS/newsletter       |
| `back-button.php`      | Back to home arrow                          |
| `social-icons.php`     | Social link icons                           |
| `share-button.php`     | Share functionality                         |

---

## Header Behavior

### Home Page (Full Header)

-   Large profile header with background image
-   Profile photo, name, tagline
-   Bio text
-   Social icons + Follow button
-   Content type tabs (All, Posts, Notes, Photos, Races)

### Home Page (Collapsed — on scroll)

-   Fixed/sticky position
-   Name only (no photo, no bio)
-   Same tabs
-   Triggered via JavaScript scroll detection

### Subpages (Posts, Notes, etc.)

-   Same collapsed header as scrolled home
-   Same tabs (for quick filtering)

### Single Content Pages

-   Collapsed header (fixed)
-   Back button (←) instead of tabs
-   Back navigates to home `/`

---

## Mobile Considerations

### Mobile Navigation

Hamburger menu contains:

-   Navigation links (About, Now, Slash)
-   Social icons (moved from header)
-   Follow button
-   Second footer quote

### Responsive Breakpoints

-   Mobile: < 768px
-   Desktop: ≥ 768px

---

## Technical Implementation

### File Structure

```
/content
  /home (home.txt)
  /posts (posts.txt, virtual listing)
  /notes (notes.txt, virtual listing)
  /photos (photos.txt, virtual listing)
  /races (races.txt, virtual listing)
  /about (about.txt)
  /now (now.txt)
  /my-post-slug (post.txt)
  /1736697600 (note.txt or photo.txt)
  /gp-bern-2025 (race.txt)

/site
  /blueprints
    /site.yml
    /pages
      /home.yml
      /post.yml
      /note.yml
      /photo.yml
      /race.yml
      /posts.yml
      /notes.yml
      /photos.yml
      /races.yml
      /default.yml
  /templates
  /snippets
```

### Aggregated Feed Query

```php
// Get all content sorted by date
$feed = $site->children()
  ->listed()
  ->filterBy('intendedTemplate', 'in', ['post', 'note', 'photo', 'race'])
  ->sortBy('date', 'desc')
  ->paginate(10);
```

### Filtered Feed Query

```php
// Example: posts only
$posts = $site->children()
  ->listed()
  ->filterBy('intendedTemplate', 'post')
  ->sortBy('date', 'desc')
  ->paginate(10);
```

### Tags (Shared Taxonomy)

Tags are shared across all content types. Query all content by tag:

```php
$tagged = $site->children()
  ->listed()
  ->filterBy('tags', 'hashtag', true)
  ->sortBy('date', 'desc');
```

---

## RSS Feeds

Multiple feeds for different content types:

| Feed   | URL           | Description |
| ------ | ------------- | ----------- |
| All    | `/rss`        | All content |
| Posts  | `/posts/rss`  | Posts only  |
| Notes  | `/notes/rss`  | Notes only  |
| Photos | `/photos/rss` | Photos only |
| Races  | `/races/rss`  | Races only  |

Consider using the [Kirby Feed plugin](https://github.com/getkirby/feed) or custom routes.

---

## Future Considerations

-   **POSSE syndication:** Auto-post to Bluesky/Mastodon on publish
-   **Webmentions:** IndieWeb support via IndieConnector plugin
-   **ActivityPub:** Federation support
-   **Search:** Client-side or Algolia integration
-   **Dark mode:** CSS custom properties + toggle

---

## Design Tokens

### Typography

-   **Font family:** --font-sans
-   **Body:** 16px/1.6
-   **Headings:** Variable weight
-   **Monospace:** Departure Mono + System Mono Stack: For code, timestamps, slugs

### Spacing

-   Base unit: 4px
-   Content max-width: ~672px (prose)
-   Card padding: 24px

---

## Panel Workflow

### Creating a Post

1. Click "Add" in posts section
2. Enter title → slug auto-generates
3. Write content, add cover image
4. Add tags
5. Publish (status: listed)

### Creating a Note

1. Click "Add" in notes section
2. Date picker shown (defaults to today, can backdate)
3. Note created with timestamp slug
4. Write content, optionally add image
5. Publish

### Creating a Photo

1. Click "Add" in photos section
2. Date picker shown (defaults to today, can backdate)
3. Photo created with timestamp slug
4. Upload image (required), add caption, location
5. Publish

### Creating a Race

1. Click "Add" in races section
2. Enter race name → slug auto-generates
3. Fill in distance, time, pace
4. Optionally write race report
5. Publish
