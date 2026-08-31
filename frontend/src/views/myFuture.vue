<template>
  <Navbar @open-booking-modal="openBookingModal" @open-contact-modal="showContactModal = true" />
  <ContactUsModal :show="showContactModal" @close="showContactModal = false" />

  <!-- Hero Banner (half-screen) -->
  <section class="relative w-full h-[60vh] overflow-hidden">
    <img
      src="/assets/logo.png"
      alt="Hero background"
      class="absolute inset-0 w-full h-full object-cover"
    />
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <!-- Content container placed above the overlay -->
    <div class="relative z-10 flex flex-col items-center justify-center h-full px-4 text-center">
      <h1
        class="text-white font-oswald tracking-wide text-4xl md:text-6xl animate-fadeIn"
        style="animation-delay: 0s"
      >
        Find Your Balance
      </h1>
      <p
        class="text-white font-quicksand mt-2 md:mt-4 text-sm md:text-lg animate-fadeIn"
        style="animation-delay: 0s"
      >
        Breath deeply. Move Mindfully. Balance your body.
      </p>
    </div>
  </section>

  <!-- Our Studio Vision -->
  <section v-scrollAnimation class="bg-white">
    <div class="container mx-auto px-4 sm:px-6 md:px-12 lg:px-16 xl:px-40 py-12">
      <div class="flex flex-col md:flex-row md:gap-x-8">
        <div class="w-full md:w-1/2 mb-8 md:mb-0">
          <h2 id="1" class="text-3xl md:text-5xl font-oswald font-thin mb-8 mt-2 md:mt-30">
            Our Mission?
          </h2>
          <p id="2" class="text-gray-700 font-quicksand text-base md:text-lg mb-6 text-justify">
            At Balance Studio, our mission is to create an inclusive community where movement,
            mindfulness, and community unite. Rooted in Pilates and Yoga, and expanding into diverse
            disciplines such as kiddie ballet, Muay Thai, and more, we offer thoughtfully curated
            classes that support strength, confidence, creativity, and inner balance at every stage
            of life.
          </p>
          <p id="3" class="text-gray-700 font-quicksand text-base md:text-lg mb-6 text-justify">
            Our modern, minimalist studio is intentionally designed to inspire calm, focus, and
            growth. From fully equipped Pilates spaces to dynamic movement studios, every detail is
            crafted to support a holistic wellness journey physically, mentally, and emotionally.
          </p>
          <p id="4" class="text-gray-700 font-quicksand text-base md:text-lg mb-12 text-justify">
            We welcome everyone from beginners to seasoned practitioners, from children to adults,
            men and women offering expert-led private and group classes tailored to individual
            goals. At Balance Studio, we believe balance is not one practice, but a way of living
            built through movement, connection, and mindful exploration.
          </p>
        </div>

        <div class="w-full md:w-1/2">
          <!-- Highlight: PRIVATE SESSION -->
          <div class="mb-8">
            <h3 id="5" class="text-2xl md:text-3xl font-oswald font-normal mb-3 mt-4">
              PRIVATE SESSION
            </h3>
            <p id="6" class="text-gray-700 font-quicksand text-base md:text-lg mb-4">
              A one-on-one session tailored to your individual goals, fitness level, and needs,
              providing personalized guidance and focused attention.
            </p>
            <div class="flex items-center gap-1 md:gap-2 text-yellow-500 text-xs md:text-sm mb-4">
              <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i
              ><i class="fas fa-star"></i><i class="fas fa-star"></i>
            </div>
          </div>
          <hr class="border-t border-gray-300 mb-8" />

          <!-- Highlight: DUO/TRIO Classes -->
          <div class="mb-8">
            <h3 id="7" class="text-2xl md:text-3xl font-oswald font-normal mb-3">
              DUO/TRIO SESSION
            </h3>
            <p id="8" class="text-gray-700 font-quicksand text-base md:text-lg mb-4">
              A semi-private session for two/three people, offering customized instruction while
              allowing for shared motivation and support.
            </p>
            <div class="flex items-center gap-1 md:gap-2 text-yellow-500 text-xs md:text-sm mb-4">
              <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i
              ><i class="fas fa-star"></i><i class="fas fa-star"></i>
            </div>
          </div>
          <hr class="border-t border-gray-300 mb-8" />

          <!-- Highlight: GROUP Sessions -->
          <div>
            <h3 id="9" class="text-2xl md:text-3xl font-oswald font-normal mb-3">GROUP SESSIONS</h3>
            <p id="10" class="text-gray-700 font-quicksand text-base md:text-lg mb-4">
              A dynamic class with up to 4-6 participants, combining the energy of a group setting
              with professional instruction and full-body reformer workouts.
            </p>
            <div class="flex items-center gap-1 md:gap-2 text-yellow-500 text-xs md:text-sm">
              <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i
              ><i class="fas fa-star"></i><i class="fas fa-star"></i>
            </div>
          </div>
          <hr class="border-t border-gray-300 mt-8" />

          <!-- Highlight: Yoga Class -->
          <div>
            <h3 id="9" class="text-2xl md:text-3xl font-oswald font-normal mb-3 mt-8">
              YOGA CLASS
            </h3>
            <p id="10" class="text-gray-700 font-quicksand text-base md:text-lg mb-4">
              Suitable for all levels, this class guides you through a flow of poses that enhance
              both physical and mental well-being, leaving you feeling centered, energized, and
              refreshed.
            </p>
            <div class="flex items-center gap-1 md:gap-2 text-yellow-500 text-xs md:text-sm">
              <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i
              ><i class="fas fa-star"></i><i class="fas fa-star"></i>
            </div>
          </div>
          <hr class="border-t border-gray-300 mt-8" />
        </div>
      </div>
    </div>
  </section>

  <!-- Amenities -->
  <section v-scrollAnimation class="bg-gray-100 overflow-hidden">
    <div class="container mx-auto flex flex-col md:flex-row">
      <div class="w-full md:w-1/2 px-4 sm:px-6 md:px-12 lg:px-16 xl:px-40 py-12">
        <div class="max-w-lg">
          <h2 id="11" class="text-2xl md:text-4xl font-oswald font-normal mb-4">Studio Features</h2>
          <p id="12" class="text-gray-700 font-quicksand text-base md:text-lg mb-6">
            Designed to inspire and rejuvenate, our facilities include modern equipment and tranquil
            spaces to support every aspect of your wellness practice.
          </p>
          <ul
            id="13"
            class="list-disc list-inside text-gray-700 font-quicksand text-base md:text-lg"
          >
            <li class="md:mb-2">Reformer Machines & Yoga Props</li>
            <li class="md:mb-2">Air-conditioned Practice Studios</li>
            <li class="md:mb-2">Changing Room</li>
            <li>Shower Room</li>
          </ul>

          <h2 id="14" class="text-2xl md:text-4xl font-oswald font-normal mb-4 mt-10">
            Our Commitment
          </h2>
          <p id="15" class="text-gray-700 font-quicksand text-base md:text-lg mb-6">
            We’re here to create a space where every individual feels supported, seen, and empowered
            through movement, mindfulness, and meaningful connection.
          </p>
        </div>
      </div>

      <div class="w-full md:w-1/2">
        <div
          id="17"
          class="h-64 md:h-full bg-cover bg-center block"
          oncontextmenu="return false;"
          style="background-image: url('./assets/image-4.webp')"
        ></div>
      </div>
    </div>
  </section>

  <!-- Benefits -->
  <section v-scrollAnimation class="bg-gray-200 overflow-hidden">
    <div class="container mx-auto px-4 sm:px-6 md:px-12 lg:px-16 xl:px-40 py-12">
      <div class="max-w-3xl mx-auto text-center">
        <h2 id="18" class="text-3xl md:text-6xl font-oswald font-thin mb-10 md:mb-35 md:mt-15">
          Why Choose Us?
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10 md:mb-20">
          <div class="text-center">
            <p id="19" class="text-gray-700 font-quicksand text-xs md:text-lg font-semibold">
              <i class="fas fa-award text-yellow-500 text-5xl md:text-7xl"></i>
              <br /><br />
              Certified Instructors with Personalized Guidance
            </p>
          </div>
          <div class="text-center">
            <p id="20" class="text-gray-700 font-quicksand font-semibold text-xs md:text-lg">
              <i class="fas fa-clock text-yellow-500 text-5xl md:text-7xl"></i><br /><br />
              Class Schedules to Fit Your Lifestyle
            </p>
          </div>
          <div class="text-center">
            <p id="21" class="text-gray-700 font-quicksand font-semibold text-xs md:text-lg">
              <i class="fas fa-handshake text-yellow-500 text-5xl md:text-7xl"></i><br /><br />
              Welcoming Environment for All Levels
            </p>
          </div>
        </div>
        <div class="text-center">
          <button
            id="22"
            class="font-cocana font-bold text-sm md:text-lg md:mb-20 md:mt-10 px-6 py-3 border border-gray-800 text-gray-800 hover:bg-gray-800 hover:text-white transition duration-300 animate-slideUpBounce"
            @click="showLoginModal = true"
          >
            JOIN A CLASS
          </button>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ Section -->
  <section v-scrollAnimation class="bg-white overflow-hidden">
    <div class="container mx-auto px-4 sm:px-6 md:px-12 lg:px-16 xl:px-40 py-12">
      <h2
        id="29"
        class="text-3xl md:text-6xl font-cocana font-bold mb-8 md:mb-20 md:mt-15 text-center"
      >
        FAQs
      </h2>
      <div class="md:mb-20">
        <div
          id="30"
          v-for="(item, index) in faqs"
          :key="index"
          class="mb-2 md:mb-4 border-b border-gray-200"
        >
          <button
            @click="toggle(index)"
            class="w-full flex justify-between items-center py-4 text-left focus:outline-none"
          >
            <span class="font-quicksand font-semibold text-md md:text-xl">{{ item.question }}</span>
            <i
              :class="[
                'fas',
                item.open ? 'fa-chevron-up' : 'fa-chevron-down',
                'transition-transform',
              ]"
            ></i>
          </button>
          <div
            v-if="item.open"
            class="pl-4 pr-4 pb-4 text-gray-700 font-quicksand text-md md:text-lg"
          >
            {{ item.answer }}
          </div>
        </div>
      </div>
    </div>
  </section>

  <LoginModal :show="showLoginModal" @close="showLoginModal = false" />

  <myFooter />
</template>

<script>
import Navbar from '../components/myNavbar.vue'
import myFooter from '../components/myFooter.vue'
import LoginModal from '../components/LoginModal.vue'
import ContactUsModal from '@/components/ContactUsModal.vue'

export default {
  name: 'myAbout',
  components: {
    Navbar,
    myFooter,
    LoginModal,
    ContactUsModal,
  },
  directives: {
    scrollAnimation: {
      mounted(el) {
        // 1. Grab all children with numeric IDs, sorted ascending
        const elements = Array.from(el.querySelectorAll('[id]'))
          .filter((child) => /^\d+$/.test(child.id))
          .sort((a, b) => Number(a.id) - Number(b.id))

        // 2. Reset helper: remove any transition/show classes and hide them
        function resetAll() {
          elements.forEach((child) => {
            child.classList.remove(
              'transition',
              'duration-700',
              'ease-out',
              'opacity-100',
              'translate-y-0',
            )
            child.classList.add('opacity-0', 'translate-y-10')
          })
        }

        // initial hide
        resetAll()

        // 3. Track scroll position for direction
        let lastScrollY = window.pageYOffset
        const threshold = 0.3
        const stagger = 200 // ms between items

        // 4. Factory for a fresh observer instance
        function createChildObserver() {
          return new IntersectionObserver(
            (entries, observer) => {
              const currentScrollY = window.pageYOffset
              const scrollingDown = currentScrollY > lastScrollY
              lastScrollY = currentScrollY

              // only the newly intersecting ones
              const visible = entries.filter((e) => e.isIntersecting)

              // sort by ID based on scroll direction
              visible.sort((a, b) => {
                const aId = Number(a.target.id),
                  bId = Number(b.target.id)
                return scrollingDown ? aId - bId : bId - aId
              })

              // stagger reveal and unobserve each
              visible.forEach((entry, idx) => {
                setTimeout(() => {
                  const t = entry.target
                  t.classList.add(
                    'transition',
                    'duration-700',
                    'ease-out',
                    'opacity-100',
                    'translate-y-0',
                  )
                  t.classList.remove('opacity-0', 'translate-y-10')
                  observer.unobserve(t)
                }, idx * stagger)
              })
            },
            { threshold },
          )
        }

        // 5. Start with a fresh childObserver
        let childObserver = createChildObserver()
        elements.forEach((child) => childObserver.observe(child))

        // 6. Observe the section itself to detect leave/re-entry
        const sectionObserver = new IntersectionObserver(
          ([sec]) => {
            if (!sec.isIntersecting) {
              // left the section → reset visuals and kill the old observer
              resetAll()
              childObserver.disconnect()
              childObserver = null
            } else {
              // re-entered → reset scroll baseline, rebuild observer, re-observe
              lastScrollY = window.pageYOffset
              childObserver = createChildObserver()
              elements.forEach((child) => {
                // ensure hidden state before re-observing
                child.classList.add('opacity-0', 'translate-y-10')
                childObserver.observe(child)
              })
            }
          },
          { threshold: 0 },
        )

        sectionObserver.observe(el)
      },
    },
  },
  data() {
    return {
      showContactModal: false,
      showLoginModal: false,
      faqs: [
        {
          question: 'What happens during my first Reformer Pilates session?',
          answer:
            'You’ll be warmly welcomed and guided through each movement by our certified instructors. The class will focus on familiarizing you with the Reformer machine, proper alignment, and breathing techniques.',
          open: false,
        },
        {
          question: 'Is prior Pilates experience required?',
          answer:
            'Not at all! Our classes are beginner-friendly and suitable for all fitness levels. Instructors will provide adjustments to suit your personal needs and abilities.',
          open: false,
        },
        {
          question: 'How long does a typical class last?',
          answer:
            'Each session runs for around 55 minutes, which includes a short warm-up, the core workout, and a guided cool-down.',
          open: false,
        },
        {
          question: 'What are your studio hours?',
          answer:
            'We’re open every day from 8:00 AM to 8:00 PM. For class-specific times, please reach out directly.',
          open: false,
        },
        {
          question: 'Can I book a one-on-one session?',
          answer:
            'Yes! We offer personalized private sessions ideal for focused goals, rehab needs, or those who prefer individual attention. Contact us to schedule.',
          open: false,
        },
        {
          question: 'Do you offer Packages?',
          answer:
            'Absolutely. We have a range of flexible packages designed to match your goals, lifestyle, and budget.',
          open: false,
        },
        {
          question: 'How do you maintain health and safety?',
          answer:
            'Our studio follows strict cleanliness protocols including frequent disinfection of equipment. We recommend bringing a personal towel for added hygiene.',
          open: false,
        },
        {
          question: 'What should I prepare before coming to class?',
          answer: 'Grip socks are required for pilates. Please wear comfortable activewear.',
          open: false,
        },
        {
          question: 'Can I share, transfer, or refund my class package?',
          answer:
            'Class packages are intended for individual use only and are non-shareable, non-transferable, and non-refundable.',
          open: false,
        },
      ],
      showModal: false,
    }
  },
  methods: {
    openBookingModal() {
      this.showLoginModal = true
    },
    toggle(index) {
      this.faqs[index].open = !this.faqs[index].open
    },
    // openModal() {
    //   this.showModal = true
    // },
    // closeModal() {
    //   this.showModal = false
    // },
  },
}
</script>

<style scoped>
/* Fade in for header and paragraph */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fadeIn {
  animation: fadeIn 0.8s ease-out forwards;
}

/* Slide up animation for button (initial appearance) */
@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Bounce animation for button (to draw extra attention) */
@keyframes bounce {
  0%,
  20%,
  50%,
  80%,
  100% {
    transform: translateY(0);
  }

  40% {
    transform: translateY(-15px);
  }

  60% {
    transform: translateY(-7px);
  }
}

/* Combined animation for button: first slide up, then bounce continuously */
.animate-slideUpBounce {
  animation:
    slideUp 0.8s ease-out forwards,
    bounce 1s 1.4s infinite;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fadeIn {
  animation: fadeIn 0.8s ease-out forwards;
}
</style>
