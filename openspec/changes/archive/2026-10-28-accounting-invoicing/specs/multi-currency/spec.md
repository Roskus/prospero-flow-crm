# Spec Delta

## Purpose

Lets a company sell products and take orders in several currencies, with each amount converted to the company currency through exchange rates whose direction is explicit and whose value is kept as it was when the order was taken.

## ADDED Requirements

### Requirement: Exchange rate direction
The exchange rate of a currency SHALL be the number of units of the company currency that one unit of that currency is worth. The rate of the company currency SHALL always be 1. The currency rates screen SHALL state this direction next to each rate (for example "1 USD = ___ ARS"). Converting an amount from currency X to the company currency SHALL multiply it by the rate of X; converting it from currency X to currency Y SHALL multiply it by the rate of X and divide it by the rate of Y.

#### Scenario: Rate shown with its direction
- **WHEN** a user of a company whose currency is ARS opens the currency rates screen
- **THEN** the USD row reads as "1 USD = ___ ARS"

#### Scenario: Converting to the company currency
- **WHEN** the rate of USD is 1200 in a company whose currency is ARS
- **THEN** 10 USD are 12000 ARS

#### Scenario: Converting between two foreign currencies
- **WHEN** the rate of USD is 1200 and the rate of EUR is 1300 in a company whose currency is ARS
- **THEN** 120 USD are 110.77 EUR

#### Scenario: Missing rate
- **WHEN** an amount must be converted from a currency that has no rate in the company
- **THEN** the conversion is rejected with a validation error asking to set the rate of that currency

### Requirement: Product currency
Every product SHALL have a currency. The product form SHALL offer a currency select that defaults to the company currency for new products. The product list and the product search used by the order form SHALL show each price with its currency. When the change is deployed, existing products SHALL get the currency of their company.

#### Scenario: New product defaults to the company currency
- **WHEN** a user of a company whose currency is ARS opens the new product form
- **THEN** the currency select shows ARS

#### Scenario: Product in another currency
- **WHEN** a user saves a product with price 50 and currency USD
- **THEN** the product price is 50 USD

#### Scenario: Existing products get the company currency
- **WHEN** the change is deployed and a company whose currency is ARS has products stored with the default EUR
- **THEN** those products have currency ARS

### Requirement: Order currency and exchange rate
Every order SHALL have one currency, chosen when the order is created or edited (web form and API) and defaulting to the company currency. Every order SHALL store the exchange rate of its currency to the company currency at the moment the currency was set; later changes of the company rates SHALL NOT change it. The exchange rate SHALL be 1 when the order currency is the company currency. When a product whose currency differs from the order currency is added to the order, its price SHALL be converted to the order currency with the current company rates. All the lines of an order SHALL be in the order currency. When the change is deployed, existing orders SHALL get the currency of their company and an exchange rate of 1.

#### Scenario: New order defaults to the company currency
- **WHEN** a user of a company whose currency is ARS creates an order without choosing a currency
- **THEN** the order currency is ARS and its exchange rate is 1

#### Scenario: Order in a foreign currency keeps its rate
- **WHEN** a user creates an order in USD while the USD rate is 1200
- **AND** the USD rate is later changed to 1300
- **THEN** the order exchange rate is still 1200

#### Scenario: Changing the order currency takes the current rate
- **WHEN** a user changes the currency of an order from ARS to USD while the USD rate is 1250
- **THEN** the order exchange rate is 1250

#### Scenario: Product price converted to the order currency
- **WHEN** a user adds a product priced 12000 ARS to an order in USD while the USD rate is 1200
- **THEN** the line price is 10 USD

#### Scenario: Order API with a currency
- **WHEN** an API client creates an order with currency USD
- **THEN** the response includes the currency USD and the exchange rate used

#### Scenario: Order in a currency without rate
- **WHEN** a user creates an order in a currency that has no rate in the company
- **THEN** the order is not saved and a validation error is shown on the currency

### Requirement: Order amount
The amount stored on an order SHALL be the total of its current lines (quantity × price minus discount), computed after the lines are saved.

#### Scenario: Amount after editing the lines
- **WHEN** a user edits an order and replaces its lines with one line of 2 × 100 without discount
- **THEN** the order amount is 200

### Requirement: Sales figures in the company currency
Dashboard and sales report figures that add up order amounts SHALL convert each order amount to the company currency with the exchange rate stored on the order before adding them, and SHALL be shown in the company currency.

#### Scenario: Sales of orders in two currencies
- **WHEN** a company whose currency is ARS has an order of 100000 ARS and an order of 100 USD taken with rate 1200
- **THEN** the sales total is 220000 ARS
