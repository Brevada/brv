/* Create company category. */
INSERT INTO company_categories (id, Title) VALUES
(1, 'Restaurant'), (2, 'Retail');

INSERT INTO company_features (id, MaxTablets, MaxAccounts, MaxStores) VALUES
(1, 3, 4, 5);

INSERT INTO companies (
    `id`, `Name`, `Active`, `Website`, `PhoneNumber`, `ExpiryDate`,
    `CategoryID`, `FeaturesID`, `DateCreated`
) VALUES
(1, 'Demo Company', 1, 'www.example.com', '4161234567', DATE_ADD(NOW(), INTERVAL 30 DAY), 1, 1, NOW());
