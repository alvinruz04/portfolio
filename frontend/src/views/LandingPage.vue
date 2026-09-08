<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

const menuOpen = ref(false)
const activeProject = ref(null)

const projects = [
  {
    name: 'Intelogram',
    eyebrow: 'Government operations · Verification platform',
    image: '/assets/projects/intelogram.webp',
    link: 'https://intelogram.ph/',
    problem:
      'Government clients needed a reliable way to record and monitor the use of serialized security stickers.',
    solution:
      'I built and maintain the complete web application: client encoding, Excel bulk upload, sticker records, and a public serial verification experience.',
    outcome:
      'Physical tamper-evident labels gained a second layer of authenticity through online verification.',
    features: [
      'Serial verification',
      'Excel bulk upload',
      'Usage monitoring',
      'Government and public access',
    ],
    stack: 'HTML · CSS · PHP',
    status: 'Live',
  },
  {
    name: 'Balance Studio',
    eyebrow: 'Wellness operations · Booking system',
    image: '/assets/projects/balance-studio.webp',
    link: 'https://balancestudio.ph/',
    problem:
      'A new Pilates and yoga studio needed an organized way to receive bookings and manage its schedule.',
    solution:
      'I delivered the customer experience and the operational side from the landing page and booking flow to the administrative calendar and approvals.',
    outcome:
      'Customers can book online while administrators can approve requests and track upcoming studio sessions in one place.',
    features: ['Customer booking', 'Admin approvals', 'Schedule calendar', 'Responsive website'],
    stack: 'Vue.js · PHP',
    status: 'Live',
  },
  {
    name: 'You Glow Babe',
    eyebrow: 'Commerce operations · Seller directory',
    image: '/assets/projects/you-glow-babe.webp',
    link: 'https://youglowbabemain.com/',
    problem:
      'The brand’s previous backend was no longer working, and customers could not reliably search for legitimate resellers or reported fake pages.',
    solution:
      'I rebuilt the public website and admin dashboard, including account access, a searchable seller locator, and a controlled fake-seller directory.',
    outcome:
      'Customers can now check authentic sellers and suspicious links while administrators maintain every record from a working dashboard.',
    features: ['Seller locator', 'Fake-seller records', 'Customer accounts', 'Admin dashboard'],
    stack: 'Vue.js · PHP',
    status: 'Live',
  },
  {
    name: 'Pagmaya Beach Resort',
    eyebrow: 'Hospitality operations · Investment tracking',
    image: '/assets/projects/pagmaya.webp',
    link: 'https://www.pagmayabeachresort.com/',
    problem:
      'The business needed a clearer way to monitor investors, investment tiers, contributions, payment schedules, and remaining balances.',
    solution:
      'I built the complete internal investment tracker used by Pagmaya administrators, with structured records and automatic computations.',
    outcome:
      'Investment accounting became easier to monitor because schedules and balances are calculated and recorded inside one system.',
    features: ['Investor records', 'Tier monitoring', 'Payment schedules', 'Automatic balances'],
    stack: 'HTML · CSS · PHP',
    status: 'Live system',
  },
  {
    name: 'FILIfinder',
    eyebrow: 'Travel and services · Phase 1',
    image: '/assets/projects/filifinder.webp',
    link: 'https://filifinder.com/',
    problem:
      'A new business needed a credible platform beyond social media while its broader service ecosystem is being developed.',
    solution:
      'Phase 1 establishes FILIfinder’s online presence with a responsive informational experience for its approved divisions, services, partnerships, and inquiries.',
    outcome:
      'The website creates an owned digital foundation that can later support planned booking, property inquiry, and other transactional capabilities.',
    features: [
      'Brand introduction',
      'Service discovery',
      'Inquiry paths',
      'Expansion-ready foundation',
    ],
    stack: 'Vue.js',
    status: 'In development',
  },
]

const closeMenu = () => {
  menuOpen.value = false
}
const closeModal = () => {
  activeProject.value = null
}
const onKeydown = (event) => {
  if (event.key === 'Escape') {
    closeMenu()
    closeModal()
  }
}

onMounted(() => window.addEventListener('keydown', onKeydown))
onBeforeUnmount(() => window.removeEventListener('keydown', onKeydown))
</script>

<template>
  <div class="site-shell">
    <header class="nav-wrap">
      <a href="#top" class="brand" @click="closeMenu" aria-label="John Alvin Ruz, home">
        <span class="brand-mark">JR</span>
        <span class="brand-name">John Alvin Ruz</span>
      </a>
      <button
        class="menu-button"
        :aria-expanded="menuOpen"
        aria-label="Toggle navigation"
        @click="menuOpen = !menuOpen"
      >
        <span></span><span></span>
      </button>
      <nav :class="['nav-links', { open: menuOpen }]" aria-label="Primary navigation">
        <a href="#work" @click="closeMenu">Selected work</a>
        <a href="#services" @click="closeMenu">Services</a>
        <a href="#process" @click="closeMenu">Process</a>
        <a href="#about" @click="closeMenu">About</a>
        <a class="nav-cta" href="mailto:alvinruz04@gmail.com">Let’s talk <span>↗</span></a>
      </nav>
    </header>

    <main id="top">
      <section class="hero section-pad">
        <div class="hero-copy">
          <p class="kicker"><span></span> Business systems developer · Quezon City, PH</p>
          <h1>I turn manual work into <em>systems that flow</em></h1>
          <p class="hero-lede">
            I analyze operational pain points, simplify workflows, and build secure web platforms
            that help businesses work with less friction and more control.
          </p>
          <p class="ai-note">
            <span>AI-assisted practice</span> I use ChatGPT by OpenAI as a guide for research,
            planning, coding support, and problem-solving while remaining responsible for every
            technical decision and delivered result.
          </p>
          <div class="hero-actions">
            <a class="button primary" href="#work">Explore selected work <span>↓</span></a>
            <a class="text-link" href="mailto:alvinruz04@gmail.com"
              >Discuss a project <span>↗</span></a
            >
          </div>
        </div>
        <div class="hero-visual" aria-label="From operational problem to practical web system">
          <div class="portrait-frame"><img src="/assets/profile.webp" alt="John Alvin Ruz" /></div>
          <div class="signal-card signal-one"><b>01</b><span>Understand the process</span></div>
          <div class="signal-card signal-two"><b>02</b><span>Remove the friction</span></div>
          <div class="signal-card signal-three"><b>03</b><span>Build for daily use</span></div>
        </div>
        <div class="hero-foot">
          <span>Process analysis</span><span>Workflow digitalization</span
          ><span>Secure web development</span>
        </div>
      </section>

      <section class="intro section-pad">
        <p class="section-label">What I actually do</p>
        <div class="intro-grid">
          <h2>Development starts with understanding how the business works</h2>
          <div>
            <p>
              I study where tasks slow down, where records get lost, and where people repeat manual
              work. Then I translate those findings into a practical platform your team can
              confidently use.
            </p>
            <div class="principles">
              <div><b>Analyze</b><span>Map the real workflow and its pain points.</span></div>
              <div><b>Simplify</b><span>Design a clearer, more efficient process.</span></div>
              <div><b>Build</b><span>Deliver a secure system around the operation.</span></div>
            </div>
          </div>
        </div>
      </section>

      <section class="ai-practice section-pad" aria-labelledby="ai-practice-title">
        <div class="ai-symbol" aria-hidden="true"><span>AI</span></div>
        <div class="ai-heading">
          <p class="section-label">How I work with AI</p>
          <h2 id="ai-practice-title">
            AI-assisted<br />
            <span class="nowrap">Human accountable</span>
          </h2>
        </div>
        <div class="ai-copy">
          <p>
            I use ChatGPT by OpenAI as a working guide across many of my projects. It helps me
            explore solutions, refine requirements, think through code, troubleshoot issues, and
            learn faster throughout development.
          </p>
          <p>
            AI supports the process while I remain responsible for understanding the client’s
            operation, choosing the architecture, validating the implementation, protecting data,
            testing the system, and delivering work that fits real users.
          </p>
          <div class="ai-uses">
            <span>Research</span><span>Requirements</span><span>Solution planning</span
            ><span>Coding support</span><span>Debugging</span><span>Documentation</span>
          </div>
        </div>
      </section>

      <section id="work" class="work section-pad">
        <div class="section-heading">
          <div>
            <p class="section-label light">Selected work</p>
            <h2>Built around real operations.</h2>
          </div>
          <p>Each project began with a business need not a feature list.</p>
        </div>
        <div class="projects">
          <article
            v-for="(project, index) in projects"
            :key="project.name"
            class="project-card"
            :class="{ featured: index === 0 }"
          >
            <button
              class="project-image"
              @click="activeProject = project"
              :aria-label="`View ${project.name} case study`"
            >
              <img :src="project.image" :alt="`${project.name} website preview`" />
              <span class="image-action">View case study ↗</span>
            </button>
            <div class="project-meta">
              <span>{{ project.eyebrow }}</span
              ><span>{{ project.status }}</span>
            </div>
            <h3>{{ project.name }}</h3>
            <p>{{ project.outcome }}</p>
            <div class="project-bottom">
              <span>{{ project.stack }}</span
              ><button @click="activeProject = project">Read the story <span>→</span></button>
            </div>
          </article>
        </div>
      </section>

      <section id="services" class="services section-pad">
        <div class="section-heading dark">
          <div>
            <p class="section-label">Ways I can help</p>
            <h2>From bottleneck to better workflow.</h2>
          </div>
        </div>
        <div class="service-list">
          <div>
            <span>01</span>
            <h3>Business process analysis</h3>
            <p>
              Clarify the current workflow, recurring problems, users, records, and decisions before
              development begins.
            </p>
          </div>
          <div>
            <span>02</span>
            <h3>Custom web systems</h3>
            <p>
              Build practical platforms, dashboards, trackers, booking tools, and approval workflows
              around your operation.
            </p>
          </div>
          <div>
            <span>03</span>
            <h3>Process digitalization</h3>
            <p>
              Replace spreadsheets, paper records, and repetitive coordination with structured,
              accessible digital workflows.
            </p>
          </div>
          <div>
            <span>04</span>
            <h3>System improvement</h3>
            <p>
              Repair or rebuild systems that no longer support the people and processes they were
              meant to serve.
            </p>
          </div>
        </div>
      </section>

      <section id="process" class="process section-pad">
        <p class="section-label">How we get there</p>
        <div class="process-head">
          <h2>A clear path from conversation to working system</h2>
          <p>
            You stay involved at the decisions that matter, with the work broken into practical
            stages.
          </p>
        </div>
        <ol class="process-steps">
          <li>
            <span>01</span>
            <div>
              <h3>Discover</h3>
              <p>Understand the business, users, records, and existing process.</p>
            </div>
          </li>
          <li>
            <span>02</span>
            <div>
              <h3>Design the workflow</h3>
              <p>Remove unnecessary steps and define a system people can use.</p>
            </div>
          </li>
          <li>
            <span>03</span>
            <div>
              <h3>Build and review</h3>
              <p>Develop in manageable stages and validate the experience together.</p>
            </div>
          </li>
          <li>
            <span>04</span>
            <div>
              <h3>Launch and support</h3>
              <p>Deploy, guide users, and keep the platform dependable.</p>
            </div>
          </li>
        </ol>
      </section>

      <section id="about" class="about section-pad">
        <div class="about-copy">
          <p class="section-label light">About</p>
          <h2>I build for the people doing the work every day</h2>
          <p>
            I’m John Alvin Ruz, a web developer and technology consultant based in Quezon City,
            Philippines. I work with businesses and organizations to understand their operations,
            find inefficient processes, and turn them into web systems that are organized, usable,
            and secure.
          </p>
          <p>
            My experience spans government verification, hospitality investment tracking, wellness
            bookings, and customer and seller management.
          </p>
          <p>
            I practice transparent AI-assisted development, using ChatGPT as a guide while keeping
            technical judgment, validation, client communication, and accountability in my hands.
          </p>
        </div>
        <div class="toolkit">
          <p>Core toolkit</p>
          <div>
            <span>Vue.js</span><span>Laravel</span><span>PHP</span><span>Tailwind CSS</span
            ><span>MySQL / MariaDB</span><span>REST APIs</span>
          </div>
          <p class="availability"><i></i> Available for select freelance projects</p>
        </div>
      </section>

      <section id="contact" class="contact section-pad">
        <p class="section-label">Start a conversation</p>
        <h2>What part of your operation still feels harder than it should?</h2>
        <p>
          Tell me about the manual process, spreadsheet, or outdated system slowing your team down.
          We’ll start by making the problem clear.
        </p>
        <a class="button primary" href="mailto:alvinruz04@gmail.com?subject=Project%20inquiry"
          >Discuss your project <span>↗</span></a
        >
        <a class="email" href="mailto:alvinruz04@gmail.com">alvinruz04@gmail.com</a>
      </section>
    </main>

    <footer>
      <div><span class="brand-mark">JR</span><span>John Alvin Ruz</span></div>
      <p>Business systems developer · AI-assisted practice</p>
      <p>© {{ new Date().getFullYear() }} John Alvin Ruz</p>
    </footer>

    <div
      v-if="activeProject"
      class="modal"
      role="dialog"
      aria-modal="true"
      :aria-label="`${activeProject.name} case study`"
      @click.self="closeModal"
    >
      <div class="modal-panel">
        <button class="modal-close" @click="closeModal" aria-label="Close case study">×</button>
        <div class="modal-image">
          <img :src="activeProject.image" :alt="`${activeProject.name} website preview`" />
        </div>
        <div class="modal-content">
          <p class="section-label">{{ activeProject.eyebrow }}</p>
          <h2>{{ activeProject.name }}</h2>
          <div class="case-grid">
            <div>
              <span>The challenge</span>
              <p>{{ activeProject.problem }}</p>
            </div>
            <div>
              <span>The solution</span>
              <p>{{ activeProject.solution }}</p>
            </div>
            <div>
              <span>The result</span>
              <p>{{ activeProject.outcome }}</p>
            </div>
          </div>
          <div class="feature-tags">
            <span v-for="feature in activeProject.features" :key="feature">{{ feature }}</span>
          </div>
          <div class="modal-footer">
            <span>{{ activeProject.stack }}</span
            ><a :href="activeProject.link" target="_blank" rel="noopener">Visit website ↗</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
