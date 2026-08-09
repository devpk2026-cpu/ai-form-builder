# Engineering Decisions

## 1. Overview

This document explains the main architectural decisions made while building the AI-Powered Form Builder technical assignment.

The implementation prioritizes:

1. A reliable schema-driven form builder.
2. Server-side validation derived from the same schema.
3. A dynamic public form renderer.
4. Deterministic Word/Excel import.
5. Editor productivity features such as autosave and undo/redo.

AI generation is intentionally left as the next implementation phase rather than shipping an incomplete integration.

---

# 2. JSON Schema as the Single Source of Truth

## Decision

The form definition is stored as a JSON schema rather than creating database columns for every possible form field.

## Why

Form fields are user-defined and can change frequently.

For example, one form may contain:

```text
Full Name
Email
Resume
Skills
```

while another may contain:

```text
Company
Job Title
Salary
Joining Date
```

Creating database columns dynamically would make the system difficult to maintain.

The JSON schema allows the builder and public renderer to work with the same structure.

## Trade-off

JSON makes arbitrary form structures easy to support, but querying individual custom fields directly at the SQL level is more difficult.

For the current product, this is an acceptable trade-off because the form schema is the primary configuration rather than an analytics warehouse.

---

# 3. Schema-Driven Server Validation

## Decision

Validation rules are derived from the stored form schema on the server.

## Why

Browser validation cannot be trusted.

A malicious or modified request could bypass HTML validation.

The public form therefore reads the stored schema and constructs Laravel validation rules before saving a submission.

## Benefit

The builder configuration and actual submission validation remain synchronized.

## Trade-off

The validation mapper must be maintained whenever new field types or validation rules are introduced.

---

# 4. Deterministic Word/Excel Import

## Decision

The first import implementation is deterministic rather than AI-first.

## Why

Documents contain predictable structures that can be parsed reliably without an LLM.

Examples:

```text
Email Address [email] [required]

Gender [radio]: Male / Female / Other

Country [dropdown]: India / USA / UK

Skills [checkbox]: PHP / Laravel / WordPress
```

Explicit metadata is more predictable and cheaper to process than sending every document through an LLM.

## Trade-off

Highly complex or ambiguous Word documents may not be interpreted perfectly.

The preview/mapping step therefore acts as a correction layer.

## Future Improvement

AI can be introduced only for ambiguous blocks, for example:

```text
"Candidate should provide their previous experience"
```

where the deterministic parser cannot confidently determine whether the field should be text, textarea or another type.

---

# 5. Import Preview Before Commit

## Decision

Imported content is converted into an intermediate schema and shown to the user before creating the final form.

## Why

Automatic document parsing will never be perfectly reliable for every document format.

A preview allows the user to correct:

* Field type
* Label
* Required state
* Key
* Detected options

before the form becomes persistent business data.

## Trade-off

The workflow contains an additional review step, but it significantly reduces the risk of silently creating incorrect forms.

---

# 6. Autosave

## Decision

Existing forms use periodic autosave.

## Implementation

The builder marks itself dirty when editor state changes.

A periodic Livewire poll triggers autosave approximately every 15 seconds.

The schema is validated before persistence.

## Why

Form builders contain a large amount of editing activity. Losing changes because the user forgets to click Save is a poor user experience.

## Trade-off

Autosave creates additional database writes.

The current implementation only writes when the form is dirty, which limits unnecessary writes.

A production version could add debouncing, version snapshots or Redis-based draft state if required.

---

# 7. Undo / Redo

## Decision

The builder maintains an in-memory editor history.

## Why

Form editing involves frequent operations such as:

* Moving fields
* Changing labels
* Changing types
* Changing sections
* Deleting fields

Undo/redo allows users to recover from accidental changes.

## Trade-off

The current history is session/editor-state based rather than a permanent database version history.

A future version could implement full form versioning and rollback.

---

# 8. Livewire Instead of a Separate SPA

## Decision

Livewire + Blade was chosen for the current implementation.

## Why

The assignment explicitly supports Livewire, and the application is primarily a server-rendered CRUD/form-builder workflow.

Livewire provides interactive behavior without requiring a separate frontend application and API layer for every builder operation.

## Trade-off

A very large collaborative builder may eventually benefit from a dedicated frontend state-management layer.

For the current scope, Livewire keeps the architecture smaller and easier to maintain.

---

# 9. Authorization

## Decision

Form ownership is checked before modifying or deleting forms.

## Why

A form ID should never be treated as sufficient authorization.

The application uses the authenticated user's form relationship and authorization policies for protected operations.

## Future Improvement

The same authorization strategy should be extended consistently to all future APIs and integrations.

---

# 10. AI Architecture — Planned

AI generation was intentionally not implemented in the current submission.

The planned architecture is:

```text
Prompt
  ↓
Generation Job
  ↓
AI Provider
  ↓
JSON Response
  ↓
Schema Validator
  ↓
Repair / Retry
  ↓
Validated Schema
  ↓
Preview
  ↓
Builder
```

The AI service will not be allowed to directly persist arbitrary model output.

This is important because the model can return:

* malformed JSON
* missing properties
* unsupported field types
* invalid options
* incorrect validation structures

Only a validated schema should reach the database.

---

# 11. AI Editing

The planned AI layer will support two operations:

### Generate

```text
Create an internship application form with education,
skills and resume upload.
```

### Modify

```text
Add an emergency contact section.
```

```text
Make phone number required.
```

```text
Translate all labels to Hindi.
```

The second operation will receive both the existing schema and the user's instruction.

The goal is to modify the existing schema instead of generating an unrelated replacement form.

---

# 12. Queue Decision for AI

The assignment requires long-running AI generation not to block a normal web request.

The application already uses Laravel's queue infrastructure.

The planned implementation will dispatch an AI generation job and expose a visible generation status to the user.

This keeps the web request responsive and makes retry/failure handling easier.

---

# 13. Part D — Current Improvements

## Improvement 1: Autosave

### User problem

Users can lose form changes if they forget to manually save.

### Implementation

Dirty builder state is periodically saved after schema validation.

### Trade-off

Additional database writes are accepted in exchange for improved reliability.

### Future

Use more efficient debouncing or draft storage for high-scale deployments.

---

## Improvement 2: Undo / Redo

### User problem

Form builders involve many destructive or rearranging actions.

### Implementation

The editor maintains a bounded history of form states and supports undo/redo.

### Trade-off

The current history is not a permanent versioning system.

### Future

Implement persistent form versions and rollback.

---

## Part D Status

The assignment requires at least three independently meaningful improvements.

The current implementation clearly contains the two editor improvements above.

A third Part-D differentiator is **not being claimed as complete in this submission** rather than overstating a mandatory core feature as an original differentiator.

---

# 14. Trade-offs

Given the available implementation time, the project prioritizes working core functionality over breadth.

The main trade-offs are:

* AI generation deferred rather than partially implemented.
* Complex Word document interpretation is limited.
* Large imports are not yet queued.
* No public REST API in the current version.
* No persistent form versioning yet.
* No Redis/Horizon requirement was introduced unnecessarily.
* No separate Python AI service was introduced before the AI requirements were implemented.

---

# 15. What I Would Build With Two More Weeks

With additional development time, the next priorities would be:

### Week 1

1. AI generation service.
2. Schema validator/repair layer.
3. Queued AI generation.
4. AI editing of existing forms.
5. AI usage/token/latency logging.
6. Automated tests for schema generation.

### Week 2

1. Persistent form versioning.
2. Conditional logic.
3. Improved Word structural parsing.
4. Background processing for large imports.
5. Submission search/filter/export verification and test coverage.
6. Public REST API.
7. Rate limiting and spam protection.
8. CI pipeline.

---

# 16. General Principle

The main architectural principle used throughout the project is:

> Keep the form schema as the source of truth and make every consumer of the form—builder, public renderer, validation, import and future AI generation—produce or consume that same schema.

This reduces duplication and makes future AI integration safer because AI output can be validated against one established contract before it reaches the application database.
