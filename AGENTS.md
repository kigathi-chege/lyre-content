# `lyre/content` Agent Guide

## Package Purpose
`lyre/content` provides a CMS-style composition model for pages and sections (nested sections, texts, buttons, data bindings, files, menu structures, interactions, articles) with API and Filament management.

## What Belongs In This Package
- Content composition entities (`Page`, `Section`, `PageSection`, `SectionSection`, `SectionText`, `SectionButton`, `Text`, `Button`, `Data`, `Menu`, `MenuItem`, `Article`, `Interaction*`).
- Content API endpoints and resources.
- Data resolution logic for dynamic section payloads (`DataRepository`).
- Content Filament plugin/resources.

## What Does Not Belong Here
- App/frontend-specific rendering implementation details.
- Generic media and taxonomy primitives already owned by `lyre/file` and `lyre/facet`.

## Public API / Stable Contracts
- Routes in `src/routes/api.php`, especially `api/pages`, `api/sections`, `api/menu`.
- Section resource payload shape (`texts`, `buttons`, `files`, nested `sections`, `data` map behavior).
- `Data.filters` behavior consumed by `DataRepository::build()` (e.g., relation, facet, order, limit).
- Filament plugin ID `lyre.content` and composed plugin registration behavior.

## Internal Areas That May Change
- Implementation internals of `DataRepository` caching/build logic, while preserving output contract.
- Internal Filament form/table layouts that do not alter data semantics.

## Usage Rules
- Use this package as source-of-truth for page/section/menu composition.
- Keep section `name`/`misc`/nested structure stable for consumers that map by name.
- Use `lyre/file` relationships for file/media, not duplicate media models here.
- Use `lyre/facet` for taxonomy-based dynamic content filters.

## Extension Rules
- Add new section behavior by extending existing composition model before introducing one-off schema.
- When adding new `Data.filters` keys, keep old keys backward compatible and document query behavior.
- If renaming/removing API fields used by frontends, coordinate and version carefully.
- Keep plugin composition (`file` + `facet`) intact unless dependency strategy is intentionally redesigned.

## Testing Requirements
- Validate `api/pages`, `api/sections`, and `api/menu` payload shapes after changes.
- Validate `DataRepository` dynamic filters resolve correctly for relation/facet/order/limit cases.
- Validate Filament resources still load through `LyreContentFilamentPlugin`.

## Docs To Update When This Package Changes
- Root [AGENTS.md](/Users/chegekigathi/Projects/packages/lyre-packages/AGENTS.md)
- [docs/architecture.md](/Users/chegekigathi/Projects/packages/lyre-packages/docs/architecture.md)
- `packages/content/README.md`
- Any integration notes that rely on section data contract
