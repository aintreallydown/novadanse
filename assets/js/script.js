window.addEventListener('DOMContentLoaded', function () {


  // burger menu toggle
  $(function () {
    $('.menu-icon').on('click', function () {
      $(this).toggleClass('isopened');
    });
  });

  // snackbar notification


  function initSnackbars() {
    const snackbars = document.querySelectorAll('[data-snackbar]');

    snackbars.forEach((snackbar, index) => {
      // Petit délai échelonné si plusieurs messages en même temps
      setTimeout(() => {
        snackbar.classList.add('is-visible');
      }, 50 + index * 100);

      // Disparition automatique après 4 secondes
      setTimeout(() => {
        snackbar.classList.remove('is-visible');
        setTimeout(() => snackbar.remove(), 300); // laisse le temps à la transition CSS
      }, 4000 + index * 100);
    });
  }

  initSnackbars();

  // number animation
  const numbers = document.querySelectorAll('.banner-number');
  const duration = 1500;

  function animateNumber(el) {
    const raw = el.textContent.trim();
    if (raw.includes('-')) {
      const [start, end] = raw.split('-').map(n => parseInt(n, 10));
      animateRange(el, start, end, duration);
      return;
    }
    const suffix = raw.match(/[^\d]+$/)?.[0] || '';
    const target = parseInt(raw, 10);
    animateCount(el, target, suffix, duration);
  }

  function animateCount(el, target, suffix, duration) {
    const startTime = performance.now();
    function update(now) {
      const elapsed = now - startTime;
      const progress = Math.min(elapsed / duration, 1);
      const easedProgress = 1 - Math.pow(1 - progress, 3);
      const current = Math.floor(easedProgress * target);
      el.textContent = current + suffix;
      if (progress < 1) requestAnimationFrame(update);
      else el.textContent = target + suffix;
    }
    requestAnimationFrame(update);
  }

  function animateRange(el, start, end, duration) {
    const startTime = performance.now();
    function update(now) {
      const elapsed = now - startTime;
      const progress = Math.min(elapsed / duration, 1);
      const easedProgress = 1 - Math.pow(1 - progress, 3);
      const currentEnd = Math.floor(start + easedProgress * (end - start));
      el.textContent = `${start}-${currentEnd}`;
      if (progress < 1) requestAnimationFrame(update);
      else el.textContent = `${start}-${end}`;
    }
    requestAnimationFrame(update);
  }

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

  // navigation mobile
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

  if (toggle && sidenav && overlay) {
    toggle.addEventListener('click', () => {
      const isOpen = sidenav.classList.contains('is-open');
      isOpen ? closeSidenav() : openSidenav();
    });
    overlay.addEventListener('click', closeSidenav);
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeSidenav();
    });
  }

  /* ====================================================================
    STEPPER — Formulaire d'adhésion (collection d'adhérents)
    ==================================================================== */

  const form = document.getElementById('adhesionForm');
  if (!form) return;

  const steps = Array.from(form.querySelectorAll('.step'));
  const stepperItems = Array.from(document.querySelectorAll('.stepper__item'));
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');
  const submitBtn = document.getElementById('submitBtn');
  const adherentsCollection = document.getElementById('adherentsCollection');
  const addAdherentBtn = document.getElementById('addAdherentBtn');

  let currentStep = 0;

  const QS_SPORT_QUESTIONS = ['qs1', 'qs2', 'qs3', 'qs4', 'qs5', 'qs6', 'qs7', 'qs8', 'qs9'];

  /* ---------------- Navigation entre étapes ---------------- */

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

  /* ---------------- Validation par étape ---------------- */

  function validateStep(index) {
    const currentStepEl = steps[index];
    let isValid = true;

    // Étapes 0 et 1 : ne valider que les champs réellement "required" en HTML
    if (index === 0 || index === 1) {
      const fields = currentStepEl.querySelectorAll('.input[name][required]');

      fields.forEach((field) => {
        const isHidden = field.offsetParent === null;
        if (isHidden) return;

        field.classList.remove('input--error');
        const errorEl = document.getElementById(`error-${field.id.replace('field-', '')}`);
        if (errorEl) errorEl.textContent = '';

        if (!field.value.trim()) {
          field.classList.add('input--error');
          isValid = false;
          return;
        }

        if (field.name.endsWith('[email]')) {
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailRegex.test(field.value.trim())) {
            field.classList.add('input--error');
            if (errorEl) errorEl.textContent = 'Merci de saisir une adresse email valide.';
            isValid = false;
          }
        }

        if (field.name.endsWith('[dateOfBirth]')) {
          const inputDate = new Date(field.value);
          const today = new Date();
          today.setHours(0, 0, 0, 0);

          const minDate = new Date();
          minDate.setFullYear(minDate.getFullYear() - 120);

          if (isNaN(inputDate.getTime()) || inputDate >= today || inputDate < minDate) {
            field.classList.add('input--error');
            if (errorEl) errorEl.textContent = 'Merci de vérifier la date de naissance saisie.';
            isValid = false;
          }
        }
      });

      const addressField = currentStepEl.querySelector('#registration_user_address');
      if (addressField) {
        addressField.classList.remove('input--error');

        if (!addressField.isAddressValid || !addressField.isAddressValid()) {
          addressField.classList.add('input--error');
          isValid = false;
        }
      }
    }

    // Étape 2 : validation de chaque bloc adhérent (cours, urgence, QS-Sport, consentements)
    if (index === 2) {
      const adherentBlocks = adherentsCollection.querySelectorAll('.adherent-block');

      adherentBlocks.forEach((block) => {
        const blockIndex = block.dataset.adherentIndex;

        const fields = block.querySelectorAll('.input[name][required]');
        fields.forEach((field) => {
          const isHidden = field.offsetParent === null;
          if (isHidden) return;

          field.classList.remove('input--error');
          if (!field.value.trim()) {
            field.classList.add('input--error');
            isValid = false;
          }
        });

        if (!isQsSportComplete(blockIndex)) {
          isValid = false;

          const resultEl = block.querySelector(`[data-qs-result="${blockIndex}"]`);
          if (resultEl) {
            resultEl.className = 'qs-sport__result is-warning';
            resultEl.textContent = 'Merci de répondre à toutes les questions du questionnaire de santé avant de continuer.';
          }

          const body = block.querySelector(`[data-qs-body="${blockIndex}"]`);
          const toggle = block.querySelector(`[data-qs-toggle="${blockIndex}"]`);
          if (body && !body.classList.contains('is-open')) {
            body.classList.add('is-open');
            if (toggle) toggle.setAttribute('aria-expanded', 'true');
          }
        }

        const consentCgvCheckbox = block.querySelector('input[name$="[cgv]"]');
        if (consentCgvCheckbox && !consentCgvCheckbox.checked) {
          isValid = false;
          consentCgvCheckbox.closest('.check')?.classList.add('input--error');
        } else if (consentCgvCheckbox) {
          consentCgvCheckbox.closest('.check')?.classList.remove('input--error');
        }

        const reglementCheckbox = block.querySelector('input[name$="[reglementInterieur]"]');
        if (reglementCheckbox && !reglementCheckbox.checked) {
          isValid = false;
          reglementCheckbox.closest('.check')?.classList.add('input--error');
        } else if (reglementCheckbox) {
          reglementCheckbox.closest('.check')?.classList.remove('input--error');
        }
      });
    }

    return isValid;
  }

  /* ---------------- Sélecteur de cours (par bloc adhérent) ---------------- */

  function initCoursSelector(block, index) {
    const coursList = block.querySelector(`[data-cours-list="${index}"]`);
    if (!coursList) return;

    const firstSelect = coursList.querySelector('.cours-select');
    if (!firstSelect) return;

    const optionsHtml = firstSelect.innerHTML;
    const fieldName = firstSelect.getAttribute('name');

    coursList.addEventListener('click', (event) => {
      const addBtn = event.target.closest('[data-add-cours]');
      if (addBtn) {
        addCoursRow(coursList, optionsHtml, fieldName, index);
        return;
      }

      const removeBtn = event.target.closest('[data-remove-cours]');
      if (removeBtn) {
        removeBtn.closest('[data-cours-row]').remove();
        updateCoursTotal(block, index);
      }
    });

    coursList.addEventListener('change', (event) => {
      if (event.target.classList.contains('cours-select')) {
        updateCoursTotal(block, index);
      }
    });
  }

  function addCoursRow(coursList, optionsHtml, fieldName, index) {
    const row = document.createElement('div');
    row.className = 'cours-row p-5';
    row.setAttribute('data-cours-row', '');
    row.innerHTML = `
    <select class="cours-select" name="${fieldName}">${optionsHtml}</select>
    <button type="button" class="btn-icon btn-icon--remove" data-remove-cours aria-label="Retirer ce cours">−</button>
  `;
    row.querySelector('select').value = '';
    coursList.appendChild(row);
  }

  function getSelectedCours(block) {
    return Array.from(block.querySelectorAll('.cours-select'))
      .map(select => {
        const option = select.options[select.selectedIndex];
        return option ? { value: select.value, label: option.textContent.trim() } : null;
      })
      .filter(item => item && item.value);
  }

  function updateCoursTotal(block, index) {
    const totalEl = block.querySelector(`[data-cours-total="${index}"]`);
    if (!totalEl) return;

    const selected = getSelectedCours(block);
    const total = selected.reduce((sum, item) => {
      const match = item.label.match(/(\d+)\s*€/);
      return sum + (match ? parseInt(match[1], 10) : 0);
    }, 0);

    totalEl.innerHTML = `<strong>Total : ${total}€</strong>`;
  }

  /* ---------------- Questionnaire santé (QS Sport) par bloc ---------------- */

  function initQsSport(block, index) {
    const toggle = block.querySelector(`[data-qs-toggle="${index}"]`);
    const body = block.querySelector(`[data-qs-body="${index}"]`);
    const resultEl = block.querySelector(`[data-qs-result="${index}"]`);
    const uploadWrapper = block.querySelector(`[data-qs-upload="${index}"]`);
    const checkAllNon = block.querySelector(`[data-qs-check-all="${index}"]`);

    if (!toggle || !body || !resultEl || !uploadWrapper) {
      console.warn(`qs-sport (bloc ${index}): un ou plusieurs éléments introuvables dans le DOM`);
      return;
    }

    toggle.addEventListener('click', () => {
      const isOpen = body.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', isOpen);
    });

    function evaluateQsSport() {
      const answers = QS_SPORT_QUESTIONS.map(name => {
        const checked = block.querySelector(`input[name="${name}_${index}"]:checked`);
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
      block.querySelectorAll(`input[name="${name}_${index}"]`).forEach(radio => {
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
            const nonRadio = block.querySelector(`input[name="${name}_${index}"][value="non"]`);
            if (nonRadio) nonRadio.checked = true;
          });
          evaluateQsSport();
        }
      });
    }
  }

  function isQsSportComplete(index) {
    const block = adherentsCollection.querySelector(`[data-adherent-index="${index}"]`);
    if (!block) return false;
    return QS_SPORT_QUESTIONS.every(name => block.querySelector(`input[name="${name}_${index}"]:checked`));
  }

  /* ---------------- Gestion de la collection d'adhérents (prototype Symfony) ---------------- */

  function updateRemoveButtonsState() {
    const blocks = adherentsCollection.querySelectorAll('.adherent-block');
    const removeButtons = adherentsCollection.querySelectorAll('[data-remove-adherent]');

    removeButtons.forEach((btn) => {
      btn.disabled = blocks.length <= 1;
    });
  }

  function initAdherentsCollection() {
    if (!adherentsCollection || !addAdherentBtn) return;

    const existingBlocks = adherentsCollection.querySelectorAll('.adherent-block');
    existingBlocks.forEach((block) => {
      const index = block.dataset.adherentIndex;
      initCoursSelector(block, index);
      initQsSport(block, index);
      updateCoursTotal(block, index);
    });

    updateRemoveButtonsState();

    addAdherentBtn.addEventListener('click', () => {
      const prototype = adherentsCollection.dataset.prototype;
      let newIndex = parseInt(adherentsCollection.dataset.index, 10);

      const newBlockHtml = prototype.replace(/__adherent_index__/g, newIndex);

      const wrapper = document.createElement('div');
      wrapper.innerHTML = newBlockHtml.trim();
      const newBlock = wrapper.firstElementChild;

      adherentsCollection.appendChild(newBlock);

      initCoursSelector(newBlock, newIndex);
      initQsSport(newBlock, newIndex);
      updateCoursTotal(newBlock, newIndex);

      adherentsCollection.dataset.index = newIndex + 1;

      updateRemoveButtonsState();
    });

    adherentsCollection.addEventListener('click', (event) => {
      const removeBtn = event.target.closest('[data-remove-adherent]');
      if (removeBtn) {
        const blocks = adherentsCollection.querySelectorAll('.adherent-block');

        if (blocks.length <= 1) return;

        removeBtn.closest('.adherent-block').remove();
        updateRemoveButtonsState();
      }
    });
  }

  /* ---------------- Autocomplétion adresse ---------------- */
  function initAdresseAutocomplete() {
    const input = document.getElementById('registration_user_address');
    const suggestionsEl = document.getElementById('adresseSuggestions');
    if (!input || !suggestionsEl) return;

    let debounceTimer = null;
    let abortController = null;
    let activeIndex = -1;
    let addressSelected = false;
    let selectedFeature = null;

    function closeSuggestions() {
      suggestionsEl.innerHTML = '';
      suggestionsEl.classList.remove('is-open');
      activeIndex = -1;
    }

    function selectAddress(feature) {
      input.value = feature.properties.label;
      addressSelected = true;
      selectedFeature = feature;
      input.classList.remove('input--error');
      closeSuggestions();
    }

    function renderSuggestions(features) {
      suggestionsEl.innerHTML = '';
      if (!features.length) {
        closeSuggestions();
        return;
      }
      features.forEach((feature) => {
        const li = document.createElement('li');
        li.className = 'adresse-suggestions__item';
        li.textContent = feature.properties.label;
        li.addEventListener('click', () => {
          selectAddress(feature);
        });
        suggestionsEl.appendChild(li);
      });
      suggestionsEl.classList.add('is-open');
    }

    async function fetchSuggestions(query) {
      if (abortController) abortController.abort();
      abortController = new AbortController();

      try {
        const url = `https://data.geopf.fr/geocodage/search?q=${encodeURIComponent(query)}&limit=5`;
        const response = await fetch(url, { signal: abortController.signal });
        if (!response.ok) throw new Error('Erreur API Adresse');

        const json = await response.json();
        renderSuggestions(json.features || []);
      } catch (err) {
        if (err.name !== 'AbortError') {
          console.warn('adresse-autocomplete:', err);
          closeSuggestions();
        }
      }
    }

    input.addEventListener('input', () => {
      // Dès que l'utilisateur retape quelque chose, l'adresse n'est plus considérée comme valide
      addressSelected = false;
      selectedFeature = null;

      const query = input.value.trim();
      clearTimeout(debounceTimer);
      if (query.length < 3) {
        closeSuggestions();
        return;
      }
      debounceTimer = setTimeout(() => fetchSuggestions(query), 300);
    });

    input.addEventListener('keydown', (event) => {
      const items = Array.from(suggestionsEl.querySelectorAll('.adresse-suggestions__item'));
      if (!items.length) return;

      if (event.key === 'ArrowDown') {
        event.preventDefault();
        activeIndex = (activeIndex + 1) % items.length;
        items.forEach((item, i) => item.classList.toggle('is-active', i === activeIndex));
      } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        activeIndex = (activeIndex - 1 + items.length) % items.length;
        items.forEach((item, i) => item.classList.toggle('is-active', i === activeIndex));
      } else if (event.key === 'Enter') {
        if (activeIndex >= 0) {
          event.preventDefault();
          items[activeIndex].click();
        }
      } else if (event.key === 'Escape') {
        closeSuggestions();
      }
    });

    document.addEventListener('click', (event) => {
      if (!input.contains(event.target) && !suggestionsEl.contains(event.target)) {
        closeSuggestions();
      }
    });

    // Expose une fonction de vérification utilisable dans validateStep()
    input.isAddressValid = () => addressSelected;
    input.getSelectedFeature = () => selectedFeature;
  }
  // remise et total final (étape 4)
  function calculateRecapTotal(adherentBlocks, bearerAdresse) {
    let totalBrut = 0;
    let nombreCoursTotal = 0;
    let nombreMineurs = 0;
    let nombrePassSport = 0;

    adherentBlocks.forEach((block) => {
      const cours = getSelectedCours(block);
      nombreCoursTotal += cours.length;

      cours.forEach((item) => {
        const match = item.label.match(/(\d+)\s*€/);
        if (match) totalBrut += parseInt(match[1], 10);
      });

      const dateOfBirthField = block.querySelector('input[name$="[dateOfBirth]"]');
      if (dateOfBirthField && dateOfBirthField.value) {
        const birthDate = new Date(dateOfBirthField.value);
        if (!isNaN(birthDate.getTime())) {
          const today = new Date();
          let age = today.getFullYear() - birthDate.getFullYear();
          const hasHadBirthdayThisYear =
            today.getMonth() > birthDate.getMonth() ||
            (today.getMonth() === birthDate.getMonth() && today.getDate() >= birthDate.getDate());
          if (!hasHadBirthdayThisYear) age -= 1;

          if (age < 16) nombreMineurs += 1;
        }
      }

      const passPortField = block.querySelector('input[name$="[passPortCode]"]');
      if (passPortField && passPortField.value.trim()) {
        nombrePassSport += 1;
      }
    });

    let tauxRemise = 0;
    if (nombreCoursTotal >= 3) {
      tauxRemise = 0.20;
    } else if (nombreCoursTotal === 2) {
      tauxRemise = 0.10;
    }

    const remise = totalBrut * tauxRemise;
    let totalApresRemise = totalBrut - remise;

    const adresseValue = bearerAdresse?.value || '';
    const estEstreesSaintDenis = /estr[ée]es?[\s-]*saint[\s-]*denis/i.test(adresseValue);

    let reductionVille = 0;
    if (estEstreesSaintDenis && nombreMineurs > 0) {
      reductionVille = 20 * nombreMineurs;
      totalApresRemise = Math.max(totalApresRemise - reductionVille, 0);
    }

    const reductionPassSport = nombrePassSport * 15;
    totalApresRemise = Math.max(totalApresRemise - reductionPassSport, 0);

    return {
      totalBrut,
      nombreCoursTotal,
      tauxRemise,
      remise,
      reductionVille,
      nombreMineurs,
      nombrePassSport,
      reductionPassSport,
      totalFinal: totalApresRemise,
    };
  }

  /* ---------------- Récapitulatif (étape 4) ---------------- */

  function buildRecap() {
    const recapEl = document.getElementById('recap');
    if (!recapEl) return;

    const bearerPrenom = document.getElementById('registration_user_prenom');
    const bearerNom = document.getElementById('registration_user_nom');
    const bearerEmail = document.getElementById('registration_user_email');
    const bearerTelephone = document.getElementById('registration_user_telephone');
    const bearerAdresse = document.getElementById('registration_user_address');

    const adherentBlocks = Array.from(adherentsCollection.querySelectorAll('.adherent-block'));

    const adherentsHtml = adherentBlocks.map((block) => {
      const index = block.dataset.adherentIndex;

      const prenom = block.querySelector(`input[name$="[prenom]"]`)?.value || '';
      const nom = block.querySelector(`input[name$="[nom]"]`)?.value || '';

      const cours = getSelectedCours(block);
      const coursLi = cours.length
        ? cours.map(c => `<li>${c.label}</li>`).join('')
        : '<li>Aucun cours sélectionné</li>';

      const certificatRequis = QS_SPORT_QUESTIONS.some(
        name => block.querySelector(`input[name="${name}_${index}"]:checked`)?.value === 'oui'
      );

      const checkedIn = (selector) => block.querySelector(selector)?.checked ?? false;

      const ancienAdherent = checkedIn('input[name$="[ancienAdherent]"]');
      const autorisation = checkedIn('input[name$="[autorisationParentale]"]');
      const droitImage = checkedIn('input[name$="[droitImage]"]');
      const consentCgv = checkedIn('input[name$="[cgv]"]');
      const reglementInterieur = checkedIn('input[name$="[reglementInterieur]"]');

      const passPortField = block.querySelector('input[name$="[passPortCode]"]');
      const passPortCode = passPortField?.value.trim() || '';

      return `
      <div class="recap__adherent">
        <ul>
          <li><strong>${prenom} ${nom}</strong></li>
          <li><strong>Cours choisi(s) :</strong><ul>${coursLi}</ul></li>
          <li><strong>Ancien(ne) adhérent(e) :</strong> ${ancienAdherent ? 'Oui' : 'Non'}</li>
          <li><strong>Certificat médical requis :</strong> ${certificatRequis ? 'Oui' : 'Non'}</li>
          <li><strong>Autorisation parentale :</strong> ${autorisation ? 'Oui' : 'Non'}</li>
          <li><strong>Droit à l'image :</strong> ${droitImage ? 'Oui' : 'Non'}</li>
          <li><strong>CGV acceptées :</strong> ${consentCgv ? 'Oui' : 'Non'}</li>
          <li><strong>Règlement intérieur accepté :</strong> ${reglementInterieur ? 'Oui' : 'Non'}</li>
          ${passPortCode ? `<li><strong>Code Pass'Sport :</strong> ${passPortCode} (-15€)</li>` : ''}
        </ul>
      </div>
    `;
    }).join('<hr class="recap__sep">');

    const totalInfo = calculateRecapTotal(adherentBlocks, bearerAdresse);

    const totalHtml = `
    <div class="recap__total">
      <p>Total brut : <strong>${totalInfo.totalBrut}€</strong></p>
      ${totalInfo.tauxRemise > 0
        ? `<p>Remise multi-cours (${totalInfo.tauxRemise * 100}%) : <strong>-${totalInfo.remise.toFixed(2)}€</strong></p>`
        : ''}
       ${totalInfo.reductionVille > 0
        ? `<p>Réduction Estrées-Saint-Denis (${totalInfo.nombreMineurs} mineur${totalInfo.nombreMineurs > 1 ? 's' : ''} inscrit${totalInfo.nombreMineurs > 1 ? 's' : ''}) : <strong>-${totalInfo.reductionVille}€</strong></p>`
        : ''}
      ${totalInfo.reductionPassSport > 0
        ? `<p>Réduction Pass'Sport (${totalInfo.nombrePassSport} code${totalInfo.nombrePassSport > 1 ? 's' : ''}) : <strong>-${totalInfo.reductionPassSport}€</strong></p>`
        : ''}
      <p class="recap__total-final"><strong>Total à régler : ${totalInfo.totalFinal.toFixed(2)}€</strong></p>
    </div>
  `;

    recapEl.innerHTML = `
    <p><strong>Titulaire :</strong> ${bearerPrenom?.value || '—'} ${bearerNom?.value || '—'}</p>
    <p><strong>Contact :</strong> ${bearerEmail?.value || '—'} · ${bearerTelephone?.value || '—'}</p>
    <p><strong>Adresse :</strong> ${bearerAdresse?.value || '—'}</p>
    <hr class="recap__sep">
    ${adherentsHtml}
    
    ${totalHtml}
  `;
  }

  /* ---------------- Snackbar (flash messages Symfony) ---------------- */

  function initSnackbars() {
    const snackbars = document.querySelectorAll('[data-snackbar]');

    snackbars.forEach((snackbar, index) => {
      setTimeout(() => snackbar.classList.add('is-visible'), 50 + index * 100);
      setTimeout(() => {
        snackbar.classList.remove('is-visible');
        setTimeout(() => snackbar.remove(), 300);
      }, 4000 + index * 100);
    });
  }

  /* ---------------- Écouteurs des boutons ---------------- */

  nextBtn.addEventListener('click', () => {
    if (validateStep(currentStep) && currentStep < steps.length - 1) {
      goToStep(currentStep + 1);
    }
  });

  prevBtn.addEventListener('click', () => {
    if (currentStep > 0) goToStep(currentStep - 1);
  });

  /* ---------------- Soumission du formulaire ---------------- */

  form.addEventListener('submit', (event) => {
    const identityValid = validateStep(0);
    const coordonneesValid = validateStep(1);
    const coursSanteValid = validateStep(2);

    if (!identityValid) {
      event.preventDefault();
      goToStep(0);
      return;
    }

    if (!coordonneesValid) {
      event.preventDefault();
      goToStep(1);
      return;
    }

    if (!coursSanteValid) {
      event.preventDefault();
      goToStep(2);
      return;
    }
  });

  /* ---------------- Initialisation ---------------- */

  goToStep(0);
  initAdherentsCollection();
  initAdresseAutocomplete();
  initSnackbars();
});