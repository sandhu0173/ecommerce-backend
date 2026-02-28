# Authentication API Documentation

## Overview

This API uses **Laravel Sanctum** for token-based authentication. After logging in or registering, you receive an API token that must be included in subsequent requests to access protected endpoints.

## Base URL

```
http://localhost:8000/api
```

## Authentication Header

Protected endpoints require the `Authorization` header with a Bearer token:

```
Authorization: Bearer {token}
```

## Endpoints

### 1. Register User

**POST** `/auth/register`

Create a new user account and receive an API token.

#### Request Body

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

#### Validation Rules

| Field | Rules |
|-------|-------|
| `name` | required, string, max:255 |
| `email` | required, email, unique |
| `password` | required, string, min:8, confirmed |

#### Response

```json
{
  "success": true,
  "message": "User registered successfully",
  "user": {
    "id": 2,
    "name": "John Doe",
    "email": "john@example.com",
    "email_verified_at": null,
    "created_at": "2026-02-27T16:30:00.000000Z",
    "updated_at": "2026-02-27T16:30:00.000000Z"
  },
  "token": "1|NpXaqM5y4DJT0i9OCkZvEo4mLxAB2cDeFgHiJkL"
}
```

Status: `201 Created`

#### Example Request

```bash
curl -X POST "http://localhost:8000/api/auth/register" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

---

### 2. Login User

**POST** `/auth/login`

Login with email and password to receive an API token.

#### Request Body

```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

#### Validation Rules

| Field | Rules |
|-------|-------|
| `email` | required, email |
| `password` | required, string |

#### Response

```json
{
  "success": true,
  "message": "Logged in successfully",
  "user": {
    "id": 2,
    "name": "John Doe",
    "email": "john@example.com",
    "email_verified_at": null,
    "created_at": "2026-02-27T16:30:00.000000Z",
    "updated_at": "2026-02-27T16:30:00.000000Z"
  },
  "token": "2|zVx0w6KMEvbxv9ZvEoW8LmNoPqRsTuVwXyZaBcD"
}
```

Status: `200 OK`

#### Example Request

```bash
curl -X POST "http://localhost:8000/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

---

### 3. Get User Profile

**GET** `/auth/profile`

Get the authenticated user's profile information.

**Requires authentication**

#### Response

```json
{
  "success": true,
  "data": {
    "id": 2,
    "name": "John Doe",
    "email": "john@example.com",
    "email_verified_at": null,
    "created_at": "2026-02-27T16:30:00.000000Z",
    "updated_at": "2026-02-27T16:30:00.000000Z"
  }
}
```

Status: `200 OK`

#### Example Request

```bash
curl -X GET "http://localhost:8000/api/auth/profile" \
  -H "Authorization: Bearer {token}"
```

---

### 4. Logout User

**POST** `/auth/logout`

Logout the user and revoke all their API tokens.

**Requires authentication**

#### Response

```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

Status: `200 OK`

#### Example Request

```bash
curl -X POST "http://localhost:8000/api/auth/logout" \
  -H "Authorization: Bearer {token}"
```

---

### 5. Revoke Specific Token

**DELETE** `/auth/tokens/{token}`

Revoke a specific API token.

**Requires authentication**

#### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `token` | string | The token ID to revoke |

#### Response

```json
{
  "success": true,
  "message": "Token revoked successfully"
}
```

Status: `200 OK`

#### Example Request

```bash
curl -X DELETE "http://localhost:8000/api/auth/tokens/3" \
  -H "Authorization: Bearer {token}"
```

---

## Complete Authentication Flow

### 1. Register New User

```bash
# Step 1: Register
curl -X POST "http://localhost:8000/api/auth/register" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Jane Smith",
    "email": "jane@example.com",
    "password": "securepass123",
    "password_confirmation": "securepass123"
  }'

# Response includes token: "1|abc123xyz..."
```

### 2. Use Token in Subsequent Requests

```bash
# Step 2: Access protected endpoint with token
curl -X GET "http://localhost:8000/api/auth/profile" \
  -H "Authorization: Bearer 1|abc123xyz..."

# Step 3: Create a product (if admin)
curl -X POST "http://localhost:8000/api/products" \
  -H "Authorization: Bearer 1|abc123xyz..." \
  -H "Content-Type: application/json" \
  -d '{
    "name": "New Product",
    "price": 99.99,
    "cost_price": 50.00,
    "stock": 100,
    "sku": "NEW-001",
    "status": "active"
  }'
```

### 3. Logout When Done

```bash
# Step 4: Logout (revoke all tokens)
curl -X POST "http://localhost:8000/api/auth/logout" \
  -H "Authorization: Bearer 1|abc123xyz..."
```

---

## Token Management

### Token Format

Sanctum tokens follow this format:
```
{id}|{hash}
```

Example:
```
1|NpXaqM5y4DJT0i9OCkZvEo4mLxAB2cDeFgHiJkL
```

### Token Lifetime

By default, tokens do **not expire** (set in `config/sanctum.php`).

To set expiration, edit `config/sanctum.php`:
```php
'expiration' => 60, // 60 minutes
```

### Revoking Tokens

Tokens can be revoked individually or in bulk:

```bash
# Logout (revoke all tokens for user)
curl -X POST "http://localhost:8000/api/auth/logout" \
  -H "Authorization: Bearer {token}"

# Revoke specific token
curl -X DELETE "http://localhost:8000/api/auth/tokens/{token_id}" \
  -H "Authorization: Bearer {token}"
```

---

## Error Responses

### 422 Unprocessable Entity - Invalid Credentials

```json
{
  "message": "The provided credentials are invalid.",
  "errors": {
    "email": ["The provided credentials are invalid."]
  }
}
```

### 422 Unprocessable Entity - Validation Failed

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### 401 Unauthorized

```json
{
  "message": "Unauthenticated."
}
```

---

## Frontend Integration

### Store Token (JavaScript/Next.js)

```javascript
// After login/register
const response = await fetch('http://localhost:8000/api/auth/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    email: 'user@example.com',
    password: 'password123'
  })
});

const data = await response.json();
const token = data.token;

// Store in localStorage
localStorage.setItem('auth_token', token);
```

### Use Token in Requests

```javascript
// Fetch product details with authentication
const token = localStorage.getItem('auth_token');

const response = await fetch('http://localhost:8000/api/products/1', {
  headers: {
    'Authorization': `Bearer ${token}`
  }
});
```

### Clear Token on Logout

```javascript
// Call logout endpoint
const token = localStorage.getItem('auth_token');

await fetch('http://localhost:8000/api/auth/logout', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`
  }
});

// Remove from storage
localStorage.removeItem('auth_token');
```

---

## Using with Axios (Frontend)

```javascript
// Configure Axios with token
const token = localStorage.getItem('auth_token');
const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Authorization': `Bearer ${token}`
  }
});

// Use in requests
api.post('/products', {
  name: 'New Product',
  price: 99.99
});
```

---

## Security Best Practices

1. **HTTPS Only** - Always use HTTPS in production
2. **Secure Storage** - Store tokens securely (localStorage or cookies)
3. **HttpOnly Cookies** - Consider using HttpOnly cookies for tokens
4. **Token Expiration** - Set token expiration in production
5. **Scope Limiting** - Create tokens with specific scopes if needed
6. **Regular Logout** - Encourage users to logout when done
7. **Token Rotation** - Regenerate tokens periodically

---

## CORS Configuration

Frontend domains are configured in `config/sanctum.php`:

```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
    '%s%s',
    'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
    Sanctum::currentApplicationUrlWithPort(),
))),
```

For production, update `.env`:

```env
SANCTUM_STATEFUL_DOMAINS=example.com,www.example.com
```

---

## Protected Endpoints

All product management endpoints (create, update, delete) require authentication:

```
POST   /api/products              # Create product
PUT    /api/products/{id}         # Update product
DELETE /api/products/{id}         # Delete product
```

Public endpoints (no auth required):

```
GET    /api/products              # List products
GET    /api/products/featured     # Get featured products
GET    /api/products/search       # Search products
GET    /api/products/{id}         # Get product details
```

---

## Testing Endpoints with cURL

```bash
# 1. Register
TOKEN=$(curl -s -X POST "http://localhost:8000/api/auth/register" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }' | jq -r '.token')

echo "Token: $TOKEN"

# 2. Get profile
curl -X GET "http://localhost:8000/api/auth/profile" \
  -H "Authorization: Bearer $TOKEN"

# 3. Create product
curl -X POST "http://localhost:8000/api/products" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Product",
    "price": 99.99,
    "cost_price": 50.00,
    "stock": 10,
    "sku": "TEST-001",
    "status": "active"
  }'

# 4. Logout
curl -X POST "http://localhost:8000/api/auth/logout" \
  -H "Authorization: Bearer $TOKEN"
```

---

## Troubleshooting

### "Unauthenticated" Error

- Token is missing or invalid
- Token has been revoked
- Token has expired (if expiration is set)
- Authorization header format is incorrect

### "Invalid Credentials" Error

- Email or password is incorrect
- User account doesn't exist
- Check email/password spelling

### Token Not Working

- Ensure token is included in `Authorization: Bearer {token}` header
- Check that token hasn't been revoked
- Verify CORS is properly configured
- Check that request includes `Content-Type: application/json` header
