# API Documentation

## Base URL
```
http://localhost:8000/api
```

## Authentication

All protected endpoints require a Bearer token in the Authorization header:
```
Authorization: Bearer {token}
```

## Endpoints

### Authentication

#### Register
```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password",
  "password_confirmation": "password",
  "mobile": "+63 912 345 6789",
  "address": "123 Main St",
  "user_type": "resident"
}
```

**Response:**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    ...
  },
  "token": "1|..."
}
```

#### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password"
}
```

#### Get Current User
```http
GET /api/user
Authorization: Bearer {token}
```

#### Update Profile
```http
PUT /api/user
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "John Updated",
  "mobile": "+63 912 345 6789"
}
```

### Service Types

#### List Service Types
```http
GET /api/service-types
```

**Response:**
```json
[
  {
    "id": 1,
    "name": "Garbage Pickup",
    "description": "Request for garbage collection",
    "department": "Sanitation",
    "icon": "🗑️",
    "is_active": true
  }
]
```

#### Get Service Type
```http
GET /api/service-types/{id}
```

#### Create Service Type (Admin Only)
```http
POST /api/service-types
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "name": "New Service",
  "description": "Description",
  "department": "Department Name",
  "icon": "🔧",
  "is_active": true
}
```

### Requests

#### List Requests
```http
GET /api/requests?status=in_progress&priority=high&service_type_id=1
Authorization: Bearer {token}
```

**Query Parameters:**
- `status`: created, assigned, in_progress, resolved, closed
- `priority`: low, medium, high, urgent
- `service_type_id`: Service type ID

#### Create Request
```http
POST /api/requests
Authorization: Bearer {token}
Content-Type: application/json

{
  "service_type_id": 1,
  "title": "Garbage not collected",
  "description": "Garbage has not been collected for 3 days",
  "address": "123 Main Street",
  "latitude": 14.5995,
  "longitude": 120.9842,
  "barangay": "Barangay 1",
  "city": "Manila",
  "priority": "high"
}
```

**Response:**
```json
{
  "id": 1,
  "user_id": 1,
  "service_type_id": 1,
  "title": "Garbage not collected",
  "status": "created",
  "location": {
    "address": "123 Main Street",
    "latitude": 14.5995,
    "longitude": 120.9842,
    "barangay": "Barangay 1",
    "city": "Manila"
  },
  "priority": "high",
  "created_at": "2024-01-01T00:00:00.000000Z"
}
```

#### Get Request Details
```http
GET /api/requests/{id}
Authorization: Bearer {token}
```

#### Update Request
```http
PUT /api/requests/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Updated title",
  "description": "Updated description"
}
```

#### Update Request Status (Staff/Admin Only)
```http
PUT /api/requests/{id}/status
Authorization: Bearer {staff_token}
Content-Type: application/json

{
  "status": "in_progress",
  "notes": "Work started"
}
```

#### Assign Request (Staff/Admin Only)
```http
POST /api/requests/{id}/assign
Authorization: Bearer {staff_token}
Content-Type: application/json

{
  "user_id": 2
}
```

### Attachments

#### Upload Attachment
```http
POST /api/requests/{requestId}/attachments
Authorization: Bearer {token}
Content-Type: multipart/form-data

file: [binary file]
```

**Response:**
```json
{
  "id": 1,
  "request_id": 1,
  "file_url": "/storage/attachments/1234567890_image.jpg",
  "file_type": "image",
  "file_name": "image.jpg",
  "file_size": 102400
}
```

#### Delete Attachment
```http
DELETE /api/attachments/{id}
Authorization: Bearer {token}
```

### Notifications

#### List Notifications
```http
GET /api/notifications?read=false
Authorization: Bearer {token}
```

#### Mark Notification as Read
```http
PUT /api/notifications/{id}/read
Authorization: Bearer {token}
```

#### Mark All Notifications as Read
```http
PUT /api/notifications/read-all
Authorization: Bearer {token}
```

### Feedback

#### Add Feedback
```http
POST /api/requests/{requestId}/feedback
Authorization: Bearer {token}
Content-Type: application/json

{
  "comment": "Great service!",
  "rating": 5
}
```

#### Get Request Feedback
```http
GET /api/requests/{requestId}/feedback
Authorization: Bearer {token}
```

### Admin Endpoints

#### Get Dashboard Stats
```http
GET /api/admin/stats
Authorization: Bearer {admin_token}
```

**Response:**
```json
{
  "total": 100,
  "by_status": {
    "created": 10,
    "assigned": 20,
    "in_progress": 30,
    "resolved": 30,
    "closed": 10
  },
  "by_priority": {
    "low": 20,
    "medium": 40,
    "high": 30,
    "urgent": 10
  },
  "by_service_type": {
    "Garbage Pickup": 50,
    "Streetlight Repair": 30,
    "Building Permit": 20
  }
}
```

#### List All Users (Admin Only)
```http
GET /api/admin/users?user_type=resident&verified=true&search=john
Authorization: Bearer {admin_token}
```

#### List All Requests (Admin Only)
```http
GET /api/admin/requests?status=created&service_type_id=1
Authorization: Bearer {admin_token}
```

#### Create Notification (Admin Only)
```http
POST /api/admin/notifications
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "title": "System Maintenance",
  "message": "System will be under maintenance tonight",
  "type": "alert",
  "target_audience": "all"
}
```

## Error Responses

All errors follow this format:

```json
{
  "message": "Error message",
  "errors": {
    "field": ["Validation error message"]
  }
}
```

**Status Codes:**
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

## Pagination

List endpoints support pagination:

```json
{
  "data": [...],
  "current_page": 1,
  "last_page": 5,
  "per_page": 15,
  "total": 75
}
```

