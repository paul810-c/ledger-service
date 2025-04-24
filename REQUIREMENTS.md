Backend dev tech task
Recruitment Task: Multi-Currency Ledger Service
Objective:
Develop a basic yet robust multi-currency ledger service using Symfony. The service should be capable of handling up to 1,000 transactions
per minute, with functionality to manage multiple ledgers, process transactions (both debits and credits), and accurately report balances in
real time.
Core Requirements:
1. Service Setup:
   a. Use Symfony as the primary framework.
   b. Implement the application with PHP v8.3.
   c. Ensure the service is containerized using Docker for easy deployment and scalability.
2. API Endpoints:
   a. POST /ledgers: Create a new ledger with a unique identifier and initial currency setting.
   b. POST /transactions: Record a new transaction in the specified ledger. This endpoint should accept details like ledger ID, transaction
   type (debit/credit), amount, currency, and a unique transaction ID.
   c. GET /balances/{ledgerId}: Retrieve the current balance of a specified ledger. This should return all currency balances if multiple
   currencies are supported.
3. Data Handling:
   a. Design a schema that supports multi-currency transactions. Consider using a relational database like PostgreSQL for ACID
   compliance.
   b. Implement transactional integrity to ensure that all financial transactions are processed reliably.
4. Concurrency and Load Handling:
   a. Demonstrate the application’s ability to handle up to 1,000 transactions per minute.
   b. Include unit tests and integration tests to validate the business logic and API endpoints.
   c. Use appropriate logging and error-handling mechanisms to ensure service reliability and maintainability.
5. Documentation:
   a. Provide a README file with setup instructions, API usage examples, and a brief discussion of the architecture.
   b. Document the API endpoints using OpenAPI (Swagger) for easy testing and integration.

   Bonus challenges:
6. Include a dev research document which describes the solution and can be used by QA and product team.
7. Implement multi-currency support where transactions can be recorded in different currencies based on the ledger settings.
8. Add functionality to convert between currencies using a mock external currency conversion API.
9. Include rate limiting to prevent abuse of the service API.
10. Deploy the application on a cloud provider.
    Evaluation Criteria:
11. Code quality and organisation.
12. Adherence to modern best practices in API design and security.
13. Efficiency of database use and query optimization.
14. Clarity and usefulness of documentation.
15. Scalability and robustness of the implementation.