FormPilot System Architecture & Capabilities
This document explains the core mechanics of the FormPilot platform, answering how forms are connected, how data is tracked (even with complex forms), how the AI engine works, and how a Form Builder could be integrated in the future.

1. How Users Connect Their Forms
FormPilot is designed to be frontend-agnostic, meaning it works with forms built in plain HTML, WordPress, React, Webflow, or any other platform. Users have three ways to connect their existing forms to the platform:

A. The Universal JavaScript Snippet (Easiest)
Users simply paste a <script> tag into the <head> of their website.

How it works: The script (
formpilot.js
) automatically finds all <form> tags on the page. It attaches an event listener that intercepts the 
submit
 event. Instead of the form redirecting the page, the script packages the form data into a JSON payload and sends it silently via AJAX to the FormPilot API.
Benefit: Requires zero coding knowledge. The user doesn't have to change their action or method attributes.
B. Direct POST Endpoint (For Developers)
Users can change their HTML form's action attribute to point directly to FormPilot.

Example: <form action="https://api.formpilot.com/api/f/YOUR_API_KEY" method="POST">
How it works: When a visitor clicks submit, the browser sends the standard application/x-www-form-urlencoded or multipart/form-data request directly to the FormPilot API.
Benefit: Works without JavaScript, meaning it functions perfectly for users with JS disabled or strict ad-blockers.
C. Webhooks (For Integrations)
Users can connect form builders (like Typeform, Gravity Forms, or Jotform) via webhooks.

How it works: The external service sends a JSON payload to https://api.formpilot.com/api/webhook/YOUR_API_KEY every time a submission occurs.
2. Tracking Complex Forms (Long Fields & Unknown Data)
If a user has a form with 50 fields, how does FormPilot handle it without knowing what those fields are in advance?

Field Normalization Pipeline
When a submission arrives, it passes through the 
FieldNormalizationService
. This service uses a sophisticated mapping dictionary to figure out what the fields mean.

Core Fields Matching: It looks for variations of common names.
Email: "email", "email_address", "contact_email", "e-mail"
Name: "name", "full_name", "first_name" (if first and last are separate, it concatenates them into full_name)
Phone: "phone", "mobile", "tel", "contact_number"
Company: "company", "org", "business_name"
The "Raw Data" Column (JSON): Any field that does not match a core field (e.g., "What is your favorite color?", "How many employees do you have?", "T-shirt size") is not lost. It is saved in a PostgreSQL JSONB column called raw_data.
Result: The database remains clean with strict columns for core identifying info (Name, Email, Phone), while maintaining infinite flexibility to store hundreds of custom fields in the JSON payload without requiring schema migrations.
Tracking Metadata
Alongside the form input, FormPilot automatically captures:

Source: IP Address, User Agent (Browser/OS).
Location: Page URL where the form was submitted.
Marketing (UTMs): The JS snippet automatically reads the URL parameters (?utm_source=google&utm_medium=cpc) and attaches them to the payload.
3. How the AI Classification Works
The AI engine (
AiClassificationService
) is designed to instantly analyze incoming leads and tag them, saving the sales team from reading through spam or unqualified leads.

The Flow
Ingestion: A form is submitted and saved to the database.
Queueing: A background job (
ClassifySubmissionJob
) is dispatched to Redis so the user's browser isn't waiting for the AI to finish.
Analysis (Rule-Based MVP vs. LLM):
Current MVP (Rule-Based): The system scans the custom fields and the message body for keywords. If it sees "WordPress", it tags the Service Type as "Web Design". If it sees "ASAP" or "urgent", the Urgency is tagged as "High". It calculates a Lead Temperature score based on the budget size and provided contact info (e.g., providing a phone number adds points).
Production (OpenAI/Anthropic): In a full production environment, the entire JSON payload is sent to an LLM (like GPT-4o-mini) with a strict JSON schema prompt. The LLM reads the context and returns standardized tags.
Outputs Generated:
Lead Temperature: Hot, Warm, Cold
Service Type: Development, SEO, Marketing, etc.
Sentiment: Positive (e.g., "Love your work!"), Negative (e.g., "Cancel my account").
Summary: A 1-2 sentence TL;DR of what the lead wants.
Smart Routing: Once classified, the 
RoutingService
 kicks in. Example: If the AI tagged the lead as "High Budget" and "SEO", it automatically assigns the lead to the Senior SEO Sales Rep based on pre-defined Routing Rules.
4. Can You Add a Form Builder?
Yes. While FormPilot's current unique value proposition is bringing your own frontend, adding a built-in Form Builder is a natural expansion path.

How a Form Builder Fits into the Architecture
If you want to add a Form Builder, the architecture is already perfectly positioned to support it. Here is how it would work:

Database Storage (forms table):
You would create a forms table containing workspace_id, brand_id, and a schema column (JSON).
The schema JSON would store the form layout: e.g., [{"type": "text", "name": "first_name", "label": "First Name"}, {"type": "dropdown", "options": ["Red", "Blue"]}].
The Builder UI (Frontend):
You would build a drag-and-drop interface in Laravel/Blade (or Vue/React) where users can piece together a form.
Rendering the Form:
Hosted Pages: FormPilot generates a standalone link (e.g., forms.formpilot.com/f/12345) that renders the form from the JSON schema.
Embed Codes: The user gets an <iframe> snippet to embed the generated form on their site.
Ingestion Compatibility:
Because your backend is already built to accept any payload via the API, the Form Builder simply submits data to the exact same /api/f/{apiKey} endpoint that the Universal JS Snippet uses.
The 
FieldNormalizationService
 treats Form Builder submissions the exact same way it treats external HTML forms.
Recommendation
Start by perfecting the "Bring Your Own Form" approach, as this solves a massive pain point for agencies who hate redesigning forms to fit backend CRMs. Once that flow is profitable, introduce the Form Builder as an upsell feature, turning FormPilot into an all-in-one ecosystem.

