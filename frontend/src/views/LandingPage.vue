<template>
  <main class="filifinder-teaser">
    <!-- Improves readability over the beach background -->
    <div class="background-overlay" aria-hidden="true"></div>

    <!-- Main teaser content -->
    <section class="teaser-content" aria-labelledby="coming-soon-title">
      <img
        src="/assets/fili-logo.png"
        alt="FILIfinder"
        class="filifinder-logo"
        width="1220"
        height="1220"
        draggable="false"
      />

      <div class="title-container">
        <span class="title-line" aria-hidden="true"></span>

        <h1 id="coming-soon-title">COMING SOON</h1>

        <span class="title-line title-line-right" aria-hidden="true"></span>
      </div>

      <div class="categories" aria-label="Work, Live, Dine and Play">
        <template v-for="(category, index) in categories" :key="category">
          <span class="category">
            {{ category }}
          </span>

          <span
            v-if="index < categories.length - 1"
            class="category-separator"
            aria-hidden="true"
          ></span>
        </template>
      </div>
    </section>
  </main>
</template>

<script>
export default {
  name: 'LandingPage',

  data() {
    return {
      categories: ['WORK', 'LIVE', 'DINE', 'PLAY'],
    }
  },
}
</script>

<style scoped>
.filifinder-teaser {
  --orange: #ff9815;
  --white: #ffffff;
  --navy: #051637;

  position: fixed;
  isolation: isolate;
  inset: 0;

  display: flex;
  width: 100vw;
  min-width: 100vw;
  max-width: 100vw;

  height: 100vh;
  height: 100svh;
  height: 100dvh;

  min-height: 100vh;
  min-height: 100svh;
  min-height: 100dvh;

  align-items: center;
  justify-content: center;
  overflow: hidden;

  margin: 0;
  padding: clamp(20px, 4vw, 50px) clamp(16px, 5vw, 64px);

  /*
   * The image is now applied directly to the full-screen page.
   * This prevents a separate background layer from ending early.
   */
  background-color: #0c6f9e;
  background-image: url('/assets/backgrounds/filifinder-beach-xs.png');

  background-position: center center;
  background-repeat: no-repeat;
  background-size: cover;

  font-family: 'Montserrat', Arial, sans-serif;
}

/* Responsive backgrounds */

@media (min-width: 481px) {
  .filifinder-teaser {
    background-image: url('/assets/backgrounds/filifinder-beach-sm.png');
  }
}

@media (min-width: 769px) {
  .filifinder-teaser {
    background-image: url('/assets/backgrounds/filifinder-beach-md.png');
  }
}

@media (min-width: 1025px) {
  .filifinder-teaser {
    background-image: url('/assets/backgrounds/filifinder-beach-lg.png');
  }
}

@media (min-width: 1441px) {
  .filifinder-teaser {
    background-image: url('/assets/backgrounds/filifinder-beach-xl.png');
  }
}

/* Premium overlay */

.background-overlay {
  position: absolute;
  z-index: 1;
  inset: 0;

  width: 100%;
  height: 100%;

  background:
    radial-gradient(
      ellipse at center,
      rgba(255, 255, 255, 0.06) 0%,
      rgba(5, 26, 52, 0.03) 40%,
      rgba(3, 21, 45, 0.14) 74%,
      rgba(1, 16, 35, 0.29) 100%
    ),
    linear-gradient(
      180deg,
      rgba(3, 21, 45, 0.08) 0%,
      rgba(3, 29, 54, 0.02) 38%,
      rgba(1, 26, 48, 0.24) 100%
    );

  pointer-events: none;
}

/* Main content */

.teaser-content {
  position: relative;
  z-index: 5;
  display: flex;
  width: min(92vw, 1100px);
  align-items: center;
  flex-direction: column;
  justify-content: center;
  text-align: center;
}

/* Soft glow behind content */

.teaser-content::before {
  position: absolute;
  z-index: -1;
  top: 50%;
  left: 50%;

  width: min(110vw, 1150px);
  height: min(70vh, 650px);

  transform: translate(-50%, -50%);
  border-radius: 50%;

  background: radial-gradient(
    ellipse,
    rgba(4, 32, 54, 0.22) 0%,
    rgba(4, 32, 54, 0.08) 48%,
    transparent 72%
  );

  filter: blur(12px);
  content: '';
  pointer-events: none;
}

/* FILIfinder logo */

.filifinder-logo {
  display: block;
  width: clamp(155px, 15vw, 215px);
  height: auto;
  flex-shrink: 0;
  object-fit: contain;

  pointer-events: none;
  user-select: none;

  filter: drop-shadow(0 2px 1px rgba(255, 255, 255, 0.22))
    drop-shadow(0 16px 24px rgba(2, 18, 41, 0.3));
}

/* Coming Soon heading */

.title-container {
  display: grid;
  width: 100%;
  grid-template-columns:
    minmax(35px, 1fr)
    auto
    minmax(35px, 1fr);
  align-items: center;
  gap: clamp(18px, 2.7vw, 36px);

  margin-top: clamp(10px, 1.8vh, 18px);
}

.title-container h1 {
  margin: 0;
  color: var(--white);

  font-family: 'Cormorant Garamond', Georgia, serif;

  font-size: clamp(58px, 7.6vw, 116px);
  font-weight: 600;
  letter-spacing: clamp(0.05em, 0.55vw, 0.1em);
  line-height: 0.88;
  text-align: center;
  white-space: nowrap;

  text-shadow:
    0 2px 1px rgba(3, 19, 45, 0.32),
    0 8px 25px rgba(3, 19, 45, 0.5),
    0 0 45px rgba(3, 19, 45, 0.14);
}

/* Decorative lines */

.title-line {
  height: 1px;
  border-radius: 100px;
  transform-origin: right center;

  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.9));

  box-shadow: 0 1px 6px rgba(2, 18, 40, 0.24);
}

.title-line-right {
  transform-origin: left center;

  background: linear-gradient(90deg, rgba(255, 255, 255, 0.9), transparent);
}

/* Work, Live, Dine and Play */

.categories {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: clamp(11px, 1.5vw, 20px);

  margin-top: clamp(26px, 3.5vh, 38px);

  color: rgba(255, 255, 255, 0.96);

  font-family: 'Montserrat', Arial, sans-serif;

  font-size: clamp(11px, 1vw, 14px);
  font-weight: 700;
  letter-spacing: clamp(0.12em, 0.22vw, 0.25em);
  line-height: 1;
  text-align: center;

  text-shadow: 0 2px 10px rgba(2, 18, 40, 0.9);
}

.category {
  white-space: nowrap;
}

.category-separator {
  width: 4px;
  height: 4px;
  flex-shrink: 0;
  border-radius: 50%;

  background: var(--orange);

  box-shadow:
    0 0 8px rgba(255, 152, 21, 0.65),
    0 2px 5px rgba(5, 20, 61, 0.4);
}

/* Extra-large screens */

@media (min-width: 1441px) {
  .teaser-content {
    width: min(88vw, 1200px);
  }

  .filifinder-logo {
    width: clamp(190px, 11vw, 225px);
  }

  .title-container h1 {
    font-size: clamp(96px, 6.8vw, 124px);
  }

  .categories {
    font-size: clamp(12px, 0.85vw, 15px);
  }
}

/* iPhone and mobile */

@media (max-width: 540px) {
  .filifinder-teaser {
    padding: max(16px, env(safe-area-inset-top)) 8px max(16px, env(safe-area-inset-bottom));

    background-position: center center;
  }

  .background-overlay {
    background:
      radial-gradient(
        ellipse at center,
        rgba(255, 255, 255, 0.04) 0%,
        rgba(3, 30, 54, 0.05) 48%,
        rgba(1, 18, 39, 0.25) 100%
      ),
      linear-gradient(
        180deg,
        rgba(3, 21, 45, 0.06) 0%,
        rgba(3, 29, 54, 0.01) 38%,
        rgba(1, 26, 48, 0.26) 100%
      );
  }

  .teaser-content {
    width: 100%;
    max-width: 500px;
  }

  .teaser-content::before {
    width: 160vw;
    height: 70vh;
  }

  /*
   * Explicit dimensions for mobile.
   * These are intentionally much larger than the previous version.
   */
  .filifinder-logo {
    width: 290px;
    min-width: 250px;
    max-width: 76vw;
  }

  .title-container {
    display: block;
    width: 100%;
    margin-top: 18px;
  }

  .title-container h1 {
    font-size: 72px;
    font-size: min(19vw, 72px);
    font-weight: 600;
    letter-spacing: 0;
    line-height: 0.86;
  }

  .title-line {
    display: none;
  }

  .categories {
    width: 100%;
    gap: 10px;
    margin-top: 30px;

    font-size: 15px;
    font-weight: 700;
    letter-spacing: 0.04em;
  }

  .category-separator {
    width: 5px;
    height: 5px;
  }
}

/* Small phones below 360px */

@media (max-width: 359px) {
  .filifinder-logo {
    width: 235px;
    min-width: 0;
    max-width: 70vw;
  }

  .title-container {
    margin-top: 14px;
  }

  .title-container h1 {
    font-size: 54px;
    letter-spacing: 0;
  }

  .categories {
    gap: 7px;
    margin-top: 24px;

    font-size: 11px;
    letter-spacing: 0.02em;
  }

  .category-separator {
    width: 3px;
    height: 3px;
  }
}

/* Short landscape screens */

@media (max-height: 560px) and (orientation: landscape) {
  .filifinder-teaser {
    padding: 10px 25px;
  }

  .teaser-content {
    width: min(92vw, 900px);
  }

  .filifinder-logo {
    width: clamp(95px, 18vh, 125px);
    min-width: 0;
  }

  .title-container {
    display: grid;
    margin-top: 3px;
  }

  .title-container h1 {
    font-size: clamp(44px, 11vh, 72px);
  }

  .title-line {
    display: block;
  }

  .categories {
    margin-top: 14px;
    font-size: clamp(9px, 2vh, 12px);
  }
}

/* Entrance animations */

@media (prefers-reduced-motion: no-preference) {
  .filifinder-logo {
    animation:
      logo-arrival 1.05s cubic-bezier(0.16, 1, 0.3, 1) both,
      logo-float 6s 1.05s ease-in-out infinite;
  }

  .title-container h1 {
    animation: title-arrival 1s 250ms cubic-bezier(0.16, 1, 0.3, 1) both;
  }

  .title-line {
    animation: line-reveal 1s 580ms cubic-bezier(0.16, 1, 0.3, 1) both;
  }

  .categories {
    animation: categories-arrival 850ms 670ms cubic-bezier(0.16, 1, 0.3, 1) both;
  }

  @keyframes logo-arrival {
    from {
      opacity: 0;
      transform: translateY(-20px) scale(0.96);
      filter: blur(5px) drop-shadow(0 14px 22px rgba(2, 18, 41, 0.2));
    }

    to {
      opacity: 1;
      transform: translateY(0) scale(1);
      filter: blur(0) drop-shadow(0 2px 1px rgba(255, 255, 255, 0.22))
        drop-shadow(0 16px 24px rgba(2, 18, 41, 0.3));
    }
  }

  @keyframes logo-float {
    0%,
    100% {
      transform: translateY(0);
    }

    50% {
      transform: translateY(-6px);
    }
  }

  @keyframes title-arrival {
    from {
      opacity: 0;
      transform: translateY(18px);
      letter-spacing: 0.14em;
      filter: blur(5px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
      filter: blur(0);
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

  @keyframes categories-arrival {
    from {
      opacity: 0;
      transform: translateY(12px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
}

/* Reduced-motion accessibility */

@media (prefers-reduced-motion: reduce) {
  .filifinder-logo,
  .title-container h1,
  .title-line,
  .categories {
    animation: none;
  }
}
</style>
