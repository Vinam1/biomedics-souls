<section class="page-shell py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="quiz-card p-4 p-lg-5 rounded-4 shadow-sm" data-animate="fade-up">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                        <div>
                            <span class="eyebrow-pill mb-3">Recomendaci&oacute;n guiada</span>
                            <h1 class="h3 fw-bold mb-2">Quiz interactivo</h1>
                            <p class="text-muted mb-0">Responde una pregunta por pantalla y descubre cu&aacute;l es tu mejor ruta de bienestar.</p>
                        </div>
                        <span class="badge bg-primary bg-opacity-15 text-primary quiz-step-badge">Paso <span id="quiz-step">1</span> / 3</span>
                    </div>

                    <div class="progress quiz-progress mb-4">
                        <div id="quizProgress" class="progress-bar rounded-pill bg-primary" role="progressbar" style="width: 0%"></div>
                    </div>

                    <div id="quiz-content"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
const quizQuestions = [
    {
        question: '&iquest;Cu&aacute;l es tu prioridad de salud m&aacute;s importante ahora?',
        options: [
            { label: 'Enfoque cognitivo', products: [3, 8, 9] },
            { label: 'Energ&iacute;a y vitalidad', products: [5, 9, 1] },
            { label: 'Relajaci&oacute;n y sue&ntilde;o', products: [3, 6, 7] },
            { label: 'Soporte inmunol&oacute;gico', products: [2, 6, 8] },
        ],
    },
    {
        question: '&iquest;Prefieres resultados r&aacute;pidos o un cambio gradual y sostenible?',
        options: [
            { label: 'Resultados r&aacute;pidos', products: [5, 1, 11] },
            { label: 'Evoluci&oacute;n constante', products: [7, 8, 9] },
            { label: 'Algo equilibrado', products: [2, 6, 3] },
            { label: 'No lo s&eacute; a&uacute;n', products: [2, 6, 8] },
        ],
    },
    {
        question: '&iquest;Qu&eacute; estilo de rutina te resulta m&aacute;s c&oacute;modo?',
        options: [
            { label: 'Una sola c&aacute;psula diaria', products: [3, 8, 9] },
            { label: 'Dos dosis al d&iacute;a', products: [5, 6, 2] },
            { label: 'Batidos o mezclas', products: [7, 1, 2] },
            { label: 'Ayuno intermitente', products: [1, 9, 4] },
        ],
    },
];

const quizAnswers = [];
const quizSelections = [];
let currentStep = 0;
const catalogUrl = '<?= site_url('catalogo'); ?>';

const content = document.getElementById('quiz-content');
const progress = document.getElementById('quizProgress');
const stepLabel = document.getElementById('quiz-step');

const renderStep = () => {
    const item = quizQuestions[currentStep];
    stepLabel.textContent = currentStep + 1;
    progress.style.width = `${((currentStep) / quizQuestions.length) * 100}%`;

    content.innerHTML = `
        <div class="mb-4" data-animate="fade-up">
            <h2 class="h4 fw-semibold">${item.question}</h2>
        </div>
        <div class="row row-cols-1 row-cols-md-2 g-3" id="quizOptions"></div>
    `;

    const optionsContainer = document.getElementById('quizOptions');
    item.options.forEach((option, index) => {
        const card = document.createElement('div');
        card.className = 'col';
        card.innerHTML = `
            <button type="button" class="quiz-option btn w-100 rounded-4 text-start py-4" data-index="${index}" data-animate="fade-up" data-animate-delay="${index * 70}">
                <span class="fw-semibold">${option.label}</span>
            </button>
        `;
        optionsContainer.appendChild(card);
    });

    if (window.initBiomedicsAnimations) {
        window.initBiomedicsAnimations(content);
    }

    document.querySelectorAll('.quiz-option').forEach((button) => {
        button.addEventListener('click', () => {
            const selectedOption = quizQuestions[currentStep].options[Number(button.dataset.index)];
            quizAnswers[currentStep] = selectedOption.label;
            quizSelections[currentStep] = selectedOption;
            currentStep += 1;
            if (currentStep < quizQuestions.length) {
                renderStep();
            } else {
                renderResult();
            }
        });
    });
};

const getRecommendedProducts = () => {
    const scores = new Map();

    quizSelections.forEach((selection) => {
        (selection?.products || []).forEach((productId, index) => {
            const currentScore = scores.get(productId) || 0;
            scores.set(productId, currentScore + (3 - index));
        });
    });

    return Array.from(scores.entries())
        .sort((first, second) => second[1] - first[1])
        .slice(0, 4)
        .map(([productId]) => productId);
};

const renderResult = () => {
    progress.style.width = '100%';
    stepLabel.textContent = quizQuestions.length;
    const selected = quizAnswers.join(' &middot; ');
    const recommendedProducts = getRecommendedProducts();
    const recommendedUrl = `${catalogUrl}&recommended=${encodeURIComponent(recommendedProducts.join(','))}`;

    content.innerHTML = `
        <div class="text-center mb-4" data-animate="fade-up">
            <h2 class="h4 fw-bold">Resultados del quiz</h2>
            <p class="text-muted">Gracias por responder. Tu perfil de bienestar queda asociado con:</p>
        </div>
        <div class="result-panel p-4 rounded-4 border border-secondary-subtle mb-4" data-animate="fade-up" data-animate-delay="100">
            <div class="d-flex align-items-center gap-3 mb-3">
                <i class="bi bi-stars fs-3 text-primary"></i>
                <h3 class="h5 fw-semibold mb-0">Tu recomendaci&oacute;n personalizada</h3>
            </div>
            <p class="mb-0">Basado en tus respuestas, preparamos una selecci&oacute;n de productos alineados con tus prioridades y tu estilo de rutina.</p>
        </div>
        <div class="d-grid gap-3">
            <div class="section-card p-4" data-animate="fade-up" data-animate-delay="160">
                <p class="mb-1 text-muted">Tu estilo:</p>
                <p class="fw-semibold mb-0">${selected}</p>
            </div>
            <a href="${recommendedUrl}" class="btn btn-primary btn-lg" data-animate="fade-up" data-animate-delay="220">Ver productos recomendados</a>
        </div>
    `;

    if (window.initBiomedicsAnimations) {
        window.initBiomedicsAnimations(content);
    }
};

renderStep();
</script>
