# annual-sales-report Specification

## Purpose
Shows how the sales of a year evolve month by month against the previous year, and which products, customers and sellers drive them, so the company can see trends that a single period comparison hides.

## Requirements

### Requirement: Annual sales figures
The annual sales report SHALL cover a calendar year (default the current year) of the active company. Sales SHALL be the confirmed and completed orders, dated by their order date and converted to the company currency with the rate stored on each order, the same rules as the sales report. For each month it SHALL show the sales and number of orders of the year and of the previous year, and the change in percentage when the previous month has sales. It SHALL show the year total, the previous year total up to the same month and day when the year is the current one, and their change.

#### Scenario: Monthly comparison
- **WHEN** a company sold 1,000 in March 2025 and 1,500 in March 2026
- **THEN** the March row of the 2026 report shows 1,500, 1,000 and +50.0%

#### Scenario: Orders that are not sales
- **WHEN** a pending order and a canceled order are dated in 2026
- **THEN** they are not counted in the 2026 figures

#### Scenario: Foreign currency order
- **WHEN** an order of 100 USD with rate 1,200 is confirmed in a company whose currency is ARS
- **THEN** it adds ARS 120,000.00 to its month

#### Scenario: Current year to date
- **WHEN** the report of the current year is opened on 15/10/2026
- **THEN** the year total is compared with the previous year from 01/01/2025 to 15/10/2025

#### Scenario: Other companies
- **WHEN** another company has sales in 2026
- **THEN** they are not included

### Requirement: Annual sales chart
The report SHALL show a bar chart with the monthly sales of the year and of the previous year side by side, with a legend and the amounts in the company currency. The same figures SHALL be available in a table below the chart, so the report stays readable without the chart.

#### Scenario: Chart and table
- **WHEN** a user opens the annual sales report
- **THEN** a chart with two series (the year and the previous year) and twelve months is shown
- **AND** a table with the same monthly figures is shown below it

### Requirement: Annual rankings
The report SHALL show the top 10 products by sales amount (with quantity sold), the top 10 customers by sales amount (with number of orders) and the sales of each seller in the year, ordered from highest to lowest, in the company currency. Products and customers that were deleted SHALL still appear with their name.

#### Scenario: Top products
- **WHEN** a company sold 12 products in 2026
- **THEN** the 10 with the highest sales amount are listed with their quantity

#### Scenario: Deleted product
- **WHEN** a product sold in 2026 was deleted later
- **THEN** it still appears in the ranking with its name

### Requirement: Annual sales permissions and export
The annual sales report SHALL require the sales report permission. Users who can only see their own sales in the sales report SHALL only see their own sales in the annual report. The report SHALL be exportable to PDF with the same figures and rankings as tables.

#### Scenario: Seller sees own sales
- **WHEN** a seller who cannot see all sellers opens the annual report
- **THEN** every figure only counts the seller's own orders and the sales by seller section shows only the seller

#### Scenario: PDF export
- **WHEN** a user exports the 2026 annual report to PDF
- **THEN** a PDF with the monthly table, totals and rankings of 2026 is downloaded
