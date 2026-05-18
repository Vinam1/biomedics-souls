<div
    class="assistant-widget"
    data-assistant-widget
    data-chat-endpoint="<?= htmlspecialchars(site_url('assistant/chat')); ?>"
    data-reset-endpoint="<?= htmlspecialchars(site_url('assistant/reset')); ?>"
    data-csrf-token="<?= htmlspecialchars($csrfToken ?? csrf_token()); ?>"
>
    <button type="button" class="assistant-toggle" data-assistant-toggle aria-label="Abrir chat inteligente">
        <span class="assistant-toggle-icon"><i class="bi bi-stars"></i></span>
        <span class="assistant-toggle-copy">
            <strong>Asistente IA</strong>
            <small>Pregúntame por productos</small>
        </span>
    </button>

    <section class="assistant-panel" data-assistant-panel hidden>
        <header class="assistant-header">
            <div>
                <span class="assistant-eyebrow">Biomedics Souls AI</span>
                <h3>Asistente de productos</h3>
                <p>Te ayudo a encontrar fórmulas según tus objetivos.</p>
            </div>
            <div class="assistant-header-actions">
                <button type="button" class="assistant-icon-btn" data-assistant-reset aria-label="Reiniciar chat">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
                <button type="button" class="assistant-icon-btn" data-assistant-close aria-label="Cerrar chat">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </header>

        <div class="assistant-body" data-assistant-messages>
            <article class="assistant-message assistant-message-bot">
                <div class="assistant-bubble">
                    Hola, soy tu asistente. Puedes preguntarme cosas como "¿qué producto recomiendas para energía?" o "¿qué opciones tienen para enfoque?".
                </div>
            </article>
        </div>

        <div class="assistant-suggestions" data-assistant-suggestions>
            <button type="button" class="assistant-suggestion-chip" data-suggestion="Quiero algo para energía y vitalidad">Energía</button>
            <button type="button" class="assistant-suggestion-chip" data-suggestion="¿Qué producto me recomiendas para enfoque mental?">Enfoque</button>
            <button type="button" class="assistant-suggestion-chip" data-suggestion="Muéstrame productos destacados del catálogo">Destacados</button>
        </div>

        <form class="assistant-form" data-assistant-form>
            <textarea
                class="assistant-input"
                data-assistant-input
                rows="1"
                maxlength="800"
                placeholder="Escribe tu pregunta sobre productos..."
            ></textarea>
            <button type="submit" class="assistant-send" data-assistant-send>
                <i class="bi bi-send-fill"></i>
            </button>
        </form>
    </section>
</div>
