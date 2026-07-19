# Navkwa Group Ltd.

Navkwa Group Ltd. builds intelligent software systems for businesses across Africa. The company focuses on practical, long-term technology: custom enterprise platforms, web applications, mobile apps, cloud infrastructure, AI automation, API integrations, and digital transformation support.

Navkwa works with organizations that need software built around their real operations, including construction, healthcare, education, logistics, retail, finance, government, and manufacturing. The goal is to help teams replace manual processes, connect fragmented tools, improve visibility, and scale with reliable systems.

## What The Website Includes

- A modern company homepage for Navkwa Group Ltd.
- A first-page carousel slider for hero images.
- A contact form for project inquiries.
- A live chat widget that stores conversations.
- A backend inbox for reviewing contact messages and chat transcripts.

## Carousel Images

Add hero carousel images to:

`public/assets/images/carousel`

Supported formats:

`jpg`, `jpeg`, `png`, `webp`, `gif`, `avif`

The carousel loads images from that folder automatically. File names are sorted naturally, so names like `01-office.jpg`, `02-dashboard.jpg`, and `03-team.jpg` will appear in that order.

After adding or replacing images, refresh the website preview.

## Local Preview

Install dependencies:

```bash
composer install
```

Run database migrations:

```bash
php artisan migrate
```

Start the Laravel server:

```bash
php artisan serve
```

Open the website:

`http://127.0.0.1:8000`

Open the backend inbox:

`http://127.0.0.1:8000/admin`
