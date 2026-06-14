function ensureCartToastContainer() {
    let container = document.querySelector('.toast-container[data-cart-toast-container="true"]');

    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1085';
        container.style.marginTop = '5.5rem';
        container.dataset.cartToastContainer = 'true';
        document.body.appendChild(container);
    }

    return container;
}

function showCartToast(message, type) {
    const toastType = type === 'error' ? 'error' : 'success';
    const container = ensureCartToastContainer();
    const toastElement = document.createElement('div');
    toastElement.className = 'toast align-items-center border-0 shadow-lg';
    toastElement.setAttribute('role', 'alert');
    toastElement.setAttribute('aria-live', 'assertive');
    toastElement.setAttribute('aria-atomic', 'true');
    toastElement.dataset.cartToast = 'true';
    toastElement.dataset.bsDelay = '3200';

    const toastBody = document.createElement('div');
    toastBody.className = 'd-flex';

    const bodyContent = document.createElement('div');
    bodyContent.className = 'toast-body';

    const title = document.createElement('strong');
    title.className = toastType === 'error' ? 'text-danger' : 'text-success';
    title.textContent = toastType === 'error' ? 'No se pudo agregar' : 'Agregado al carrito';

    const description = document.createElement('div');
    description.className = 'small text-muted mt-1';
    description.textContent = message;

    const closeButton = document.createElement('button');
    closeButton.type = 'button';
    closeButton.className = 'btn-close me-2 m-auto';
    closeButton.setAttribute('data-bs-dismiss', 'toast');
    closeButton.setAttribute('aria-label', 'Cerrar');

    bodyContent.appendChild(title);
    bodyContent.appendChild(description);
    toastBody.appendChild(bodyContent);
    toastBody.appendChild(closeButton);
    toastElement.appendChild(toastBody);
    container.appendChild(toastElement);

    const toast = window.bootstrap && typeof window.bootstrap.Toast === 'function'
        ? window.bootstrap.Toast.getOrCreateInstance(toastElement)
        : null;

    if (toast) {
        toast.show();
        toastElement.addEventListener('hidden.bs.toast', function () {
            toastElement.remove();
        });
    } else {
        toastElement.remove();
    }
}

async function handleCartAddSubmit(event) {
    const form = event.target.closest('form');
    if (!form) {
        return;
    }

    const action = form.getAttribute('action') || '';
    if (!/\/carrito\/agregar\//.test(action)) {
        return;
    }

    event.preventDefault();

    const submitButton = form.querySelector('button[type="submit"]');
    const originalLabel = submitButton ? submitButton.innerHTML : '';
    const csrfToken = form.querySelector('input[name="csrf_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Agregando...';
    }

    try {
        const response = await fetch(action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-Token': csrfToken,
            },
            body: new URLSearchParams(new FormData(form)),
            credentials: 'same-origin'
        });

        let payload = null;
        try {
            payload = await response.json();
        } catch (error) {
            payload = null;
        }

        if (!response.ok || !payload || payload.success !== true) {
            const message = payload && typeof payload.message === 'string' && payload.message !== ''
                ? payload.message
                : 'No se pudo agregar el producto al carrito.';
            showCartToast(message, 'error');
            return;
        }

        const cartCountElement = document.getElementById('cartCount');
        if (cartCountElement) {
            const nextCount = Number(payload.cartCount);
            if (Number.isFinite(nextCount)) {
                cartCountElement.textContent = String(nextCount);
            }
        }

        showCartToast(payload.message || 'Producto agregado al carrito.', 'success');
    } catch (error) {
        showCartToast('No se pudo agregar el producto al carrito.', 'error');
    } finally {
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.innerHTML = originalLabel;
        }
    }
}

function initCartAddForms() {
    const forms = document.querySelectorAll('form[action*="/carrito/agregar/"]');

    forms.forEach(function (form) {
        if (form.dataset.cartAddBound === '1') {
            return;
        }

        form.dataset.cartAddBound = '1';
        form.addEventListener('submit', handleCartAddSubmit);
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const navbar = document.querySelector('.site-navbar');
    const parallaxElements = document.querySelectorAll('[data-parallax]');
    let revealObserver = null;

    function initAnimations(scope) {
        const root = scope || document;
        const revealElements = root.querySelectorAll('[data-animate]');

        revealElements.forEach(function (element) {
            const delay = element.getAttribute('data-animate-delay');
            if (delay) {
                element.style.transitionDelay = delay + 'ms';
            }
        });

        if (prefersReducedMotion) {
            revealElements.forEach(function (element) {
                element.classList.add('is-visible');
            });
            return;
        }

        if ('IntersectionObserver' in window) {
            if (!revealObserver) {
                revealObserver = new IntersectionObserver(function (entries, observer) {
                    entries.forEach(function (entry) {
                        if (!entry.isIntersecting) return;
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    });
                }, { threshold: 0.08, rootMargin: '0px 0px 140px 0px' });
            }

            revealElements.forEach(function (element) {
                if (!element.classList.contains('is-visible')) {
                    revealObserver.observe(element);
                }
            });
        } else {
            revealElements.forEach(function (element) {
                element.classList.add('is-visible');
            });
        }
    }

    function initClickableProductCards(scope) {
        const root = scope || document;
        const cards = root.querySelectorAll('.product-card-clickable');

        cards.forEach(function (card) {
            if (card.dataset.cardBound === '1') return;
            card.dataset.cardBound = '1';

            card.addEventListener('click', function (event) {
                if (event.target.closest('a, button, form, input, select, textarea, label')) {
                    return;
                }

                const targetUrl = card.getAttribute('data-product-link');
                if (targetUrl) {
                    window.location.href = targetUrl;
                }
            });

            card.addEventListener('keydown', function (event) {
                if (event.key !== 'Enter' && event.key !== ' ') return;
                if (event.target.closest('a, button, form, input, select, textarea, label') && event.target !== card) {
                    return;
                }

                event.preventDefault();
                const targetUrl = card.getAttribute('data-product-link');
                if (targetUrl) {
                    window.location.href = targetUrl;
                }
            });
        });
    }

    function initAssistantWidget() {
        const widget = document.querySelector('[data-assistant-widget]');
        if (!widget || widget.dataset.bound === '1') {
            return;
        }
        widget.dataset.bound = '1';

        const toggle = widget.querySelector('[data-assistant-toggle]');
        const panel = widget.querySelector('[data-assistant-panel]');
        const closeBtn = widget.querySelector('[data-assistant-close]');
        const resetBtn = widget.querySelector('[data-assistant-reset]');
        const form = widget.querySelector('[data-assistant-form]');
        const input = widget.querySelector('[data-assistant-input]');
        const messages = widget.querySelector('[data-assistant-messages]');
        const suggestionsContainer = widget.querySelector('[data-assistant-suggestions]');
        const sendButton = widget.querySelector('[data-assistant-send]');
        const typingIndicator = widget.querySelector('[data-assistant-typing]');
        const chatEndpoint = widget.dataset.chatEndpoint;
        const resetEndpoint = widget.dataset.resetEndpoint;
        const csrfToken = widget.dataset.csrfToken;
        const storageKey = widget.dataset.storageKey || 'assistant-chat';

        function setOpen(open) {
            widget.classList.toggle('is-open', open);
            panel.hidden = !open;
            if (open) {
                input.focus();
                messages.scrollTop = messages.scrollHeight;
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function serializeMessages() {
            const data = [];
            messages.querySelectorAll('.assistant-message').forEach(function (node) {
                const bubble = node.querySelector('.assistant-bubble');
                if (!bubble) return;
                data.push({
                    role: node.classList.contains('assistant-message-user') ? 'user' : 'assistant',
                    text: bubble.textContent || ''
                });
            });
            sessionStorage.setItem(storageKey, JSON.stringify(data.slice(-12)));
        }

        function appendMessage(role, text, products) {
            const item = document.createElement('article');
            item.className = 'assistant-message ' + (role === 'user' ? 'assistant-message-user' : 'assistant-message-bot');

            const bubble = document.createElement('div');
            bubble.className = 'assistant-bubble';
            bubble.innerHTML = escapeHtml(text).replace(/\n/g, '<br>');
            item.appendChild(bubble);

            if (Array.isArray(products) && products.length > 0) {
                const productList = document.createElement('div');
                productList.className = 'assistant-product-list';

                const maxProducts = Math.min(products.length, 5);
                products.slice(0, maxProducts).forEach(function (product) {
                    const card = document.createElement('a');
                    card.className = 'assistant-product-card';
                    card.href = product.url || '#';

                    const image = product.image
                        ? `<img src="${escapeHtml(product.image)}" alt="${escapeHtml(product.name || 'Producto')}">`
                        : `<div class="assistant-product-placeholder"><i class="bi bi-capsule"></i></div>`;

                    card.innerHTML = `
                        <div class="assistant-product-media">${image}</div>
                        <div class="assistant-product-copy">
                            <strong>${escapeHtml(product.name || 'Producto')}</strong>
                            <span>${escapeHtml(product.status || 'Disponible')}</span>
                            <small>$${Number(product.price || 0).toFixed(2)}</small>
                        </div>
                    `;

                    productList.appendChild(card);
                });

                item.appendChild(productList);
            }

            messages.appendChild(item);
            messages.scrollTop = messages.scrollHeight;
            serializeMessages();
        }

        function restoreMessages() {
            const raw = sessionStorage.getItem(storageKey);
            if (!raw) return;

            try {
                const items = JSON.parse(raw);
                if (!Array.isArray(items) || items.length === 0) return;
                messages.innerHTML = '';
                items.forEach(function (item) {
                    appendMessage(item.role === 'user' ? 'user' : 'assistant', item.text || '');
                });
            } catch (error) {
                sessionStorage.removeItem(storageKey);
            }
        }

        function renderSuggestions(items) {
            if (!suggestionsContainer) return;
            const suggestions = Array.isArray(items) ? items.filter(Boolean).slice(0, 4) : [];
            suggestionsContainer.innerHTML = '';

            suggestions.forEach(function (text) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'assistant-suggestion-chip';
                button.dataset.suggestion = text;
                button.textContent = text;
                button.addEventListener('click', function () {
                    setOpen(true);
                    sendMessage(text);
                });
                suggestionsContainer.appendChild(button);
            });
        }

        function setLoading(loading) {
            widget.classList.toggle('is-loading', loading);
            sendButton.disabled = loading;
            input.disabled = loading;
            if (typingIndicator) {
                typingIndicator.hidden = !loading;
            }
        }

        async function sendMessage(message) {
            const content = (message || '').trim();
            if (!content) return;

            appendMessage('user', content);
            input.value = '';
            input.style.height = 'auto';
            setLoading(true);

            try {
                const response = await fetch(chatEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: content })
                });

                const payload = await response.json();
                if (!response.ok || !payload.success) {
                    throw new Error(payload.message || 'No se pudo procesar la solicitud.');
                }

                appendMessage('assistant', payload.reply || 'No se recibió respuesta.', payload.products || []);
                renderSuggestions(payload.suggestions || []);
            } catch (error) {
                appendMessage('assistant', error.message || 'Ocurrió un error inesperado.');
            } finally {
                setLoading(false);
                input.focus();
            }
        }

        async function resetChat() {
            try {
                await fetch(resetEndpoint, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-Token': csrfToken,
                        'Accept': 'application/json'
                    }
                });
            } catch (error) {
                console.error('No se pudo reiniciar el chat', error);
            }

            messages.innerHTML = '';
            sessionStorage.removeItem(storageKey);
            appendMessage('assistant', 'Listo, reinicié la conversación. Puedes volver a preguntarme por cualquier producto del catálogo.');
            renderSuggestions([
                'Quiero algo para energía y vitalidad',
                '¿Qué producto recomiendas para enfoque mental?',
                'Muéstrame productos destacados del catálogo'
            ]);
        }

        toggle.addEventListener('click', function () {
            setOpen(!widget.classList.contains('is-open'));
        });

        closeBtn.addEventListener('click', function () {
            setOpen(false);
        });

        resetBtn.addEventListener('click', function () {
            resetChat();
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            sendMessage(input.value);
        });

        input.addEventListener('input', function () {
            input.style.height = 'auto';
            input.style.height = Math.min(input.scrollHeight, 140) + 'px';
        });

        input.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                form.requestSubmit();
            }
        });

        restoreMessages();
        renderSuggestions([
            'Quiero algo para energía y vitalidad',
            '¿Qué producto recomiendas para enfoque mental?',
            'Muéstrame productos destacados del catálogo'
        ]);
    }

    window.initBiomedicsAnimations = initAnimations;
    window.initBiomedicsProductCards = initClickableProductCards;

    const cartToast = document.querySelector('[data-cart-toast="true"]');
    if (cartToast && window.bootstrap && typeof window.bootstrap.Toast === 'function') {
        window.bootstrap.Toast.getOrCreateInstance(cartToast).show();
    }

    const alerts = document.querySelectorAll('.alert-auto-close');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.classList.add('fade');
            alert.addEventListener('transitionend', function () {
                alert.remove();
            });
        }, 4000);
    });

    function syncNavbarState() {
        if (!navbar) return;
        navbar.classList.toggle('is-scrolled', window.scrollY > 18);
    }

    syncNavbarState();
    window.addEventListener('scroll', syncNavbarState, { passive: true });

    initAnimations(document);
    initClickableProductCards(document);
    initCartAddForms();
    initAssistantWidget();

    if (prefersReducedMotion) {
        return;
    }

    if (parallaxElements.length > 0) {
        let ticking = false;

        function updateParallax() {
            const offset = Math.min(window.scrollY * 0.12, 32);
            parallaxElements.forEach(function (element) {
                element.style.transform = 'translate3d(0, ' + offset + 'px, 0) scale(1.04)';
            });
            ticking = false;
        }

        window.addEventListener('scroll', function () {
            if (ticking) return;
            window.requestAnimationFrame(updateParallax);
            ticking = true;
        }, { passive: true });

        updateParallax();
    }
});
