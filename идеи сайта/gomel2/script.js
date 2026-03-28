const body = document.body;
const visionToggle = document.querySelector(".vision-toggle");
const menuToggle = document.querySelector(".menu-toggle");
const nav = document.querySelector(".site-nav");
const faqButtons = document.querySelectorAll(".faq-question");
const revealItems = document.querySelectorAll(".reveal");
const requestForm = document.querySelector("#request-form");
const formStatus = document.querySelector("#form-status");
const carousels = document.querySelectorAll("[data-carousel]");
const navGroups = document.querySelectorAll(".site-nav__item");

const VISION_STORAGE_KEY = "tcson-vision-mode";

const readVisionMode = () => {
  try {
    return localStorage.getItem(VISION_STORAGE_KEY);
  } catch {
    return null;
  }
};

const writeVisionMode = (enabled) => {
  try {
    localStorage.setItem(VISION_STORAGE_KEY, enabled ? "on" : "off");
  } catch {
    // Storage access can fail in strict browser modes. Ignore it.
  }
};

const setVisionMode = (enabled) => {
  body.classList.toggle("vision-mode", enabled);
  visionToggle?.setAttribute("aria-pressed", String(enabled));
  writeVisionMode(enabled);
};

const closeMenu = () => {
  nav?.classList.remove("is-open");
  menuToggle?.setAttribute("aria-expanded", "false");
  navGroups.forEach((group) => {
    group.removeAttribute("open");
  });
};

if (readVisionMode() === "on") {
  setVisionMode(true);
}

visionToggle?.addEventListener("click", () => {
  setVisionMode(!body.classList.contains("vision-mode"));
});

menuToggle?.addEventListener("click", () => {
  const isOpen = nav ? nav.classList.toggle("is-open") : false;
  menuToggle.setAttribute("aria-expanded", String(Boolean(isOpen)));
});

nav?.querySelectorAll("a").forEach((link) => {
  link.addEventListener("click", () => {
    closeMenu();
  });
});

window.addEventListener("keydown", (event) => {
  if (event.key === "Escape" && nav?.classList.contains("is-open")) {
    closeMenu();
    menuToggle?.focus();
  }
});

window.addEventListener("resize", () => {
  if (window.innerWidth > 900 && nav?.classList.contains("is-open")) {
    closeMenu();
  }
});

faqButtons.forEach((button) => {
  button.addEventListener("click", () => {
    const item = button.closest(".faq-item");
    const expanded = button.getAttribute("aria-expanded") === "true";

    faqButtons.forEach((otherButton) => {
      otherButton.setAttribute("aria-expanded", "false");
      otherButton.closest(".faq-item")?.classList.remove("is-open");
    });

    if (!expanded) {
      button.setAttribute("aria-expanded", "true");
      item?.classList.add("is-open");
    }
  });
});

if ("IntersectionObserver" in window) {
  const revealObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          revealObserver.unobserve(entry.target);
        }
      });
    },
    {
      threshold: 0.16,
      rootMargin: "0px 0px -48px 0px",
    },
  );

  revealItems.forEach((item) => revealObserver.observe(item));
} else {
  revealItems.forEach((item) => item.classList.add("is-visible"));
}

if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
  body.classList.add("reduce-motion");
}

carousels.forEach((carousel) => {
  const track = carousel.querySelector(".media-carousel__track");
  const slides = Array.from(carousel.querySelectorAll(".media-slide"));
  const prevButton = carousel.querySelector("[data-carousel-prev]");
  const nextButton = carousel.querySelector("[data-carousel-next]");
  const dots = Array.from(carousel.querySelectorAll("[data-carousel-dot]"));
  const autoplayDelay = Number(carousel.dataset.autoplay || 0);
  const canAutoplay = autoplayDelay > 0 && !body.classList.contains("reduce-motion");

  if (!track || slides.length === 0) {
    return;
  }

  let activeIndex = 0;
  let autoplayId = null;

  const render = () => {
    track.style.transform = `translateX(-${activeIndex * 100}%)`;

    slides.forEach((slide, index) => {
      slide.classList.toggle("is-active", index === activeIndex);
      slide.setAttribute("aria-hidden", String(index !== activeIndex));
    });

    dots.forEach((dot, index) => {
      dot.classList.toggle("is-active", index === activeIndex);
      dot.setAttribute("aria-current", index === activeIndex ? "true" : "false");
    });
  };

  const goTo = (nextIndex) => {
    activeIndex = (nextIndex + slides.length) % slides.length;
    render();
  };

  const stopAutoplay = () => {
    if (autoplayId) {
      window.clearInterval(autoplayId);
      autoplayId = null;
    }
  };

  const startAutoplay = () => {
    if (!canAutoplay || autoplayId) {
      return;
    }

    autoplayId = window.setInterval(() => {
      goTo(activeIndex + 1);
    }, autoplayDelay);
  };

  prevButton?.addEventListener("click", () => {
    goTo(activeIndex - 1);
  });

  nextButton?.addEventListener("click", () => {
    goTo(activeIndex + 1);
  });

  dots.forEach((dot, index) => {
    dot.addEventListener("click", () => {
      goTo(index);
    });
  });

  carousel.addEventListener("mouseenter", stopAutoplay);
  carousel.addEventListener("mouseleave", startAutoplay);
  carousel.addEventListener("focusin", stopAutoplay);
  carousel.addEventListener("focusout", startAutoplay);

  render();
  startAutoplay();
});

requestForm?.addEventListener("submit", (event) => {
  event.preventDefault();

  const name = requestForm.querySelector("#name");
  const phone = requestForm.querySelector("#phone");
  const topic = requestForm.querySelector("#topic");

  const fields = [name, phone, topic];
  const firstInvalid = fields.find((field) => !field?.value.trim());

  formStatus?.classList.remove("is-error", "is-success");

  if (firstInvalid) {
    formStatus.textContent =
      "Заполните обязательные поля: ФИО, телефон и тему обращения.";
    formStatus?.classList.add("is-error");
    firstInvalid.focus();
    return;
  }

  formStatus.textContent =
    "Демо-обращение зарегистрировано. Для рабочей отправки подключите форму к почте учреждения или системе электронных обращений.";
  formStatus?.classList.add("is-success");
  requestForm.reset();
});
