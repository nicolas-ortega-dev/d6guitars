<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<!-- Modal que muestra los detalles del producto seleccionado. -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0 pb-5 px-5">
                <div class="row g-5">
                    <div class="col-md-6">
                        <div class="modal-img-container rounded overflow-hidden shadow-sm">
                            <img src="" id="modalProductImg" class="img-fluid w-100" style="object-fit: cover; height: 100%;">
                        </div>
                    </div>
                    <div class="col-md-6 d-flex flex-column justify-content-center">
                        <h2 id="modalProductTitle" class="fw-bold mb-3 display-6"></h2>
                        <h3 id="modalProductPrice" class="text-success fw-bold mb-4"></h3>
                        <p id="modalProductDesc" class="text-muted fs-6 mb-4 lh-lg"></p>
                        <hr class="mb-4">
                        <div class="d-flex gap-3 align-items-center mb-4">
                            <label for="modalQuantity" class="fw-bold">Cantidad:</label>
                            <input type="number" id="modalQuantity" class="form-control text-center" value="1" min="1" style="width: 80px;">
                        </div>
                        <button type="button" id="modalAddToCartBtn" class="btn btn-danger btn-lg w-100 fw-bold text-uppercase d-flex align-items-center justify-content-center gap-2 shadow">
                            <i class="bi bi-cart-plus fs-4"></i> Agregar al carrito
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast que confirma acciones del carrito. -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1090;">
    <div id="cartToast" class="toast align-items-center text-bg-success border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fs-6 d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill fs-5"></i> Producto agregado al carrito.
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<!-- Panel lateral que lista los productos agregados al carrito. -->
<div class="offcanvas offcanvas-end shadow" tabindex="-1" id="cartOffcanvas">
    <div class="offcanvas-header bg-light border-bottom">
        <h5 class="offcanvas-title fw-bold d-flex align-items-center gap-2"><i class="bi bi-cart3"></i> Tu Carrito</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-0">
        <ul class="list-group list-group-flush mb-auto" id="cartItems">
            <li class="list-group-item text-center text-muted py-5">
                <i class="bi bi-cart-x display-1 d-block mb-3 opacity-25"></i>El carrito está vacío
            </li>
        </ul>
        <div class="p-4 bg-light border-top">
            <h4 class="d-flex justify-content-between fw-bold mb-4">
                <span>Total:</span><span class="text-success">$<span id="cartTotal">0.00</span></span>
            </h4>
            <button class="btn btn-success w-100 btn-lg fw-bold text-uppercase shadow-sm">Finalizar Compra</button>
        </div>
    </div>
</div>
