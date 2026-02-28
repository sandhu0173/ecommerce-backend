# Product API Documentation

## Base URL

```
http://localhost:8000/api
```

## Authentication

Protected endpoints (Create, Update, Delete) require JWT authentication:

```
Authorization: Bearer {token}
```

## Endpoints

### 1. List Products

**GET** `/products`

Get all active products with filtering, sorting, and pagination.

#### Query Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | integer | 1 | Page number for pagination |
| `limit` | integer | 20 | Items per page (max 100) |
| `search` | string | - | Search by name, description, or SKU |
| `sort` | string | created_at | Sort field: name, price, rating, review_count, created_at |
| `order` | string | desc | Sort order: asc or desc |
| `featured` | boolean | - | Filter featured products only |
| `status` | string | active | Filter by status: active, inactive, archived |
| `min_price` | decimal | - | Minimum price filter |
| `max_price` | decimal | - | Maximum price filter |
| `in_stock` | boolean | - | Show only in-stock products |

#### Response

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Product Name",
      "slug": "product-name",
      "description": "Product description...",
      "price": "99.99",
      "cost_price": "50.00",
      "stock": 100,
      "sku": "SKU-001",
      "image_url": "https://example.com/image.jpg",
      "images": ["url1", "url2"],
      "status": "active",
      "is_featured": true,
      "rating": 4,
      "review_count": 25,
      "meta_description": "SEO description",
      "meta_keywords": ["keyword1", "keyword2"],
      "created_at": "2026-02-27T15:24:08.000000Z",
      "updated_at": "2026-02-27T15:24:08.000000Z"
    }
  ],
  "pagination": {
    "total": 23,
    "count": 20,
    "per_page": 20,
    "current_page": 1,
    "last_page": 2,
    "from": 1,
    "to": 20
  }
}
```

#### Example Requests

```bash
# Get active products with pagination
curl "http://localhost:8000/api/products?limit=10&page=1"

# Search products
curl "http://localhost:8000/api/products?search=laptop"

# Filter by price range
curl "http://localhost:8000/api/products?min_price=100&max_price=500"

# Get featured products sorted by rating
curl "http://localhost:8000/api/products?featured=true&sort=rating&order=desc"

# Get in-stock products only
curl "http://localhost:8000/api/products?in_stock=true"
```

---

### 2. Get Featured Products

**GET** `/products/featured`

Get featured active products.

#### Query Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `limit` | integer | 10 | Number of products (max 50) |

#### Response

```json
{
  "success": true,
  "data": [
    {
      "id": 2,
      "name": "Featured Product",
      "is_featured": true,
      ...
    }
  ]
}
```

#### Example Request

```bash
curl "http://localhost:8000/api/products/featured?limit=5"
```

---

### 3. Search Products

**GET** `/products/search`

Search products by name, description, or SKU.

#### Query Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `q` | string | Yes | Search query (min 2 characters) |

#### Response

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Matching Product",
      ...
    }
  ]
}
```

#### Example Request

```bash
curl "http://localhost:8000/api/products/search?q=laptop"
```

---

### 4. Get Product Details

**GET** `/products/{product}`

Get detailed information about a specific product using slug or ID.

#### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `product` | string/integer | Product slug or ID |

#### Response

```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Product Name",
    "slug": "product-name",
    "price": "99.99",
    "cost_price": "50.00",
    "stock": 100,
    "profit": "49.99",
    "profit_margin": "50.01",
    "is_available": true,
    "is_low_stock": false,
    "sku": "SKU-001",
    "rating": 4,
    "review_count": 25,
    "created_at": "2026-02-27T15:24:08.000000Z",
    "updated_at": "2026-02-27T15:24:08.000000Z"
  }
}
```

#### Example Requests

```bash
# Get by slug
curl "http://localhost:8000/api/products/product-name"

# Get by ID
curl "http://localhost:8000/api/products/1"
```

---

### 5. Create Product (Admin)

**POST** `/products`

Create a new product.

**Requires authentication**

#### Request Body

```json
{
  "name": "New Product",
  "description": "Product description...",
  "price": 99.99,
  "cost_price": 50.00,
  "stock": 100,
  "sku": "SKU-NEW-001",
  "image_url": "https://example.com/image.jpg",
  "images": ["url1", "url2"],
  "status": "active",
  "is_featured": false,
  "meta_description": "SEO description",
  "meta_keywords": ["keyword1", "keyword2"]
}
```

#### Validation Rules

| Field | Rules |
|-------|-------|
| `name` | required, string, max:255 |
| `description` | nullable, string |
| `price` | required, numeric, min:0.01 |
| `cost_price` | nullable, numeric, min:0 |
| `stock` | required, integer, min:0 |
| `sku` | required, string, unique |
| `image_url` | nullable, url |
| `images` | nullable, array |
| `status` | required, in:active,inactive,archived |
| `is_featured` | nullable, boolean |
| `meta_description` | nullable, string, max:255 |
| `meta_keywords` | nullable, array |

#### Response

```json
{
  "success": true,
  "message": "Product created successfully",
  "data": {
    "id": 25,
    "name": "New Product",
    "slug": "new-product",
    "price": "99.99",
    ...
  }
}
```

Status: `201 Created`

#### Example Request

```bash
curl -X POST "http://localhost:8000/api/products" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "New Laptop",
    "price": 999.99,
    "cost_price": 500.00,
    "stock": 50,
    "sku": "SKU-LAPTOP-001",
    "status": "active"
  }'
```

---

### 6. Update Product (Admin)

**PUT** `/products/{product}`

Update an existing product.

**Requires authentication**

#### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `product` | string/integer | Product slug or ID |

#### Request Body

All fields are optional. Only include fields you want to update.

```json
{
  "name": "Updated Product Name",
  "price": 129.99,
  "stock": 75,
  "is_featured": true
}
```

#### Response

```json
{
  "success": true,
  "message": "Product updated successfully",
  "data": {
    "id": 1,
    "name": "Updated Product Name",
    ...
  }
}
```

#### Example Request

```bash
curl -X PUT "http://localhost:8000/api/products/1" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "price": 129.99,
    "stock": 75
  }'
```

---

### 7. Delete Product (Admin)

**DELETE** `/products/{product}`

Delete a product.

**Requires authentication**

#### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `product` | string/integer | Product slug or ID |

#### Response

```json
{
  "success": true,
  "message": "Product deleted successfully"
}
```

Status: `200 OK`

#### Example Request

```bash
curl -X DELETE "http://localhost:8000/api/products/1" \
  -H "Authorization: Bearer {token}"
```

---

## Filter Examples

### Get Active Products Under $100

```bash
curl "http://localhost:8000/api/products?max_price=100&status=active"
```

### Get Featured In-Stock Products

```bash
curl "http://localhost:8000/api/products?featured=true&in_stock=true"
```

### Search and Sort by Price

```bash
curl "http://localhost:8000/api/products?search=laptop&sort=price&order=asc"
```

### Get High-Rated Products

```bash
curl "http://localhost:8000/api/products?sort=rating&order=desc&limit=10"
```

### Get Products with Low Stock

```bash
curl "http://localhost:8000/api/products?in_stock=true&sort=stock&order=asc&limit=20"
```

---

## Error Responses

### 400 Bad Request

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": ["The name field is required."],
    "price": ["The price must be at least 0.01."]
  }
}
```

### 401 Unauthorized

```json
{
  "message": "Unauthenticated."
}
```

### 404 Not Found

```json
{
  "message": "Not found."
}
```

### 422 Unprocessable Entity

```json
{
  "message": "The SKU has already been taken.",
  "errors": {
    "sku": ["The SKU has already been taken."]
  }
}
```

---

## Pagination

All list endpoints support pagination:

```json
{
  "pagination": {
    "total": 50,           // Total items in database
    "count": 20,           // Items in this response
    "per_page": 20,        // Items per page
    "current_page": 1,     // Current page number
    "last_page": 3,        // Last page number
    "from": 1,             // First item number
    "to": 20               // Last item number
  }
}
```

Navigate to next page:
```bash
curl "http://localhost:8000/api/products?page=2"
```

---

## Product Fields Reference

### Status Values
- `active` - Product is active and visible
- `inactive` - Product is hidden but not deleted
- `archived` - Product is archived

### Computed Attributes

The show endpoint includes computed fields:
- `profit` - Price minus cost_price
- `profit_margin` - Profit percentage (0-100)
- `is_available` - true if in stock and active
- `is_low_stock` - true if stock < 10 and > 0

---

## Rate Limiting

API requests are currently unlimited. Implement rate limiting as needed for production.

---

## CORS

Frontend is allowed from: `http://localhost:3000`

Configure in `config/cors.php` for production domains.
