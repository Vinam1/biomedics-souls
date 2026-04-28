<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="quiz-card p-5 rounded-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h4 fw-bold mb-2">Quiz interactivo</h1>
                        <p class="text-muted mb-0">Responde una pregunta por pantalla y descubre cuÃ¡l es tu mejor ruta de bienestar.</p>
                    </div>
                    <span class="badge bg-primary bg-opacity-15 text-primary">Paso <span id="quiz-step">1</span> / 3</span>
                </div>

                <div class="progress mb-4 rounded-pill" style="height: 8px; background: rgba(15,23,42,0.08);">
                    <div id="quizProgress" class="progress-bar rounded-pill bg-primary" role="progressbar" style="width: 0%"></div>
                </div>

                <div id="quiz-content"></div>
            </div>
        </div>
    </div>
</div>

<script>
    const quizQuestions = [
        {
            question: 'Â¿CuÃ¡l es tu prioridad de salud mÃ¡s importante ahora?',
            options: ['Enfoque cognitivo', 'EnergÃ­a y vitalidad', 'RelajaciÃ³n y sueÃ±o', 'Soporte inmunolÃ³gico'],
        },
        {
            question: 'Â¿Prefieres resultados rÃ¡pidos o un cambio gradual y sostenible?',
            options: ['Resultados rÃ¡pidos', 'EvoluciÃ³n constante', 'Algo equilibrado', 'No lo sÃ© aÃºn'],
        },
        {
            question: 'Â¿QuÃ© estilo de rutina te resulta mÃ¡s cÃ³modo?',
            options: ['Una sola cÃ¡psula diaria', 'Dos dosis al dÃ­a', 'Batidos o mezclas', 'Ayuno intermitente'],
        },
    ];

    const quizAnswers = [];
    let currentStep = 0;

    const content = document.getElementById('quiz-content');
    const progress = document.getElementById('quizProgress');
    const stepLabel = document.getElementById('quiz-step');

    const renderStep = () => {
        const item = quizQuestions[currentStep];
        stepLabel.textContent = currentStep + 1;
        progress.style.width = `${((currentStep) / (quizQuestions.length)) * 100}%`;

        content.innerHTML = `
            <div class="mb-4">
                <h2 class="h5 fw-semibold">${item.question}</h2>
            </div>
            <div class="row row-cols-1 row-cols-md-2 g-3" id="quizOptions"></div>
        `;

        const optionsContainer = document.getElementById('quizOptions');
        item.options.forEach((option, index) => {
            const card = document.createElement('div');
            card.className = 'col';
            card.innerHTML = `
                <button type="button" class="quiz-option btn btn-outline-secondary w-100 rounded-4 text-start py-4" data-index="${index}">
                    <span class="fw-semibold">${option}</span>
                </button>
            `;
            optionsContainer.appendChild(card);
        });

        document.querySelectorAll('.quiz-option').forEach(button => {
            button.addEventListener('click', () => {
                quizAnswers[currentStep] = button.textContent.trim();
                currentStep += 1;
                if (currentStep < quizQuestions.length) {
                    renderStep();
                } else {
                    renderResult();
                }
            });
        });
    };

    const renderResult = () => {
        progress.style.width = '100%';
        stepLabel.textContent = quizQuestions.length;
        const selected = quizAnswers.join(' Â· ');
        content.innerHTML = `
            <div class="text-center mb-4">
                <h2 class="h4 fw-bold">Resultados del quiz</h2>
                <p class="text-muted">Gracias por responder. Tu perfil de bienestar queda asociado con:</p>
            </div>
            <div class="result-panel p-4 rounded-4 bg-surface border border-secondary-subtle mb-4">
                <h3 class="h5 fw-semibold mb-3">Tu recomendaciÃ³n personalizada</h3>
                <p class="mb-0">Basado en tus respuestas, los productos formulados para <strong>claridad mental suave</strong> y <strong>apoyo de energÃ­as sostenidas</strong> son los mÃ¡s adecuados para ti.</p>
            </div>
            <div class="d-grid gap-3">
                <div class="card p-4 rounded-4 shadow-sm">
                    <p class="mb-1 text-muted">Tu estilo:</p>
                    <p class="fw-semibold mb-0">${selected}</p>
                </div>
                <a href="<?= site_url('catalogo'); ?>" class="btn btn-primary btn-lg">Ver productos recomendados</a>
            </div>
        `;
    };

    renderStep();
</script>
