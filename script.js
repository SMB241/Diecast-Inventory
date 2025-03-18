const apiUrl = 'api.php';

document.addEventListener('DOMContentLoaded', fetchProducts);

function fetchProducts() {
  fetch(`${apiUrl}?action=list`)
    .then(res => res.json())
    .then(data => displayProducts(data));
}

function displayProducts(products) {
  const tableBody = document.querySelector('#productTable tbody');
  tableBody.innerHTML = '';

  products.forEach(product => {
    const row = document.createElement('tr');

    row.innerHTML = `
      <td>${product.id}</td>
      <td>${product.brand}</td>
      <td>${product.model}</td>
      <td>${product.price}</td>
      <td>${product.status}</td>
      <td>
        <button class="edit-btn" onclick="openEditModal(${product.id}, '${product.brand}', '${product.model}', ${product.price}, '${product.status}')">Edit</button>
        <button class="delete-btn" onclick="deleteProduct(${product.id})">Delete</button>
      </td>
    `;

    tableBody.appendChild(row);
  });
}

function addProduct() {
  const brand = document.getElementById('brand').value;
  const model = document.getElementById('model').value;
  const price = document.getElementById('price').value;
  const status = document.getElementById('status').value;

  if (!brand || !model || !price || !status) {
    alert('All fields are required!');
    return;
  }

  const formData = new FormData();
  formData.append('brand', brand);
  formData.append('model', model);
  formData.append('price', price);
  formData.append('status', status);
  formData.append('action', 'add');

  fetch(apiUrl, { method: 'POST', body: formData })
    .then(() => {
      fetchProducts();
      document.getElementById('brand').value = '';
      document.getElementById('model').value = '';
      document.getElementById('price').value = '';
      document.getElementById('status').value = '';
    });
}

function deleteProduct(id) {
  if (!confirm('Are you sure you want to delete this product?')) return;

  const formData = new FormData();
  formData.append('id', id);
  formData.append('action', 'delete');

  fetch(apiUrl, { method: 'POST', body: formData })
    .then(() => fetchProducts());
}

function openEditModal(id, brand, model, price, status) {
  document.getElementById('editId').value = id;
  document.getElementById('editBrand').value = brand;
  document.getElementById('editModel').value = model;
  document.getElementById('editPrice').value = price;
  document.getElementById('editStatus').value = status;

  document.getElementById('editModal').style.display = 'block';
}

function closeModal() {
  document.getElementById('editModal').style.display = 'none';
}

function saveEdit() {
  const id = document.getElementById('editId').value;
  const brand = document.getElementById('editBrand').value;
  const model = document.getElementById('editModel').value;
  const price = document.getElementById('editPrice').value;
  const status = document.getElementById('editStatus').value;

  if (!brand || !model || !price || !status) {
    alert('All fields are required!');
    return;
  }

  const formData = new FormData();
  formData.append('id', id);
  formData.append('brand', brand);
  formData.append('model', model);
  formData.append('price', price);
  formData.append('status', status);
  formData.append('action', 'edit');

  fetch(apiUrl, { method: 'POST', body: formData })
    .then(() => {
      closeModal();
      fetchProducts();
    });
}

function searchProduct() {
  const model = document.getElementById('searchModel').value;

  fetch(`${apiUrl}?action=search&model=${model}`)
    .then(res => res.json())
    .then(data => displayProducts(data));
}

function sortByStatus() {
  const status = document.getElementById('sortStatus').value;

  if (!status) {
    fetchProducts();
    return;
  }

  fetch(`${apiUrl}?action=sort&status=${status}`)
    .then(res => res.json())
    .then(data => displayProducts(data));
}
