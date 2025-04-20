# 🧮 Relational Schema

This document describes the structure of each table in the Uniform Shop Management System.

---

### 📄 `users`
| Column     | Description         | Type       | Constraints            |
|------------|---------------------|------------|------------------------|
| user_id    | User ID (PK)        | INT        | PRIMARY KEY, AUTO_INCREMENT, NOT NULL |
| username   | User name           | VARCHAR(50)| NOT NULL               |
| email      | Email address       | VARCHAR(100)| NOT NULL              |
| password   | Hashed password     | VARCHAR(255)| NOT NULL             |
| role       | User role (e.g., admin) | VARCHAR(20) |                      |

---

### 📄 `uniforms` (products)
| Column       | Description        | Type         | Constraints                          |
|--------------|--------------------|--------------|--------------------------------------|
| uniform_id   | Product ID (PK)    | INT          | PRIMARY KEY, AUTO_INCREMENT, NOT NULL |
| name         | Product name       | VARCHAR(100) | NOT NULL                             |
| price        | Product price      | INT          | NOT NULL                             |
| thumbnail    | Image URL          | VARCHAR(255) |                                      |
| description  | Product description| TEXT         |                                      |
| stock        | Available stock    | INT          | NOT NULL                             |
| category_id  | FK to categories   | INT          | FOREIGN KEY (category_id) REFERENCES categories(category_id) |

---

### 📄 `categories`
| Column       | Description        | Type          | Constraints                           |
|--------------|--------------------|---------------|---------------------------------------|
| category_id  | Category ID (PK)   | INT           | PRIMARY KEY, AUTO_INCREMENT, NOT NULL  |
| category_name| Category name      | VARCHAR(100)  | NOT NULL                              |

---

### 📄 `orders`
| Column       | Description         | Type         | Constraints                          |
|--------------|---------------------|--------------|--------------------------------------|
| order_id     | Order ID (PK)       | INT          | PRIMARY KEY, AUTO_INCREMENT, NOT NULL |
| user_id      | FK to users         | INT          | FOREIGN KEY (user_id) REFERENCES users(user_id) |
| uniform_id   | FK to products      | INT          | FOREIGN KEY (uniform_id) REFERENCES uniforms(uniform_id) |
| size         | Product size        | VARCHAR(10)  |                                      |
| quantity     | Quantity ordered    | INT          | NOT NULL                             |
| order_date   | Date of order       | DATETIME     | NOT NULL                             |
| total_price  | Total order price   | INT          | NOT NULL                             |

---

### 📄 `shipping`
| Column       | Description         | Type         | Constraints                          |
|--------------|---------------------|--------------|--------------------------------------|
| shipping_id  | Shipping ID (PK)    | INT          | PRIMARY KEY, AUTO_INCREMENT, NOT NULL |
| order_id     | FK to orders        | INT          | FOREIGN KEY (order_id) REFERENCES orders(order_id) |
| receiver_name| Recipient name      | VARCHAR(100) | NOT NULL                             |
| phone        | Contact number      | VARCHAR(20)  |                                      |
| address      | Shipping address    | VARCHAR(255) | NOT NULL                             |
| address_detail| Extra details      | VARCHAR(255) |                                      |
| shipping_date| Delivery date       | DATETIME     |                                      |
| status       | Delivery status     | VARCHAR(50)  |                                      |
