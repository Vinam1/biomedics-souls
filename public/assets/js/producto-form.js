(function () {
    function boot() {
        initCantidadEnvase();
        initAutoSlug();
        initQuickAddModal();
        initTagHelpers();
        initImageManager();
        initFormValidation();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }

    function initCantidadEnvase() {
        const formaSelect = document.getElementById('forma_id');
        const cantidadEnvaseGroup = document.getElementById('cantidad-envase-group');

        if (!formaSelect || !cantidadEnvaseGroup) {
            return;
        }

        const trackedNames = ['capsulas', 'capsulas blandas', 'tabletas', 'pildoras', 'comprimidos'];

        function toggleCantidadEnvase() {
            const nombre = slugifyText(formaSelect.options[formaSelect.selectedIndex]?.getAttribute('data-nombre') || '');
            const shouldShow = trackedNames.includes(nombre);
            cantidadEnvaseGroup.style.display = shouldShow ? '' : 'none';
        }

        formaSelect.addEventListener('change', toggleCantidadEnvase);
        toggleCantidadEnvase();
    }

    function initAutoSlug() {
        const nameInput = document.getElementById('nombre');
        const slugInput = document.getElementById('slug');

        if (!nameInput || !slugInput || slugInput.readOnly) {
            return;
        }

        let slugTouched = slugInput.value.trim() !== '';

        slugInput.addEventListener('input', function () {
            slugTouched = this.value.trim() !== '';
            this.value = slugifyText(this.value);
        });

        nameInput.addEventListener('input', function () {
            if (!slugTouched) {
                slugInput.value = slugifyText(this.value);
            }
        });
    }

    function initQuickAddModal() {
        const modalElement = document.getElementById('quickAddModal');
        const saveButton = document.getElementById('save-attribute-btn');
        const nameInput = document.getElementById('attribute-name');
        const colorInput = document.getElementById('attribute-color');
        const quickAddForm = document.getElementById('quickAddForm');

        window.openQuickAddModal = function () {};

        if (!modalElement || !saveButton || !nameInput || !window.bootstrap?.Modal) {
            return;
        }

        const quickAddModal = new window.bootstrap.Modal(modalElement);
        let currentAttributeType = 'etiqueta';

        window.openQuickAddModal = function (tipo) {
            currentAttributeType = tipo;
            document.getElementById('modal-attribute-type').value = tipo;
            quickAddForm?.reset();
            document.getElementById('dynamic-fields')?.classList.remove('d-none');
            saveButton.disabled = true;

            const title = document.getElementById('quickAddModalLabel');
            if (title) {
                title.innerHTML = '<i class="fas fa-plus-circle text-primary me-2"></i>Agregar nueva etiqueta';
            }

            const colorField = document.getElementById('color-field');
            const nameLabel = document.getElementById('name-label');
            if (nameLabel) {
                nameLabel.textContent = 'Nombre de la etiqueta';
            }
            if (colorField) {
                colorField.style.display = 'block';
            }

            quickAddModal.show();
            window.setTimeout(() => nameInput.focus(), 150);
        };

        colorInput?.addEventListener('input', function () {
            const colorText = document.getElementById('color-text');
            if (colorText) {
                colorText.value = this.value;
            }
        });

        nameInput.addEventListener('input', function () {
            saveButton.disabled = this.value.trim() === '';
        });

        quickAddForm?.addEventListener('submit', function (event) {
            event.preventDefault();
            if (!saveButton.disabled) {
                saveButton.click();
            }
        });

        saveButton.addEventListener('click', async function () {
            const name = nameInput.value.trim();
            if (!name) {
                return;
            }

            const payload = new URLSearchParams({
                nombre: name,
                color: colorInput?.value || '#3B82F6',
                csrf_token: window.productoFormData?.csrfToken || '',
            });

            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creando...';

            try {
                const response = await fetch(`${window.productoFormData?.endpoints?.add || ''}/etiquetas`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: payload.toString(),
                });

                const result = await response.json();
                if (!result.success) {
                    showToast(result.message || 'Error al crear atributo', 'error');
                    return;
                }

                agregarNuevoTag(currentAttributeType, result.id, result.nombre);
                await updateSelectOptions(currentAttributeType);
                quickAddModal.hide();
                showToast('Atributo creado y agregado correctamente', 'success');
            } catch (error) {
                console.error(error);
                showToast('Error de conexion', 'error');
            } finally {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-save me-2"></i>Crear y agregar';
            }
        });
    }

    function initTagHelpers() {
        window.agregarNuevoTag = function (tipo, id = null, nombre = null) {
            if (tipo !== 'etiqueta') {
                return;
            }

            const select = document.getElementById('select-etiqueta');
            const container = document.getElementById('etiquetas-container');
            if (!container) {
                return;
            }

            let value = id;
            let label = nombre;

            if (!value || !label) {
                const selectedOption = select?.options[select.selectedIndex];
                if (!selectedOption?.value) {
                    return;
                }
                value = selectedOption.value;
                label = selectedOption.text;
            }

            if (container.querySelector(`input[name="etiquetas[]"][value="${value}"]`)) {
                showToast('Este atributo ya esta agregado', 'warning');
                return;
            }

            const item = document.createElement('span');
            item.className = 'badge rounded-pill px-3 py-2 me-2 mb-2 bg-primary text-white d-inline-flex align-items-center';
            item.innerHTML = `
                <span>${escapeHtml(label)}</span>
                <button type="button" class="btn-close btn-close-white ms-2" style="font-size:0.75rem;" aria-label="Eliminar"></button>
                <input type="hidden" name="etiquetas[]" value="${escapeHtml(value)}">
            `;
            item.querySelector('.btn-close')?.addEventListener('click', () => item.remove());
            container.appendChild(item);

            if (select) {
                select.value = '';
            }
        };

        const currentTags = Array.isArray(window.productoFormData?.currentTags) ? window.productoFormData.currentTags : [];
        currentTags.forEach((item) => {
            agregarNuevoTag('etiqueta', item.id || item, item.nombre || item);
        });
    }

    function initImageManager() {
        const form = document.getElementById('product-form');
        const input = document.getElementById('imagenesInput');
        const previewContainer = document.getElementById('preview-container');
        const emptyState = document.getElementById('image-empty-state');

        if (!form || !input || !previewContainer) {
            return;
        }

        const state = {
            existingImages: getExistingImages(previewContainer),
            newImages: [],
            draggedId: null,
        };

        input.addEventListener('change', function (event) {
            const files = Array.from(event.target.files || []);
            if (files.length === 0) {
                return;
            }

            files.forEach((file) => {
                const localId = createClientId();
                state.newImages.push({
                    id: localId,
                    file,
                    type: 'new',
                    name: file.name,
                    previewUrl: URL.createObjectURL(file),
                });
            });

            syncInputFiles();
            render();
        });

        form.addEventListener('submit', function () {
            syncInputFiles();
            syncHiddenInputs();
        });

        render();

        function render() {
            const images = getMergedImages();
            previewContainer.innerHTML = '';

            images.forEach((image, index) => {
                const card = document.createElement('div');
                card.className = 'image-card';
                card.draggable = true;
                card.dataset.imageId = String(image.id);
                card.innerHTML = `
                    <button type="button" class="image-card__remove" aria-label="Eliminar imagen">&times;</button>
                    <div class="image-card__handle" title="Arrastra para reordenar"><i class="fas fa-grip-vertical"></i></div>
                    ${index === 0 ? '<span class="image-badge">Principal</span>' : ''}
                    <div class="image-card__frame">
                        <img src="${escapeAttribute(image.previewUrl)}" alt="${escapeAttribute(image.name)}" class="image-card__img">
                    </div>
                    <div class="image-card__body">
                        <div class="image-card__name" title="${escapeAttribute(image.name)}">${escapeHtml(image.name)}</div>
                    </div>
                `;

                card.querySelector('.image-card__remove')?.addEventListener('click', function () {
                    removeImage(image);
                });

                card.addEventListener('dragstart', function (event) {
                    state.draggedId = String(image.id);
                    card.classList.add('is-dragging');
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', String(image.id));
                });

                card.addEventListener('dragover', function (event) {
                    event.preventDefault();
                    card.classList.add('is-drop-target');
                });

                card.addEventListener('dragleave', function () {
                    card.classList.remove('is-drop-target');
                });

                card.addEventListener('drop', function (event) {
                    event.preventDefault();
                    moveImage(state.draggedId, String(image.id));
                });

                card.addEventListener('dragend', function () {
                    state.draggedId = null;
                    previewContainer.querySelectorAll('.image-card').forEach((node) => {
                        node.classList.remove('is-dragging', 'is-drop-target');
                    });
                });

                previewContainer.appendChild(card);
            });

            if (emptyState) {
                emptyState.classList.toggle('d-none', images.length > 0);
            }

            syncHiddenInputs();
        }

        function moveImage(sourceId, targetId) {
            if (!sourceId || !targetId || sourceId === targetId) {
                render();
                return;
            }

            const merged = getMergedImages();
            const from = merged.findIndex((item) => String(item.id) === String(sourceId));
            const to = merged.findIndex((item) => String(item.id) === String(targetId));
            if (from === -1 || to === -1) {
                render();
                return;
            }

            const [moved] = merged.splice(from, 1);
            merged.splice(to, 0, moved);

            const existingIds = merged.filter((item) => item.type === 'existing').map((item) => String(item.id));
            const newIds = merged.filter((item) => item.type === 'new').map((item) => String(item.id));

            state.existingImages.sort((a, b) => existingIds.indexOf(String(a.id)) - existingIds.indexOf(String(b.id)));
            state.newImages.sort((a, b) => newIds.indexOf(String(a.id)) - newIds.indexOf(String(b.id)));

            syncInputFiles();
            render();
        }

        function removeImage(image) {
            if (image.type === 'existing') {
                state.existingImages = state.existingImages.filter((item) => String(item.id) !== String(image.id));
            } else {
                const target = state.newImages.find((item) => String(item.id) === String(image.id));
                if (target?.previewUrl) {
                    URL.revokeObjectURL(target.previewUrl);
                }
                state.newImages = state.newImages.filter((item) => String(item.id) !== String(image.id));
                syncInputFiles();
            }

            render();
        }

        function getMergedImages() {
            return [
                ...state.existingImages.map((image) => ({
                    ...image,
                    type: 'existing',
                })),
                ...state.newImages,
            ];
        }

        function syncInputFiles() {
            if (typeof DataTransfer === 'undefined') {
                return;
            }

            const dt = new DataTransfer();
            state.newImages.forEach((image) => dt.items.add(image.file));
            input.files = dt.files;
        }

        function syncHiddenInputs() {
            form.querySelectorAll('input[data-image-input="1"]').forEach((node) => node.remove());

            getMergedImages().forEach((image) => {
                appendHidden('image_order[]', image.type === 'existing' ? `existing:${image.id}` : 'new');
            });

            state.existingImages.forEach((image) => {
                appendHidden('existing_image_ids[]', String(image.id));
            });
        }

        function appendHidden(name, value) {
            const inputHidden = document.createElement('input');
            inputHidden.type = 'hidden';
            inputHidden.name = name;
            inputHidden.value = value;
            inputHidden.dataset.imageInput = '1';
            form.appendChild(inputHidden);
        }
    }

    function initFormValidation() {
        const form = document.getElementById('product-form');
        const feedback = document.getElementById('form-feedback');
        const precioInput = document.getElementById('precio');
        const descuentoInput = document.getElementById('precio_descuento');
        const slugInput = document.getElementById('slug');

        if (!form || !feedback) {
            return;
        }

        form.addEventListener('submit', function (event) {
            const errors = [];
            const precio = Number.parseFloat(precioInput?.value || '0');
            const descuento = Number.parseFloat(descuentoInput?.value || '');
            const slug = slugInput?.value.trim() || '';
            const imagesCount = form.querySelectorAll('input[name="existing_image_ids[]"]').length + (document.getElementById('imagenesInput')?.files.length || 0);

            if (!slug || slugifyText(slug) !== slug) {
                errors.push('El slug solo puede contener letras minusculas, numeros y guiones.');
            }

            if (Number.isNaN(precio) || precio < 0) {
                errors.push('El precio normal debe ser un numero valido.');
            }

            if (descuentoInput?.value && (Number.isNaN(descuento) || descuento < 0 || descuento >= precio)) {
                errors.push('El precio con descuento debe ser menor al precio normal.');
            }

            if (imagesCount === 0) {
                errors.push('Debes conservar o cargar al menos una imagen.');
            }

            if (!form.checkValidity()) {
                errors.push('Completa los campos obligatorios antes de guardar.');
            }

            if (errors.length > 0) {
                event.preventDefault();
                feedback.innerHTML = `<strong>Revisa el formulario:</strong><br>${errors.map(escapeHtml).join('<br>')}`;
                feedback.classList.remove('d-none');
                feedback.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            feedback.classList.add('d-none');
            feedback.innerHTML = '';
        });
    }

    function getExistingImages(previewContainer) {
        const source = Array.isArray(window.productoFormData?.currentImages) && window.productoFormData.currentImages.length > 0
            ? window.productoFormData.currentImages
            : safeParseJson(previewContainer.dataset.existingImages || '[]');

        return (Array.isArray(source) ? source : []).map((image) => ({
            id: String(image.id),
            type: 'existing',
            name: image.filename || 'Imagen actual',
            previewUrl: image.url || '',
        }));
    }

    function safeParseJson(raw) {
        try {
            return JSON.parse(raw);
        } catch (error) {
            console.error('Error al leer imagenes existentes.', error);
            return [];
        }
    }

    async function updateSelectOptions(tipo) {
        try {
            const baseUrl = window.productoFormData?.endpoints?.update || '';
            const response = await fetch(`${baseUrl}/${tipo === 'etiqueta' ? 'etiquetas' : tipo}s`);
            const data = await response.json();
            if (!data.success || !Array.isArray(data.items)) {
                return;
            }

            const select = document.getElementById(`select-${tipo}`);
            if (!select) {
                return;
            }

            select.innerHTML = `<option value="">Selecciona ${tipo}</option>`;
            data.items.forEach((item) => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.nombre;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error updating select:', error);
        }
    }

    function slugifyText(value) {
        return String(value)
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s-]/g, '')
            .trim()
            .replace(/[\s-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    function createClientId() {
        return `new-${Date.now()}-${Math.random().toString(16).slice(2, 8)}`;
    }

    function showToast(message, type) {
        const color = type === 'success' ? 'success' : type === 'error' ? 'danger' : type === 'warning' ? 'warning' : 'info';
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${color} border-0`;
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${escapeHtml(message)}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        container.appendChild(toast);

        if (window.bootstrap?.Toast) {
            const bsToast = new window.bootstrap.Toast(toast);
            bsToast.show();
            toast.addEventListener('hidden.bs.toast', () => toast.remove());
        } else {
            setTimeout(() => toast.remove(), 3000);
        }
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function escapeAttribute(value) {
        return escapeHtml(value);
    }
})();
