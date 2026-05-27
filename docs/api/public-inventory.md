# Public Inventory API

Base URL:

```txt
https://your-domain.com/api

GET /public/listings?lang=es
GET /public/listings?lang=en
GET /public/listings/{slug}?lang=es
search
property_type
listing_type
min_price
max_price
bedrooms
bathrooms
location
per_page
```
En `.env.example` agrega:

```env
FRONTEND_URL=https://example.com
PUBLIC_API_CACHE_MINUTES=10
