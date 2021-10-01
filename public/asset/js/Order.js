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
            subtotal: 0
        }
        let product_id = document.getElementById('product_id');
        let quantity = document.getElementById('quantity');
        let price = document.getElementById('price');
        if (product_id.options[product_id.selectedIndex].value && quantity.value > 0 && price.value > 0.0)
        {
            if (rowNoData) rowNoData.remove();
            product_id.classList.remove('is-invalid');
            quantity.classList.remove("is-invalid");
            price.classList.remove("is-invalid");

            item.id = product_id.options[product_id.selectedIndex].value;
            item.name = product_id.options[product_id.selectedIndex].text;
            item.quantity = quantity.value;
            item.price = price.value;
            item.subtotal = quantity.value * price.value;
            this.total += item.subtotal;
            let total_field = document.getElementById('total')
            total_field.value = this.total;

            this.items.push(item);

            let product_row = document.getElementById('product-row');
            let tds = product_row.content.querySelectorAll('td');
            tds[0].textContent = item.name;
            tds[1].textContent = item.quantity;
            tds[2].textContent = item.price;
            tds[3].textContent = item.subtotal;
            //Clone row
            var table_body = document.querySelector('#order-items');
            var row_clone = document.importNode(product_row.content, true);
            table_body.appendChild(row_clone);
        } else {
            if(!product_id.options[product_id.selectedIndex].value) product_id.classList.add("is-invalid");
            if(quantity.value <= 0) quantity.classList.add("is-invalid");
            if(!price.value) price.classList.add("is-invalid");
        }
    },
    updatePrice() {
        let product_id = document.getElementById('product_id');
        let price_field = document.getElementById('price');
        let selected_option = product_id.options[product_id.selectedIndex];
        price_field.value = selected_option.getAttribute('data-price');
    }
};
