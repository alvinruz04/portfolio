<template>
  <AdminLayout
    title="Admin Dashboard"
    subtitle="Overview of APESCON service operations and management tools"
    active="dashboard"
    v-model:sidebarOpen="sidebarOpen"
  >
    <template #default="{ me }">
      <div class="space-y-6">
        <!-- Welcome -->
        <section
          class="relative overflow-hidden rounded-[26px] border border-[#D8E4ED] bg-white shadow-[0_18px_50px_rgba(2,81,153,0.06)]"
        >
          <div
            class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-[#025199] via-[#F27C29] to-[#D51C26]"
          ></div>

          <div
            class="pointer-events-none absolute -right-16 -top-16 h-56 w-56 rounded-full bg-[#025199]/6 blur-3xl"
          ></div>

          <div class="relative z-10 p-6 sm:p-7">
            <div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-center">
              <div>
                <p
                  class="font-montserrat text-[10px] font-semibold uppercase tracking-[0.19em] text-[#F27C29]"
                >
                  APESCON Administration
                </p>

                <h2
                  class="mt-3 font-montserrat text-[25px] font-bold leading-tight text-[#163A5F] sm:text-[31px]"
                >
                  Welcome back{{ me?.name ? `, ${me.name}` : '' }}
                </h2>

                <p class="mt-3 max-w-2xl font-nunito text-[14px] leading-7 text-[#64748B]">
                  Manage customer records, service schedules, quotations, contracts, and other
                  APESCON operations from one secure workspace.
                </p>
              </div>

              <div
                class="flex shrink-0 items-center gap-3 rounded-[18px] border border-[#D8E4ED] bg-[#F8FBFD] px-4 py-3"
              >
                <span
                  class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#025199]/8 text-[#025199]"
                >
                  <i class="fa-solid fa-shield-halved text-[14px]"></i>
                </span>

                <div>
                  <p
                    class="font-montserrat text-[9px] font-semibold uppercase tracking-[0.15em] text-[#94A3B8]"
                  >
                    Account
                  </p>

                  <p class="mt-0.5 font-nunito text-[12px] font-semibold text-[#334155]">
                    Administrator
                  </p>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Statistics -->
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
          <article
            v-for="card in summaryCards"
            :key="card.key"
            class="group relative overflow-hidden rounded-[22px] border border-[#D8E4ED] bg-white p-5 shadow-[0_12px_34px_rgba(2,81,153,0.045)] transition-all duration-300 hover:-translate-y-1 hover:border-[#025199]/25 hover:shadow-[0_18px_42px_rgba(2,81,153,0.09)]"
          >
            <div class="flex items-start justify-between gap-4">
              <span
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-[14px] border border-[#025199]/10 bg-[#025199]/7 text-[#025199]"
              >
                <i :class="[card.icon, 'text-[15px]']"></i>
              </span>

              <span
                class="rounded-full bg-[#F3F7FA] px-2.5 py-1 font-montserrat text-[8px] font-semibold uppercase tracking-[0.12em] text-[#94A3B8]"
              >
                Coming Soon
              </span>
            </div>

            <p
              class="mt-5 font-montserrat text-[10px] font-semibold uppercase tracking-[0.15em] text-[#64748B]"
            >
              {{ card.label }}
            </p>

            <p class="mt-2 font-montserrat text-[30px] font-bold text-[#163A5F]">—</p>

            <p class="mt-1 font-nunito text-[12px] leading-5 text-[#94A3B8]">
              {{ card.description }}
            </p>
          </article>
        </section>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
          <!-- Workflow -->
          <section
            class="rounded-[26px] border border-[#D8E4ED] bg-white p-6 shadow-[0_16px_46px_rgba(2,81,153,0.055)] sm:p-7 xl:col-span-8"
          >
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
              <div>
                <p
                  class="font-montserrat text-[10px] font-semibold uppercase tracking-[0.18em] text-[#F27C29]"
                >
                  Business Workflow
                </p>

                <h3 class="mt-2 font-montserrat text-[21px] font-bold text-[#163A5F]">
                  APESCON service process
                </h3>

                <p class="mt-2 font-nunito text-[13px] leading-6 text-[#64748B]">
                  The portal will follow the company's actual customer-to-service process.
                </p>
              </div>

              <span
                class="inline-flex w-fit items-center gap-2 rounded-full border border-[#D8E4ED] bg-[#F8FBFD] px-3 py-2 font-montserrat text-[8px] font-semibold uppercase tracking-[0.12em] text-[#64748B]"
              >
                <i class="fa-solid fa-diagram-project text-[#025199]"></i>
                Operational Flow
              </span>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
              <article
                v-for="(step, index) in workflow"
                :key="step.title"
                class="rounded-[18px] border border-[#E1EAF1] bg-[#F8FBFD] p-4"
              >
                <div class="flex items-start gap-3">
                  <span
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#025199] font-montserrat text-[10px] font-bold text-white shadow-[0_8px_20px_rgba(2,81,153,0.18)]"
                  >
                    {{ String(index + 1).padStart(2, '0') }}
                  </span>

                  <div>
                    <h4
                      class="font-montserrat text-[11px] font-semibold uppercase tracking-widest text-[#163A5F]"
                    >
                      {{ step.title }}
                    </h4>

                    <p class="mt-1.5 font-nunito text-[12px] leading-5 text-[#64748B]">
                      {{ step.description }}
                    </p>
                  </div>
                </div>
              </article>
            </div>
          </section>

          <!-- Initial modules -->
          <section
            class="rounded-[26px] border border-[#D8E4ED] bg-linear-to-br from-[#062B4C] via-[#025199] to-[#073D68] p-6 shadow-[0_18px_50px_rgba(5,35,61,0.16)] sm:p-7 xl:col-span-4"
          >
            <p
              class="font-montserrat text-[10px] font-semibold uppercase tracking-[0.18em] text-[#F7B267]"
            >
              Initial Modules
            </p>

            <h3 class="mt-2 font-montserrat text-[21px] font-bold leading-tight text-white">
              Core tools being prepared
            </h3>

            <div class="mt-6 space-y-3">
              <div
                v-for="module in initialModules"
                :key="module.title"
                class="flex items-start gap-3 rounded-2xl border border-white/12 bg-white/8 p-4"
              >
                <span
                  class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#F27C29] text-white"
                >
                  <i :class="[module.icon, 'text-[12px]']"></i>
                </span>

                <div>
                  <p
                    class="font-montserrat text-[10px] font-semibold uppercase tracking-widest text-white"
                  >
                    {{ module.title }}
                  </p>

                  <p class="mt-1 font-nunito text-[11px] leading-5 text-white/60">
                    {{ module.description }}
                  </p>
                </div>
              </div>
            </div>
          </section>
        </div>
      </div>
    </template>
  </AdminLayout>
</template>

<script>
import AdminLayout from '@/layouts/AdminLayout.vue'

export default {
  name: 'AdminDashboard',

  components: {
    AdminLayout,
  },

  data() {
    return {
      sidebarOpen: false,

      summaryCards: [
        {
          key: 'customers',
          label: 'Customers',
          description: 'Residential, commercial and government profiles',
          icon: 'fa-solid fa-users',
        },
        {
          key: 'quotations',
          label: 'Pending Quotations',
          description: 'Inspection estimates awaiting action',
          icon: 'fa-regular fa-file-lines',
        },
        {
          key: 'contracts',
          label: 'Active Contracts',
          description: 'Approved service agreements and coverage',
          icon: 'fa-solid fa-file-signature',
        },
        {
          key: 'services',
          label: 'Upcoming Services',
          description: 'Scheduled treatments and follow-up visits',
          icon: 'fa-regular fa-calendar-check',
        },
      ],

      workflow: [
        {
          title: 'Customer Inquiry',
          description: 'Create or locate the customer profile after a service inquiry.',
        },
        {
          title: 'Site Inspection',
          description: 'Record the property assessment and identified pest concern.',
        },
        {
          title: 'Quotation',
          description: 'Prepare the proposed treatment scope and service cost.',
        },
        {
          title: 'Contract',
          description: 'Document the approved service agreement and warranty terms.',
        },
        {
          title: 'Payment',
          description: 'Record payment information related to the approved service.',
        },
        {
          title: 'Service Execution',
          description: 'Schedule and monitor the treatment and assigned service activity.',
        },
        {
          title: 'Follow-Up',
          description: 'Track quarterly or warranty-related revisit schedules.',
        },
      ],

      initialModules: [
        {
          title: 'Customer Profiling',
          description: 'Centralized customer and property records.',
          icon: 'fa-solid fa-address-card',
        },
        {
          title: 'Service Calendar',
          description: 'Monitor upcoming treatments and revisit schedules.',
          icon: 'fa-regular fa-calendar-days',
        },
        {
          title: 'Access Control',
          description: 'Role-based access for authorized APESCON personnel.',
          icon: 'fa-solid fa-user-shield',
        },
      ],
    }
  },
}
</script>
