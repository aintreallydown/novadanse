window.addEventListener('DOMContentLoaded', function () {



  // burger menu toggle

  $(function () {
    $('.menu-icon').on('click', function () {
      $(this).toggleClass('isopened');
    });
  });




  // number animation


  const numbers = document.querySelectorAll('.banner-number');
  const duration = 1500; // durée de l'animation en ms

  function animateNumber(el) {
    const raw = el.textContent.trim();

    // Cas "3-99" : on anime chaque partie séparément
    if (raw.includes('-')) {
      const [start, end] = raw.split('-').map(n => parseInt(n, 10));
      animateRange(el, start, end, duration);
      return;
    }

    // Cas "120+" : on garde le suffixe
    const suffix = raw.match(/[^\d]+$/)?.[0] || '';
    const target = parseInt(raw, 10);
    animateCount(el, target, suffix, duration);
  }

  function animateCount(el, target, suffix, duration) {
    const startTime = performance.now();

    function update(now) {
      const elapsed = now - startTime;
      const progress = Math.min(elapsed / duration, 1);
      const easedProgress = 1 - Math.pow(1 - progress, 3); // ease-out cubic
      const current = Math.floor(easedProgress * target);

      el.textContent = current + suffix;

      if (progress < 1) {
        requestAnimationFrame(update);
      } else {
        el.textContent = target + suffix; // valeur finale exacte
      }
    }

    requestAnimationFrame(update);
  }

  // Carroussel de photo animé pour mobile

  function animateRange(el, start, end, duration) {
    const startTime = performance.now();

    function update(now) {
      const elapsed = now - startTime;
      const progress = Math.min(elapsed / duration, 1);
      const easedProgress = 1 - Math.pow(1 - progress, 3);
      const currentEnd = Math.floor(start + easedProgress * (end - start));

      el.textContent = `${start}-${currentEnd}`;

      if (progress < 1) {
        requestAnimationFrame(update);
      } else {
        el.textContent = `${start}-${end}`;
      }
    }

    requestAnimationFrame(update);
  }

  // Déclenchement au scroll (une seule fois par élément)
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        animateNumber(entry.target);
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.3 });

  numbers.forEach(el => observer.observe(el));


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

  // navigation de coté pour mobile

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


  // stepper de formulaire d'adhésion


  // stepper de formulaire d'adhésion

  const form = document.getElementById('adhesionForm');
  if (!form) return;

  const steps = Array.from(form.querySelectorAll('.step'));
  const stepperItems = Array.from(document.querySelectorAll('.stepper__item'));
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');
  const submitBtn = document.getElementById('submitBtn');
  const addAdherentBtn = document.getElementById('addAdherentBtn');
  const successPanel = document.getElementById('success');
  const successName = document.getElementById('successName');
  const resetBtn = document.getElementById('resetBtn');
  const recapEl = document.getElementById('recap');
  const cardBody = form;

  let currentStep = 0;
  const adherents = []; // adhérents déjà validés dans ce dossier

  const IDENTITY_FIELDS = ['prenom', 'nom', 'naissance', 'cours', 'urgenceNom', 'urgenceTel', 'autorisation', 'reglementInterieur'];
  const QS_SPORT_QUESTIONS = ['qs1', 'qs2', 'qs3', 'qs4', 'qs5', 'qs6', 'qs7', 'qs8', 'qs9'];

  /* --------------------------------------------------------------------
     Navigation entre étapes
     -------------------------------------------------------------------- */

  function goToStep(index) {
    steps.forEach((step, i) => step.classList.toggle('is-current', i === index));

    stepperItems.forEach((item, i) => {
      item.classList.toggle('is-active', i === index);
      item.classList.toggle('is-done', i < index);
    });

    prevBtn.disabled = index === 0;

    const isLastStep = index === steps.length - 1;
    nextBtn.classList.toggle('is-hidden', isLastStep);
    submitBtn.classList.toggle('is-hidden', !isLastStep);

    currentStep = index;

    if (isLastStep) buildRecap();
  }

  /* --------------------------------------------------------------------
     Validation simple par étape
     -------------------------------------------------------------------- */

  function validateStep(index) {
    const currentStepEl = steps[index];
    const fields = currentStepEl.querySelectorAll('.input[name]:not([data-optional="true"])');
    let isValid = true;

    fields.forEach((field) => {
      const isHidden = field.offsetParent === null;
      if (isHidden) return;

      field.classList.remove('input--error');
      if (!field.value.trim()) {
        field.classList.add('input--error');
        isValid = false;
      }
    });

    // Étape "Cours & santé" (index 2) : questionnaire santé + règlement intérieur obligatoires
    if (index === 2) {
      if (!isQsSportComplete()) {
        isValid = false;

        const resultEl = document.getElementById('qsSportResult');
        if (resultEl) {
          resultEl.className = 'qs-sport__result is-warning';
          resultEl.textContent = 'Merci de répondre à toutes les questions du questionnaire de santé avant de continuer.';
        }

        const body = document.getElementById('qsSportBody');
        const toggle = document.getElementById('qsSportToggle');
        if (body && !body.classList.contains('is-open')) {
          body.classList.add('is-open');
          if (toggle) toggle.setAttribute('aria-expanded', 'true');
        }
      }

      const reglementCheckbox = form.querySelector('[name="reglementInterieur"]');
      if (reglementCheckbox && !reglementCheckbox.checked) {
        isValid = false;
        reglementCheckbox.closest('.check')?.classList.add('input--error');
      } else if (reglementCheckbox) {
        reglementCheckbox.closest('.check')?.classList.remove('input--error');
      }
    }

    return isValid;
  }

  /* --------------------------------------------------------------------
     Questionnaire santé (QS Sport)
     -------------------------------------------------------------------- */

  function initQsSport() {
    const toggle = document.getElementById('qsSportToggle');
    const body = document.getElementById('qsSportBody');
    const resultEl = document.getElementById('qsSportResult');
    const uploadWrapper = document.getElementById('certificatUploadWrapper');
    const checkAllNon = document.getElementById('qsSportCheckAllNon');

    if (!toggle || !body || !resultEl || !uploadWrapper) {
      console.warn('qs-sport: un ou plusieurs éléments introuvables dans le DOM');
      return;
    }

    toggle.addEventListener('click', () => {
      const isOpen = body.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', isOpen);
    });

    function evaluateQsSport() {
      const answers = QS_SPORT_QUESTIONS.map(name => {
        const checked = form.querySelector(`input[name="${name}"]:checked`);
        return checked ? checked.value : null;
      });

      const allAnswered = answers.every(v => v !== null);
      const hasOui = answers.includes('oui');

      if (!allAnswered) {
        resultEl.className = 'qs-sport__result';
        resultEl.textContent = '';
        uploadWrapper.classList.remove('is-visible');
        return;
      }

      if (hasOui) {
        resultEl.className = 'qs-sport__result is-warning';
        resultEl.textContent = "D'après vos réponses, un certificat médical est nécessaire. Consultez un médecin et présentez-lui ce questionnaire.";
        uploadWrapper.classList.add('is-visible');
      } else {
        resultEl.className = 'qs-sport__result is-ok';
        resultEl.textContent = "D'après vos réponses, aucun certificat médical n'est requis.";
        uploadWrapper.classList.remove('is-visible');
      }
    }

    QS_SPORT_QUESTIONS.forEach(name => {
      form.querySelectorAll(`input[name="${name}"]`).forEach(radio => {
        radio.addEventListener('change', () => {
          if (checkAllNon.checked) checkAllNon.checked = false;
          evaluateQsSport();
        });
      });
    });

    if (checkAllNon) {
      checkAllNon.addEventListener('change', () => {
        if (checkAllNon.checked) {
          QS_SPORT_QUESTIONS.forEach(name => {
            const nonRadio = form.querySelector(`input[name="${name}"][value="non"]`);
            if (nonRadio) nonRadio.checked = true;
          });
          evaluateQsSport();
        }
      });
    }
  }

  function isQsSportComplete() {
    return QS_SPORT_QUESTIONS.every(name => form.querySelector(`input[name="${name}"]:checked`));
  }

  /* --------------------------------------------------------------------
     Gestion multi-adhérents
     -------------------------------------------------------------------- */

  function getCurrentAdherentData() {
    const data = new FormData(form);
    const get = (name) => (data.get(name) || '').toString().trim();

    const certificatRequis = QS_SPORT_QUESTIONS.some(
      name => form.querySelector(`input[name="${name}"]:checked`)?.value === 'oui'
    );

    const certificatFileInput = form.querySelector('[name="certificatFile"]');
    const certificatUploade = certificatFileInput ? certificatFileInput.files.length > 0 : false;

    const droitImageRadio = form.querySelector('input[name="droitImage"]:checked');
    const droitImage = droitImageRadio ? droitImageRadio.value === 'oui' : false;

    return {
      prenom: get('prenom'),
      nom: get('nom'),
      naissance: get('naissance'),
      cours: get('cours'),
      urgenceNom: get('urgenceNom'),
      urgenceTel: get('urgenceTel'),
      passSport: get('passSport'),
      certificatRequis,
      certificatUploade,
      droitImage,
      autorisation: form.querySelector('[name="autorisation"]')?.checked ?? false,
      reglementInterieur: form.querySelector('[name="reglementInterieur"]')?.checked ?? false,
    };
  }

  function resetIdentityFields() {
    IDENTITY_FIELDS.forEach((name) => {
      const field = form.querySelector(`[name="${name}"]`);
      if (!field) return;
      if (field.type === 'checkbox') {
        field.checked = false;
      } else {
        field.value = '';
      }
      field.classList.remove('input--error');
    });

    // Reset du droit à l'image (radio)
    form.querySelectorAll('input[name="droitImage"]').forEach(radio => {
      radio.checked = false;
    });

    // Reset des réponses du questionnaire santé + fichier certificat
    QS_SPORT_QUESTIONS.forEach((name) => {
      form.querySelectorAll(`input[name="${name}"]`).forEach(radio => {
        radio.checked = false;
      });
    });

    const certificatFileInput = form.querySelector('[name="certificatFile"]');
    if (certificatFileInput) certificatFileInput.value = '';

    const checkAllNon = document.getElementById('qsSportCheckAllNon');
    if (checkAllNon) checkAllNon.checked = false;

    const resultEl = document.getElementById('qsSportResult');
    const uploadWrapper = document.getElementById('certificatUploadWrapper');
    if (resultEl) {
      resultEl.className = 'qs-sport__result';
      resultEl.textContent = '';
    }
    if (uploadWrapper) uploadWrapper.classList.remove('is-visible');
  }

  function addAdherent() {
    // On valide identité (0) et cours/santé (2) avant d'ajouter
    if (!validateStep(0)) { goToStep(0); return; }
    if (!validateStep(2)) { goToStep(2); return; }

    adherents.push(getCurrentAdherentData());
    resetIdentityFields();
    goToStep(0);
  }

  /* --------------------------------------------------------------------
     Construction du récapitulatif (étape 4)
     -------------------------------------------------------------------- */

  function buildRecap() {
    if (!recapEl) return;

    const data = new FormData(form);
    const get = (name) => (data.get(name) || '').toString().trim();

    const email = get('email');
    const tel = get('tel');
    const adresse = get('adresse');

    const currentAdherent = getCurrentAdherentData();
    const allAdherents = [...adherents, currentAdherent];

    const adherentsHtml = allAdherents.map((a, i) => `
  <div class="recap__adherent">
    <p><strong>${i + 1}. ${a.prenom} ${a.nom}</strong>${a.naissance ? ' · né(e) le ' + formatDate(a.naissance) : ''}</p>
    <p>Cours choisi : <strong>${a.cours || '—'}</strong>${a.passSport ? ' · Pass\'Sport : ' + a.passSport : ''}</p>
    <p>Urgence : ${a.urgenceNom || '—'} (${a.urgenceTel || '—'})</p>
    <p>Certificat médical : ${a.certificatRequis ? 'Oui' : 'Non'} · Fichier certificat uploadé : ${a.certificatUploade ? 'Oui' : 'Non'}</p>
    <p>Autorisation parentale : ${a.autorisation ? 'Oui' : 'Non'}</p>
    <p>Droit à l'image : ${a.droitImage ? 'Oui' : 'Non'}</p>
    <p>Règlement intérieur accepté : ${a.reglementInterieur ? 'Oui' : 'Non'}</p>
  </div>
`).join('<hr class="recap__sep">');

    recapEl.innerHTML = `
      <p>${email || '—'} · ${tel || '—'}</p>
      <p>${adresse || '—'}</p>
      <hr class="recap__sep">
      ${adherentsHtml}
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
    if (currentStep > 0) goToStep(currentStep - 1);
  });

  if (addAdherentBtn) {
    addAdherentBtn.addEventListener('click', addAdherent);
  }

  /* --------------------------------------------------------------------
     Soumission du formulaire
     -------------------------------------------------------------------- */

  form.addEventListener('submit', (event) => {
    event.preventDefault();
    if (!validateStep(currentStep)) return;

    const allAdherents = [...adherents, getCurrentAdherentData()];
    const first = allAdherents[0];

    if (successName) {
      successName.textContent = allAdherents.length > 1
        ? `${first.prenom} ${first.nom} et ${allAdherents.length - 1} autre(s)`
        : `${first.prenom} ${first.nom}`.trim() || '—';
    }

    // TODO: envoyer `allAdherents` + coordonnées communes (email/tel/adresse) au backend

    cardBody.classList.add('is-hidden');
    if (successPanel) successPanel.classList.add('is-active');
  });

  /* --------------------------------------------------------------------
     Nouveau dossier (réinitialisation)
     -------------------------------------------------------------------- */

  if (resetBtn) {
    resetBtn.addEventListener('click', () => {
      form.reset();
      adherents.length = 0;
      cardBody.classList.remove('is-hidden');
      if (successPanel) successPanel.classList.remove('is-active');
      goToStep(0);
    });
  }

  /* --------------------------------------------------------------------
     Initialisation
     -------------------------------------------------------------------- */

  goToStep(0);
  initQsSport();
});