# IndieConnector Setup

This project uses [IndieConnector](https://github.com/mauricerenck/indieConnector) for cross-posting to social networks (POSSE) and collecting responses.

## Features

- **Cross-posting**: Automatically posts to Mastodon and Bluesky when publishing content
- **Panel stats**: View posting statistics in the Kirby Panel
- **Response collection**: Collect likes, reposts, and replies from Mastodon/Bluesky

## Environment Variables

| Variable | Required | Description |
|----------|----------|-------------|
| `MASTODON_BEARER` | For Mastodon | Mastodon access token |
| `MASTODON_INSTANCE_URL` | For Mastodon | e.g., `https://mastodon.social` |
| `BLUESKY_HANDLE` | For Bluesky | Your Bluesky handle |
| `BLUESKY_APP_PASSWORD` | For Bluesky | Bluesky app password |
| `INDIECONNECTOR_SECRET` | For responses | Secret for webhook authentication |
| `INDIECONNECTOR_STATS` | Optional | Set to `true` to enable Panel statistics |
| `INDIECONNECTOR_RESPONSES` | Optional | Set to `true` to enable response collection |

## Generating the Secret

Generate a secure random secret:

```bash
openssl rand -hex 32
```

This produces a 64-character hex string like `a1b2c3d4e5f6...`

## Response Collection

Response collection pulls likes, reposts, and replies from Mastodon/Bluesky posts back to your site.

### Prerequisites

- SQLite database (configured via `sqlitePath` in config)
- `INDIECONNECTOR_SECRET` set
- `INDIECONNECTOR_RESPONSES=true`

### Cron Jobs

Set up two cron jobs on your production server:

```bash
# Queue responses - checks which posts need response collection
*/15 * * * * curl -s "https://dominikhofer.me/indieConnector/cron/queue-responses?secret=YOUR-SECRET"

# Fetch responses - processes the queue and stores responses
*/15 * * * * curl -s "https://dominikhofer.me/indieConnector/cron/fetch-responses?secret=YOUR-SECRET"
```

Replace `YOUR-SECRET` with your actual `INDIECONNECTOR_SECRET` value.

### How It Works

1. When you publish a post, IndieConnector cross-posts to Mastodon/Bluesky
2. The plugin tracks the URLs of those social posts
3. `queue-responses` cron identifies posts that need checking (default: not checked in last hour)
4. `fetch-responses` cron pulls responses and stores them as webmentions
5. Responses appear in the Panel under each post

### Configuration Options

These can be adjusted in `site/config/config.php` if needed:

| Option | Default | Description |
|--------|---------|-------------|
| `responses.ttl` | 3600 | Seconds before rechecking a post for responses |
| `responses.limit` | 10 | Posts to check per queue run |
| `responses.queue.limit` | 50 | Responses to process per fetch run |

## Panel Statistics

When `INDIECONNECTOR_STATS=true`, the Panel shows an IndieConnector menu item with posting statistics.

## Resources

- [IndieConnector Documentation](https://github.com/mauricerenck/indieConnector)
- [All Configuration Options](https://github.com/mauricerenck/indieConnector/blob/main/docs/options.md)
- [Response Collection Details](https://github.com/mauricerenck/indieConnector/blob/main/docs/collecting-responses.md)
