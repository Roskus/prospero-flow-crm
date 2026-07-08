/**
 * Order.js
 * @type {{addItem: Order.addItem, total: number, items: *[], updatePrice(): void, delete: Order.delete}}
 */
var Order =
{
    total : 0,
    items : [],
    addItem : function()
    {
        let rowNoData = document.getElementById("row-no-data");
        let item = {
            id : null,
            name: '',
            quantity: 0,
            price : 0,
            discount: 0,
            tax: 0,
            subtotal: 0
        }
        let product_id = document.getElementById('product_id');
        let product_name = document.getElementById('product_name');
        let quantity = document.getElementById('quantity');
        let price = document.getElementById('price');
        let discount = document.getElementById('discount');
        let product_tax = document.getElementById('product_tax');
        if (product_id.value && quantity.value > 0 && price.value > 0.0)
        {
            if (rowNoData) rowNoData.remove();
            product_id.classList.remove('is-invalid');
            quantity.classList.remove("is-invalid");
            price.classList.remove("is-invalid");

            item.id = product_id.value;
            item.name = product_name.value;
            item.quantity = quantity.value;
            item.price = price.value;
            item.discount = discount.value || 0;
            item.tax = Number.parseFloat(product_tax.value) || 0;
            item.subtotal = quantity.value * price.value;
            this.total += Number.parseFloat(item.subtotal);
            let total_field = document.getElementById('total')
            total_field.value = this.total;

            this.items.push(item);

            let product_row_template = document.getElementById('product-row');
            let product_row = product_row_template.cloneNode(true);
            let tds = product_row.content.querySelectorAll('td');

            let index = (this.items.length > 0) ? this.items.length -1 : 0;

            // tds[0] = Product column
            let inputProduct = document.createElement("INPUT");
            inputProduct.setAttribute("type", "hidden");
            inputProduct.setAttribute("name", "items["+index+"][product_id]");
            inputProduct.setAttribute("value", item.id);
            tds[0].appendChild(inputProduct);

            let inputName = document.createElement("INPUT");
            inputName.setAttribute("type", "text");
            inputName.setAttribute("name", "items["+index+"][name]");
            inputName.setAttribute("value", item.name);
            inputName.setAttribute("readonly", "readonly");
            tds[0].appendChild(inputName);

            // tds[1] = Price column
            let inputPrice = document.createElement("INPUT");
            inputPrice.setAttribute("type", "number");
            inputPrice.setAttribute("name", "items["+index+"][price]");
            inputPrice.setAttribute("value", item.price);
            inputPrice.setAttribute("readonly", "readonly");
            tds[1].appendChild(inputPrice);

            // tds[2] = Tax column
            let inputTax = document.createElement("INPUT");
            inputTax.setAttribute("type", "hidden");
            inputTax.setAttribute("name", "items["+index+"][tax]");
            inputTax.setAttribute("value", item.tax);
            let taxAmount = ((item.tax / 100) * item.subtotal).toFixed(2);
            tds[2].appendChild(inputTax);
            tds[2].appendChild(document.createTextNode(taxAmount + ' (' + item.tax + '%)'));

            // tds[3] = Quantity column
            let inputQty = document.createElement("INPUT");
            inputQty.setAttribute("type", "number");
            inputQty.setAttribute("name", "items["+index+"][quantity]");
            inputQty.setAttribute("value", item.quantity);
            inputQty.setAttribute("readonly", "readonly");
            tds[3].appendChild(inputQty);

            // tds[4] = Discount column
            let inputDiscount = document.createElement("INPUT");
            inputDiscount.setAttribute("type", "hidden");
            inputDiscount.setAttribute("name", "items["+index+"][discount]");
            inputDiscount.setAttribute("value", item.discount);
            tds[4].appendChild(inputDiscount);
            tds[4].appendChild(document.createTextNode(item.discount + '%'));

            // tds[5] = Subtotal column
            tds[5].classList.add('item-subtotal');
            tds[5].textContent = Number.parseFloat(item.subtotal).toFixed(2);

            let table_body = document.querySelector('#order-items');
            let row_clone = document.importNode(product_row.content, true);
            table_body.appendChild(row_clone);
        } else {
            if(!product_id.value) product_id.classList.add("is-invalid");
            if(quantity.value <= 0) quantity.classList.add("is-invalid");
            if(!price.value) price.classList.add("is-invalid");
        }
    },
    removeRow: function(button) {
        let row = button.closest('tr');
        row.remove();
        this.recalculateTotal();
        if (document.querySelectorAll('#order-items tr').length === 0) {
            let tbody = document.getElementById('order-items');
            let noDataRow = document.createElement('tr');
            noDataRow.id = 'row-no-data';
            noDataRow.innerHTML = '<td colspan="7">No items</td>';
            tbody.appendChild(noDataRow);
        }
    },
    recalculateRow: function(input) {
        let row = input.closest('tr');
        let price = Number.parseFloat(row.querySelector('[name*="[price]"]').value) || 0;
        let quantity = Number.parseFloat(row.querySelector('[name*="[quantity]"]').value) || 0;
        let discount = Number.parseFloat(row.querySelector('[name*="[discount]"]').value) || 0;
        let tax = Number.parseFloat(row.dataset.tax) || 0;
        let subtotal = price * quantity * (1 - discount / 100);
        let taxAmount = (tax / 100) * subtotal;
        let subtotalCell = row.querySelector('.item-subtotal');
        if (subtotalCell) subtotalCell.textContent = subtotal.toFixed(2);
        let taxDisplay = row.querySelector('.tax-display');
        if (taxDisplay) taxDisplay.textContent = taxAmount.toFixed(2) + ' (' + tax + '%)';
        this.recalculateTotal();
    },
    recalculateTotal: function() {
        let total = 0;
        document.querySelectorAll('#order-items .item-subtotal').forEach(function(cell) {
            total += Number.parseFloat(cell.textContent) || 0;
        });
        document.getElementById('total').value = total.toFixed(2);
    },
    updatePrice() {
        let product_id = document.getElementById('product_id');
        let price_field = document.getElementById('price');
        let selected_option = product_id.options[product_id.selectedIndex];
        price_field.value = selected_option.dataset.price;
    },
    delete: function (id, message)
    {
        let response = confirm(message);
        if(response) {
            window.location.href = window.location.protocol+'//'+window.location.host + '/order/delete/'+id;
        }
    }
};
