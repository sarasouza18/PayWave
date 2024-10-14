# PayWave

**PayWave** is a RESTful API designed to process payments, offering complete payment handling functionality with support for multiple payment gateways. It integrates **Stripe** and **PayPal** to provide a robust multi-gateway solution, ensuring reliability through a fallback mechanism and optimized performance via **Redis caching** and event-driven architecture. The system is containerized with **Docker** and orchestrated using **Kubernetes** for scalability and resilience under heavy load.

## 🚀 Project Objectives

- **Complete Payment Handling**: Full support for processing, updating, and managing payments through multiple payment gateways.
- **Multi-Gateway Support**: Integration with **Stripe** and **PayPal**, ensuring flexibility and reliability in payment processing.
- **Scalability and Resilience**: Built using containerized architecture (**Docker** and **Kubernetes**) to guarantee scalability and high availability, even under heavy load.
- **Optimized Performance**: Utilizes **Redis** to cache frequent payment data, reducing latency and improving system responsiveness.
- **Fallback Mechanism**: A robust fallback system ensures that if one payment gateway fails, the transaction is automatically retried with another gateway.

---

## 🛠️ Technologies Used

### 1. **Backend Framework**
- **PHP (Symfony Framework)**: The main platform for implementing the API, ensuring flexibility and ease of integration with multiple services.

### 2. **Payment Gateways**
- **Stripe** and **PayPal**: Integrated as the primary payment gateways for processing transactions.
  
### 3. **Cache**
- **Redis**: In-memory caching system used to reduce load on payment gateways, improving response times and overall performance.
  
### 4. **Messaging**
- **RabbitMQ**: Message broker used to implement event-driven architecture, ensuring asynchronous and resilient payment processing.

### 5. **Containerization and Orchestration**
- **Docker**: Containerizes the application, ensuring consistency across development, testing, and production environments.
- **Kubernetes**: Manages container orchestration, scaling, and high availability of the payment API.

### 6. **Authentication**
- **JWT (JSON Web Tokens)**: Used for user authentication and authorization to secure sensitive operations like payment modifications.

---

## 📊 Architecture Overview

PayWave's architecture is designed with scalability and fault tolerance in mind, ensuring that payments are processed efficiently across multiple gateways. The system integrates a fallback mechanism to automatically retry failed transactions using alternate gateways.

### Main Components

1. **Payment Management**: Handles the processing, updating, and management of payments.
2. **Gateway Integration**: Interfaces with **Stripe** and **PayPal** to process transactions.
3. **Retry and Fallback Logic**: Implements automatic retries and gateway fallback in case of failures to ensure payment continuity.

---

## 🔄 Operation Flow

### 1. **Process a Payment**

1. The client sends a `POST /payments` request with payment details.
2. The request is received by the `PaymentController`, triggering the `ProcessPayment` use case.
3. The payment is processed by the primary gateway (**Stripe** or **PayPal**).
4. If the payment fails, the fallback logic switches to the alternate gateway for processing.
5. **Redis** updates the cache with the payment status to optimize subsequent queries.

### 2. **Get Payment Status**

1. The client sends a `GET /payments/:id` request to check the payment status.
2. The `PaymentController` retrieves the payment status from the **Redis** cache.
3. If the data is not cached, the status is fetched from the database and then cached for future requests.

### 3. **Update a Payment**

1. The client sends a `PUT /payments/:id` request to update payment information.
2. The `PaymentController` triggers the `UpdatePayment` use case to modify the payment details.
3. The updated information is sent to the selected gateway, and the **Redis** cache is invalidated.

### 4. **Handle Payment Failures**

1. If a payment fails, the system initiates a retry mechanism with exponential backoff.
2. After several retries, the system falls back to the alternate payment gateway.
3. If all retries fail, the payment is marked as failed, and the status is updated in both the cache and the database.

---

## ⚙️ Configuration and Orchestration

### 1. **Docker**

PayWave is containerized using **Docker**, facilitating easy development, deployment, and scaling across environments. **Docker Compose** is used for local development, orchestrating the API and its dependencies like Redis and RabbitMQ.

### 2. **Kubernetes**

In production, **Kubernetes** manages PayWave’s API, allowing for automatic scaling and ensuring high availability of the services. Kubernetes configurations include:

- **Deployment**: Defines the number of API replicas and their distribution within the cluster.
- **Service**: Provides a stable interface to access the API containers.
- **Ingress**: Manages external access to the API and routes requests to the appropriate services.

---

## 📝 How to Run the Project

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/paywave.git
cd paywave
```

### 2. Set up Environment Variables

Create a `.env` file based on the provided **`env.example`** file:

```bash
cp .env.example .env
```

Edit the `.env` file and add your own configurations for **Redis**, **RabbitMQ**, and **Payment Gateway** credentials:

```bash
# Example .env configuration
REDIS_ADDRESS=your_localhost
RABBITMQ_URL=amqp:your_localhost
STRIPE_API_KEY=your-stripe-api-key
PAYPAL_CLIENT_ID=your-paypal-client-id
PAYPAL_CLIENT_SECRET=your-paypal-client-secret
```

### 3. Build and Run the Application

Install dependencies:

```bash
composer install
```

Start the services (Redis, RabbitMQ, etc.) using Docker Compose:

```bash
docker-compose up --build
```

Run the application:

```bash
php bin/console server:run
```
