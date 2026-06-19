# 📡 HK CRM - API Documentation

> **Base URL:** `http://localhost:8000/api`
> **Authentication:** Firebase ID Token (Bearer Token)

---

## 🔐 Authentication

All API endpoints require a valid Firebase ID Token sent via the `Authorization` header:

```
Authorization: Bearer <firebase_id_token>
```

Get the Firebase ID token from the client-side Firebase SDK after Google Sign-In:

```javascript
const user = firebase.auth().currentUser;
const token = await user.getIdToken();
```

---

## 📊 Dashboard

### GET `/api/dashboard/stats`

Get dashboard statistics for the authenticated user.

**Response (200):**
```json
{
    "total_leads": 25,
    "today_follow_ups": 3,
    "pending_follow_ups": 8,
    "status_counts": {
        "new": 5,
        "contacted": 8,
        "interested": 4,
        "follow_up": 3,
        "won": 3,
        "lost": 2
    }
}
```

---

## 👥 Leads

### GET `/api/leads`

List all leads for the authenticated user with optional search & filters.

**Query Parameters:**

| Parameter | Type   | Required | Description                     |
|-----------|--------|----------|---------------------------------|
| search    | string | No       | Search by name, email, or mobile |
| status    | string | No       | Filter by status                |
| source    | string | No       | Filter by source                |
| page      | int    | No       | Pagination page number          |

**Response (200):**
```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "name": "John Doe",
            "mobile_number": "9876543210",
            "email": "john@example.com",
            "source": "Website",
            "status": "new",
            "created_at": "2026-06-19T10:00:00.000000Z",
            "updated_at": "2026-06-19T10:00:00.000000Z",
            "follow_ups_count": 2
        }
    ],
    "per_page": 15,
    "total": 1
}
```

---

### POST `/api/leads`

Create a new lead.

**Request Body:**
```json
{
    "name": "John Doe",
    "mobile_number": "9876543210",
    "email": "john@example.com",
    "source": "Website",
    "status": "new"
}
```

**Validation Rules:**

| Field         | Rules                                                    |
|---------------|----------------------------------------------------------|
| name          | required, string, max:255                                |
| mobile_number | required, string, max:20                                 |
| email         | nullable, email, max:255                                 |
| source        | nullable, string, max:255                                |
| status        | required, in: new, contacted, interested, follow_up, won, lost |

**Response (201):**
```json
{
    "success": true,
    "lead": {
        "id": 1,
        "name": "John Doe",
        "mobile_number": "9876543210",
        "email": "john@example.com",
        "source": "Website",
        "status": "new",
        "user_id": 1,
        "created_at": "2026-06-19T10:00:00.000000Z",
        "updated_at": "2026-06-19T10:00:00.000000Z"
    }
}
```

---

### GET `/api/leads/{id}`

Get details of a specific lead (including follow-ups).

**Response (200):**
```json
{
    "id": 1,
    "name": "John Doe",
    "mobile_number": "9876543210",
    "email": "john@example.com",
    "source": "Website",
    "status": "new",
    "follow_ups": [
        {
            "id": 1,
            "follow_up_date": "2026-06-20",
            "follow_up_time": "10:30",
            "notes": "Call about pricing",
            "status": "pending"
        }
    ]
}
```

---

### PUT `/api/leads/{id}`

Update an existing lead.

**Request Body:** Same as POST (all fields optional with `sometimes` validation).

**Response (200):**
```json
{
    "success": true,
    "lead": { ... }
}
```

---

### DELETE `/api/leads/{id}`

Soft-delete a lead.

**Response (200):**
```json
{
    "success": true,
    "message": "Lead deleted successfully"
}
```

---

## 📅 Follow-ups

### GET `/api/leads/{lead_id}/follow-ups`

List all follow-ups for a specific lead.

**Response (200):**
```json
[
    {
        "id": 1,
        "lead_id": 1,
        "follow_up_date": "2026-06-20",
        "follow_up_time": "10:30",
        "notes": "Discuss pricing",
        "status": "pending",
        "created_at": "2026-06-19T10:00:00.000000Z"
    }
]
```

---

### POST `/api/leads/{lead_id}/follow-ups`

Create a new follow-up for a lead.

**Request Body:**
```json
{
    "follow_up_date": "2026-06-20",
    "follow_up_time": "10:30",
    "notes": "Call about pricing",
    "status": "pending"
}
```

**Validation Rules:**

| Field           | Rules                       |
|-----------------|-----------------------------|
| follow_up_date  | required, date              |
| follow_up_time  | nullable, format: H:i       |
| notes           | nullable, string, max:1000  |
| status          | in: pending, completed      |

**Response (201):**
```json
{
    "success": true,
    "follow_up": { ... }
}
```

---

### PUT `/api/follow-ups/{id}`

Update a follow-up (e.g., mark as completed).

**Request Body:**
```json
{
    "status": "completed"
}
```

**Response (200):**
```json
{
    "success": true,
    "follow_up": { ... }
}
```

---

### DELETE `/api/follow-ups/{id}`

Delete a follow-up.

**Response (200):**
```json
{
    "success": true,
    "message": "Follow-up deleted"
}
```

---

## ❌ Error Responses

### 401 Unauthorized
```json
{
    "success": false,
    "message": "Authorization token required"
}
```

### 403 Forbidden
```json
{
    "message": "This action is unauthorized."
}
```

### 422 Validation Error
```json
{
    "message": "The name field is required.",
    "errors": {
        "name": ["The name field is required."]
    }
}
```

### 404 Not Found
```json
{
    "message": "No query results for model [App\\Models\\Lead] 999"
}
```

---

## 📝 Status Enums

### Lead Statuses
| Value       | Description                      |
|-------------|----------------------------------|
| new         | New lead, not yet contacted      |
| contacted   | Initial contact made             |
| interested  | Lead showed interest             |
| follow_up   | Needs follow-up                  |
| won         | Successfully converted           |
| lost        | Lead was lost                    |

### Follow-up Statuses
| Value     | Description                        |
|-----------|------------------------------------|
| pending   | Follow-up not yet completed        |
| completed | Follow-up has been completed       |
