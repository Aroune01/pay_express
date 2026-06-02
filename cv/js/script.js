function addItem(id) {
  const container = document.getElementById(id);
  const clone = container.firstElementChild.cloneNode(true);
  clone.querySelectorAll("input, textarea").forEach((i) => (i.value = ""));
  const btn = document.createElement("button");
  btn.className = "btn-del";
  btn.innerText = "Supprimer";
  btn.onclick = function () {
    this.parentElement.remove();
    sync();
  };
  clone.appendChild(btn);
  container.appendChild(clone);
}

function sync() {
  document.getElementById("out_nom").innerText =
    document.getElementById("in_nom").value || "VOTRE NOM";
  document.getElementById("out_titre").innerText =
    document.getElementById("in_titre").value || "VOTRE TITRE";
  document.getElementById("out_bio").innerText =
    document.getElementById("in_bio").value;
  document.getElementById("out_lang").innerText =
    document.getElementById("in_lang").value || "---";
  document.getElementById("out_hobbies").innerText =
    document.getElementById("in_hobbies").value || "---";

  // Tags Compétences
  const skills = document.getElementById("in_skills").value.split(",");
  const box = document.getElementById("out_skills_box");
  box.innerHTML = "";
  skills.forEach((s) => {
    if (s.trim()) {
      const span = document.createElement("span");
      span.className = "skill-badge";
      span.innerText = s.trim();
      box.appendChild(span);
    }
  });

  // Expériences
  let expH = "";
  document.querySelectorAll("#exp_list .dynamic-item").forEach((it) => {
    const p = it.querySelector(".exp_p").value;
    const e = it.querySelector(".exp_e").value;
    const d = it.querySelector(".exp_d").value;
    const m = it.querySelector(".exp_m").value;
    if (p)
      expH += `<div class="block-title"><span>${p}</span><span class="block-date">${d}</span></div><div style="color:var(--accent);font-size:12px;margin-bottom:5px;">${e}</div><div class="block-desc">${m}</div>`;
  });
  document.getElementById("out_exps").innerHTML = expH;

  // Formations
  let formH = "";
  document.querySelectorAll("#form_list .dynamic-item").forEach((it) => {
    const d = it.querySelector(".f_d").value;
    const e = it.querySelector(".f_e").value;
    const a = it.querySelector(".f_a").value;
    if (d)
      formH += `<div class="block-title"><span>${d}</span><span class="block-date">${a}</span></div><div style="font-size:12px;margin-bottom:10px;">${e}</div>`;
  });
  document.getElementById("out_forms").innerHTML = formH;
}

function previewImg(input) {
  if (input.files && input.files[0]) {
    const r = new FileReader();
    r.onload = (e) => {
      const i = document.getElementById("out_photo");
      i.src = e.target.result;
      i.style.display = "block";
    };
    r.readAsDataURL(input.files[0]);
  }
}
