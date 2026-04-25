# Lyre Content Skills

## Use This Skill When

- adding or editing CMS-backed public pages
- changing the `Page` -> `Section` -> `Text/Button/File/Data` graph
- updating seed data for `lyre/content`
- tracing how SvelteKit consumes seeded pages and sections
- preparing AI-safe edits for the Aspire public website

## Required Reading Order

1. `packages/content/src/Models/Page.php`
2. `packages/content/src/Models/Section.php`
3. `packages/content/src/Http/Resources/Section.php`
4. `packages/content/src/Repositories/DataRepository.php`
5. `aspire-api/database/seeders/PageSeeder.php`
6. `aspire-api/database/seeders/PublicPageTemplateSeeder.php`
7. `aspire-api/database/data/pages.json`
8. `aspire-api/database/data/public-page-templates.json`
9. `aspire-ui/src/routes/(public)/+layout.server.ts`
10. `aspire-ui/src/lib/components/page.svelte`
11. `aspire-ui/src/lib/components/base.svelte`
12. `aspire-ui/src/lib/components/registry.ts`

## Mental Model

### Backend

- `Page` is mostly SEO and page identity.
- `Section` is the real layout/content building block.
- section relationships are recursive through `section_sections`.
- sections collect `texts`, `buttons`, `files`, `icon`, and `data`.
- `Section` eagerly loads those relations by default.
- `Section` resource transforms `data` from an array of `Data` models into a keyed object by lowercased data name.

### Frontend

- public layout fetches all pages from `/pages?with=sections&unpaginated=true`
- each route using the shared page component selects a page by slug
- `base.svelte` maps each `section.name` to a concrete Svelte component from `registry.ts`

This means:

- section names are component identifiers
- order is stored in pivot tables, not only in JSON
- changing a section contract requires checking the matching Svelte component

## Seeder Rules

- Prefer editing JSON data files, not hand-building pages in PHP.
- Use the shared seeding concern for recursive pages.
- Keep seeders idempotent with `updateOrCreate`.
- For dynamic pages, use `misc` to describe route params, endpoints, and query semantics.
- Use `data` only when the current `DataRepository` can actually resolve the dataset you want.

## Data Repository Limits

`DataRepository` currently supports these filter concepts well:

- `limit`
- `offset`
- `unpaginated`
- `orderByColumn`
- `orderByOrder`
- `relation`
- `facet`

It does not currently provide a fully generic pass-through for every repository query-string feature, so do not assume every frontend listing can be expressed purely through section `data`.

## Aspire-Specific Public Pages

There are two categories of pages:

- already CMS-driven pages like `home`
- hard-coded Svelte pages that now have seeded templates for migration, such as `blog`, `contact-us`, `study/exams`, and `study/notes`

For migration work:

1. lock the section names and `misc` contract in seeded templates
2. build the matching Svelte components
3. switch the route to the shared page renderer last
