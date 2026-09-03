<template>
  <main class="filifinder-teaser">
    <!-- Decorative overlays -->
    <div class="background-overlay" aria-hidden="true"></div>
    <div class="ambient-light" aria-hidden="true"></div>

    <!-- Main teaser content -->
    <section class="teaser-content" aria-labelledby="teaser-title">
      <img
        src="/assets/fili-logo.png?v=2"
        alt="FILIfinder"
        class="filifinder-logo"
        width="1230"
        height="1278"
        draggable="false"
      />

      <div class="title-container">
        <span class="title-line" aria-hidden="true"></span>

        <h1 id="teaser-title">
          <span>We Find.</span>
          <span>You Decide.</span>
        </h1>

        <span class="title-line title-line-right" aria-hidden="true"></span>
      </div>

      <nav class="experiences" aria-label="Explore FILIfinder">
        <a
          v-for="experience in experiences"
          :key="experience.highlight"
          :href="experience.url"
          class="experience-circle"
          target="_blank"
          rel="noopener noreferrer"
          :aria-label="`${experience.label} — opens in a new tab`"
        >
          <span class="circle-glow" aria-hidden="true"></span>

          <span class="circle-inner">
            <i :class="experience.icon" aria-hidden="true"></i>

            <span class="experience-title">
              <span class="experience-prefix">
                {{ experience.prefix }}
              </span>

              <strong class="experience-highlight">
                {{ experience.highlight }}
              </strong>

              <span v-if="experience.suffix" class="experience-suffix">
                {{ experience.suffix }}
              </span>
            </span>
          </span>
        </a>
      </nav>

      <p class="closing-message">And along the way, find your reason to love the Philippines</p>
    </section>

    <p class="copyright">© {{ currentYear }} FILIfinder. All rights reserved.</p>
  </main>
</template>

<script>
export default {
  name: 'LandingPage',

  data() {
    return {
      currentYear: new Date().getFullYear(),

      experiences: [
        {
          prefix: 'Where to',
          highlight: 'LIVE',
          suffix: '',
          label: 'Where to live',
          icon: 'fa-solid fa-house',
          url: 'https://drive.google.com/drive/u/0/folders/1kppZS9ZuBJi6yBTJZds3IkH3udlvMlqV',
        },
        {
          prefix: 'Who to',
          highlight: 'WORK',
          suffix: 'with',
          label: 'Who to work with',
          icon: 'fa-solid fa-briefcase',
          url: 'https://drive.google.com/drive/folders/1f1EUde9dahGSPJWAkTusWMbc9qdYfwVD',
        },
        {
          prefix: 'When to',
          highlight: 'DINE',
          suffix: '',
          label: 'When to dine',
          icon: 'fa-solid fa-utensils',
          url: 'https://drive.google.com/drive/folders/1GWn_UeuJ3ug7dVMhuos0UforvFZja9yu',
        },
        {
          prefix: 'How to',
          highlight: 'PLAY',
          suffix: '',
          label: 'What to play',
          icon: 'fa-solid fa-umbrella-beach',
          url: 'https://drive.google.com/drive/folders/1L4uiv8DQzzSZBcHMqJVD0_k0WLCT5BtW',
        },
      ],
    }
  },
}
</script>

<style scoped>
.filifinder-teaser {
  --white: #ffffff;
  --soft-white: rgba(255, 255, 255, 0.9);
  --gold: #f8cb72;
  --orange: #ff9a19;
  --deep-navy: #021126;
  --beach-image: url('/assets/backgrounds/filifinder-beach-xs.png');

  position: fixed;
  isolation: isolate;
  inset: 0;

  display: flex;
  width: 100vw;
  height: 100vh;
  height: 100svh;
  height: 100dvh;
  align-items: center;
  justify-content: center;
  overflow: hidden;

  box-sizing: border-box;
  margin: 0;
  padding: clamp(24px, 4vw, 54px) clamp(18px, 5vw, 70px);

  background-color: #14799c;
  background-image:
    linear-gradient(
      180deg,
      rgba(2, 18, 39, 0.16) 0%,
      rgba(2, 25, 44, 0.1) 42%,
      rgba(1, 15, 32, 0.5) 100%
    ),
    var(--beach-image), linear-gradient(145deg, #62ced6 0%, #238db2 50%, #075477 100%);

  background-position: center center;
  background-repeat: no-repeat;
  background-size: cover;

  font-family: 'Montserrat', Arial, sans-serif;
}

/* Responsive background images */

@media (min-width: 481px) {
  .filifinder-teaser {
    --beach-image: url('/assets/backgrounds/filifinder-beach-sm.png');
  }
}

@media (min-width: 769px) {
  .filifinder-teaser {
    --beach-image: url('/assets/backgrounds/filifinder-beach-md.png');
  }
}

@media (min-width: 1025px) {
  .filifinder-teaser {
    --beach-image: url('/assets/backgrounds/filifinder-beach-lg.png');
  }
}

@media (min-width: 1441px) {
  .filifinder-teaser {
    --beach-image: url('/assets/backgrounds/filifinder-beach-xl.png');
  }
}

/* Background overlay */

.background-overlay {
  position: absolute;
  z-index: 1;
  inset: 0;

  background:
    radial-gradient(
      ellipse at center,
      transparent 8%,
      rgba(2, 20, 39, 0.05) 44%,
      rgba(1, 14, 30, 0.34) 100%
    ),
    linear-gradient(180deg, rgba(3, 18, 37, 0.08) 0%, transparent 31%, rgba(2, 16, 34, 0.3) 100%);

  pointer-events: none;
}

/* Ambient sunlight */

.ambient-light {
  position: absolute;
  z-index: 2;
  top: -35%;
  left: 50%;

  width: min(90vw, 1100px);
  aspect-ratio: 1;

  transform: translateX(-50%);
  border-radius: 50%;

  background: radial-gradient(
    circle,
    rgba(255, 230, 155, 0.2) 0%,
    rgba(255, 217, 120, 0.07) 45%,
    transparent 72%
  );

  filter: blur(20px);
  pointer-events: none;
}

/* Main content */

.teaser-content {
  position: relative;
  z-index: 5;

  display: flex;
  width: min(94vw, 1180px);
  align-items: center;
  flex-direction: column;
  justify-content: center;

  text-align: center;
}

/* Soft contrast behind the content */

.teaser-content::before {
  position: absolute;
  z-index: -1;
  top: 50%;
  left: 50%;

  width: min(118vw, 1150px);
  height: 118%;

  transform: translate(-50%, -50%);
  border-radius: 50%;

  background: radial-gradient(
    ellipse,
    rgba(2, 22, 43, 0.3) 0%,
    rgba(2, 22, 43, 0.1) 50%,
    transparent 75%
  );

  filter: blur(14px);
  content: '';
  pointer-events: none;
}

/* FILIfinder logo */

.filifinder-logo {
  display: block;
  width: clamp(120px, 10vw, 170px);
  height: auto;
  max-width: 100%;

  object-fit: contain;

  pointer-events: none;
  user-select: none;
  -webkit-user-select: none;

  /*
   * Filters are intentionally removed because some iPhone
   * in-app browsers render transparent PNGs as white boxes
   * when filter and animation are combined.
   */
  filter: none;
  -webkit-filter: none;

  backface-visibility: hidden;
  -webkit-backface-visibility: hidden;
}

/* Discover the Philippines */

.eyebrow {
  margin: clamp(10px, 1.5vh, 15px) 0 0;

  color: var(--gold);

  font-family: 'Montserrat', Arial, sans-serif;
  font-size: clamp(9px, 0.75vw, 12px);
  font-weight: 700;
  letter-spacing: 0.32em;
  line-height: 1.4;
  text-align: center;
  text-transform: uppercase;

  text-shadow: 0 2px 10px rgba(1, 15, 35, 0.62);
}

/* Main heading */

.title-container {
  display: grid;
  width: 100%;
  grid-template-columns:
    minmax(25px, 1fr)
    auto
    minmax(25px, 1fr);
  align-items: center;
  gap: clamp(16px, 2.4vw, 34px);

  margin-top: clamp(8px, 1.4vh, 14px);
}

.title-container h1 {
  display: flex;
  align-items: baseline;
  justify-content: center;
  gap: 0.18em;

  margin: 0;
  color: var(--white);

  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: clamp(50px, 6.2vw, 98px);
  font-weight: 600;
  letter-spacing: 0.015em;
  line-height: 0.95;
  text-align: center;
  white-space: nowrap;

  text-shadow:
    0 2px 1px rgba(2, 17, 38, 0.32),
    0 9px 26px rgba(2, 17, 38, 0.56),
    0 0 42px rgba(2, 17, 38, 0.2);
}

.title-container h1 span {
  display: inline-block;
  flex-shrink: 0;
}

.title-container h1 span:last-child {
  color: #fffdf7;
}

/* Decorative heading lines */

.title-line {
  height: 1px;
  transform-origin: right center;

  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.86));

  box-shadow: 0 1px 6px rgba(2, 18, 40, 0.2);
}

.title-line-right {
  transform-origin: left center;

  background: linear-gradient(90deg, rgba(255, 255, 255, 0.86), transparent);
}

/* Experience buttons container */

.experiences {
  display: grid;
  grid-template-columns: repeat(4, auto);
  align-items: center;
  justify-content: center;
  gap: clamp(16px, 2.2vw, 28px);

  margin-top: clamp(26px, 4vh, 42px);
}

/* Clickable circular buttons */

.experience-circle {
  position: relative;

  display: grid;
  width: clamp(108px, 9vw, 132px);
  height: clamp(108px, 9vw, 132px);
  place-items: center;
  flex-shrink: 0;

  overflow: hidden;
  border: 1px solid rgba(255, 255, 255, 0.62);
  border-radius: 50%;

  background: linear-gradient(145deg, rgba(255, 255, 255, 0.24), rgba(255, 255, 255, 0.08));

  color: var(--white);
  text-decoration: none;

  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.4),
    0 14px 30px rgba(1, 18, 38, 0.22);

  backdrop-filter: blur(7px);
  -webkit-backdrop-filter: blur(7px);

  cursor: pointer;

  transition:
    transform 280ms ease,
    border-color 280ms ease,
    background 280ms ease,
    box-shadow 280ms ease;
}

.experience-circle:hover {
  transform: translateY(-7px) scale(1.045);
  border-color: rgba(248, 203, 114, 0.98);

  background: linear-gradient(145deg, rgba(255, 255, 255, 0.34), rgba(255, 255, 255, 0.13));

  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.52),
    0 20px 38px rgba(1, 18, 38, 0.3),
    0 0 26px rgba(248, 203, 114, 0.2);
}

.experience-circle:focus-visible {
  outline: 3px solid rgba(248, 203, 114, 0.98);
  outline-offset: 5px;
}

/* Moving reflection inside the buttons */

.experience-circle::before {
  position: absolute;
  top: -30%;
  left: -70%;

  width: 55%;
  height: 160%;

  transform: rotate(22deg);

  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.27), transparent);

  content: '';
  pointer-events: none;
}

.experience-circle::after {
  position: absolute;
  inset: 5px;

  border: 1px solid rgba(255, 255, 255, 0.14);
  border-radius: 50%;

  content: '';
  pointer-events: none;
}

.circle-glow {
  position: absolute;
  inset: 15%;

  border-radius: 50%;

  background: radial-gradient(circle, rgba(248, 203, 114, 0.12), transparent 70%);

  opacity: 0;
  transition: opacity 280ms ease;
}

.experience-circle:hover .circle-glow {
  opacity: 1;
}

/* Content inside each button */

.circle-inner {
  position: relative;
  z-index: 2;

  display: flex;
  width: 86%;
  height: 86%;
  align-items: center;
  flex-direction: column;
  justify-content: center;
  gap: clamp(7px, 0.8vh, 10px);

  border-radius: 50%;
  text-align: center;
}

/* Font Awesome icons */

.circle-inner i {
  color: var(--gold);
  font-size: clamp(17px, 1.5vw, 22px);

  filter: drop-shadow(0 3px 7px rgba(1, 16, 34, 0.42));

  transition:
    color 280ms ease,
    transform 280ms ease;
}

.experience-circle:hover .circle-inner i {
  color: #ffe1a0;
  transform: translateY(-2px) scale(1.08);
}

/* Three-part button labels */

.experience-title {
  display: flex;
  align-items: center;
  flex-direction: column;
  justify-content: center;
  gap: 2px;

  color: var(--white);

  font-family: 'Montserrat', Arial, sans-serif;
  line-height: 1.05;
  text-align: center;

  text-shadow: 0 2px 7px rgba(1, 15, 35, 0.7);
}

.experience-prefix,
.experience-suffix {
  color: rgba(255, 255, 255, 0.92);

  font-size: clamp(8px, 0.62vw, 10px);
  font-weight: 500;
  letter-spacing: 0.025em;
  white-space: nowrap;
}

.experience-highlight {
  color: var(--white);

  font-size: clamp(11px, 0.85vw, 14px);
  font-weight: 700;
  letter-spacing: 0.13em;
  line-height: 1.05;
  white-space: nowrap;
}

/* Sentence below the buttons */

.closing-message {
  width: min(92%, 720px);
  margin: clamp(22px, 3.5vh, 36px) auto 0;

  color: rgba(255, 255, 255, 0.96);

  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: clamp(17px, 1.5vw, 22px);
  font-style: italic;
  font-weight: 500;
  letter-spacing: 0.025em;
  line-height: 1.35;
  text-align: center;

  text-shadow:
    0 2px 9px rgba(1, 15, 35, 0.9),
    0 0 22px rgba(1, 15, 35, 0.4);
}

/* Copyright */

.copyright {
  position: absolute;
  z-index: 5;
  right: 20px;
  bottom: clamp(15px, 2.5vh, 25px);
  left: 20px;

  margin: 0;
  color: rgba(255, 255, 255, 0.66);

  font-family: 'Montserrat', Arial, sans-serif;
  font-size: clamp(8px, 0.7vw, 10px);
  font-weight: 500;
  letter-spacing: 0.06em;
  line-height: 1.4;
  text-align: center;

  text-shadow: 0 2px 8px rgba(1, 15, 35, 0.75);
}

/* Extra-large displays */

@media (min-width: 1441px) {
  .teaser-content {
    width: min(88vw, 1280px);
  }

  .filifinder-logo {
    width: 185px;
  }

  .title-container h1 {
    font-size: 104px;
  }

  .experience-circle {
    width: 136px;
    height: 136px;
  }

  .closing-message {
    font-size: 23px;
  }
}

/* Tablets */

@media (min-width: 601px) and (max-width: 900px) {
  .teaser-content {
    width: min(94vw, 780px);
  }

  .title-container h1 {
    font-size: clamp(48px, 7.5vw, 68px);
  }

  .experience-circle {
    width: clamp(102px, 13vw, 120px);
    height: clamp(102px, 13vw, 120px);
  }
}

/* Mobile phones */

@media (max-width: 600px) {
  .filifinder-teaser {
    align-items: center;

    padding: max(14px, env(safe-area-inset-top)) 10px max(38px, env(safe-area-inset-bottom));

    background-position: center center;
  }

  .teaser-content {
    width: 100%;
    max-width: 390px;
    align-items: center;
    justify-content: center;

    transform: translateY(-3px);
  }

  .teaser-content::before {
    width: 150vw;
    height: 110%;
  }

  /* Mobile logo */

  .filifinder-logo {
    width: clamp(118px, 34vw, 145px);
    max-width: 145px;
  }

  /* Mobile eyebrow */

  .eyebrow {
    width: 100%;
    margin-top: 7px;

    font-size: clamp(8px, 2.3vw, 10px);
    letter-spacing: 0.2em;
    line-height: 1.3;
    text-align: center;
    white-space: nowrap;
  }

  /* Mobile heading: one line */

  .title-container {
    display: block;
    width: 100%;
    margin-top: 8px;
  }

  .title-container h1 {
    display: flex;
    width: 100%;
    align-items: baseline;
    flex-direction: row;
    justify-content: center;
    gap: 0.15em;

    font-size: clamp(27px, 8.4vw, 38px);
    letter-spacing: 0;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
  }

  .title-container h1 span {
    display: inline-block;
    width: auto;
    flex-shrink: 0;
    text-align: center;
  }

  .title-line {
    display: none;
  }

  /* Mobile circle grid */

  .experiences {
    display: grid;
    width: 100%;
    grid-template-columns: repeat(2, auto);
    align-items: center;
    justify-content: center;
    gap: 13px;

    margin-top: 23px;
  }

  .experience-circle {
    width: clamp(108px, 31vw, 126px);
    height: clamp(108px, 31vw, 126px);
  }

  .circle-inner {
    width: 86%;
    height: 86%;
    gap: 7px;
  }

  .circle-inner i {
    font-size: 19px;
  }

  .experience-title {
    align-items: center;
    gap: 1px;
  }

  .experience-prefix,
  .experience-suffix {
    font-size: 8px;
    letter-spacing: 0.02em;
  }

  .experience-highlight {
    font-size: 11px;
    letter-spacing: 0.11em;
  }

  /* Mobile closing sentence: one line */

  /* Mobile closing sentence */

  @media (max-width: 600px) {
    .closing-message {
      width: auto;
      max-width: none;
      margin: 19px auto 0;

      font-size: clamp(9px, 2.75vw, 12px);
      letter-spacing: -0.015em;
      line-height: 1.2;
      text-align: center;
      white-space: nowrap;
    }
  }

  /* iPhone SE and other short phones */

  @media (max-width: 600px) and (max-height: 700px) {
    .closing-message {
      margin-top: 14px;

      font-size: clamp(8.5px, 2.55vw, 10.5px);
      letter-spacing: -0.025em;
      white-space: nowrap;
    }
  }

  /* Very narrow phones */

  @media (max-width: 350px) {
    .closing-message {
      font-size: 8px;
      letter-spacing: -0.035em;
      white-space: nowrap;
    }
  }

  /* Mobile copyright */

  .copyright {
    right: 10px;
    bottom: max(9px, env(safe-area-inset-bottom));
    left: 10px;

    font-size: 7px;
  }
}

/* Short phones, including iPhone SE */

@media (max-width: 600px) and (max-height: 700px) {
  .filifinder-teaser {
    padding: max(8px, env(safe-area-inset-top)) 8px max(27px, env(safe-area-inset-bottom));
  }

  .teaser-content {
    max-width: 375px;
    transform: translateY(-2px);
  }

  .filifinder-logo {
    width: clamp(98px, 28vw, 112px);
  }

  .eyebrow {
    margin-top: 4px;

    font-size: 7.5px;
    letter-spacing: 0.17em;
  }

  .title-container {
    margin-top: 5px;
  }

  .title-container h1 {
    display: flex;
    align-items: baseline;
    flex-direction: row;
    justify-content: center;
    gap: 0.14em;

    font-size: clamp(26px, 8.1vw, 32px);
    line-height: 1;
    white-space: nowrap;
  }

  .title-container h1 span {
    display: inline-block;
    width: auto;
    flex-shrink: 0;
  }

  .experiences {
    gap: 10px;
    margin-top: 17px;
  }

  .experience-circle {
    width: clamp(96px, 28vw, 106px);
    height: clamp(96px, 28vw, 106px);
  }

  .circle-inner {
    gap: 5px;
  }

  .circle-inner i {
    font-size: 17px;
  }

  .experience-prefix,
  .experience-suffix {
    font-size: 7.5px;
  }

  .experience-highlight {
    font-size: 10px;
  }

  .closing-message {
    width: auto;
    max-width: none;
    margin-top: 14px;

    font-size: clamp(7.5px, 2.35vw, 9.5px);
    letter-spacing: 0;
    line-height: 1.2;
    white-space: nowrap;
  }

  .copyright {
    bottom: 6px;
    font-size: 6.5px;
  }
}

/* Very narrow phones */

@media (max-width: 350px) {
  .filifinder-logo {
    width: 96px;
  }

  .eyebrow {
    font-size: 7px;
    letter-spacing: 0.14em;
  }

  .title-container h1 {
    gap: 0.12em;
    font-size: 25px;
  }

  .experiences {
    gap: 8px;
  }

  .experience-circle {
    width: 94px;
    height: 94px;
  }

  .circle-inner i {
    font-size: 16px;
  }

  .experience-prefix,
  .experience-suffix {
    font-size: 7px;
  }

  .experience-highlight {
    font-size: 9.5px;
  }

  .closing-message {
    width: auto;
    max-width: none;

    font-size: 7.3px;
    white-space: nowrap;
  }
}

/* Short landscape screens */

@media (max-height: 560px) and (orientation: landscape) {
  .filifinder-teaser {
    padding: 8px 24px 24px;
  }

  .teaser-content {
    width: min(94vw, 950px);
  }

  .filifinder-logo {
    width: clamp(65px, 15vh, 84px);
  }

  .eyebrow {
    display: none;
  }

  .title-container {
    margin-top: 3px;
  }

  .title-container h1 {
    display: flex;
    align-items: baseline;
    flex-direction: row;
    justify-content: center;
    gap: 0.15em;

    font-size: clamp(32px, 9vh, 52px);
    line-height: 1;
    white-space: nowrap;
  }

  .title-container h1 span {
    width: auto;
  }

  .experiences {
    grid-template-columns: repeat(4, auto);
    gap: 12px;

    margin-top: 12px;
  }

  .experience-circle {
    width: clamp(68px, 17vh, 88px);
    height: clamp(68px, 17vh, 88px);
  }

  .circle-inner {
    gap: 3px;
  }

  .circle-inner i {
    font-size: clamp(12px, 3vh, 16px);
  }

  .experience-prefix,
  .experience-suffix {
    font-size: 6px;
  }

  .experience-highlight {
    font-size: 8px;
  }

  .closing-message {
    width: auto;
    max-width: none;
    margin-top: 9px;

    font-size: clamp(8px, 2.2vh, 12px);
    white-space: nowrap;
  }

  .copyright {
    bottom: 5px;
    font-size: 7px;
  }
}

/* Animations */

@media (prefers-reduced-motion: no-preference) {
  .ambient-light {
    animation: ambient-drift 9s ease-in-out infinite alternate;
  }

  .filifinder-logo {
    animation:
      logo-arrival 1.05s cubic-bezier(0.16, 1, 0.3, 1) both,
      logo-float 6s 1.05s ease-in-out infinite;
  }

  .eyebrow {
    animation: fade-up 800ms 150ms cubic-bezier(0.16, 1, 0.3, 1) both;
  }

  .title-container h1 {
    animation: title-arrival 1s 240ms cubic-bezier(0.16, 1, 0.3, 1) both;
  }

  .title-line {
    animation: line-reveal 1s 510ms cubic-bezier(0.16, 1, 0.3, 1) both;
  }

  .experience-circle {
    animation: circle-arrival 750ms var(--item-delay, 520ms) cubic-bezier(0.16, 1, 0.3, 1) both;
  }

  .experience-circle:nth-child(1) {
    --item-delay: 500ms;
  }

  .experience-circle:nth-child(2) {
    --item-delay: 590ms;
  }

  .experience-circle:nth-child(3) {
    --item-delay: 680ms;
  }

  .experience-circle:nth-child(4) {
    --item-delay: 770ms;
  }

  .experience-circle::before {
    animation: circle-shimmer 5s calc(var(--item-delay, 500ms) + 1s) ease-in-out infinite;
  }

  .closing-message {
    animation: fade-up 850ms 880ms cubic-bezier(0.16, 1, 0.3, 1) both;
  }

  @keyframes ambient-drift {
    from {
      transform: translateX(-52%) translateY(0);
    }

    to {
      transform: translateX(-48%) translateY(3%);
    }
  }

  @keyframes logo-arrival {
    from {
      opacity: 0;
      transform: translateY(-16px) scale(0.96);
    }

    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }

  @keyframes logo-float {
    0%,
    100% {
      transform: translateY(0);
    }

    50% {
      transform: translateY(-5px);
    }
  }

  @keyframes title-arrival {
    from {
      opacity: 0;
      transform: translateY(16px);
      letter-spacing: 0.07em;
      filter: blur(4px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
      letter-spacing: 0.015em;
      filter: blur(0);
    }
  }

  @keyframes fade-up {
    from {
      opacity: 0;
      transform: translateY(10px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes line-reveal {
    from {
      opacity: 0;
      transform: scaleX(0);
    }

    to {
      opacity: 1;
      transform: scaleX(1);
    }
  }

  @keyframes circle-arrival {
    from {
      opacity: 0;
      transform: translateY(18px) scale(0.88);
    }

    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }

  @keyframes circle-shimmer {
    0%,
    60% {
      left: -70%;
      opacity: 0;
    }

    72% {
      opacity: 1;
    }

    100% {
      left: 125%;
      opacity: 0;
    }
  }
}

/* Reduced-motion accessibility */

@media (prefers-reduced-motion: reduce) {
  .ambient-light,
  .filifinder-logo,
  .eyebrow,
  .title-container h1,
  .title-line,
  .experience-circle,
  .experience-circle::before,
  .closing-message {
    animation: none;
  }
}

/* Prevent sticky hover movement on touchscreen devices */

@media (hover: none) {
  .experience-circle:hover {
    transform: none;
  }
}

/* iPhone Safari and in-app browser safeguard */

@supports (-webkit-touch-callout: none) {
  .filifinder-logo {
    filter: none !important;
    -webkit-filter: none !important;
  }
}
</style>
