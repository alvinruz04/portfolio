<template>
  <div class="bg-white overflow-hidden">
    <Navbar @open-booking-modal="openBookingModal" @open-contact-modal="showContactModal = true" />
    <ContactUsModal :show="showContactModal" @close="showContactModal = false" />

    <!-- Hero Section -->
    <section
      v-scrollAnimation
      class="relative h-screen bg-cover bg-center"
      style="background-image: url('./assets/image-2.webp')"
    >
      <div class="absolute inset-0 bg-black opacity-60"></div>
      <div class="relative z-10 flex flex-col items-center justify-center h-full px-4 text-center">
        <h1 class="text-white font-quicksand tracking-wider text-3xl md:text-5xl animate-fadeIn">
          BREATHE, MOVE, BALANCE
        </h1>
        <p id="1" class="text-white font-quicksand mt-3 md:mt-4 text-xs md:text-lg animate-fadeIn">
          Join our community for uplifting classes that inspire strength, flexibility, and a sense
          of inner peace
        </p>
        <button
          id="2"
          class="mt-15 md:mt-21 px-6 py-3 text-lg tracking-wider font-quicksand text-white border-white border hover:border-[#E8CEB0] hover:text-[#E8CEB0]"
          @click="openBookingModal"
        >
          BOOK NOW !
        </button>
      </div>
    </section>

    <!-- Introduction Section -->
    <section
      v-scrollAnimation
      id="introduction"
      class="py-10 md:py-16 px-6 md:px-12 bg-white text-black"
    >
      <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-center md:mt-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <h2 id="3" class="text-2xl md:text-4xl text-[#7B4F2E] font-oswald mb-4">
            What Awaits You?
          </h2>
          <p
            id="4"
            class="text-base font-quicksand md:text-xl text-gray-700 md:leading-loose mt-2 text-justify tracking-wide"
          >
            A journey beyond the ordinary. Our classes are designed to reconnect you with your inner
            balance flow through mindful movement, breathe with intention, and discover the
            transformative power of Pilates and Yoga.
          </p>
        </div>
        <div>
          <img
            id="5"
            src="/assets/image-3.webp"
            alt="Balance Studio Introduction"
            class="w-full transition-transform duration-300 hover:scale-105 shadow-xl"
            oncontextmenu="return false;"
          />
        </div>
      </div>
    </section>

    <!-- Pricing Section -->
    <section v-scrollAnimation class="bg-gray-100 py-16">
      <div class="container mx-auto px-4 sm:px-6 md:px-12 lg:px-16 xl:px-40">
        <div class="text-center mb-12">
          <h2 id="6" class="text-3xl md:text-5xl font-thin font-oswald mb-4 uppercase">
            Flexible Plans for Every <strong>Body</strong>
          </h2>
          <p id="7" class="text-gray-700 font-quicksand text-base md:text-xl leading-relaxed">
            We offer accessible pricing to help you stay committed and reach your fitness goals with
            ease.
          </p>
        </div>

        <div class="relative">
          <div
            ref="scrollContainer"
            class="flex space-x-4 overflow-x-auto no-scrollbar snap-x snap-start px-2 pb-4 pt-6 md:pt-8 cursor-grab active:cursor-grabbing select-none"
            style="scroll-snap-type: x mandatory"
            @scroll="handleScroll"
          >
            <!-- Clone Before -->
            <template v-for="(plan, index) in uiPlans" :key="'start-' + index">
              <PricingCard id="8" :plan="plan" :hoverEffect="true" />
            </template>
            <!-- Original -->
            <template v-for="(plan, index) in uiPlans" :key="'main-' + index">
              <PricingCard id="9" :plan="plan" :hoverEffect="true" />
            </template>
            <!-- Clone After -->
            <template v-for="(plan, index) in uiPlans" :key="'end-' + index">
              <PricingCard id="10" :plan="plan" :hoverEffect="true" />
            </template>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Login Modal -->
  <LoginModal :show="showLoginModal" @close="showLoginModal = false" />

  <myFooterShort />
</template>

<script>
import Navbar from '../components/myNavbar.vue'
import myFooterShort from '../components/myFooter.vue'
import PricingCard from '../components/PricingCard.vue'
import LoginModal from '../components/LoginModal.vue'
import ContactUsModal from '@/components/ContactUsModal.vue'

export default {
  name: 'LandingPage',
  components: {
    Navbar,
    myFooterShort,
    PricingCard,
    LoginModal,
    ContactUsModal,
  },
  data() {
    return {
      showLoginModal: false,
      showContactModal: false,
      plans: [
        {
          title: 'Reformer Duo',
          icon: 'fas fa-user-friends',
          bgColor: 'bg-[#7B4F2E]',
          textColor: '#7B4F2E',
          note: '*Prices are per person',
          options: [
            { label: '1 Session', price: '₱1,300' },
            { label: '10 Sessions', price: '₱12,000', note: '₱1,200/session' },
            { label: '20 Sessions', price: '₱23,000', note: '₱1,150/session' },
          ],
        },
        {
          title: 'Private Class',
          icon: 'fas fa-user',
          bgColor: 'bg-[#A4472E]',
          textColor: '#A4472E',
          options: [
            { label: '1 Session', price: '₱1,500' },
            { label: '5 Sessions', price: '₱7,250', note: '₱1,450/session' },
            { label: '10 Sessions', price: '₱14,000', note: '₱1,400/session' },
            { label: '20 Sessions', price: '₱26,000', note: '₱1,300/session' },
          ],
        },

        {
          title: 'Reformer Trio',
          icon: 'fas fa-users',
          bgColor: 'bg-yellow-400',
          textColor: '#8B572A',
          note: '*Prices are per person',
          options: [
            { label: '1 Session', price: '₱1,200' },
            { label: '10 Sessions', price: '₱11,500', note: '₱1,150/session' },
            { label: '20 Sessions', price: '₱22,000', note: '₱1,100/session' },
          ],
        },
        {
          title: 'Group Class',
          icon: 'fas fa-users',
          bgColor: 'bg-[#2F855A]',
          textColor: '#2F855A',
          options: [
            { label: '1 Session', price: '₱1,100' },
            { label: '10 Sessions', price: '₱9,750', note: '₱975/session' },
            { label: '20 Sessions', price: '₱18,000', note: '₱900/session' },
          ],
        },
        {
          title: 'Yoga Class',
          icon: 'fas fa-user',
          bgColor: 'bg-[#333]',
          textColor: '#333',
          options: [
            { label: '1 Session', price: '₱500' },
            { label: '5 Sessions', price: '₱2,250', note: '₱450/session' },
            { label: '10 Sessions', price: '₱4,000', note: '₱400/session' },
            {
              label: 'Monthly Unlimited',
              price: '₱4,500',
              note: 'Best Value',
              highlight: true,
            },
          ],
        },
      ],

      // plans: [
      //   {
      //     title: 'Reformer Duo',
      //     icon: 'fas fa-user-friends',
      //     bgColor: 'bg-[#7B4F2E]',
      //     textColor: '#7B4F2E',
      //     note: '*Prices are per person',
      //     options: [
      //       { label: '1 Session', price: '₱1,200' },
      //       { label: '10 Sessions', price: '₱11,250', note: '₱1,125/session' },
      //       { label: '20 Sessions', price: '₱21,500', note: '₱1,075/session' },
      //     ],
      //   },
      //   {
      //     title: 'Private Class',
      //     icon: 'fas fa-user',
      //     bgColor: 'bg-[#A4472E]',
      //     textColor: '#A4472E',
      //     options: [
      //       { label: '1 Session', price: '₱1,400' },
      //       { label: '10 Sessions', price: '₱13,000', note: '₱1,300/session' },
      //       { label: '20 Sessions', price: '₱24,000', note: '₱1,200/session' },
      //     ],
      //   },

      //   {
      //     title: 'Reformer Trio',
      //     icon: 'fas fa-users',
      //     bgColor: 'bg-yellow-400',
      //     textColor: '#8B572A',
      //     note: '*Prices are per person',
      //     options: [
      //       { label: '1 Session', price: '₱1,050' },
      //       { label: '10 Sessions', price: '₱9,750', note: '₱975/session' },
      //       { label: '20 Sessions', price: '₱18,500', note: '₱925/session' },
      //     ],
      //   },
      //   {
      //     title: 'Group Class',
      //     icon: 'fas fa-users',
      //     bgColor: 'bg-[#2F855A]',
      //     textColor: '#2F855A',
      //     options: [
      //       { label: '1 Session', price: '₱999' },
      //       { label: '10 Sessions', price: '₱9,000', note: '₱900/session' },
      //       { label: '20 Sessions', price: '₱17,000', note: '₱850/session' },
      //     ],
      //   },
      //   {
      //     title: 'Yoga Class',
      //     icon: 'fas fa-user',
      //     bgColor: 'bg-[#333]',
      //     textColor: '#333',
      //     options: [
      //       { label: '1 Session', price: '₱400' },
      //       { label: '5 Sessions', price: '₱1,875', note: '₱375/session' },
      //       { label: '10 Sessions', price: '₱3,500', note: '₱350/session' },
      //       {
      //         label: 'Monthly Unlimited',
      //         price: '₱4,000',
      //         note: 'Best Value',
      //         highlight: true,
      //       },
      //     ],
      //   },
      // ],
      scrollDebounce: null,
    }
  },
  mounted() {
    const container = this.$refs.scrollContainer
    this.$nextTick(() => {
      const cardWidth = container.firstElementChild.offsetWidth + 16
      container.scrollLeft = this.plans.length * cardWidth
    })

    let isDown = false
    let startX, scrollLeft

    container.addEventListener('mousedown', (e) => {
      isDown = true
      startX = e.pageX - container.offsetLeft
      scrollLeft = container.scrollLeft
    })

    container.addEventListener('mouseleave', () => (isDown = false))
    container.addEventListener('mouseup', () => (isDown = false))
    container.addEventListener('mousemove', (e) => {
      if (!isDown) return
      e.preventDefault()
      const x = e.pageX - container.offsetLeft
      const walk = (x - startX) * 0.5
      container.scrollLeft = scrollLeft - walk
    })

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') this.showLoginModal = false
    })
  },
  methods: {
    openBookingModal() {
      this.showLoginModal = true
    },
    handleScroll() {
      if (this.scrollDebounce) clearTimeout(this.scrollDebounce)

      this.scrollDebounce = setTimeout(() => {
        const container = this.$refs.scrollContainer
        const cardWidth = container.firstElementChild.offsetWidth + 16
        const totalWidth = this.plans.length * cardWidth
        const scrollLeft = container.scrollLeft

        const prevScrollBehavior = container.style.scrollBehavior
        container.style.scrollBehavior = 'auto'

        if (scrollLeft >= totalWidth * 2) {
          container.scrollLeft = scrollLeft - totalWidth
        } else if (scrollLeft < totalWidth * 0.5) {
          container.scrollLeft = scrollLeft + totalWidth
        }

        requestAnimationFrame(() => {
          container.style.scrollBehavior = prevScrollBehavior
        })
      }, 60)
    },
  },
  computed: {
    uiPlans() {
      return this.plans.map((p) => {
        if (p.title === 'Private Class') {
          return { ...p, title: 'Reformer Solo' } // UI-only rename
        }
        return p
      })
    },
  },
  directives: {
    scrollAnimation: {
      mounted(el) {
        const elements = Array.from(el.querySelectorAll('[id]'))
          .filter((child) => /^\d+$/.test(child.id))
          .sort((a, b) => Number(a.id) - Number(b.id))

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

        resetAll()

        let lastScrollY = window.pageYOffset
        const threshold = 0.3
        const stagger = 200

        function createChildObserver() {
          return new IntersectionObserver(
            (entries, observer) => {
              const currentScrollY = window.pageYOffset
              const scrollingDown = currentScrollY > lastScrollY
              lastScrollY = currentScrollY

              const visible = entries.filter((e) => e.isIntersecting)

              visible.sort((a, b) => {
                const aId = Number(a.target.id),
                  bId = Number(b.target.id)
                return scrollingDown ? aId - bId : bId - aId
              })

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

        let childObserver = createChildObserver()
        elements.forEach((child) => childObserver.observe(child))

        const sectionObserver = new IntersectionObserver(
          ([sec]) => {
            if (!sec.isIntersecting) {
              resetAll()
              childObserver.disconnect()
              childObserver = null
            } else {
              lastScrollY = window.pageYOffset
              childObserver = createChildObserver()
              elements.forEach((child) => {
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
}
</script>

<style scoped>
@keyframes fadeIn {
  from {
    opacity: 0;
  }

  to {
    opacity: 1;
  }
}

.animate-fadeIn {
  animation: fadeIn 1s ease-in;
}

.no-scrollbar::-webkit-scrollbar {
  display: none;
}

.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
