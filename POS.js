let cart = [];
let total = 0;

window.onload = () => {
  loadInventory();
};

// Load items from api.php?action=list
async function loadInventory() {
  try {
    const response = await fetch('api.php?action=list');
    const items = await response.json();

    const inventoryTable = document.querySelector('#inventoryTable tbody');
    inventoryTable.innerHTML = '';

    items.forEach(item => {
      if (item.status !== 'sold') { // Show only available items
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${item.id}</td>
          <td>${item.brand}</td>
          <td>${item.model}</td>
          <td>₱${parseFloat(item.price).toFixed(2)}</td>
          <td><button onclick="addToCart(${item.id}, '${item.brand}', '${item.model}', ${item.price})">Add</button></td>
        `;
        inventoryTable.appendChild(row);
      }
    });
  } catch (error) {
    console.error("Error loading inventory:", error);
  }
}

function addToCart(id, brand, model, price) {
  // Check if item already in cart
  const existing = cart.find(item => item.id === id);
  if (existing) {
    alert('Item already in cart!');
    return;
  }

  cart.push({
    id: id,
    brand: brand,
    model: model,
    price: price
  });

  total += parseFloat(price);
  updateCartTable();
  updateTotalDisplay();
}

function updateCartTable() {
  const tbody = document.querySelector('#cartTable tbody');
  tbody.innerHTML = '';

  cart.forEach(item => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${item.id}</td>
      <td>${item.brand} ${item.model}</td>
      <td>₱${parseFloat(item.price).toFixed(2)}</td>
    `;
    tbody.appendChild(row);
  });
}

function updateTotalDisplay() {
  document.getElementById('total').textContent = `₱${total.toFixed(2)}`;
}

async function processPayment() {
  if (cart.length === 0) {
    alert("Cart is empty!");
    return;
  }

  const cashTendered = parseFloat(document.getElementById('cashTendered').value);

  if (isNaN(cashTendered)) {
    alert("Please enter cash tendered.");
    return;
  }

  if (cashTendered < total) {
    alert("Not enough cash!");
    return;
  }

  const change = cashTendered - total;
  document.getElementById('changeOutput').textContent = `Change: ₱${change.toFixed(2)}`;

  // Get all item IDs to mark as sold
  const itemIds = cart.map(item => item.id);

  try {
    const response = await fetch('api.php?action=mark_sold', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ ids: itemIds })
    });

    const result = await response.json();

    if (result.success) {
      // Move items to sold list
      const soldList = document.getElementById('soldItemsList');
      cart.forEach(item => {
        const li = document.createElement('li');
        li.textContent = `${item.brand} ${item.model} - SOLD @ ₱${parseFloat(item.price).toFixed(2)}`;
        soldList.appendChild(li);
      });

      // Reset cart & total
      cart = [];
      total = 0;
      updateCartTable();
      updateTotalDisplay();
      document.getElementById('cashTendered').value = '';

      // Reload inventory
      loadInventory();
    } else {
      alert("Failed to mark items as sold.");
    }

  } catch (error) {
    console.error("Error processing payment:", error);
  }
}
