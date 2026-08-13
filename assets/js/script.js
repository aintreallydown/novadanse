window.addEventListener('DOMContentLoaded', function() {


    $(function () {
        $('.menu-icon').on('click', function () {
            $(this).toggleClass('isopened');
        });
    });


    if (window.innerWidth < 1200) {

        const figures = document.querySelectorAll('#nos-cours figure');
        let currentIndex = 0;

        figures.forEach((figure, index) => {
            const img = figure.querySelector('img');
            const figcaption = figure.querySelector('figcaption');
            img.style.opacity = index === 0 ? 1 : 0;
            figcaption.style.opacity = index === 0 ? 1 : 0;
        });

        function showNextImage() {
            const currentFigure = figures[currentIndex];
            currentFigure.querySelector('img').style.opacity = 0;
            currentFigure.querySelector('figcaption').style.opacity = 0;

            currentIndex = (currentIndex + 1) % figures.length;

            const nextFigure = figures[currentIndex];
            nextFigure.querySelector('img').style.opacity = 1;
            nextFigure.querySelector('figcaption').style.opacity = 1;
        }

        setInterval(showNextImage, 3000);
    }

    const toggle = document.querySelector('.mobile-toggle');
    const sidenav = document.getElementById('sidenav');
    const overlay = document.querySelector('.sidenav-overlay');

    function closeSidenav() {
        sidenav.classList.remove('is-open');
        overlay.classList.remove('is-visible');
        toggle.setAttribute('aria-expanded', 'false');
    }

    function openSidenav() {
        sidenav.classList.add('is-open');
        overlay.classList.add('is-visible');
        toggle.setAttribute('aria-expanded', 'true');
    }

    toggle.addEventListener('click', () => {
        const isOpen = sidenav.classList.contains('is-open');
        isOpen ? closeSidenav() : openSidenav();
    });

    overlay.addEventListener('click', closeSidenav);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeSidenav();
    });


  const form = document.getElementById('adhesionForm');
  if (!form) return;

  const steps = Array.from(form.querySelectorAll('.step'));
  const stepperItems = Array.from(document.querySelectorAll('.stepper__item'));
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');
  const submitBtn = document.getElementById('submitBtn');
  const successPanel = document.getElementById('success');
  const successName = document.getElementById('successName');
  const resetBtn = document.getElementById('resetBtn');
  const recapEl = document.getElementById('recap');
  const cardBody = form; // conteneur du formulaire lui-même

  let currentStep = 0;

  /* --------------------------------------------------------------------
     Navigation entre étapes
     -------------------------------------------------------------------- */

  function goToStep(index) {
    steps.forEach((step, i) => {
      step.classList.toggle('is-current', i === index);
    });

    stepperItems.forEach((item, i) => {
      item.classList.toggle('is-active', i === index);
      item.classList.toggle('is-done', i < index);
    });

    prevBtn.disabled = index === 0;

    const isLastStep = index === steps.length - 1;
    nextBtn.classList.toggle('is-hidden', isLastStep);
    submitBtn.classList.toggle('is-hidden', !isLastStep);

    currentStep = index;

    if (isLastStep) {
      buildRecap();
    }
  }

  /* --------------------------------------------------------------------
     Validation simple par étape
     -------------------------------------------------------------------- */

  function validateStep(index) {
    const currentStepEl = steps[index];
    const fields = currentStepEl.querySelectorAll('.input[name]');
    let isValid = true;

    fields.forEach((field) => {
      field.classList.remove('input--error');

      // On ignore les champs non requis fonctionnellement,
      // mais on vérifie que les champs visibles ne sont pas vides.
      if (!field.value.trim()) {
        field.classList.add('input--error');
        isValid = false;
      }
    });

    return isValid;
  }

  /* --------------------------------------------------------------------
     Construction du récapitulatif (étape 4)
     -------------------------------------------------------------------- */

  function buildRecap() {
    if (!recapEl) return;

    const data = new FormData(form);
    const get = (name) => (data.get(name) || '').toString().trim();

    const prenom = get('prenom');
    const nom = get('nom');
    const naissance = get('naissance');
    const email = get('email');
    const tel = get('tel');
    const adresse = get('adresse');
    const cours = get('cours');
    const urgenceNom = get('urgenceNom');
    const urgenceTel = get('urgenceTel');
    const certificat = form.querySelector('[name="certificat"]').checked;
    const autorisation = form.querySelector('[name="autorisation"]').checked;

    recapEl.innerHTML = `
      <p><strong>${prenom} ${nom}</strong>${naissance ? ' · né(e) le ' + formatDate(naissance) : ''}</p>
      <p>${email || '—'} · ${tel || '—'}</p>
      <p>${adresse || '—'}</p>
      <p>Cours choisi : <strong>${cours || '—'}</strong></p>
      <p>Urgence : ${urgenceNom || '—'} (${urgenceTel || '—'})</p>
      <p>Certificat médical : ${certificat ? 'Oui' : 'Non'} · Autorisation parentale : ${autorisation ? 'Oui' : 'Non'}</p>
    `;
  }

  function formatDate(isoDate) {
    if (!isoDate) return '';
    const [year, month, day] = isoDate.split('-');
    return `${day}/${month}/${year}`;
  }

  /* --------------------------------------------------------------------
     Écouteurs des boutons
     -------------------------------------------------------------------- */

  nextBtn.addEventListener('click', () => {
    if (validateStep(currentStep) && currentStep < steps.length - 1) {
      goToStep(currentStep + 1);
    }
  });

  prevBtn.addEventListener('click', () => {
    if (currentStep > 0) {
      goToStep(currentStep - 1);
    }
  });

  /* --------------------------------------------------------------------
     Soumission du formulaire
     -------------------------------------------------------------------- */

  form.addEventListener('submit', (event) => {
    event.preventDefault();

    if (!validateStep(currentStep)) return;

    const data = new FormData(form);
    const prenom = (data.get('prenom') || '').toString().trim();
    const nom = (data.get('nom') || '').toString().trim();

    if (successName) {
      successName.textContent = `${prenom} ${nom}`.trim() || '—';
    }

    cardBody.classList.add('is-hidden');
    if (successPanel) successPanel.classList.add('is-active');
  });

  /* --------------------------------------------------------------------
     Nouveau dossier (réinitialisation)
     -------------------------------------------------------------------- */

  if (resetBtn) {
    resetBtn.addEventListener('click', () => {
      form.reset();
      cardBody.classList.remove('is-hidden');
      if (successPanel) successPanel.classList.remove('is-active');
      goToStep(0);
    });
  }

  /* --------------------------------------------------------------------
     Initialisation
     -------------------------------------------------------------------- */

  goToStep(0);
});