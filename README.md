# AI-Powered Form Builder

A Laravel + Livewire based form builder that allows users to create structured forms using a JSON schema, publish forms through public URLs, collect submissions, and import form structures from Word and Excel documents.

> **Assignment Status:** Core Form Builder and Word/Excel Import are implemented. AI Form Generation is planned but not included in the current submission.

---

## Live Demo

**Live URL:** `https://ai-form-builder-n23l.onrender.com/`

**Demo Email:** `demo@example.com`

**Demo Password:** `password`

> Do not commit real API keys or sensitive credentials to the repository.

---

# Features

## Core Form Builder

The application provides a visual form builder based on a JSON schema.

Supported field types include:

* Text
* Textarea
* Number
* Email
* Phone
* Date
* Dropdown
* Radio
* Checkbox
* File Upload
* Rating
* Section Heading

The builder supports:

* Add fields
* Add sections
* Drag and drop field reordering
* Edit sections inline
* Edit field configuration
* Duplicate/edit/delete field operations
* Required fields
* Placeholders
* Help text
* Default values
* Options for dropdown/radio/checkbox fields
* Field validation configuration
* File type and file size configuration
* Undo/redo
* Autosave
* Live preview

---

# JSON Schema

The JSON schema is the central representation of a form.

A form follows this general structure:

```json
{
  "version": "1.0",
  "title": "Job Application Form",
  "description": "Application form",
  "settings": {
    "submit_button": "Submit"
  },
  "sections": [
    {
      "id": "uuid",
      "title": "Personal Information",
      "fields": [
        {
          "id": "uuid",
          "type": "text",
          "key": "full_name",
          "label": "Full Name",
          "placeholder": "Enter your full name",
          "help_text": null,
          "default": null,
          "required": true,
          "validation": {
            "min": 2,
            "max": 50
          }
        }
      ]
    }
  ]
}
```

The schema is validated before it is persisted.

The public form also derives server-side validation rules from the same schema instead of trusting browser-side validation.

---

# Public Forms

Published forms receive a unique public slug/URL.

A public form:

1. Loads the stored schema.
2. Renders fields dynamically.
3. Builds server-side validation rules from the schema.
4. Handles file uploads.
5. Stores the submission.
6. Displays a successful submission state.

Submission records include:

* Form ID
* Submitted form data
* IP address
* User agent
* Submission timestamp

---

# Autosave

Existing forms support periodic autosave.

The builder checks for dirty changes and automatically persists the validated form schema approximately every 15 seconds.

The UI displays:

* `Autosave pending...`
* `All changes saved`

This reduces the risk of losing changes while editing a form.

---

# Undo / Redo

The builder maintains a history of editor states and provides undo/redo functionality.

The history stores the form title, description, status and sections.

The history is intentionally bounded to prevent unlimited memory growth in the browser/session state.

---

# Word & Excel Import

The application supports importing:

* `.docx`
* `.xlsx`

The import flow is:

```text
Upload
   ↓
Store Import Record
   ↓
Parse Document
   ↓
Detect Fields
   ↓
Preview / Mapping
   ↓
User Reviews
   ↓
Create Form
```

## Word Import

The Word parser currently detects:

* Text questions
* Email fields
* Number fields
* Date fields
* Textarea-like fields
* File fields
* Radio fields
* Dropdown fields
* Checkbox fields
* Explicit field type markers
* Basic required markers
* Choice/options

Example supported notation:

```text
Full Name [text] [required]
Email Address [email] [required]
Gender [radio] [required]: Male / Female / Other
Country [dropdown]: India / USA / UK
Skills [checkbox]: PHP / Laravel / WordPress
Resume [file] [required]
```

## Excel Import

Excel supports a documented header-row based import layout.

The first row can represent form fields, for example:

```text
Full Name | Email Address | Mobile Number | Date of Birth | Address
```

Field types can be inferred from field names and explicit metadata where supported.

---

# Import Preview

Imported data is not immediately committed as a form.

The application first creates an import record and generates a parsed schema.

The user then reviews the detected fields and can change:

* Label
* Field type
* Required state
* Key

before creating the final form.

This follows the assignment requirement of providing a review/mapping stage before committing imported forms.

---

# Import Architecture

The import implementation uses a deterministic parser first.

### Word

`PhpOffice\PhpWord`

### Excel

`PhpOffice\PhpSpreadsheet`

The parser converts external document structures into the application's internal JSON schema.

The intention is to keep document parsing deterministic and use AI later only where document structure is ambiguous.

---

# Database Architecture

The application uses MySQL.

Main entities include:

```text
users
  |
  +---- forms
          |
          +---- submissions
          |
          +---- imports
```

## Forms

A form stores:

* user ownership
* title
* slug
* description
* status
* JSON schema
* timestamps

The JSON schema is stored as the form's canonical structure.

## Submissions

A submission belongs to a form and stores dynamic response data as JSON together with request metadata.

## Imports

An import belongs to a user and can optionally belong to a form.

It stores:

* original file name
* storage path
* file type
* processing status
* parsed data
* error message

---

# Indexing Strategy

Indexes are added around frequently queried ownership/status/time fields.

The project uses relational foreign keys for:

* user → forms
* form → submissions
* user → imports
* form → imports

The application keeps dynamic form fields inside JSON rather than creating database columns for every user-defined field.

This avoids schema migrations every time a user creates or modifies a form.

---

# Technology Stack

* PHP 8.2+
* Laravel 11
* Livewire 3
* Blade
* Tailwind CSS
* MySQL
* Composer
* PHPWord
* PhpSpreadsheet
* Laravel queues/database infrastructure
* Git

---

# Local Setup

## Requirements

* PHP 8.2+
* Composer
* MySQL 8
* Node.js / npm
* Git

## Installation

Clone the repository:

```bash
git clone <YOUR_GITHUB_REPOSITORY_URL>

cd ai-form-builder
```

Install PHP dependencies:

```bash
composer install
```

Install frontend dependencies:

```bash
npm install
```

Create environment file:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Configure MySQL credentials in `.env`.

Run migrations:

```bash
php artisan migrate
```

If seeders are available:

```bash
php artisan db:seed
```

Create the storage link:

```bash
php artisan storage:link
```

Build frontend assets:

```bash
npm run build
```

Start the application:

```bash
php artisan serve
```

---

# Environment Variables

At minimum configure:

```env
APP_NAME="AI Form Builder"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ai_form_builder
DB_USERNAME=root
DB_PASSWORD=
```

Do not commit `.env`.

Use `.env.example` for shareable configuration.

---

# Queue

The application is configured to support Laravel queues.

The current project uses the database queue driver during development.

Run a worker with:

```bash
php artisan queue:work
```

AI generation is not yet connected to the queue in the current submission.

---

# AI Form Generation

## Current Status

**Not implemented in this submission.**

The planned architecture is:

```text
User Prompt
    ↓
Queued AI Generation Job
    ↓
LLM Provider
    ↓
Structured JSON Schema
    ↓
Schema Validation
    ↓
Repair / Retry
    ↓
Preview
    ↓
Existing Form Builder
```

The implementation will also support editing an existing schema with natural-language instructions such as:

```text
Add an emergency contact section.

Make phone number required.

Translate labels to Hindi.
```

Only schema-validated output should be persisted.

The planned implementation will log:

* Model
* Input tokens
* Output tokens
* Total tokens
* Latency
* Generation status
* Errors/retry information

---

# AI Prompt Strategy — Planned

The planned system prompt will instruct the model to:

1. Return JSON only.
2. Follow the application's schema contract.
3. Use only supported field types.
4. Generate sensible labels and keys.
5. Generate placeholders where useful.
6. Generate options for choice-based fields.
7. Generate validation rules where appropriate.
8. Never invent unsupported field types.
9. Preserve existing schema structure when editing an existing form.

Malformed responses will be passed through schema validation.

Invalid responses will be repaired or retried rather than persisted directly.

---

# API Endpoints

The current submission does not expose a separate public REST API.

The application currently uses Laravel/Livewire server interactions for its web interface.

A REST API can be added later for:

* Form creation
* Form retrieval
* Submission creation
* Submission retrieval
* Webhooks/integrations

This was intentionally kept outside the current implementation scope.

---

# Known Limitations

### AI

AI Form Generation and AI editing are not implemented yet.

### Word Import

Word documents with highly complex formatting, nested structures, shapes, advanced tables or unusual Word elements may not be interpreted perfectly.

The parser is designed around predictable text/question patterns and provides a preview step so detected fields can be corrected.

### Large Imports

Large document processing is not yet moved to a dedicated background import queue.

### API

A public REST API is not currently exposed.

### Part D

The current submission contains editor improvements such as autosave and undo/redo, but does not yet contain three fully independent Part-D differentiators.

---

# Sample Files

Sample import files are included in:

```text
samples/
```

The samples are used to verify Word and Excel import behavior.

---

# Project Structure

```text
app/
├── Livewire/
│   ├── Forms/
│   └── Imports/
│
├── Models/
│
├── Services/
│   └── Import/
│
├── Policies/
└── ...

database/
├── migrations/
├── seeders/
└── ...

resources/
├── views/
│   └── livewire/
│
└── ...

samples/
├── *.docx
└── *.xlsx
```

---

# Security Considerations

* Form ownership is authorization checked before modification/deletion.
* Public forms are only accessible when published.
* Submission validation is performed server-side.
* Uploaded files are processed through Livewire's file upload mechanism.
* API keys/secrets should be stored in `.env`.
* User-owned forms should never be accessed solely by trusting client-provided IDs.

---

# Development Roadmap

## Completed

* Manual form builder
* Dynamic JSON schema
* Field configuration
* Sections
* Public forms
* Server-side schema-driven validation
* Submission storage
* Autosave
* Undo/redo
* Word import
* Excel import
* Import preview/mapping
* Sample import documents

## Next

1. AI Form Generation
2. AI editing of existing forms
3. AI schema validation/repair/retry
4. Queued AI generation
5. AI usage/latency logging
6. Large-file import queues
7. Improved Word structural/heading detection
8. Public REST API
9. Additional Part-D differentiators
10. Automated test coverage

---

# Assignment Scope

This submission intentionally prioritizes the working Core Form Builder and Word/Excel import pipeline.

The AI portion is documented as the next implementation phase rather than shipping a partially working AI feature.

The assignment explicitly prioritizes A → B → C → D and allows incomplete work to be stated clearly when time is limited.

---

# License

This project was created as part of a technical assignment.
