// Guarda los productos del carrito y se sincroniza con la sesión PHP.
let cart = []; // Se inicializa vacío y se llena desde PHP al cargar.
const basePath = window.D6_BASE_PATH || "";

// Referencias a los elementos principales del carrito.
const cartCount = document.getElementById("cart-count");
const cartItemsContainer = document.getElementById("cartItems");
const cartTotalDisplay = document.getElementById("cartTotal");

// Referencias a los elementos del modal de producto.
const modalProductImg = document.getElementById("modalProductImg");
const modalProductTitle = document.getElementById("modalProductTitle");
const modalProductDesc = document.getElementById("modalProductDesc");
const modalProductPrice = document.getElementById("modalProductPrice");
const modalAddToCartBtn = document.getElementById("modalAddToCartBtn");
const modalQuantity = document.getElementById("modalQuantity");

// Guarda la instancia del toast para reutilizarla.
let cartToast;

// Inserta el modal de pago al final del body.
document.body.insertAdjacentHTML(
  "beforeend",
  `
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
      
      <!-- Cabecera del modal con el logo del método de pago. -->
      <div class="modal-header border-0" style="position: relative; padding: 10px 16px 0 16px;">
        <img src="${basePath}mercado-papu.png" 
             alt="Mercado Papu" 
             style="height: 100px; width: auto; margin-top: -15px; margin-bottom: -15px;">
        <button type="button" class="btn-close" data-bs-dismiss="modal" 
                style="position: absolute; right: 16px; top: 10px;"></button>
      </div>
      
      <!-- Cuerpo del modal con los campos de pago. -->
      <div class="modal-body px-4 pb-4 pt-0">
        
        <div class="mb-3">
          <label class="form-label fw-semibold text-muted small">NÚMERO DE TARJETA</label>
          <input id="pay-number" type="text" class="form-control form-control-lg"
            placeholder="0000 0000 0000 0000" maxlength="19" inputmode="numeric">
        </div>

          <div class="mb-3">
            <label class="form-label fw-semibold text-muted small">TITULAR</label>
            <input id="pay-name" type="text" class="form-control form-control-lg"
              placeholder="Nombre como aparece en la tarjeta">
          </div>

          <div class="row g-3 mb-4">
            <div class="col-6">
              <label class="form-label fw-semibold text-muted small">FECHA DE EXPIRACIÓN</label>
              <input id="pay-expiry" type="text" class="form-control form-control-lg"
                placeholder="MM/AA" maxlength="5" inputmode="numeric">
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold text-muted small">CVV</label>
              <input id="pay-cvv" type="text" class="form-control form-control-lg"
                placeholder="123" maxlength="3" inputmode="numeric">
            </div>
          </div>

          <div id="pay-status" class="mb-3 text-center fw-bold"></div>

          <button id="pay-confirm-btn" type="button"
            class="btn btn-danger btn-lg w-100 fw-bold text-uppercase shadow-sm">
            <i class="bi bi-lock-fill me-2"></i>Confirmar Pago
          </button>
        </div>
      </div>
    </div>
  </div>
`,
);

// Formatea el número de tarjeta en grupos de cuatro dígitos.
document.getElementById("pay-number").addEventListener("input", function () {
  let val = this.value.replace(/\D/g, "").substring(0, 16);
  this.value = val.match(/.{1,4}/g)?.join(" ") || val;
});

// Formatea la fecha de expiración con el formato MM/AA.
document.getElementById("pay-expiry").addEventListener("input", function () {
  let val = this.value.replace(/\D/g, "").substring(0, 4);
  this.value = val.length >= 3 ? val.slice(0, 2) + "/" + val.slice(2) : val;
});

// Limita el CVV a tres dígitos numéricos.
document.getElementById("pay-cvv").addEventListener("input", function () {
  this.value = this.value.replace(/\D/g, "").substring(0, 3);
});

// Valida el formulario de pago y genera la boleta si los datos son correctos.
document
  .getElementById("pay-confirm-btn")
  .addEventListener("click", function () {
    // Obtiene los elementos y valores actuales del formulario de pago.
    const btn = document.getElementById("pay-confirm-btn");
    const status = document.getElementById("pay-status");

    const number = document
      .getElementById("pay-number")
      .value.replace(/\s/g, "")
      .trim();
    const name = document.getElementById("pay-name").value.trim();
    const expiry = document.getElementById("pay-expiry").value.trim();
    const cvv = document.getElementById("pay-cvv").value.trim();

    // Valida que el número de tarjeta tenga 16 dígitos.
    if (number.length !== 16) {
      status.innerHTML = `<span class="text-danger"> Número de tarjeta inválido</span>`;
      return;
    }

    // Valida que exista un nombre de titular suficiente.
    if (name.length < 3) {
      status.innerHTML = `<span class="text-danger"> Ingresa el nombre del titular</span>`;
      return;
    }

    // Valida el largo del campo de expiración.
    if (expiry.length !== 5) {
      status.innerHTML = `<span class="text-danger"> Fecha inválida (MM/AA)</span>`;
      return;
    }

    // Valida que el CVV tenga tres dígitos.
    if (cvv.length !== 3) {
      status.innerHTML = `<span class="text-danger"> CVV inválido (3 dígitos)</span>`;
      return;
    }

    // Separa mes y año para validar la fecha.
    const partesFecha = expiry.split('/');
    if (partesFecha.length !== 2) {
      status.innerHTML = `<span class="text-danger"> El formato debe incluir la barra (MM/AA)</span>`;
      return;
    }

    const expMonth = parseInt(partesFecha[0], 10);
    // Convierte el año corto AA a un año completo AAAA.
    const expYear = parseInt(partesFecha[1], 10) + 2000; 

    const currentDate = new Date();
    // Ajusta getMonth porque JavaScript devuelve meses desde 0 hasta 11.
    const currentMonth = currentDate.getMonth() + 1; 
    const currentYear = currentDate.getFullYear();

    // Valida que el mes esté dentro del rango permitido.
    if (expMonth < 1 || expMonth > 12) {
      status.innerHTML = `<span class="text-danger"> Mes inválido (01-12)</span>`;
      return;
    }

    // Rechaza tarjetas vencidas según mes y año actuales.
    if (expYear < currentYear || (expYear === currentYear && expMonth < currentMonth)) {
      status.innerHTML = `<span class="text-danger"> Tarjeta vencida</span>`;
      return;
    }

    // Simula una tarjeta rechazada para pruebas.
    const cleanNumber = number.replace(/\s+/g, "");
    if (cleanNumber === "1111111111111111") {
      status.innerHTML = `<span class="text-danger"> Tarjeta rechazada</span>`;
      return;
    }

    btn.disabled = true;
    status.innerHTML = `<span class="text-secondary"> Procesando pago...</span>`;

    setTimeout(() => {
      status.innerHTML = `<span class="text-success"> ¡Pago aprobado! Descargando boleta...</span>`;

      // Genera el comprobante de compra en PDF.
      generateReceipt(name);

      setTimeout(() => {
        bootstrap.Modal.getInstance(
          document.getElementById("paymentModal"),
        ).hide();

        // Vacía el carrito y sincroniza la sesión PHP.
        cart = [];
        updateCartUI();

        // Limpia los campos del formulario de pago.
        ["pay-number", "pay-name", "pay-expiry", "pay-cvv"].forEach((id) => {
          document.getElementById(id).value = "";
        });
        status.innerHTML = "";
        btn.disabled = false;

        const successToast = new bootstrap.Toast(
          document.getElementById("cartToast"),
          { delay: 4000 },
        );
        document.querySelector("#cartToast .toast-body").innerHTML =
          `<i class="bi bi-check-circle-fill fs-5"></i> ¡Compra realizada con éxito!`;
        successToast.show();
      }, 1500);
    }, 2000);
  });

// Genera un PDF con los datos de la compra actual.
function generateReceipt(cardholderName) {
  // Evita continuar si la librería jsPDF no está disponible.
  if (!window.jspdf) {
    console.error("jsPDF no está cargado.");
    return;
  }

  // Prepara el documento, número de orden y fecha.
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  const orden = Math.floor(Math.random() * 900000) + 100000;
  const fecha = new Date().toLocaleDateString();

  // Dibuja la cabecera del comprobante.
  doc.setFillColor(40, 40, 40);
  doc.rect(0, 0, 210, 40, "F");

  doc.setTextColor(255, 255, 255);
  doc.setFontSize(24);
  doc.setFont("helvetica", "bold");
  doc.text("D6GUITARS", 15, 25);

  doc.setFontSize(10);
  doc.setFont("helvetica", "normal");
  doc.text("COMPROBANTE DE PAGO", 15, 33);
  doc.text(`Fecha: ${fecha}`, 195, 25, { align: "right" });
  doc.text(`Orden: #${orden}`, 195, 33, { align: "right" });

  // Escribe los datos del cliente.
  doc.setTextColor(0, 0, 0);
  doc.setFontSize(12);
  doc.setFont("helvetica", "bold");
  doc.text("DETALLES DEL CLIENTE", 15, 55);
  doc.setFont("helvetica", "normal");
  doc.text(`Nombre: ${cardholderName.toUpperCase()}`, 15, 62);

  doc.setDrawColor(200, 200, 200);
  doc.line(15, 70, 195, 70);

  doc.setFont("helvetica", "bold");
  // Dibuja los encabezados de la tabla de productos.
  doc.text("Producto", 15, 77);
  doc.text("Cant.", 140, 77);
  doc.text("Subtotal", 195, 77, { align: "right" });
  doc.line(15, 80, 195, 80);

  let y = 90;
  let totalAcumulado = 0;
  doc.setFont("helvetica", "normal");

  // Agrega cada producto del carrito al comprobante.
  cart.forEach((item) => {
    const subtotal = item.price * item.quantity;
    totalAcumulado += subtotal;

    doc.text(item.name, 15, y);
    doc.text(item.quantity.toString(), 142, y);
    doc.text(`$${subtotal.toFixed(2)}`, 195, y, { align: "right" });

    y += 10;
  });

  // Muestra el total final de la compra.
  y += 10;
  doc.setDrawColor(220, 53, 69);
  doc.setLineWidth(1);
  doc.line(120, y, 195, y);

  y += 10;
  doc.setFontSize(14);
  doc.setFont("helvetica", "bold");
  doc.text("TOTAL COMPRA:", 120, y);
  doc.setTextColor(25, 135, 84);
  doc.text(`$${totalAcumulado.toFixed(2)}`, 195, y, { align: "right" });

  doc.setFontSize(8);
  doc.setTextColor(150, 150, 150);
  doc.text(
    "Gracias por elegir D6guitars. Este es un documento digital.",
    105,
    280,
    { align: "center" },
  );

  doc.save(`Boleta_D6guitars_${orden}.pdf`);
}

// Abre el modal de pago cuando el usuario presiona finalizar compra.
document.addEventListener("click", function (e) {
  if (e.target.innerText.includes("FINALIZAR COMPRA")) {
    if (cart.length === 0) return;
    const offcanvas = bootstrap.Offcanvas.getInstance(
      document.getElementById("cartOffcanvas"),
    );
    if (offcanvas) offcanvas.hide();
    setTimeout(() => {
      new bootstrap.Modal(document.getElementById("paymentModal")).show();
    }, 300);
  }
});

// Carga el carrito guardado en PHP cuando el DOM está listo.
document.addEventListener("DOMContentLoaded", async () => {
  // Obtiene el carrito de la sesión antes de renderizar la interfaz.
  try {
    const response = await fetch(`${basePath}cart_sync.php?action=get`);
    const data = await response.json();
    if (Array.isArray(data)) {
      cart = data;
    }
  } catch (error) {
    console.error("Error al cargar el carrito desde PHP:", error);
  }

  // Renderiza sin volver a sincronizar lo recién cargado.
  updateCartUI(false);

  // Prepara el toast usado al agregar productos o finalizar compras.
  const toastEl = document.getElementById("cartToast");
  if (toastEl) {
    cartToast = new bootstrap.Toast(toastEl, { delay: 3000 });
  }
});

// Carga los datos del producto seleccionado dentro del modal de detalles.
document.querySelectorAll(".btn-details").forEach((button) => {
  button.addEventListener("click", (e) => {
    const btn = e.currentTarget;
    if (modalQuantity) modalQuantity.value = 1;
    if (modalProductTitle) modalProductTitle.innerText = btn.dataset.name;
    if (modalProductDesc) modalProductDesc.innerText = btn.dataset.desc;
    if (modalProductPrice)
      modalProductPrice.innerText = `$${btn.dataset.price}`;
    if (modalProductImg) modalProductImg.src = btn.dataset.img;
    if (modalAddToCartBtn) {
      modalAddToCartBtn.dataset.name = btn.dataset.name;
      modalAddToCartBtn.dataset.price = btn.dataset.price;
    }
  });
});

// Mantiene la cantidad del modal entre 1 y 99.
if (modalQuantity) {
  modalQuantity.addEventListener("change", function () {
    if (this.value <= 0) {
      this.value = 1;
    } else if (this.value >= 100) {
      this.value = 99;
    }
  });
}

// Agrega el producto seleccionado al carrito.
if (modalAddToCartBtn) {
  modalAddToCartBtn.addEventListener("click", (e) => {
    // Lee nombre, precio y cantidad desde el botón y el input del modal.
    const name = e.target.closest("button").dataset.name;
    const price = parseFloat(e.target.closest("button").dataset.price);
    let quantityToAdd = parseInt(modalQuantity.value) || 1;

    // Ajusta la cantidad por seguridad antes de guardarla.
    if (quantityToAdd <= 0) {
      quantityToAdd = 1;
    } else if (quantityToAdd >= 100) {
      quantityToAdd = 99;
    }

    // Suma cantidad si el producto ya existe en el carrito.
    const existingItem = cart.find((item) => item.name === name);
    if (existingItem) {
      existingItem.quantity += quantityToAdd;

      // Mantiene la cantidad acumulada dentro del límite permitido.
      if (existingItem.quantity <= 0) {
        existingItem.quantity = 1;
      } else if (existingItem.quantity >= 100) {
        existingItem.quantity = 99;
      }
    } else {
      cart.push({ name, price, quantity: quantityToAdd });
    }

    updateCartUI();

    // Cierra el modal de producto después de agregar al carrito.
    const modalEl = document.getElementById("productModal");
    const modalInstance = bootstrap.Modal.getInstance(modalEl);
    if (modalInstance) modalInstance.hide();

    document.querySelector("#cartToast .toast-body").innerHTML =
      `<i class="bi bi-check-circle-fill fs-5"></i> Producto agregado al carrito.`;
    if (cartToast) cartToast.show();
  });
}

// Cambia la cantidad de un producto del carrito.
window.changeQuantity = function (index, delta) {
  const spinner = document.getElementById("cart-spinner");

  // Muestra una transición visual mientras se actualiza la cantidad.
  if (spinner) spinner.classList.remove("d-none");
  if (cartItemsContainer) cartItemsContainer.style.opacity = "0.3";

  setTimeout(() => {
    cart[index].quantity += delta;

    if (cart[index].quantity <= 0) {
      removeFromCart(index);
    } else {
      // Limita cada producto a un máximo de 99 unidades.
      if (cart[index].quantity >= 100) {
        cart[index].quantity = 99;
      }

      updateCartUI();
    }

    if (spinner) spinner.classList.add("d-none");
    if (cartItemsContainer) cartItemsContainer.style.opacity = "1";
  }, 250);
};

// Elimina un producto del carrito según su posición.
window.removeFromCart = function (index) {
  cart.splice(index, 1);
  updateCartUI();
};

// Actualiza la interfaz del carrito y opcionalmente sincroniza con PHP.
function updateCartUI(sync = true) {
  // Envía el carrito a PHP para guardarlo en la sesión.
  if (sync) {
    fetch(`${basePath}cart_sync.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(cart),
    }).catch((err) =>
      console.error("Error sincronizando carrito con PHP:", err),
    );
  }

  if (!cartCount || !cartItemsContainer || !cartTotalDisplay) return;

  // Reinicia los totales antes de volver a dibujar el carrito.
  cartItemsContainer.innerHTML = "";
  let total = 0;
  let count = 0;

  // Muestra el estado vacío cuando no hay productos.
  if (cart.length === 0) {
    cartItemsContainer.innerHTML = `
      <li class="list-group-item text-center text-muted py-5">
        <i class="bi bi-cart-x display-1 d-block mb-3 opacity-25"></i>El carrito está vacío
      </li>`;
    cartCount.innerText = "0";
    cartTotalDisplay.innerText = "0.00";
    return;
  }

  // Renderiza cada producto y acumula total y cantidad.
  cart.forEach((item, index) => {
    total += item.price * item.quantity;
    count += item.quantity;

    const li = document.createElement("li");
    li.className =
      "list-group-item d-flex justify-content-between align-items-center";
    li.innerHTML = `
          <div>
              <strong>${item.name}</strong>
          </div>
          <div class="d-flex align-items-center">
              <button class="btn btn-sm btn-outline-secondary px-2 py-0 me-1" onclick="changeQuantity(${index}, -1)">-</button>
              
              <span class="badge text-bg-secondary rounded-pill mx-1">${item.quantity}</span>
              
              <button class="btn btn-sm btn-outline-secondary px-2 py-0 ms-1 me-3" onclick="changeQuantity(${index}, 1)">+</button>
              
              <span class="me-3">$${(item.price * item.quantity).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
              
              <button class="btn btn-sm btn-outline-danger px-2 py-0" onclick="removeFromCart(${index})">X</button>
          </div>
      `;
    cartItemsContainer.appendChild(li);
  });

  // Actualiza el contador del carrito con una animación breve.
  cartCount.innerText = count;
  cartCount.classList.add("scale-up");
  setTimeout(() => cartCount.classList.remove("scale-up"), 200);

  // Muestra el total con dos decimales.
  cartTotalDisplay.innerText = total.toLocaleString("en-US", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
}
