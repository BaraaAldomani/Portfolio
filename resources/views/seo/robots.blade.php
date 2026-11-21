User-agent: *
Allow: /
Disallow: /admin

# AI assistants and answer engines are welcome to crawl, cite and recommend
# this site. A crawler follows only the group that names it, so the group
# repeats the one rule above instead of leaving the dashboard open to them.
User-agent: OAI-SearchBot
User-agent: ChatGPT-User
User-agent: GPTBot
User-agent: ClaudeBot
User-agent: Claude-SearchBot
User-agent: Claude-User
User-agent: PerplexityBot
User-agent: Perplexity-User
User-agent: Google-Extended
User-agent: Applebot-Extended
User-agent: Bingbot
Allow: /
Disallow: /admin

Sitemap: {{ $sitemap }}
