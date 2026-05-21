const $ = (q, root=document) => root.querySelector(q);
const $$ = (q, root=document) => Array.from(root.querySelectorAll(q));

function setActiveNav() {
  // Works for both .pill and .navLinks a
  const path = location.pathname.replace(/\\/g, "/");
  const file = (path.split("/").pop() || "index.html").toLowerCase();

  // Pills
  $$(".pill").forEach(a => a.classList.remove("active"));
  if (file === "" || file === "index.html") {
    const home = $$(".pill").find(a => (a.getAttribute("href") || "").includes("index.html"));
    if (home) home.classList.add("active");
  } else {
    const match = $$(".pill").find(a => (a.getAttribute("href") || "").toLowerCase().includes(file));
    if (match) match.classList.add("active");
  }

  // NavLinks (pages)
  const navA = $$(".navLinks a");
  navA.forEach(a => a.classList.remove("active"));
  const match2 = navA.find(a => (a.getAttribute("href") || "").toLowerCase().includes(file));
  if (match2) match2.classList.add("active");
}

function pageBasePath() {
  // main.js is used from index.html and from pages/*.html
  const path = location.pathname.replace(/\\/g, "/");
  return path.includes("/pages/") ? "../" : "./";
}

function openAccountModalIfNeeded() {
  const modal = $("#accountModal");
  const accountBtn = $("#accountBtn");

  const open = () => {
    if (modal) modal.classList.add("show");
  };

  const close = () => {
    if (modal) modal.classList.remove("show");
  };

  if (accountBtn) {
    accountBtn.addEventListener("click", (e) => {
      e.preventDefault();

      if (localStorage.getItem("loggedIn") === "true") {
        window.location.href = pageBasePath() + "profile.php";
      } else {
        open();
      }
    });
  }

  if (modal) {
    const closeBtn = $("#accountClose");
    if (closeBtn) closeBtn.addEventListener("click", close);

    modal.addEventListener("click", (e) => {
      if (e.target === modal) close();
    });

    if (location.hash === "#account") open();
  }
}

/**
 * Generic carousel
 * - Standard mode: pages (like old behavior)
 * - Slide mode (step=1): moves by one card-set, used for water activities.
 */
function makeCarousel(rootId, config={}) {
  const root = document.getElementById(rootId);
  if (!root) return;

  const track = $(".track", root) || $("#waterTrack", root) || $("#desertTrack", root);
  if (!track) return;

  const cards = $$(".card", track);
  const prev = $(".cbtn.prev", root) || $(".prev", root);
  const next = $(".cbtn.next", root) || $(".next", root);
  const dotsWrap = $(".dots", root);

  const perView = config.perView || 3;
  const gap = config.gap || 18;

  const slide = !!config.slide;   // if true, index is "start index"
  const centerMode = !!config.centerMode;

  let index = 0;

  const maxIndex = () => {
    if (slide) return Math.max(0, cards.length - perView);
    return Math.max(0, Math.ceil(cards.length / perView) - 1);
  };

  const pageCount = () => maxIndex() + 1;

  const buildDots = () => {
    if (!dotsWrap) return;
    dotsWrap.innerHTML = "";
    for (let i=0; i<pageCount(); i++){
      const d = document.createElement("div");
      d.className = "dot" + (i===index ? " active" : "");
      d.addEventListener("click", () => { index = i; render(); });
      dotsWrap.appendChild(d);
    }
  };

  const render = () => {
    const w = root.clientWidth;
    const cardW = (w - gap*(perView-1)) / perView;

    cards.forEach(c => { c.style.flex = `0 0 ${cardW}px`; });

    let start = index;
    if (!slide) start = index * perView;

    const move = start * (cardW + gap);
    track.style.transform = `translateX(${-move}px)`;

    if (centerMode) {
      cards.forEach(c => c.classList.remove("is-center"));
      const centerIdx = Math.min(cards.length-1, start + 1);
      if (cards[centerIdx]) cards[centerIdx].classList.add("is-center");
    }

    if (dotsWrap) {
      $$(".dot", dotsWrap).forEach((d,i)=> d.classList.toggle("active", i===index));
    }

    if (prev) prev.disabled = index === 0;
    if (next) next.disabled = index === maxIndex();
  };

  const clampIndex = () => { index = Math.max(0, Math.min(index, maxIndex())); };

  buildDots();
  render();

  window.addEventListener("resize", () => {
    clampIndex();
    buildDots();
    render();
  });

  if (prev) prev.addEventListener("click", () => {
    index = Math.max(0, index-1);
    render();
  });

  if (next) next.addEventListener("click", () => {
    index = Math.min(maxIndex(), index+1);
    render();
  });

  return { setIndex(i){ index = i; clampIndex(); render(); } };
}

function initActivitiesFilter() {
  const root = $("#activitiesPanel");
  if (!root) return;

  const tabs = $$(".tab[data-key]", root);
  const waterWrap = $("#waterWrap", root);
  const desertWrap = $("#desertWrap", root);

  let active = "water";

  const setActive = (key) => {
    active = key;
    tabs.forEach(t => t.classList.toggle("active", t.dataset.key === key));
    if (waterWrap) waterWrap.style.display = key === "water" ? "" : "none";
    if (desertWrap) desertWrap.style.display = key === "desert" ? "" : "none";
  };

  tabs.forEach(t => t.addEventListener("click", () => setActive(t.dataset.key)));

  setActive(active);

  // Water: slide by 1, center emphasis
  makeCarousel("waterWrap", { perView: 3, gap: 18, slide: true, centerMode: true });
}

function initBookingDrawer() {
  const drawer = $("#bookingDrawer");
  if (!drawer) return;

  const close = () => drawer.classList.remove("show");
  $("#drawerClose")?.addEventListener("click", close);
  document.addEventListener("keydown", (e) => { if (e.key === "Escape") close(); });

  const title = $("#drawerTitle");
  const img = $("#drawerImg");
  const subtitle = $("#drawerSubtitle");
  const facts = $("#drawerFacts");

  window.openRoomDrawer = (payload) => {
    if (!payload) return;

    if (title) title.textContent = payload.name || "Room";
    if (subtitle) subtitle.textContent = payload.desc || "";
    if (img) img.src = payload.image || "";
    if (facts) {
      facts.innerHTML = "";
      (payload.facts || []).forEach(f => {
        const div = document.createElement("div");
        div.className = "fact";
        div.innerHTML = `<b>${f.label}</b><span>${f.value}</span>`;
        facts.appendChild(div);
      });
    }

    drawer.classList.add("show");
  };
}

function initResortHero() {
  const hero = $("#bookingHeroBg");
  const name = $("#bookingResortName");
  const sub = $("#bookingResortSub");
  if (!hero) return;

  const q = new URLSearchParams(location.search);
  const resort = (q.get("resort") || "shebara").toLowerCase();

  const map = {
    shebara: {
      title: "Shebara",
      sub: "Overwater villas, island calm, and effortless booking.",
      bg: "../images/shebara_main.jpg"
    },
    dunes: {
      title: "Dunes",
      sub: "Desert serenity, mountain views, and quiet luxury.",
      bg: "../images/dunes_main.webp"
    }
  };

  const data = map[resort] || map.shebara;

  hero.style.backgroundImage = `url('${data.bg}')`;
  if (name) name.textContent = data.title;
  if (sub) sub.textContent = data.sub;

  const roomsRoot = $("#roomsRoot");
  if (!roomsRoot) return;

  const rooms = {
    shebara: [
      {
        name: "Four Bedroom Beach Royal Villa",
        image: "../images/Shebara/FourBedroomBeachRoyalVilla.jpg",
        desc: "A spacious villa designed for groups who want privacy and sea calm.",
        facts: [
          {label:"Size", value:"900 sqm (I: 451 | E: 449)"},
          {label:"Max Occupancy", value:"8 Adults + 4 Children"},
          {label:"Beds", value:"King + King + King + 2 Queen"},
          {label:"Highlights", value:"Infinity pool, BBQ station, outdoor terrace"}
        ]
      },
      {
        name: "One Bedroom Villa",
        image: "../images/Shebara/OneBedroom.jpg",
        desc: "A calm villa for couples who want privacy and sea views.",
        facts: [
          {label:"Max Occupancy", value:"2 Adults"},
          {label:"Beds", value:"King size bed"},
          {label:"Highlights", value:"Terrace, sea view, lounge area"},
          {label:"Style", value:"Minimal & bright"}
        ]
      },
      {
        name: "Three Bedroom Beach Crown Villa",
        image: "../images/Shebara/ThreeBedroomBeachCrownVilla.jpg",
        desc: "For families who want beach access, space, and calm luxury.",
        facts: [
          {label:"Size", value:"692 sqm"},
          {label:"Max Occupancy", value:"6 Adults + 3 Children"},
          {label:"Beds", value:"King + King + 2 Queen"},
          {label:"Highlights", value:"Infinity pool, deck, outdoor bar"}
        ]
      }
    ],
    dunes: [
      {
        name: "Wadi King Room",
        image: "../images/Dunes/1_Wadi_King_Room_bedroom_view.webp",
        desc: "Oasis level comfort with valley views and easy resort access.",
        facts: [
          {label:"Size", value:"60 sqm"},
          {label:"View", value:"Valley View"},
          {label:"Occupancy", value:"2 Adults"},
          {label:"Highlights", value:"Wi‑Fi, minibar, organic bathrobes"}
        ]
      },
      {
        name: "Two Bedroom Sunset Pool Villa",
        image: "../images/Dunes/2_Two_Bedroom_Sunset_Pool_Villa_exterior.webp",
        desc: "Private pool, outdoor terrace, and sunset views across the mountains.",
        facts: [
          {label:"Size", value:"140 sqm"},
          {label:"Beds", value:"1 King + 2 Queen"},
          {label:"Occupancy", value:"4 Adults + 2 Children"},
          {label:"Highlights", value:"Private pool, outdoor dining"}
        ]
      },
      {
        name: "Wadi King Room (Side view)",
        image: "../images/Dunes/2_Wadi_King_Room_bedroom_sideview.webp",
        desc: "Generous internal space with a calm wadi view.",
        facts: [
          {label:"Size", value:"60 sqm"},
          {label:"Beds", value:"1 King Bed"},
          {label:"Occupancy", value:"2 Adults"},
          {label:"Highlights", value:"Wi‑Fi, minibar, AC, safe"}
        ]
      }
    ]
  };

  const chosen = rooms[resort] || rooms.shebara;

  const cards = $$(".card", roomsRoot);
  cards.forEach((c, i) => {
    const r = chosen[i];
    if (!r) return;

    const imgEl = $("img", c);
    const titleEl = $("h3", c);
    const pEl = $("p", c);

    if (imgEl) imgEl.src = r.image;
    if (titleEl) titleEl.textContent = r.name;
    if (pEl) pEl.textContent = "Tap to view details";

    c.addEventListener("click", (e) => {
      e.preventDefault();
      window.openRoomDrawer?.(r);
    });
  });
}

function initActivitiesBookingPage(){
  const heroBg = $("#activityHeroBg");
  if (!heroBg) return;

  const title = $("#activityTitle");
  const sub = $("#activitySub");
  const meta = $("#activityMeta");
  const ageNotice = $("#ageNotice");
  const bringText = $("#bringText");

  const q = new URLSearchParams(location.search);
  const key = (q.get("activity") || "dive").toLowerCase();

  const map = {
    dive: {
      name:"Dive",
      type:"Water",
      img:"../images/Activites/Dive_main.jpg",
      duration:"2 hours",
      age:"12+",
      bring:"Swimwear, towel, and basic dive comfort."
    },
    snorkeling: {
      name:"Snorkeling",
      type:"Water",
      img:"../images/Activites/Snorkeling_main.webp",
      duration:"1.5 hours",
      age:"8+",
      bring:"Swimwear, towel, and sunscreen."
    },
    sailing: {
      name:"Sailing",
      type:"Water",
      img:"../images/Activites/Sailing_main.webp",
      duration:"2 hours",
      age:"10+",
      bring:"Light jacket (wind), sunglasses, and sunscreen."
    },
    kayaking: {
      name:"Kayaking",
      type:"Water",
      img:"../images/Activites/Kayaking_main.webp",
      duration:"1 hour",
      age:"10+",
      bring:"Quick‑dry clothing and water."
    },
    hiking: {
      name:"Hiking",
      type:"Desert",
      img:"../images/Activites/Hiking_main.webp",
      duration:"2 hours",
      age:"12+",
      bring:"Hiking shoes, hat, and water."
    },
    ebiking: {
      name:"E‑Biking",
      type:"Desert",
      img:"../images/Activites/E-Biking_main.webp",
      duration:"1.5 hours",
      age:"16+",
      bring:"Closed shoes and water."
    }
  };

  const data = map[key] || map.dive;

  heroBg.style.backgroundImage = `url('${data.img}')`;
  if (title) title.textContent = data.name;
  if (sub) sub.textContent = "Choose a time, then book.";
  if (meta) {
    meta.innerHTML = `
      <div class="pillTag">${data.type} activity</div>
      <div class="pillTag">${data.duration}</div>
      <div class="pillTag">Age ${data.age}</div>
    `;
  }
  if (ageNotice) ageNotice.textContent = `Age restriction: ${data.age}`;
  if (bringText) bringText.textContent = data.bring;

  // highlight selected mini card
  $$("[data-activity-link]").forEach(a => {
    a.classList.toggle("active", (a.dataset.activityLink || "").toLowerCase() === key);
  });

  // tabs switch
  const waterTab = $("[data-activity-tab='water']");
  const desertTab = $("[data-activity-tab='desert']");
  const waterList = $("#waterList");
  const desertList = $("#desertList");

  const setTab = (which) => {
    if (waterTab) waterTab.classList.toggle("active", which === "water");
    if (desertTab) desertTab.classList.toggle("active", which === "desert");
    if (waterList) waterList.style.display = which === "water" ? "" : "none";
    if (desertList) desertList.style.display = which === "desert" ? "" : "none";
  };

  waterTab?.addEventListener("click", () => setTab("water"));
  desertTab?.addEventListener("click", () => setTab("desert"));

  setTab(data.type.toLowerCase() === "desert" ? "desert" : "water");

  // Demo booking
  $("#actBookBtn")?.addEventListener("click", () => {
    const date = $("#actDate")?.value || "(no date)";
    const slot = document.querySelector("input[name='slot']:checked")?.value || "(no time)";
    const people = $("#actPeople")?.value || "1";
    alert(`Booked (demo): ${data.name}\nDate: ${date}\nTime: ${slot}\nPeople: ${people}`);
  });
}

document.addEventListener("DOMContentLoaded", () => {
  setActiveNav();
  openAccountModalIfNeeded();

  // Water carousel is inside #waterWrap (index)
  initActivitiesFilter();

  initBookingDrawer();
  initResortHero();
  initActivitiesBookingPage();
});


const registerBtn = document.getElementById("registerBtn");
const loginBtn = document.getElementById("loginBtn");

const accountName = document.getElementById("accountName");
const accountEmail = document.getElementById("accountEmail");
const accountPassword = document.getElementById("accountPassword");

const accountMessage = document.getElementById("accountMessage");
const nameError = document.getElementById("nameError");
const emailError = document.getElementById("emailError");
const passwordError = document.getElementById("passwordError");

const showLogin = document.getElementById("showLogin");
const showRegister = document.getElementById("showRegister");

function backendPath(file) {
  return pageBasePath() + "server/" + file;
}

function showAccountMessage(message, type) {
  if (!accountMessage) return;
  accountMessage.textContent = message;
  accountMessage.className = type;
  accountMessage.style.display = "block";
}

function setInputError(input, errorBox, message) {
  if (!input || !errorBox) return;
  input.classList.add("input-error");
  input.classList.remove("input-success");
  errorBox.textContent = message;
}

function setInputSuccess(input, errorBox) {
  if (!input || !errorBox) return;
  input.classList.remove("input-error");
  input.classList.add("input-success");
  errorBox.textContent = "";
}

function validateAccountEmail() {
  if (!accountEmail) return false;

  const email = accountEmail.value.trim();
  const emailPattern = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;

  if (email === "") {
    setInputError(accountEmail, emailError, "Email is required.");
    return false;
  }

  if (!emailPattern.test(email)) {
    setInputError(accountEmail, emailError, "Enter a valid email like name@email.com.");
    return false;
  }

  setInputSuccess(accountEmail, emailError);
  return true;
}

function validateAccountPassword() {
  if (!accountPassword) return false;

  const password = accountPassword.value;

  if (password === "") {
    setInputError(accountPassword, passwordError, "Password is required.");
    return false;
  }

  if (password.length < 8) {
    setInputError(accountPassword, passwordError, "Password must be at least 8 characters.");
    return false;
  }

  if (!/[A-Z]/.test(password)) {
    setInputError(accountPassword, passwordError, "Password must include at least one uppercase letter.");
    return false;
  }

  if (!/[a-z]/.test(password)) {
    setInputError(accountPassword, passwordError, "Password must include at least one lowercase letter.");
    return false;
  }

  if (!/[0-9]/.test(password)) {
    setInputError(accountPassword, passwordError, "Password must include at least one number.");
    return false;
  }

  setInputSuccess(accountPassword, passwordError);
  return true;
}

function validateAccountName() {
  if (!accountName) return false;

  const name = accountName.value.trim();
  const namePattern = /^[A-Za-z ]{2,100}$/;

  if (name === "") {
    setInputError(accountName, nameError, "Name is required for registration.");
    return false;
  }

  if (!namePattern.test(name)) {
    setInputError(accountName, nameError, "Use letters only. Minimum 2 characters.");
    return false;
  }

  setInputSuccess(accountName, nameError);
  return true;
}

function updateAccountButton() {
  const accountBtn = document.getElementById("accountBtn");
  if (!accountBtn) return;

  if (localStorage.getItem("loggedIn") === "true") {
    accountBtn.textContent = "Profile";
  } else {
    accountBtn.textContent = "Account";
  }
}

if (showLogin && showRegister) {
  showLogin.addEventListener("click", function () {
    showLogin.classList.add("brand");
    showRegister.classList.remove("brand");

    if (accountName) accountName.style.display = "none";
    if (loginBtn) loginBtn.style.display = "inline-flex";
    if (registerBtn) registerBtn.style.display = "none";
    if (accountMessage) accountMessage.style.display = "none";
  });

  showRegister.addEventListener("click", function () {
    showRegister.classList.add("brand");
    showLogin.classList.remove("brand");

    if (accountName) accountName.style.display = "block";
    if (loginBtn) loginBtn.style.display = "none";
    if (registerBtn) registerBtn.style.display = "inline-flex";
    if (accountMessage) accountMessage.style.display = "none";

    if (passwordError) {
      passwordError.textContent = "Password must be at least 8 characters and include uppercase, lowercase, and a number.";
      passwordError.style.color = "#64748b";
    }
  });
}

if (registerBtn) {
  registerBtn.addEventListener("click", function () {
    const validName = validateAccountName();
    const validEmail = validateAccountEmail();
    const validPassword = validateAccountPassword();

    if (!validName || !validEmail || !validPassword) {
      showAccountMessage("Please fix the highlighted fields.", "error");
      return;
    }

    const formData = new FormData();
    formData.append("name", accountName.value.trim());
    formData.append("email", accountEmail.value.trim());
    formData.append("password", accountPassword.value);

    fetch(backendPath("process_register.php"), {
      method: "POST",
      body: formData
    })
    .then(res => res.text())
    .then(data => {
      const cleanData = data.trim();

      if (cleanData === "registered_email_sent") {
        showAccountMessage("Registration successful! Welcome email sent.", "success");
        localStorage.setItem("loggedIn", "true");
        updateAccountButton();

        setTimeout(() => {
          window.location.reload();
        }, 1200);

      } else if (cleanData === "registered_email_failed") {
        showAccountMessage("Registration successful, but the welcome email could not be sent.", "success");
        localStorage.setItem("loggedIn", "true");
        updateAccountButton();

        setTimeout(() => {
          window.location.reload();
        }, 1600);

      } else {
        showAccountMessage(cleanData, "error");
      }
    })
    .catch(() => {
      showAccountMessage("Connection error. Please try again.", "error");
    });
  });
}

if (loginBtn) {
  loginBtn.addEventListener("click", function () {
    const validEmail = validateAccountEmail();
    const validPassword = validateAccountPassword();

    if (!validEmail || !validPassword) {
      showAccountMessage("Please fix the highlighted fields.", "error");
      return;
    }

    const formData = new FormData();
    formData.append("email", accountEmail.value.trim());
    formData.append("password", accountPassword.value);

    fetch(backendPath("process_login.php"), {
      method: "POST",
      body: formData
    })
    .then(res => res.text())
    .then(data => {
      const cleanData = data.trim();

      if (cleanData === "admin") {
        showAccountMessage("Admin login successful!", "success");
        localStorage.setItem("loggedIn", "true");
        updateAccountButton();

        setTimeout(() => {
          window.location.href = pageBasePath() + "admin/dashboard.php";
        }, 1000);

      } else if (cleanData === "user") {
        showAccountMessage("Login successful!", "success");
        localStorage.setItem("loggedIn", "true");
        updateAccountButton();

        setTimeout(() => {
          window.location.reload();
        }, 1000);

      } else {
        showAccountMessage(cleanData, "error");
      }
    })
    .catch(() => {
      showAccountMessage("Connection error. Please try again.", "error");
    });
  });
}

document.addEventListener("DOMContentLoaded", updateAccountButton);
updateAccountButton();
