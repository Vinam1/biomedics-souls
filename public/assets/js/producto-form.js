(function () {
    function boot() {
        initImageManager();
        initCantidadEnvase();
        initQuickAddModal();
        initTagHelpers();
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

        window.toggleCantidadEnvase = function () {
            const nombre = formaSelect.options[formaSelect.selectedIndex]?.getAttribute('data-nombre') || '';
            cantidadEnvaseGroup.style.display = ['Capsulas', 'Tabletas', 'Pildoras', 'Cápsulas', 'Píldoras'].includes(nombre) ? 'block' : 'none';
        };

        formaSelect.addEventListener('change', window.toggleCantidadEnvase);
        window.toggleCantidadEnvase();
    }

    function initQuickAddModal() {
        const modalElement = document.getElementById('quickAddModal');
        const saveButton = document.getElementById('save-attribute-btn');
        const nameInput = document.getElementById('attribute-name');
        const colorInput = document.getElementById('attribute-color');
        const iconInput = document.getElementById('attribute-icon');

        window.openQuickAddModal = function () {};

        if (!modalElement || !saveButton || !nameInput || !window.bootstrap?.Modal) {
            return;
        }

        const quickAddModal = new window.bootstrap.Modal(modalElement);
        let currentAttributeType = 'etiqueta';

        window.openQuickAddModal = function (tipo) {
            currentAttributeType = tipo;
            document.getElementById('modal-attribute-type').value = tipo;
            document.getElementById('quickAddForm')?.reset();
            document.getElementById('dynamic-fields')?.classList.remove('d-none');
            saveButton.disabled = true;

            const title = document.getElementById('quickAddModalLabel');
            if (title) {
                title.innerHTML = '<i class="fas fa-plus-circle text-primary me-2"></i>Agregar nueva etiqueta';
            }

            const colorField = document.getElementById('color-field');
            const descriptionField = document.getElementById('description-field');
            const iconField = document.getElementById('icon-field');
            const nameLabel = document.getElementById('name-label');
            if (nameLabel) {
                nameLabel.textContent = 'Nombre de la etiqueta';
            }
            if (colorField) {
                colorField.style.display = 'block';
            }
            if (descriptionField) {
                descriptionField.style.display = 'none';
            }
            if (iconField) {
                iconField.style.display = 'none';
            }

            quickAddModal.show();
        };

        if (colorInput) {
            colorInput.addEventListener('input', function () {
                const colorText = document.getElementById('color-text');
                if (colorText) {
                    colorText.value = this.value;
                }
            });
        }

        if (iconInput) {
            iconInput.addEventListener('input', function () {
                const preview = document.getElementById('icon-preview');
                if (preview) {
                    preview.innerHTML = `<i class="fas ${this.value.trim()}"></i>`;
                }
            });
        }

        nameInput.addEventListener('input', function () {
            saveButton.disabled = this.value.trim() === '';
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
                showToast('Error de conexión', 'error');
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
                showToast('Este atributo ya está agregado', 'warning');
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
        currentTags.forEach(item => {
            agregarNuevoTag('etiqueta', item.id || item, item.nombre || item);
        });
    }

    function initImageManager() {
        const form = document.querySelector('.admin-product-form form');
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

            files.forEach(file => {
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
                    <button type="button" class="image-card__remove" aria-label="Eliminar imagen">×</button>
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
                    if (state.draggedId && state.draggedId !== String(image.id)) {
                        moveImage(state.draggedId, String(image.id), true);
                    }
                });

                card.addEventListener('dragleave', function () {
                    card.classList.remove('is-drop-target');
                });

                card.addEventListener('drop', function (event) {
                    event.preventDefault();
                    const targetId = String(image.id);
                    moveImage(state.draggedId, targetId, false);
                });

                card.addEventListener('dragend', function () {
                    state.draggedId = null;
                    previewContainer.querySelectorAll('.image-card').forEach(node => {
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

        function moveImage(sourceId, targetId, isLiveReorder) {
            if (!sourceId || !targetId || sourceId === targetId) {
                if (!isLiveReorder) {
                    render();
                }
                return;
            }

            const merged = getMergedImages();
            const from = merged.findIndex(item => String(item.id) === String(sourceId));
            const to = merged.findIndex(item => String(item.id) === String(targetId));
            if (from === -1 || to === -1) {
                if (!isLiveReorder) {
                    render();
                }
                return;
            }

            const [moved] = merged.splice(from, 1);
            merged.splice(to, 0, moved);

            const existingIds = merged.filter(item => item.type === 'existing').map(item => String(item.id));
            const newIds = merged.filter(item => item.type === 'new').map(item => String(item.id));

            state.existingImages.sort((a, b) => existingIds.indexOf(String(a.id)) - existingIds.indexOf(String(b.id)));
            state.newImages.sort((a, b) => newIds.indexOf(String(a.id)) - newIds.indexOf(String(b.id)));

            syncInputFiles();
            render();
        }

        function removeImage(image) {
            if (image.type === 'existing') {
                state.existingImages = state.existingImages.filter(item => String(item.id) !== String(image.id));
            } else {
                const target = state.newImages.find(item => String(item.id) === String(image.id));
                if (target?.previewUrl) {
                    URL.revokeObjectURL(target.previewUrl);
                }
                state.newImages = state.newImages.filter(item => String(item.id) !== String(image.id));
                syncInputFiles();
            }

            render();
        }

        function getMergedImages() {
            return [
                ...state.existingImages.map(image => ({
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
            state.newImages.forEach(image => dt.items.add(image.file));
            input.files = dt.files;
        }

        function syncHiddenInputs() {
            form.querySelectorAll('input[data-image-input="1"]').forEach(node => node.remove());

            getMergedImages().forEach(image => {
                appendHidden('image_order[]', image.type === 'existing' ? `existing:${image.id}` : 'new');
            });

            state.existingImages.forEach(image => {
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

    function getExistingImages(previewContainer) {
        const source = Array.isArray(window.productoFormData?.currentImages) && window.productoFormData.currentImages.length > 0
            ? window.productoFormData.currentImages
            : safeParseJson(previewContainer.dataset.existingImages || '[]');

        return (Array.isArray(source) ? source : []).map(image => ({
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
            console.error('Error al leer imágenes existentes.', error);
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
            data.items.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.nombre;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error updating select:', error);
        }
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
