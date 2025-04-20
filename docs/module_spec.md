# 📦 Module Specification

This document outlines the specifications of individual modules for the Uniform Shop Management System. Each module handles a specific functionality within the shopping and order process.

---

### M1.1.1 – Add Product
- **Description**: Adds a new product to the catalog.
- **Inputs**: name, category, price, stock quantity, description, thumbnail URL
- **Outputs**: None
- **Query**:
```sql
INSERT INTO products (name, category, price, stock, description, thumbnail)
VALUES (...);
```

---

### M1.1.2 – Edit Product
- **Description**: Modifies existing product information.
- **Inputs**: product_id, name, category, price, stock, description, thumbnail
- **Outputs**: None
- **Query**:
```sql
UPDATE products SET ... WHERE product_id = ...;
```

---

### M1.1.3 – Delete Product
- **Description**: Removes a product from the catalog.
- **Inputs**: product_id
- **Outputs**: None
- **Query**:
```sql
DELETE FROM products WHERE product_id = ...;
```

---

### M1.2.1 – Create Order
- **Description**: Submits a new order.
- **Inputs**: user_id, product_id, quantity, size (optional), order date, total price
- **Outputs**: None
- **Query**:
```sql
INSERT INTO orders (user_id, product_id, quantity, size, order_date, total_price)
VALUES (...);
```

---

### M1.2.2 – Check Order Status
- **Description**: Views order status by user.
- **Inputs**: user_id
- **Outputs**: Order information
- **Query**:
```sql
SELECT * FROM orders WHERE user_id = ...;
```

---

### M1.3.1 – Enter Shipping Information
- **Description**: Adds delivery details for an order.
- **Inputs**: order_id, receiver name, phone, address, address detail, delivery date, status
- **Outputs**: None
- **Query**:
```sql
INSERT INTO shipping (...)
VALUES (...);
```

---

### M1.4.1 – Check Inventory
- **Description**: Checks available stock for a product.
- **Inputs**: product_id
- **Outputs**: stock amount
- **Query**:
```sql
SELECT stock FROM products WHERE product_id = ...;
```

---

### M1.4.2 – Update Inventory
- **Description**: Updates stock quantity for a product.
- **Inputs**: product_id, new stock
- **Outputs**: None
- **Query**:
```sql
UPDATE products SET stock = ... WHERE product_id = ...;
```

---

### M1.5.1 – Register User
- **Description**: Creates a new user account.
- **Inputs**: username, email, password
- **Outputs**: None
- **Query**:
```sql
INSERT INTO users (username, email, password)
VALUES (...);
```

---

### M1.5.2 – Login
- **Description**: Authenticates a user.
- **Inputs**: email, password
- **Outputs**: Login status
- **Query**:
```sql
SELECT * FROM users WHERE email = ... AND password = ...;
```

---

### M1.5.3 – Logout
- **Description**: Logs a user out.
- **Inputs**: None
- **Outputs**: None
- **Action**: End session, clear authentication

---

### M1.6.1 – View Profile
- **Description**: Retrieves user profile.
- **Inputs**: user_id
- **Outputs**: username, email
- **Query**:
```sql
SELECT username, email FROM users WHERE user_id = ...;
```

---

### M1.6.2 – Edit Profile
- **Description**: Updates user information.
- **Inputs**: user_id, email, password (optional)
- **Outputs**: None
- **Query**:
```sql
UPDATE users SET email = ..., password = ... WHERE user_id = ...;
```

