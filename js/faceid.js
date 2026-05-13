// faceid.js - Face ID (client-side descriptor capture via webcam)

const STUDYHUB_FACE_MODELS_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/model/';

async function studyhubLoadFaceApi() {
  if (!window.faceapi) throw new Error('face-api non chargé');
  if (studyhubLoadFaceApi._loaded) return;
  await faceapi.nets.ssdMobilenetv1.loadFromUri(STUDYHUB_FACE_MODELS_URL);
  await faceapi.nets.faceLandmark68Net.loadFromUri(STUDYHUB_FACE_MODELS_URL);
  await faceapi.nets.faceRecognitionNet.loadFromUri(STUDYHUB_FACE_MODELS_URL);
  studyhubLoadFaceApi._loaded = true;
}

function studyhubSetFaceStatus(el, text, type) {
  if (!el) return;
  el.textContent = text || '';
  el.className = 'small mt-2 ' + (type === 'error' ? 'text-danger' : type === 'ok' ? 'text-success' : 'text-muted');
}

async function studyhubGetDescriptorFromVideo(videoEl) {
  await studyhubLoadFaceApi();
  const detection = await faceapi
    .detectSingleFace(videoEl)
    .withFaceLandmarks()
    .withFaceDescriptor();
  if (!detection || !detection.descriptor) return null;
  return Array.from(detection.descriptor);
}

async function studyhubStartCamera(videoEl) {
  const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
  videoEl.srcObject = stream;
  await videoEl.play();
  return stream;
}

function studyhubStopCamera(stream) {
  if (!stream) return;
  try {
    stream.getTracks().forEach(t => t.stop());
  } catch {}
}

// Register page hook
window.studyhubInitFaceIdRegister = function () {
  const openBtn = document.getElementById('faceEnrollOpen');
  const modal = document.getElementById('faceModal');
  const closeBtn = document.getElementById('faceModalClose');
  const captureBtn = document.getElementById('faceCapture');
  const video = document.getElementById('faceVideo');
  const status = document.getElementById('faceStatus');
  const hiddenDesc = document.getElementById('face_descriptor');
  const hiddenEnabled = document.getElementById('face_enabled');
  const previewOk = document.getElementById('facePreviewOk');

  if (!openBtn || !modal || !video || !captureBtn || !hiddenDesc || !hiddenEnabled) return;

  let stream = null;

  const open = async () => {
    modal.style.display = 'flex';
    studyhubSetFaceStatus(status, 'Ouverture de la caméra…', 'muted');
    try {
      stream = await studyhubStartCamera(video);
      studyhubSetFaceStatus(status, 'Placez votre visage devant la caméra puis cliquez Capturer.', 'muted');
    } catch (e) {
      studyhubSetFaceStatus(status, "Impossible d'accéder à la caméra.", 'error');
    }
  };

  const close = () => {
    modal.style.display = 'none';
    studyhubStopCamera(stream);
    stream = null;
    video.srcObject = null;
  };

  openBtn.addEventListener('click', open);
  closeBtn && closeBtn.addEventListener('click', close);
  modal.addEventListener('click', (e) => { if (e.target === modal) close(); });

  captureBtn.addEventListener('click', async () => {
    studyhubSetFaceStatus(status, 'Analyse du visage…', 'muted');
    try {
      const desc = await studyhubGetDescriptorFromVideo(video);
      if (!desc) {
        studyhubSetFaceStatus(status, 'Aucun visage détecté. Rapprochez-vous et réessayez.', 'error');
        return;
      }
      hiddenDesc.value = JSON.stringify(desc);
      hiddenEnabled.value = '1';
      if (previewOk) previewOk.style.display = 'block';
      studyhubSetFaceStatus(status, 'Face ID enregistré. Vous pouvez fermer.', 'ok');
    } catch (e) {
      studyhubSetFaceStatus(status, 'Erreur Face ID. Réessayez.', 'error');
    }
  });
};

// Login page hook
window.studyhubInitFaceIdLogin = function () {
  const openBtn = document.getElementById('faceLoginOpen');
  const modal = document.getElementById('faceLoginModal');
  const closeBtn = document.getElementById('faceLoginClose');
  const captureBtn = document.getElementById('faceLoginCapture');
  const video = document.getElementById('faceLoginVideo');
  const status = document.getElementById('faceLoginStatus');
  const emailInput = document.getElementById('email');

  const form = document.getElementById('faceLoginForm');
  const formEmail = document.getElementById('faceLoginEmail');
  const formDesc = document.getElementById('faceLoginDescriptor');

  if (!openBtn || !modal || !video || !captureBtn || !form || !formEmail || !formDesc || !emailInput) return;

  let stream = null;

  const open = async () => {
    if (!emailInput.value || emailInput.value.indexOf('@') === -1) {
      studyhubSetFaceStatus(status, "Entrez d'abord votre email dans le formulaire.", 'error');
      modal.style.display = 'flex';
      return;
    }
    modal.style.display = 'flex';
    studyhubSetFaceStatus(status, 'Ouverture de la caméra…', 'muted');
    try {
      stream = await studyhubStartCamera(video);
      studyhubSetFaceStatus(status, 'Regardez la caméra puis cliquez Se connecter.', 'muted');
    } catch (e) {
      studyhubSetFaceStatus(status, "Impossible d'accéder à la caméra.", 'error');
    }
  };

  const close = () => {
    modal.style.display = 'none';
    studyhubStopCamera(stream);
    stream = null;
    video.srcObject = null;
  };

  openBtn.addEventListener('click', open);
  closeBtn && closeBtn.addEventListener('click', close);
  modal.addEventListener('click', (e) => { if (e.target === modal) close(); });

  captureBtn.addEventListener('click', async () => {
    if (!emailInput.value) return;
    studyhubSetFaceStatus(status, 'Analyse du visage…', 'muted');
    try {
      const desc = await studyhubGetDescriptorFromVideo(video);
      if (!desc) {
        studyhubSetFaceStatus(status, 'Aucun visage détecté. Réessayez.', 'error');
        return;
      }
      formEmail.value = emailInput.value.trim();
      formDesc.value = JSON.stringify(desc);
      form.submit();
    } catch (e) {
      studyhubSetFaceStatus(status, 'Erreur Face ID. Réessayez.', 'error');
    }
  });
};

