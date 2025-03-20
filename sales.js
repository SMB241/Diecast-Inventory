const apiSellUrl = 'sell.php';
    const apiGetSoldUrl = 'get_sold.php';

    let totalSales = 0;

    document.addEventListener('DOMContentLoaded', () => {
      loadSoldItems(); // Loads sold items on page refresh
    });

    function loadSoldItems() {
      fetch(apiGetSoldUrl)
        .then(response => response.json())
        .then(result => {
          if (result.success && result.soldItems) {
            totalSales = 0; // Reset total on reload
            result.soldItems.forEach(item => {
              addSoldItemToTable(item);
              updateTotalSales(item.price);
            });
          } else {
            document.getElementById('message').textContent = 'No sold items found.';
          }
        })
        .catch(error => {
          console.error('Error fetching sold items:', error);
          document.getElementById('message').textContent = 'Error fetching sold items.';
        });
    }

    function sellProduct() {
      const productId = document.getElementById('id').value;
      const messageDiv = document.getElementById('message');

      if (!productId) {
        messageDiv.textContent = 'Please enter a Product ID.';
        return;
      }

      const formData = new FormData();
      formData.append('id', productId);

      fetch(apiSellUrl, {
        method: 'POST',
        body: formData
      })
        .then(response => response.json())
        .then(result => {
          messageDiv.textContent = result.message;

          if (result.success && result.product) {
            addSoldItemToTable(result.product);
            updateTotalSales(result.product.price);
          }

          document.getElementById('id').value = '';
        })
        .catch(error => {
          console.error('Error:', error);
          messageDiv.textContent = 'Error processing sale.';
        });
    }

    function addSoldItemToTable(product) {
      const tableBody = document.querySelector('#productTable tbody');

      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${product.id}</td>
        <td>${product.brand}</td>
        <td>${product.model}</td>
        <td>₱${parseFloat(product.price).toFixed(2)}</td>
      `;

      tableBody.appendChild(row);
    }

    function updateTotalSales(price) {
      totalSales += parseFloat(price);
      const totalSalesDiv = document.getElementById('totalSales');
      totalSalesDiv.textContent = `Total Sales: ₱${totalSales.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
    }