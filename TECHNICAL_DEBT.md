# Technical Debt

## Review Scope

This review covers the current Laravel implementation for authentication, roles, dashboard, properties, pipeline, documents/property links, team, marketing, users/client access, reports, and the polished client report.

Small refactors completed in this pass:

- Centralized repeated admin/staff Form Request authorization in `App\Http\Requests\Concerns\AuthorizesAdminStaff`.
- Added Eloquent `visibleToClient()` scopes to `Prospect`, `MarketingActivity`, and `PropertyLink`.
- Updated `Property` visible relationships to use those scopes.

## Improvements Future

- Introduce Policies for user/property/report access.
  - `ClientReportController` currently contains direct authorization logic for client property access.
  - `UserController` and `UserRequest` both carry staff/admin user-management rules.
  - A future `PropertyPolicy`, `ReportPolicy`, and `UserPolicy` would make access rules easier to audit.

- Extract report composition into a dedicated service.
  - `ClientReportController` currently prepares report metrics, eager loads relationships, calculates status counts, and computes timestamps.
  - A future `PropertyReportData` service/action could return a stable DTO/array for both `/client/properties/{property}` and `/reports/{property}`.
  - This should wait until the report stabilizes further to avoid premature abstraction.

- Extract repeated Blade UI primitives.
  - Badges, status pills, empty states, form field wrappers, table headers, and section cards are repeated across modules.
  - Good candidates:
    - `x-status-badge`
    - `x-empty-state`
    - `x-form.input`
    - `x-form.select`
    - `x-report.section`
    - `x-kpi-card`

- Add route grouping helpers or resource routes selectively.
  - Current routes are explicit and easy to read.
  - As modules grow, consider `Route::resource()` for conventional CRUD modules while keeping custom routes explicit.

- Introduce tenant/company readiness without implementing multi-company yet.
  - Future tables will likely need `company_id` or `organization_id`.
  - Avoid hard-coding RSFLA-specific assumptions into business logic; keep RSFLA branding in views/config until multi-company is prioritized.

## Debt Technical

- `resources/views/client/report.blade.php` is large.
  - It now acts as the core product surface and contains significant presentation logic.
  - This is acceptable for the current phase, but it should be split into partials or components before adding more report sections.

- Controller filtering logic is repeated.
  - `PipelineController`, `DocumentController`, `MarketingController`, `TeamController`, and `UserController` all implement inline search/filter query logic.
  - This can remain for now, but query scopes or small filter objects may help once filters become more complex.

- `UserRequest` contains authorization logic that belongs in a future policy.
  - It correctly enforces current staff/admin constraints.
  - Moving this into `UserPolicy` later would reduce duplicated mental models between request validation and controller authorization.

- `PipelineController::recordActivity()` is a private controller method.
  - This is fine for now.
  - If more modules create timeline entries, extract an action such as `RecordProspectActivity`.

- `PropertyController::show()` mixes page loading and metric calculation.
  - It is still manageable, but it has started to resemble a property workspace presenter.
  - Consider a `PropertyWorkspaceData` action if the page gains more tabs or operational widgets.

- `ReportController::show()` delegates to `ClientReportController::show()`.
  - This keeps one shared layout and avoids duplication.
  - A report data service would eventually be cleaner than controller-to-controller delegation.

- `resources/views/welcome.blade.php` remains in the project.
  - It is no longer routed from `/`.
  - It can be deleted in a cleanup pass if no tests or references use it.

## Possible Optimizations

- Replace some collection-based report metrics with database aggregates if report data grows.
  - The current report loads visible prospects and counts in memory.
  - This is fine for small property pipelines.
  - If a property can have hundreds or thousands of prospects, move summary counts to grouped SQL queries.

- Review eager loading around report timeline.
  - Current report eager loads activities with `prospect` and `teamMember`, and filters activities through visible prospects.
  - This avoids obvious N+1 issues for the current page.
  - Keep watching this as timeline types expand beyond prospect activities.

- Add indexes for common filters.
  - Useful future indexes:
    - `prospects(property_id, status)`
    - `prospects(property_id, visible_to_client)`
    - `marketing_activities(property_id, visible_to_client, activity_date)`
    - `property_links(property_id, is_visible_to_client)`
    - `users(role, is_active)`

- Consider pagination or limits on property detail sections.
  - Internal property detail currently loads recent marketing and activity with limits, but all prospects/links/team members for the property.
  - This is acceptable now; pagination or section-level search may be useful later.

- Add model factories for core entities.
  - Tests currently create many records manually.
  - Factories for `Property`, `Prospect`, `MarketingActivity`, `PropertyLink`, and `TeamMember` would reduce test noise.

- Consider route model binding consistency.
  - Properties use slug binding.
  - Other modules use IDs.
  - This is reasonable, but public/client-facing routes should continue to prefer slugs.

## Modules Recommended For Next Phase

- Settings foundation.
  - Basic company/profile settings, brand colors, report footer text, default contact info.
  - Keep it single-company for now but shape fields for future multi-company support.

- Report configuration.
  - Per-property controls for report title, visible sections, default summary notes, and ordering.
  - Avoid PDF generation until print layout is stable.

- Activity timeline unification.
  - Create a shared activity feed concept that can include prospect activities, marketing activities, document updates, and future notes.

- Property workspace polish.
  - Convert `/properties/{property}` sections into a more robust internal command center.
  - Add clearer cross-links to Pipeline, Marketing, Documents, Team, and Reports.

- Client access audit.
  - Add an internal view showing which clients can access each property and when access was last changed.

- SaaS readiness.
  - Plan `companies`/`organizations`, branding settings, and ownership boundaries before introducing billing or external clients.
